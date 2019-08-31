<?php

/**
 * @package   yii2-mail-manager
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.0
 */

namespace kartik\mailmanager\models;

use kartik\base\Config;
use kartik\mailmanager\components\Message;
use kartik\mailmanager\Module;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%mm_queue}}".
 *
 * @property string $id
 * @property string $category
 * @property string $subject
 * @property int $attempts
 * @property int $created_at
 * @property int $scheduled_at
 * @property int $processed_at
 * @property int $sent_at
 * @property int $status
 * @property string $log
 * @property string $message
 * @property string $categoryDesc
 * @property string $statusDesc
 * @property Message $swiftMessage
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since  1.0
 */
class Queue extends ActiveRecord
{
    const STATUS_PENDING = 0;
    const STATUS_PROCESSED = 1;
    const STATUS_ERROR = 2;
    const STATUS_DELETED = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%mm_queue}}';
    }

    /**
     * Parse source array property in module setting and return array value
     * @param string $source module array property name
     * @param int|string $value array key to find
     * @return mixed
     * @throws InvalidConfigException
     */
    public static function parseSetting($source, $value)
    {
        $module = Config::getModule(Module::NAME);
        return ArrayHelper::getValue($module->$source, $value, null);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['processed_at'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category'], 'required'],
            [['attempts', 'created_at', 'processed_at', 'scheduled_at', 'sent_at', 'status'], 'integer'],
            [['log', 'message'], 'string'],
            [['category', 'subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('kvmailmanager', 'ID'),
            'category' => Yii::t('kvmailmanager', 'Category'),
            'categoryDesc' => Yii::t('kvmailmanager', 'Category'),
            'subject' => Yii::t('kvmailmanager', 'Subject'),
            'attempts' => Yii::t('kvmailmanager', 'Attempts'),
            'created_at' => Yii::t('kvmailmanager', 'Created At'),
            'processed_at' => Yii::t('kvmailmanager', 'Processed At'),
            'scheduled_at' => Yii::t('kvmailmanager', 'Scheduled At'),
            'sent_at' => Yii::t('kvmailmanager', 'Sent At'),
            'status' => Yii::t('kvmailmanager', 'Status'),
            'statusDesc' => Yii::t('kvmailmanager', 'Status'),
            'statusHtml' => Yii::t('kvmailmanager', 'Status'),
            'log' => Yii::t('kvmailmanager', 'Log'),
            'message' => Yii::t('kvmailmanager', 'Message'),
        ];
    }

    /**
     * Get category description
     * @return string
     * @throws InvalidConfigException
     */
    public function getCategoryDesc()
    {
        return static::parseSetting('queueCategories', $this->category);
    }

    /**
     * Get status description
     * @return string
     * @throws InvalidConfigException
     */
    public function getStatusDesc()
    {
        return static::parseSetting('queueStatuses', $this->status);
    }

    /**
     * Get status HTML markup
     * @param array $options the HTML attributes for the status container. Includes special attribute `tag` which
     * defaults to `span`.
     * @return string
     * @throws InvalidConfigException
     */
    public function getStatusHtml($options = [])
    {
        $css = static::parseSetting('queueStatusCss', $this->status);
        $tag = ArrayHelper::remove($options, 'tag', 'span');
        Html::addCssClass($options, $css);
        return Html::tag($tag, $this->getStatusDesc(), $options);
    }

    /**
     * Get the decoded swift message object
     * @return Message
     */
    public function getSwiftMessage()
    {
        return Message::decode($this->message);
    }
}