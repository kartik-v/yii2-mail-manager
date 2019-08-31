<?php

/**
 * @package   yii2-mail-manager
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.0
 */

use yii\db\Migration;

/**
 * Create database migration class for the yii2-mail-manager module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since  1.0
 */
class m190828_141737_create_db extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        $isMysql = $this->db->driverName === 'mysql';
        if ($isMysql) {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $schema = $this->db->schema;
        if ($schema->getTableSchema('{{%mm_queue}}', true) === null) {
            $this->createTable('{{%mm_queue}}', [
                'id' => $this->bigPrimaryKey(),
                'category' => $this->string()->notNull(),
                'subject' => $this->string(),
                'attempts' => $this->integer()->notNull()->defaultValue(0),
                'created_at' => $this->integer()->notNull(),
                'processed_at' => $this->integer()->notNull(),
                'scheduled_at' => $this->integer(),
                'sent_at' => $this->integer(),
                'status' => $this->tinyInteger(3)->notNull()->defaultValue(0),
                'log' => $this->text(),
                'message' => $isMysql ? 'LONGTEXT' : $this->text(),
            ], $tableOptions);
            $this->createIndex('mm_queue_NK1', '{{%mm_queue}}', 'category');
            $this->createIndex('mm_queue_NK2', '{{%mm_queue}}', 'status');
        }
        if ($schema->getTableSchema('{{%mm_template}}', true) === null) {
            $this->createTable('{{%mm_template}}', [
                'id' => $this->bigPrimaryKey(),
                'name' => $this->string()->notNull(),
                'subject' => $this->string(),
                'priority' => $this->tinyInteger(2),
                'html_body' => $this->text(),
                'text_body' => $this->text(),
                'from' => $this->string(5000),
                'to' => $this->string(5000),
                'cc' => $this->string(5000),
                'bcc' => $this->string(5000),
                'reply_to' => $this->string(5000),
                'read_receipt_to' => $this->string(5000),
                'created_at' => $this->integer()->notNull(),
                'created_by' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
                'updated_by' => $this->integer()->notNull(),
            ], $tableOptions);
            $this->createIndex('mm_template_UK1', '{{%mm_template}}', 'name', true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->safeDrop('{{%mm_template}}');
        $this->safeDrop('{{%mm_queue}}');
    }

    /**
     * Safe drop table
     * @param string $table
     */
    protected function safeDrop($table)
    {
        $schema = $this->db->schema;
        if ($schema->getTableSchema($table, true) !== null) {
            $this->dropTable($table);
        }
    }
}
