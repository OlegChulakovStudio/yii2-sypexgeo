<?php
/**
 * Файл класса ImportService
 *
 * @copyright Copyright (c) 2018, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace chulakov\sypexgeo\services;

use chulakov\sypexgeo\enum\DataFileEnum;
use chulakov\sypexgeo\enum\ZipFileEnum;
use chulakov\sypexgeo\exceptions\DataImportException;
use yii\base\InvalidArgumentException;

/**
 * Класс ImportService - сервис для загрузки файлов БД Sypexgeo и заполнения таблиц sxgeo_*
 *
 * @package chulakov\sypexgeo\services
 */
class ImportService
{
    /**
     * @var string URL сервера с файлами Sypexgeo
     */
    public $sourceUrl = 'https://sypexgeo.net/files';
    /**
     * @var bool Флаг вывода информации в поток вывода
     */
    public $infoMode = true;
    /**
     * @var string Путь на сервере, куда будут складываться загруженные и распакованные файлы
     */
    public $dataDir = '@console/runtime/sypexgeo';

    /**
     * Конструктор ImportService.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        foreach ($options as $key => $value) {
            if (isset($this->{$key})) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Выполнение импорта данных требуемого типа
     *
     * @param string $type
     */
    public function run($type)
    {
        try {

            $this->typeValidate($type);
            $this->makeDataDir();
            $this->downloadZip($type);
            $this->unpackZip($type);

        } catch (DataImportException $e) {
            $this->info($e->getMessage());
        } catch (\Exception $e) {
            $this->info("Во время импорта .dat файла произошла ошибка! " . $e->getMessage());
            \Yii::error($e->getTrace());
        }
    }

    /**
     * Проверка коррекности переданного значения типа файла
     *
     * @param string $type
     */
    protected function typeValidate($type)
    {
        if (!in_array($type, ZipFileEnum::getRange())) {
            throw new InvalidArgumentException("Неподдерживаемый тип файла: {$type}");
        }
    }

    /**
     * Создание каталога для хранения файлов .dat
     */
    protected function makeDataDir()
    {
        if (!is_dir($this->getDataDir())) {
            mkdir($this->getDataDir(), 0777, true);
        }
    }

    /**
     * Скачивание архива с БД Sypexgeo
     *
     * @param string $type
     * @throws DataImportException
     */
    protected function downloadZip($type)
    {
        $this->info('Скачиваем файл...');

        $zipFilePath = $this->getZipFilePath($type);

        $fp = fopen($zipFilePath, 'wb');
        $ch = curl_init($this->getSourceFile($type));
        curl_setopt_array($ch, [
            CURLOPT_FILE => $fp,
            CURLOPT_HTTPHEADER => $this->getCurlHeader($type)
        ]);
        if (!curl_exec($ch)) {
            throw new \RuntimeException('Ошибка при скачивании архива');
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

        if (304 == $code) {
            @unlink($zipFilePath);
            throw new DataImportException('Архив не обновился с момента предыдущего скачивания');
        }

        $this->info('Файл скачан успешно');
    }

    /**
     * Распаковка скачанного архива
     *
     * @param $type
     */
    protected function unpackZip($type)
    {
        $this->info('Распаковываем архив...');

        $zipFilePath = $this->getZipFilePath($type);
        $extractFileName = $this->extractFileName($type);
        $dataDir = $this->getDataDir();

        $fp = fopen('zip://' . $zipFilePath . '#' . $extractFileName, 'rb');
        if (!$fp) {
            throw new \RuntimeException("Не удалось открыть файл {$zipFilePath}#{$extractFileName}");
        }

        $tempExtractFileName = $dataDir . DIRECTORY_SEPARATOR . $extractFileName . '.tmp';
        $fw = fopen($tempExtractFileName, 'wb');
        if (!$fw) {
            throw new \RuntimeException("Не удалось открыть/создать файл {$tempExtractFileName}");
        }

        stream_copy_to_stream($fp, $fw);
        fclose($fp);
        fclose($fw);

        if (filesize($tempExtractFileName) == 0) {
            throw new \RuntimeException('Ошибка при распаковке архива');
        }

        @unlink($zipFilePath);
        if (!rename($tempExtractFileName, $dataDir . DIRECTORY_SEPARATOR . $extractFileName)) {
            throw new \RuntimeException('Ошибка при переименовании файла');
        }

        $this->updateLastUpdateFile($type);

        $this->info('Архив распакован');
    }

    /**
     * Вывести сообщение в канал вывода
     *
     * @param string $message
     */
    protected function info($message)
    {
        if ($this->infoMode) {
            echo $message . "\n";
        }
    }

    /**
     * Получение полного пути к каталогу с файлами БД Sypexgeo
     *
     * @return string
     */
    protected function getDataDir()
    {
        return \Yii::getAlias(rtrim($this->dataDir, '/\\'));
    }

    /**
     * Получение URL загружаемого файла
     *
     * @param $type
     * @return string
     */
    protected function getSourceFile($type)
    {
        return $this->sourceUrl . '/' . ZipFileEnum::getLabel($type);
    }

    /**
     * Получение полного пути к .upd файлу
     *
     * @param string $type
     * @return string
     */
    protected function getLastUpdateFilePath($type)
    {
        return $this->getDataDir() . DIRECTORY_SEPARATOR . ZipFileEnum::getUpdFile($type);
    }

    /**
     * Получение полного пути к .zip файлу
     *
     * @param string $type
     * @return string
     */
    protected function getZipFilePath($type)
    {
        return $this->getDataDir() . DIRECTORY_SEPARATOR . ZipFileEnum::getTempFile($type);
    }

    /**
     * Получение имени файла в архиве для распаковки
     *
     * @param string $type
     * @return string
     */
    protected function extractFileName($type)
    {
        return DataFileEnum::getLabel(ZipFileEnum::getDatType($type));
    }

    /**
     * Получение заголовков для CURL
     *
     * @param string $type
     * @return array
     */
    protected function getCurlHeader($type)
    {
        $lastUpdatedFile = $this->getLastUpdateFilePath($type);
        return file_exists($lastUpdatedFile)
            ? ['If-Modified-Since: ' . file_get_contents($lastUpdatedFile)]
            : [];
    }

    /**
     * Обновление даты последнего обновления файла
     *
     * @param string $type
     */
    protected function updateLastUpdateFile($type)
    {
        file_put_contents(
            $this->getLastUpdateFilePath($type),
            gmdate('D, d M Y H:i:s') . ' GMT'
        );
    }
}
