<?php

/**
 * @package   yii2-mail-manager
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.0
 */

namespace kartik\mailmanager\components;

use Exception;
use kartik\base\Config;
use kartik\mailmanager\Module;
use kartik\mailmanager\models\Queue;
use kartik\mailmanager\models\Template;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\swiftmailer\Mailer as YiiMailer;

/**
 * Mailer is a sub class of [[yii\swiftmailer\Mailer]] which intends to replace it. The configuration is same as
 * in [[yii\swiftmailer\Mailer]] with some additional properties to control the mail queue. This extension replaces
 * [[yii\swiftmailer\Mailer]] with [[kartik\mailmanager\components\Mailer]] to enable queuing right from the message.
 *
 * ~~~
 *    'components' => [
 *        ...
 *        'mailer' => [
 *            'class' => 'kartik\mailmanager\components\Mailer',
 *            'batchSize' => 10,
 *            'maxAttempts' => 3,
 *            'transport' => [
 *                'class' => 'Swift_SmtpTransport',
 *                'host' => 'localhost',
 *                'username' => 'username',
 *                'password' => 'password',
 *                'port' => '587',
 *                'encryption' => 'tls',
 *            ],
 *        ],
 *        ...
 *    ],
 * ~~~
 *
 * @see http://www.yiiframework.com/doc-2.0/yii-swiftmailer-mailer.html
 * @see http://www.yiiframework.com/doc-2.0/ext-swiftmailer-index.html
 */
class Mailer extends YiiMailer
{
    /**
     * @var string queue model class name. Must be an instance of [[kartik\mailmanager\models\Queue]]. If not set
     * will default to 'kartik\mailmanager\models\Queue'.
     */
    public $queueClass = null;
    /**
     * @var string template model class name. Must be an instance of [[kartik\mailmanager\models\Template]]. If not set
     * will default to 'kartik\mailmanager\models\Template'.
     */
    public $templateClass = null;

    /**
     * @var string the message component class name. Must be an instance of [[kartik\mailmanager\components\Message]].
     * If not set will default to 'kartik\mailmanager\components\Message'.
     */
    public $messageClass = null;

    /**
     * @var integer the default batch size for the number of mails to be processed and sent per processing round.
     */
    public $batchSize = 10;

    /**
     * @var integer maximum number of attempts to try sending an email out.
     */
    public $maxAttempts = 3;

    /**
     * @var boolean soft purge items in queue by setting it to inactive status and cleaning up swift message after
     * sending.
     */
    public $softPurge = true;

    /**
     * @var boolean auto purge messages from queue after sending. If `softPurge` is set to `true` the record in the
     * queue will not be hard deleted - but will be set a status of INACTIVE.
     */
    public $autoPurge = true;

    /**
     * @var Module the current module
     */
    protected $_module;

    /**
     * Mailer constructor.
     * @param array $config
     * @throws InvalidConfigException
     */
    public function __construct(array $config = [])
    {
        $module = $this->getModule();
        if ($module instanceof Module) {
            $config = ArrayHelper::merge($module->mailerConfig, $config);
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->initConfig();
    }

    /**
     * Initialize configurations and defaults
     * @throws InvalidConfigException
     */
    protected function initConfig()
    {
        $module = $this->getModule();
        $settings = [
            'queue' => ['class' => Queue::class, 'valid' => $this->queueClass instanceof Queue],
            'template' => ['class' => Template::class, 'valid' => $this->templateClass instanceof Template],
            'message' => ['class' => Message::class, 'valid' => $this->messageClass instanceof Message],
        ];
        foreach ($settings as $type => $config) {
            $property = "{$type}Class";
            if (empty($this->$property)) {
                $this->$property = ArrayHelper::getValue($module->classMap, $type, $config['class']);
            } elseif (!$config['valid']) {
                throw new InvalidConfigException("'Mailer::{$property}' must extend from '{$config['class']}'.");
            }
        }
    }

    /**
     * Gets mailmanager module instance
     * @return Module
     * @throws InvalidConfigException
     */
    public function getModule()
    {
        if (empty($this->_module)) {
            $this->_module = Config::getModule(Module::NAME);
        }
        return $this->_module;
    }

    /**
     * Sends out the messages in email queue and update the database.
     *
     * @return array output status of processing
     */
    public function process()
    {
        $success = true;
        $now = time();
        /**
         * @var Queue $class
         */
        $class = $this->queueClass;
        $items = $class::find()->where([
            'and',
            ['sent_at' => null],
            ['<', 'attempts', $this->maxAttempts],
            ['<=', 'scheduled_at', $now],
        ])->orderBy(['created_at' => SORT_ASC]);
        $successCount = $errorCount = 0;
        foreach ($items->each($this->batchSize) as $item) {
            if ($message = $item->getSwiftMessage()) {
                $attributes = ['attempts', 'processed_at'];
                $out = $this->processMessage($message);
                if ($out['status']) {
                    $item->status = Queue::STATUS_PROCESSED;
                    $item->sent_at = $now;
                    $attributes[] = 'sent_at';
                    $successCount++;
                } else {
                    $item->status = Queue::STATUS_ERROR;
                    $success = false;
                    $errorCount++;
                }
                $item->log = $out['message'];
                $item->attempts++;
                $item->processed_at = $now;
                $item->updateAttributes($attributes);
            }
        }

        // Purge messages now?
        if ($this->autoPurge) {
            $this->purge();
        }

        return ['success' => $success, 'successCount' => $successCount, 'errorCount' => $errorCount];
    }

    /**
     * Processes sending of a message and traps exception and messages
     * @param Message $message
     * @return array success status and message
     */
    public function processMessage($message)
    {
        $time = date('Y-m-d h:i:s');
        try {
            $success = $this->send($message);
            if ($success) {
                return [
                    'success' => true,
                    'message' => Yii::t('kvmailmanager', 'Successfully sent the message at {time}.', ['time' => $time]),
                ];
            }
            return [
                'success' => false,
                'message' => Yii::t('kvmailmanager', 'Error sending the message at {time}.', ['time' => $time]),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => Yii::t('kvmailmanager', "Exception sending the message at {time}.{para}{exception}",
                    ['time' => $time, 'para' => Module::PARA, 'exception' => $e->getMessage()]),
            ];
        }
    }

    /**
     * Purges sent messages from queue.
     *
     * @param string $category the queue category. If not specified all sent mails will be purged.
     *
     * @return int number of rows deleted
     */
    public function purge($category = null)
    {
        $condition = ['not', ['sent_at' => null]];
        if ($category !== null) {
            $condition = ['and', ['category' => $category], $condition];
        }
        /**
         * @var Queue $class
         */
        $class = $this->queueClass;
        if ($this->softPurge) {
            return $class::updateAll(['status' => Queue::STATUS_DELETED, 'message' => null], $condition);
        }
        return $class::deleteAll($condition);
    }

    /**
     * Clean up entire queue. This will truncate the table and reset the primary key auto increments.
     *
     * @return int number of rows deleted
     */
    public function clean()
    {
        /**
         * @var Queue $class
         */
        $class = $this->queueClass;
        $db = $class::getDb();
        try {
            $db->createCommand()->truncateTable($class::tableName())->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
