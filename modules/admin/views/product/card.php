<?php

use yii\bootstrap5\Html;

?>

<!-- <div class="card my-3" style="width: 18rem;">
    <img src="..." class="card-img-top" alt="...">
    <div class="card-body">
        <h5 class="card-title">< $model->title ?></h5>
        <p class="card-text">< $model->description ?></p>
    </div>
     Html::a('edit', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-warning']) #?>
</div> -->

<div class="card my-3" style="width: 18rem;">
    <!-- <img src="..." class="card-img-top" alt="..."> -->
    <?php if ($model['files']): ?>
        <div class="img_block">
            <?= Html::img('/uploads/' . $model['files'][0]['file_url'], ['alt' => 'картинка', 'class' => 'w-100']) ?>
        </div>
    <?php endif ?>
    <div class="card-body">
        <h5 class="card-title"><?= $model['title'] ?></h5>
        <p class="card-text"><?= $model['description'] ?></p>
        <?= Html::a('edit', ['update', 'id' => $model['id']], ['class' => 'btn btn-outline-warning']) ?>
    </div>
</div>