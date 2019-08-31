<?php

/**
 * @package   yii2-mail-manager
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.0
 */

namespace kartik\mailmanager\components;

use kartik\mailmanager\models\Queue;
use kartik\mailmanager\models\Template;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\swiftmailer\Message as YiiMessage;

/**
 *
 * Message implements a message class based on SwiftMailer with queuing functionality enabled and ability to apply
 * templates to render and deliver mail content.
 *
 * @property Mailer $mailer
 *
 * @see http://www.yiiframework.com/doc-2.0/yii-swiftmailer-message.html
 */
class Message extends YiiMessage
{
    /**
     * Encodes a message object
     * @param Message $source
     * @return string
     */
    public static function encode($source)
    {
        return base64_encode(serialize($source));
    }

    /**
     * Deoodes an encoded message object
     * @param string $source
     * @return Message
     */
    public static function decode($source)
    {
        return unserialize(base64_decode($source));
    }

    /**
     * Enqueue the message storing it in database.
     *
     * @param string $category the message category - if not set will default to module's `defaultQueueCategory`
     * @param integer $schedule time scheduled at - if not set will default to current time
     * @return array success status and message
     * @throws InvalidConfigException
     */
    public function queue($category = null, $schedule = null)
    {
        $module = $this->mailer->getModule();
        $now = time();
        $queue = new Queue();
        $queue->category = $category === null ? $module->defaultQueueCategory : $category;
        $queue->subject = $this->getSubject();
        $queue->status = Queue::STATUS_PENDING;
        $queue->attempts = 0;
        $queue->message = self::encode($this);
        $queue->scheduled_at = empty($schedule) ? $now : $schedule;
        $success = $queue->save();
        return [
            'success' => $success,
            'message' => $success ? Yii::t('kvmailmanager', 'Message queued successfully.') :
                Yii::t('kvmailmanager', 'Error queuing message.{errors}', ['errors' => Html::errorSummary($queue)]),
        ];
    }

    /**
     * Queues and sends this email message.
     * @param string $category the message category - if not set will default to module's `defaultQueueCategory`
     * @param integer $schedule time scheduled at - if not set will default to current time
     * @param Mailer $mailer the mailer that should be used to send this message
     * @return array success status and message
     * @throws InvalidConfigException
     */
    public function queueAndSend($category = null, $schedule = null, $mailer = null)
    {
        $status = $this->queue($category, $schedule);
        $status['message'] = nl2br($status['message']);
        if (!$status['success']) {
            return $status;
        }
        $out = $mailer->processMessage($this);
        $out['message'] = $status['message'] . '<br><br>' .  nl2br($out['message']);
        return $out;
    }

    /**
     * Applies template settings for a specific template name
     * @param string $name the template name
     * @param array $params the tokens that will be replaced
     * @return Message
     * @throws InvalidConfigException
     */
    public function applyTemplate($name, $params = [])
    {
        return $this->applyTemplateProperties(Template::findOne(['name' => $name]), $params);
    }

    /**
     * Applies template settings for a specific template id
     * @param string $id the template identifier
     * @param array $params the tokens that will be replaced
     * @return Message
     * @throws InvalidConfigException
     */
    public function applyTemplateId($id, $params = [])
    {
        return $this->applyTemplateProperties(Template::findOne($id), $params);
    }

    /**
     * @param Template $template
     * @param array $params
     * @return Message
     * @throws InvalidConfigException
     */
    public function applyTemplateProperties($template, $params = [])
    {
        if ($template === null || !$template instanceof Template) {
            return $this;
        }
        $module = $this->mailer->getModule();
        foreach ($module->templatePropertiesToApply as $property) {
            if (isset($template->$property) && $template->$property !== '') {
                $value = Yii::t('kvmailmanager', $template->$property, $params);
                $method = 'set' . Inflector::camelize($property);
                if (method_exists($this, $method)) {
                    if (in_array($property, $module->templateArrayProperties) && strpos($value, ',') !== false) {
                        $value = explode(',', $value);
                    }
                    $this->$method($value);
                }
            }
        }
        return $this;
    }
}
