<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model kartik\mailmanager\models\Template */

$this->title = Yii::t('kvmailmanager', 'Create Template');
$this->params['breadcrumbs'][] = ['label' => Yii::t('kvmailmanager', 'Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="template-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
