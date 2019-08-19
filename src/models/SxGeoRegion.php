<?php
/**
 * Файл класса SxGeoRegion
 *
 * @copyright Copyright (c) 2019, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

use yii\db\ActiveRecord;

/**
 * Модель доступа к данным регионов
 *
 * @property int $id
 * @property string $iso
 * @property string $country
 * @property string $name_ru
 * @property string $name_en
 * @property string $timezone
 * @property string $okato
 */
class SxGeoRegion extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return '{{%sxgeo_region}}';
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['iso'], 'string', 'max' => 7],
            [['country'], 'string', 'max' => 2],
            [['name_ru', 'name_en'], 'string', 'max' => 128],
            [['lat', 'lon'], 'double'],
            [['timezone'], 'string', 'max' => 30],
            [['okato'], 'string', 'max' => 4],
        ];
    }
}
