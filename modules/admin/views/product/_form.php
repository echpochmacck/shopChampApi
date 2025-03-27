<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Product $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput() ?>
    <div class="container mt-5">
        <div id="box" class="border border-primary rounded p-5 text-center">
            <p>Перетащите файлы сюда или кликните для выбора</p>
            <?= $form->field($model, 'files[]')->fileInput(['id' => 'hiddenInpt', 'multiple' => true, 'class' => 'd-none']) ?>

        </div>
        <ul id="list" class="list-group mt-3"></ul>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>


    <?php
    $this->registerJsFile('js/drag.js', ['depends' => 'yii\bootstrap5\BootstrapAsset'])
    ?>
</div>