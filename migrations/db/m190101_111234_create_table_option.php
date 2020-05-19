<?php

use yii\db\Migration;

class m190101_111234_create_table_option extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%system_option}}', [
            'id' => $this->integer(11)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY')->comment('شناسه'),
            'grp' => $this->string(100)->comment('گروه'),
            'key' => $this->string(190)->comment('کلید'),
            'data' => $this->text()->comment('داده ها'),
            'created_at' => $this->integer(4)->unsigned()->comment('تاریخ ایجاد'),
            'updated_at' => $this->integer(4)->unsigned()->comment('تاریخ بروزرسانی'),
            'lang' => $this->char(5)->comment('زبان'),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%system_option}}');
    }
}
