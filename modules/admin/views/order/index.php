<?php

use app\models\Order;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\OrderAdnminSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'sum',
            'date',
            'status_id',
            'status',
            'user_email',
            'created_at',
            [
                'label' => 'Действие',
                'value' => fn($model) => Html::a('сменить статус', ['order/update/', 'id' => $model->id], ['class' => 'btn btn-outline-danger']),
                'format' => 'html'
            ],
            //'total_quantity',
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>