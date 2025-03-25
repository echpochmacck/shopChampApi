<?php

namespace app\modules\api\controllers;

use app\models\Product;
use app\models\Role;
use app\models\User;
use Yii;
use yii\filters\auth\HttpBearerAuth;

class ProductController extends \yii\rest\ActiveController
{
    public $enableCsrfValidation = '';
    public $modelClass = '';
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => [isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://' . $_SERVER['REMOTE_ADDR']],
                'Access-Control-Request-Method' => ['GET', 'HEAD', 'OPTIONS'],
                'Access-Control-Allow-Credentials' => false,
            ],
            'actions' => [
                'logout' => [
                    'Access-Control-Allow-Credentials' => true,
                ]
            ]
        ];

        $auth = [
            'class' => HttpBearerAuth::class,
            'only' => ['logout'],
        ];
        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        return $behaviors;
    }
    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create']);

        // customize the data provider preparation with the "prepareDataProvider()" method
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }
    public function actionGetProducts()
    {
        return [
            'data' => [
                'products' => Product::getProduct(),
                'code' => 200,
                'message' => 'список товаров получен'
            ]
        ];
    }
    public function actionGetProduct($id)
    {
        $product =  Product::getProduct(['product_id' => $id]);
        if ($product) {
            return [
                'data' => [
                    'product' => $product,
                    'code' => 200,
                    'message' => 'список товаров получен'
                ]
            ];

        } else {
            Yii::$app->response->statusCode = 404;
            return '';
        }
    }
}
