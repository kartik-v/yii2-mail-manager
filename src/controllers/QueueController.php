<?php

/**
 * @package   yii2-mail-manager
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.0
 */
namespace kartik\mailmanager\controllers;

use kartik\base\Config;
use kartik\mailmanager\Module;
use kartik\mailmanager\models\Queue;
use kartik\mailmanager\models\QueueSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * QueueController implements the CRUD actions for Queue model.
 */
class QueueController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        /**
         * @var Module $module
         */
        $module = Config::getModule(Module::NAME);
        return  $module->queueControllerBehaviors;
    }

    /**
     * Lists all Queue models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new QueueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Queue model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /** @noinspection PhpUndefinedClassInspection */

    /**
     * Deletes an existing Queue model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Queue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Queue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Queue::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('kvmailmanager', 'The requested page does not exist.'));
    }
}
