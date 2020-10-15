<?php
/**
 * Файл класса SxGeoCountry
 *
 * @copyright Copyright (c) 2019, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace chulakov\sypexgeo\models;

use yii\db\ActiveRecord;

/**
 * Модель доступа к данным стран
 *
 * @property int $id
 * @property string $iso
 * @property string $continent
 * @property string $name_ru
 * @property string $name_en
 * @property float $lat
 * @property float $lon
 * @property string $timezone
 */
class SxGeoCountry extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return '{{%sxgeo_country}}';
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['iso', 'continent'], 'string', 'max' => 2],
            [['name_ru', 'name_en'], 'string', 'max' => 128],
            [['lat', 'lon'], 'double'],
            [['timezone'], 'string', 'max' => 30],
        ];
    }
}
