<?php
/**
 * Файл класса SypexgeoController
 *
 * @copyright Copyright (c) 2018, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace chulakov\sypexgeo\console\controllers;

use yii\base\Module;
use yii\console\Controller;
use chulakov\sypexgeo\services\ImportService;

/**
 * Отвечает за выполнение импорта .dat файлов через консольную команду sypexgeo/import
 * Имеет необязательный параметр $type, с помощью которого определяет какой файл следует загружать
 * Допустимые значения параметра:
 *    - country - файл с данными только стран
 *    - city_cp1251 - файл с данными городов в формате CP1251
 *    - city_utf8 (по умолчанию) - файл с данными городов в формате UTF8
 *
 * Конфигурируется при подключении контроллера через массив $importService
 * @see ImportService::$sourceUrl
 * @see ImportService::$infoMode
 * @see ImportService::$dataDir
 *
 * @package chulakov\sypexgeo\console\controllers
 */
class SypexgeoController extends Controller
{
    /**
     * Настройки для процесса импорта файлов .dat
     *
     * @var array
     */
    public $importOptions = [];

    /**
     * @var ImportService
     */
    protected $importService;

    /**
     * Консольный контроллер импорта данных с сервера sypexgeo
     *
     * @param string $id
     * @param Module $module
     * @param array $config
     */
    public function __construct($id, $module, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->importService = new ImportService($this->importOptions);
    }

    /**
     * Экшн для импорта данных sypexgeo с сервера в БД
     *
     * @param string $type
     */
    public function actionImport($type = 'city_utf8')
    {
        $this->importService->run($type);
    }
}
