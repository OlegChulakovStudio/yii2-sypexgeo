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
     * @var string Файл .dat с сжатыми данными
     */
    public $dataFile = '@console/runtime/sypexgeo/SxGeoCity.dat';

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
        throw new \RuntimeException('Не удалось определить IP');
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
        if ($data = $this->getData($ip, 'city')) {
            return $data['city'];
        }
        throw new NotFoundGeoException("Не удалось определить город для IP: {$this->ip}");
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
        if ($data = $this->getData($ip, 'region')) {
            return $data['region'];
        }
        throw new NotFoundGeoException("Не удалось определить регион для IP: {$this->ip}");
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
        if ($data = $this->getData($ip, 'country')) {
            return $data['country'];
        }
        throw new NotFoundGeoException("Не удалось определить страну для IP: {$this->ip}");
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
        if ($data = $this->getData($ip, 'full')) {
            return $data;
        }
        throw new NotFoundGeoException("Не удалось определить данные для IP: {$this->ip}");
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
        if ($data = $this->getData($ip, 'countryIso')) {
            return $data;
        }
        throw new NotFoundGeoException("Не удалось определить страну для IP: {$this->ip}");
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

        switch ($type) {
            case 'city':
                $data = $this->getSxGeo()->getCity($this->ip);
                break;
            case 'countryIso':
                $data = $this->getSxGeo()->getCountry($this->ip);
                break;
            default:
                $data = $this->getSxGeo()->getCityFull($this->ip);
        }
        return $data;
    }
}
