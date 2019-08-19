<?php
/**
 * Файл класса SxGeoCity
 *
 * @copyright Copyright (c) 2019, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

use yii\db\ActiveRecord;

/**
 * Модель доступа к данным о городах
 *
 * @property int $id
 * @property int $region_id
 * @property string $name_ru
 * @property string $name_en
 * @property float $lat
 * @property float $lon
 * @property string $okato
 */
class SxGeoCity extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%sxgeo_city}}';
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['iso'], 'string', 'max' => 2],
            [['region_id'], 'integer'],
            [['name_ru', 'name_en'], 'string', 'max' => 128],
            [['lat', 'lon'], 'double'],
            [['okato'], 'string', 'max' => 20],
        ];
    }
}
