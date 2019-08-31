<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model kartik\mailmanager\models\Template */
/* @var $form kartik\form\ActiveForm */
?>

<div class="template-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'priority')->dropDownList(\kartik\mailmanager\models\Template::$priorities) ?>
        </div>
    </div>
    <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'html_body')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'text_body')->textarea(['rows' => 6]) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'from')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'to')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'cc')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'bcc')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'reply_to')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'read_receipt_to')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::resetButton(Yii::t('kvmailmanager', 'Reset'), ['class' => 'btn btn-default btn-outline-secondary']) ?>
        <?= Html::submitButton(Yii::t('kvmailmanager', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
