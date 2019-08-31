<?php

namespace kartik\mailmanager\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\HtmlPurifier;

/**
 * This is the model class for table "{{%mm_template}}".
 *
 * @property string $id
 * @property string $name
 * @property string $subject
 * @property int $priority
 * @property string $html_body
 * @property string $text_body
 * @property string $from
 * @property string $to
 * @property string $cc
 * @property string $bcc
 * @property string $reply_to
 * @property string $read_receipt_to
 * @property int $created_at
 * @property int $created_by
 * @property int $updated_at
 * @property int $updated_by
 */
class Template extends ActiveRecord
{
    /**
     * @var array list of priorities
     */
    public static $priorities = [1 => 1, 2, 3, 4, 5];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%mm_template}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
            ],
            'audit' => [
                'class' => 'yii\behaviors\BlameableBehavior',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['priority'], 'in', 'range' => array_values(static::$priorities)],
            [
                ['html_body'],
                'filter',
                'filter' => function ($value) {
                    return HtmlPurifier::process($value);
                },
            ],
            [['html_body', 'text_body'], 'string'],
            [['name', 'subject'], 'string', 'max' => 255],
            [['from', 'to', 'cc', 'bcc', 'reply_to', 'read_receipt_to'], 'string', 'max' => 5000],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('kvmailmanager', 'ID'),
            'name' => Yii::t('kvmailmanager', 'Name'),
            'subject' => Yii::t('kvmailmanager', 'Subject'),
            'priority' => Yii::t('kvmailmanager', 'Priority'),
            'html_body' => Yii::t('kvmailmanager', 'Message Body (HTML)'),
            'text_body' => Yii::t('kvmailmanager', 'Message Body (Text)'),
            'from' => Yii::t('kvmailmanager', 'From'),
            'to' => Yii::t('kvmailmanager', 'To'),
            'cc' => Yii::t('kvmailmanager', 'CC'),
            'bcc' => Yii::t('kvmailmanager', 'BCC'),
            'reply_to' => Yii::t('kvmailmanager', 'Reply To'),
            'read_receipt_to' => Yii::t('kvmailmanager', 'Read Receipt To'),
            'created_at' => Yii::t('kvmailmanager', 'Created At'),
            'created_by' => Yii::t('kvmailmanager', 'Created By'),
            'updated_at' => Yii::t('kvmailmanager', 'Updated At'),
            'updated_by' => Yii::t('kvmailmanager', 'Updated By'),
        ];
    }
}
