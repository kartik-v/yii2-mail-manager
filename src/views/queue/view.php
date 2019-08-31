<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model kartik\mailmanager\models\Queue */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('kvmailmanager', 'Queues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="queue-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('kvmailmanager', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('kvmailmanager', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'category',
            'subject',
            'attempts',
            'created_at',
            'processed_at',
            'scheduled_at',
            'sent_at',
            'status',
            'log:ntext',
            'message:ntext',
        ],
    ]) ?>

</div>
