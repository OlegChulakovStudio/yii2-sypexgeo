<?php
/**
 * Файл класса ZipFileEnum
 *
 * @copyright Copyright (c) 2018, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace chulakov\sypexgeo\enum;

class ZipFileEnum
{
    /**
     * Файл со странами
     */
    const COUNTRY = 'country';
    /**
     * Файл с городами в формате cp1251
     */
    const CITY_CP1251 = 'city_cp1251';
    /**
     * Файл с городами в формате uft-8
     */
    const CITY_UTF8 = 'city_utf8';

    /**
     * Именования доступных zip файлов
     *
     * @var array
     */
    public static $labels = [
        self::COUNTRY => 'SxGeoCountry.zip',
        self::CITY_CP1251 => 'SxGeoCity_cp1251.zip',
        self::CITY_UTF8 => 'SxGeoCity_utf8.zip',
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

    /**
     * Соответствие загружаемым zip файлам их наименованиям файлов внутри архива
     *
     * @var array
     */
    public static $datTypes = [
        self::COUNTRY => DataFileEnum::COUNTRY,
        self::CITY_CP1251 => DataFileEnum::CITY,
        self::CITY_UTF8 => DataFileEnum::CITY,
    ];

    /**
     * Получение конкретного типа .dat файла из .zip файла
     *
     * @param string $type
     * @return string
     */
    public static function getDatType($type)
    {
        return isset(static::$datTypes[$type])
            ? static::$datTypes[$type]
            : static::$defaultLabel;
    }

    /**
     * Наименование для временных файлов
     *
     * @var array
     */
    public static $tempFileNames = [
        self::COUNTRY => 'SxGeoCountryTmp.zip',
        self::CITY_CP1251 => 'SxGeoCityCp1251Tmp.zip',
        self::CITY_UTF8 => 'SxGeoCityUtf8Tmp.zip',
    ];

    /**
     * Получение наименования временного файла
     *
     * @param string $type
     * @return string
     */
    public static function getTempFile($type)
    {
        return isset(static::$tempFileNames[$type])
            ? static::$tempFileNames[$type]
            : static::$defaultLabel;
    }

    /**
     * Наименование для файлов c информацией о последней загрузке
     *
     * @var array
     */
    public static $updFileNames = [
        self::COUNTRY => 'SxGeoCountry.upd',
        self::CITY_CP1251 => 'SxGeoCityCp1251.upd',
        self::CITY_UTF8 => 'SxGeoCityUtf8.upd',
    ];

    /**
     * Получение наименования файла c информацией о последней загрузке
     *
     * @param string $type
     * @return string
     */
    public static function getUpdFile($type)
    {
        return isset(static::$updFileNames[$type])
            ? static::$updFileNames[$type]
            : static::$defaultLabel;
    }
}
