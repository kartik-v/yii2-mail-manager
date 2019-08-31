<?php

/**
 * @package   yii2-mail-manager
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.0
 */

namespace kartik\mailmanager;

use yii\base\Application;
use yii\base\BootstrapInterface;

/**
 * Bootstrap class for mail management module
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since  1.0
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage. Will add the `kvmailer` command to the
     * console controller map.
     *
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $app->controllerMap['kvmailer'] = 'kartik\mailmanager\commands\MailerController';
        }
    }
}