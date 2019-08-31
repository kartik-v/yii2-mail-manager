<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model kartik\mailmanager\models\Template */

$this->title = Yii::t('kvmailmanager', 'Update Template: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('kvmailmanager', 'Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('kvmailmanager', 'Update');
?>
<div class="template-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
