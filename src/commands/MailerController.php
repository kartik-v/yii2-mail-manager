<?php

/**
 * @package   yii2-mail-manager
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.0
 */

namespace kartik\mailmanager\commands;

use kartik\base\Config;
use kartik\mailmanager\Module;
use kartik\mailmanager\components\Mailer;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 * Mailer command controller
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since  1.0
 */
class MailerController extends Controller
{

    public $defaultAction = 'process';

    /**
     * This command processes the mail queue
     */
    public function actionProcess()
    {
        /**
         * @var Module $module
         * @var Mailer $mailer
         */
        $module = Config::getModule(Module::NAME);
        $class = ArrayHelper::getValue($module->classMap, 'mailer', Mailer::class);
        $mailer = new $class();
        $out = $mailer->process();
        $success = ArrayHelper::remove($out, 'success');
        $out += ['para' => Module::PARA];
        if ($success) {
            echo Yii::t(
                'kvmailmanager',
                'Mailers sent successfully.{para}(Sent: {successCount}, Errors: {errorCount}).',
                $out
            );

        } else {
            echo Yii::t(
                'kvmailmanager',
                'Mailers processed with errors.{para}(Sent: {successCount}, Errors: {errorCount}).{para}Visit queue log for details.',
                $out
            );
        }
    }
}
