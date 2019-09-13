<?php
/**
 * Файл класса Sypexgeo
 *
 * @copyright Copyright (c) 2018, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace chulakov\sypexgeo;

use chulakov\sypexgeo\exceptions\NotFoundGeoException;
use chulakov\sypexgeo\libraries\SxGeo;
use sem\helpers\RequestHelper;
use yii\base\Component;

/**
 * Класс получения информации о локации пользователя по ip адресу из базы Sypexgeo
 *
 * @package chulakov\sypexgeo
 */
class Sypexgeo extends Component
{
    /**
     * Тип получаемых данных - Весь пакет
     */
    const DATA_TYPE_ALL = 'full';
    /**
     * Тип получаемых данных - Только информация о городе
     */
    const DATA_TYPE_CITY = 'city';
    /**
     * Тип получаемых данных - Только информация о регионе
     */
    const DATA_TYPE_REGION = 'region';
    /**
     * Тип получаемых данных - Только информация о стране
     */
    const DATA_TYPE_COUNTRY = 'country';
    /**
     * Тип получаемых данных - Только информация о стране по ISO
     */
    const DATA_TYPE_COUNTRY_BY_ISO = 'countryIso';

    /**
     * @var string Файл .dat с сжатыми данными
     */
    public $dataFile = '@app/runtime/sypexgeo/SxGeoCity.dat';

    /**
     * @var SxGeo
     */
    protected $sxGeo;

    /**
     * @var string Последний запрошенный ip адрес пользователя
     */
    protected $ip;

    /**
     * Получение объекта для работы с файлом БД Sypexgeo
     *
     * @return SxGeo
     */
    public function getSxGeo()
    {
        if (!is_object($this->sxGeo)) {
            $this->sxGeo = new SxGeo(\Yii::getAlias($this->dataFile));
        }
        return $this->sxGeo;
    }

    /**
     * Получение корректного IP клиента
     *
     * @return string
     */
    public function getUserIp()
    {
        if ($ip = RequestHelper::getUserIp()) {
            return $ip;
        }
        if ($ip = \Yii::$app->request->getUserIP()) {
            return $ip;
        }
        throw new \RuntimeException(\Yii::t('ch/sypexgeo', 'Unable to determine client IP'));
    }

    /**
     * Получение ID города по IP
     *
     * @param string|null $ip
     * @return int
     * @throws NotFoundGeoException
     */
    public function getCityId($ip = null)
    {
        $data = $this->getCity($ip);
        return isset($data['id']) ? $data['id'] : null;
    }

    /**
     * Получение информации о городе по IP
     *
     * @param string|null $ip
     * @return array
     * @throws NotFoundGeoException
     */
    public function getCity($ip = null)
    {
        if ($data = $this->getData($ip, static::DATA_TYPE_CITY)) {
            return $data[static::DATA_TYPE_CITY];
        }
        throw new NotFoundGeoException(
            \Yii::t('ch/sypexgeo', 'Unable to determine city by client IP: {ip}', [
                'ip' => $this->ip,
            ])
        );
    }

    /**
     * Получение информации о регионе по IP
     *
     * @param string|null $ip
     * @return array
     * @throws NotFoundGeoException
     */
    public function getRegion($ip = null)
    {
        if ($data = $this->getData($ip, static::DATA_TYPE_REGION)) {
            return $data[static::DATA_TYPE_REGION];
        }
        throw new NotFoundGeoException(
            \Yii::t('ch/sypexgeo', 'Unable to determine region by client IP: {ip}', [
                'ip' => $this->ip,
            ])
        );
    }

    /**
     * Получение ID страны по IP
     *
     * @param string|null $ip
     * @return int
     * @throws NotFoundGeoException
     */
    public function getCountryId($ip = null)
    {
        $data = $this->getCountry($ip);
        return isset($data['id']) ? $data['id'] : null;
    }

    /**
     * Получение информации о стране по IP
     *
     * @param string|null $ip
     * @return array
     * @throws NotFoundGeoException
     */
    public function getCountry($ip = null)
    {
        if ($data = $this->getData($ip, static::DATA_TYPE_COUNTRY)) {
            return $data[static::DATA_TYPE_COUNTRY];
        }
        throw new NotFoundGeoException(
            \Yii::t('ch/sypexgeo', 'Unable to determine country by client IP: {ip}', [
                'ip' => $this->ip,
            ])
        );
    }

    /**
     * Получение полной гео информации по IP
     *
     * @param string|null $ip
     * @return array
     * @throws NotFoundGeoException
     */
    public function getFull($ip = null)
    {
        if ($data = $this->getData($ip, static::DATA_TYPE_ALL)) {
            return $data;
        }
        throw new NotFoundGeoException(
            \Yii::t('ch/sypexgeo', 'Unable to determine data by client IP: {ip}', [
                'ip' => $this->ip,
            ])
        );
    }

    /**
     * Получение информации о стране по IP
     *
     * @param string|null $ip
     * @return string
     * @throws NotFoundGeoException
     */
    public function getCountryIso($ip = null)
    {
        if ($data = $this->getData($ip, static::DATA_TYPE_COUNTRY_BY_ISO)) {
            return $data;
        }
        throw new NotFoundGeoException(
            \Yii::t('ch/sypexgeo', 'Could not be resolve country by client IP: {ip}', [
                'ip' => $this->ip,
            ])
        );
    }

    /**
     * Получение конкретных данных для разных методов
     *
     * @param string $ip
     * @param string $type
     * @return string|array
     */
    protected function getData($ip, $type)
    {
        $this->ip = $ip ?: $this->getUserIp();
        if ($type === static::DATA_TYPE_CITY) {
            return $this->getSxGeo()->getCity($this->ip);
        }
        if ($type === static::DATA_TYPE_COUNTRY_BY_ISO) {
            return $this->getSxGeo()->getCountry($this->ip);
        }
        return $this->getSxGeo()->getCityFull($this->ip);
    }
}
