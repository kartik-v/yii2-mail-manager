<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel kartik\mailmanager\models\QueueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('kvmailmanager', 'Queues');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="queue-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('kvmailmanager', 'Create Queue'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'categoryDesc',
            'subject',
            'attempts:number',
            'created_at:datetime',
            'processed_at:datetime',
            'scheduled_at:datetime',
            'sent_at:datetime',
            'statusHtml:raw'
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
