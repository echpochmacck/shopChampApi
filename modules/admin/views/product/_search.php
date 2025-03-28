<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ProductSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="product-search col-lg-5">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
            'inputOptions' => ['class' => 'col-lg-3 form-control'],
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'description')->textInput() ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'quantity')->textInput() ?>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', '/admin/product', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>