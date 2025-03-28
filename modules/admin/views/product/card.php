<?php

use yii\bootstrap5\Html;
use yii\helpers\VarDumper;

// VarDumper::dump($model->files[0], 10, true);
// die;
// var_dump($model->files);die;
?>

<div class="card my-3" style="width: 18rem;">

    <?php if ($model->files): ?>
        <div id="myCarousel" class="carousel slide mb-6 w-100" data-bs-ride="carousel" data-bs-interval='2000'>
            <div class="carousel-inner">
                <?php foreach ($model->files as $index => $file): ?>
                    <?php if ($index == 0): ?>
                        <div class="carousel-item active" data-bs-interval='2000'>
                        <?php else: ?>
                            <div class="carousel-item">
                            <?php endif ?>
                            <?= Html::img('/uploads/' . $file->title, ['class' => 'w-100 slide_img', 'alt' => 'картинка'])  ?>
                            </div>
                        <?php endforeach ?>
                        </div>
            </div>
        <?php else: ?>
            <?= Html::img('/uploads/placeholder.PNG', ['class' => 'w-100', 'alt' => 'картинка'])  ?>

        <?php endif ?>

        <div class="card-body">
            <h5 class="card-title">
                <?= $model->title ?>
            </h5>
            <p class="card-text">
                <?= $model->description ?>
            </p>
        </div>
        <?= Html::a('edit', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-warning']) ?>
        </div>