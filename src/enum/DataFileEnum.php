<?php
/**
 * Файл класса DataFileEnum
 *
 * @copyright Copyright (c) 2018, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace chulakov\sypexgeo\enum;

class DataFileEnum
{
    /**
     * Файл БД со странами
     */
    const COUNTRY = 'country';
    /**
     * Файл БД с городами
     */
    const CITY = 'city';

    /**
     * Именования доступных dat файлов
     *
     * @var array
     */
    public static $labels = [
        self::COUNTRY => 'SxGeo.dat',
        self::CITY => 'SxGeoCity.dat',
    ];

    /**
     * @var string Дефолтное именование справочника
     */
    public static $defaultLabel = '(не задано)';

    /**
     * Получение расшифровки из справочника
     *
     * @param string $type
     * @return string
     */
    public static function getLabel($type)
    {
        return isset(static::$labels[$type])
            ? static::$labels[$type]
            : static::$defaultLabel;
    }

    /**
     * Разрешенные справочные значения для быстрой валидации
     *
     * @return array
     */
    public static function getRange()
    {
        return array_keys(static::$labels);
    }
}
