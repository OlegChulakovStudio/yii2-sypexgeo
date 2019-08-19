<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%region}}`.
 */
class m190603_163133_create_region_table extends Migration
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

        $this->createTable('{{%sxgeo_region}}', [
            'id' => $this->primaryKey(),
            'iso' => $this->string(7),
            'country' => $this->char(2)->notNull(),
            'name_ru' => $this->string(128)->notNull(),
            'name_en' => $this->string(128)->notNull(),
            'timezone' => $this->string(30),
            'okato' => $this->char(4),
        ], $tableOptions);

        $this->importRegion();
    }

    /**
     * Импорт справочника регионов
     */
    protected function importRegion()
    {
        $this->truncateTable('{{%sxgeo_region}}');

        $this->compact = true;

        $file = new SplFileObject(__DIR__ . '/data/region.tsv');
        while ($row = $file->fgetcsv("\t")) {
            try {
                $this->insert('{{%sxgeo_region}}', [
                    'id' => $row[0],
                    'iso' => $row[1],
                    'country' => $row[2],
                    'name_ru' => $row[3],
                    'name_en' => $row[4],
                    'timezone' => $row[5],
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
        $this->dropTable('{{%sxgeo_region}}');
    }
}
