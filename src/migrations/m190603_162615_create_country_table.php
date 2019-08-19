<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%country}}`.
 */
class m190603_162615_create_country_table extends Migration
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

        $this->createTable('{{%sxgeo_country}}', [
            'id' => $this->primaryKey(),
            'iso' => $this->char(2)->notNull(),
            'continent' => $this->char(2)->notNull(),
            'name_ru' => $this->string(128)->notNull(),
            'name_en' => $this->string(128)->notNull(),
            'lat' => $this->decimal(6, 2)->notNull(),
            'lon' => $this->decimal(6, 2)->notNull(),
            'timezone' => $this->string(30),
        ], $tableOptions);

        $this->importCountry();
    }

    /**
     * Импорт справочника стран
     */
    protected function importCountry()
    {
        $this->truncateTable('{{%sxgeo_country}}');

        $this->compact = true;

        $file = new SplFileObject(__DIR__ . '/data/country.tsv');
        while ($row = $file->fgetcsv("\t")) {
            try {
                $this->insert('{{%sxgeo_country}}', [
                    'id' => $row[0],
                    'iso' => $row[1],
                    'continent' => $row[2],
                    'name_ru' => $row[3],
                    'name_en' => $row[4],
                    'lat' => $row[5],
                    'lon' => $row[6],
                    'timezone' => $row[7],
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
        $this->dropTable('{{%sxgeo_country}}');
    }
}
