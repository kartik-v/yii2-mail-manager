<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel kartik\mailmanager\models\TemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('kvmailmanager', 'Templates');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="template-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('kvmailmanager', 'Create Template'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'subject',
            'priority',
            'html_body:ntext',
            //'text_body:ntext',
            //'from',
            //'to',
            //'cc',
            //'bcc',
            //'reply_to',
            //'read_receipt_to',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
