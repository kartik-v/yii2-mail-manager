<?php

/**
 * @package   yii2-mail-manager
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.0
 */

namespace kartik\mailmanager;

use kartik\base\Module as KrajeeModule;
use kartik\mailmanager\components\Mailer;
use kartik\mailmanager\components\Message;
use kartik\mailmanager\models\Queue;
use kartik\mailmanager\models\QueueSearch;
use kartik\mailmanager\models\Template;
use kartik\mailmanager\models\TemplateSearch;
use yii\filters\VerbFilter;
use Yii;

/**
 * This module allows global level configurations for the Krajee Mail Manager module.
 *
 * ```php
 * 'modules' => [
 *     'mailmanager' => [
 *          'class' => 'kartik\mailmanager\Module',
 *     ]
 * ]
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since  1.0
 */
class Module extends KrajeeModule
{
    /**
     * The module name for Krajee mail manager
     */
    const NAME = "mailmanager";

    /**
     * New paragraph break
     */
    const PARA = "\r\n";

    /**
     * @var array the class map
     */
    public $classMap = [];

    /**
     * @var array the queue controller behaviors. Use this to setup the access control rules and controller behaviors.
     */
    public $queueControllerBehaviors = [];

    /**
     * @var array the template controller behaviors. Use this to setup the access control rules and controller behaviors.
     */
    public $templateControllerBehaviors = [];

    /**
     * @var array the mailer component default configuration settings
     */
    public $mailerConfig = [];

    /**
     * @var array list of queue categories set as key value pairs
     */
    public $queueCategories = [];

    /**
     * @var array list of queue statuses set as key value pairs
     */
    public $queueStatuses = [];

    /**
     * @var array CSS classes for queue statuses set as key value pairs
     */
    public $queueStatusCss = [];

    /**
     * @var string the default queue category
     */
    public $defaultQueueCategory = 'other';

    /**
     * @var array the list of template properties to apply when applying template to the message
     */
    public $templatePropertiesToApply = [
        'subject',
        'html_body',
        'text_body',
        'priority',
        'from',
        'to',
        'cc',
        'bcc',
        'reply_to',
        'read_receipt_to',
    ];

    /**
     * @var array the list of template properties that can send an array list of values as a string separated by commas
     */
    public $templateArrayProperties = [
        'from',
        'to',
        'cc',
        'bcc',
        'reply_to',
        'read_receipt_to',
    ];

    /**
     * @inheritdoc
     */
    protected $_msgCat = 'kvmailmanager';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->initSettings();
    }

    /**
     * Initialize module settings
     */
    protected function initSettings()
    {
        if (empty($this->queueCategories)) {
            $this->queueCategories = [
                'user-account' => Yii::t('kvmailmanager', 'User Account'),
                'user-registration' => Yii::t('kvmailmanager', 'User Registration'),
                'other' => Yii::t('kvmailmanager', 'Other'),
            ];
        }
        $this->queueStatuses += [
            Queue::STATUS_PENDING => Yii::t('kvmailmanager', 'Pending'),
            Queue::STATUS_PROCESSED => Yii::t('kvmailmanager', 'Processed'),
            Queue::STATUS_ERROR => Yii::t('kvmailmanager', 'Error'),
            Queue::STATUS_DELETED => Yii::t('kvmailmanager', 'Deleted'),
        ];
        $this->queueStatusCss += [
            Queue::STATUS_PENDING => 'text-warning',
            Queue::STATUS_PROCESSED => 'text-success',
            Queue::STATUS_ERROR => 'text-danger',
            Queue::STATUS_DELETED => 'text-muted',
        ];
        $this->classMap += [
            // model classes
            'queue' => Queue::class,
            'template' => Template::class,
            // search classes
            'queueSearch' => QueueSearch::class,
            'templateSearch' => TemplateSearch::class,
            // component classes
            'mailer' => Mailer::class,
            'message' => Message::class,
        ];
        $defaultBehaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        if (empty($this->queueControllerBehaviors)) {
            $this->queueControllerBehaviors = $defaultBehaviors;
        }
        if (empty($this->templateControllerBehaviors)) {
            $this->templateControllerBehaviors = $defaultBehaviors;
        }
    }
}
