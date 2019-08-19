<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%city}}`.
 */
class m190603_164439_create_city_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sxgeo_city}}', [
            'id' => $this->primaryKey(),
            'region_id' => $this->integer(),
            'name_ru' => $this->string(128)->notNull(),
            'name_en' => $this->string(128)->notNull(),
            'lat' => $this->decimal(10, 5)->notNull(),
            'lon' => $this->decimal(10, 5)->notNull(),
            'okato' => $this->string(20),
        ], $tableOptions);

        $this->importCities();
    }

    /**
     * Импорт справочника городов
     */
    protected function importCities()
    {
        $this->truncateTable('{{%sxgeo_city}}');

        $this->compact = true;

        $file = new SplFileObject(__DIR__ . '/data/city.tsv');
        while ($row = $file->fgetcsv("\t")) {
            try {
                $this->insert('{{%sxgeo_city}}', [
                    'id' => $row[0],
                    'region_id' => $row[1],
                    'name_ru' => $row[2],
                    'name_en' => $row[3],
                    'lat' => $row[4],
                    'lon' => $row[5],
                    'okato' => isset($row[6]) ? $row[6] : "",
                ]);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sxgeo_city}}');
    }
}
