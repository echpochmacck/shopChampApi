<?php

namespace app\modules\api\controllers;

use app\models\Pager;
use app\models\Product;
use app\models\ProductQuery;
use app\models\Role;
use app\models\User;
use Yii;
use yii\data\ArrayDataProvider;
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

        if ($data = Yii::$app->request->post()) {
            $model = new Pager();
            $model->load($data, '');
            if ($model->validate()) {
                $dataProvider = new ArrayDataProvider([
                    'allModels' =>  Product::getProduct(['sort_quantity' => 1]),
                    'pagination' => [
                        'page' =>  --$model->page,
                        'pageSize' => $model->count
                    ]
                ]);
                return [
                    'data' => [
                        'product' => $dataProvider->getModels(),
                        'total_products' => $dataProvider->getTotalCount(),
                        'code' => 200,
                        'message' => 'список товаров получен'
                    ]
                ];
            } else {
                return [
                    'erro' => [
                        'errors' => $model->errors,
                        'code' => 422,
                        'message' => 'Validation error'
                    ]
                ];
            }
        }
        if ($data = $this->request->queryParams) {

            return [
                'data' => [
                    'product' => Product::getProduct(['sort_quantity' => 1, 'search' => array_map(fn($key, $val) => [
                        'operator' => 'like',
                        'field' => $key,
                        'val' => $val
                    ], array_keys($data), array_values($data))]),
                    'code' => 200,
                    'message' => 'список товаров по поиску получен'
                ]
            ];
        }
        return [
            'data' => [
                'products' => Product::getProduct(['sort_quantity' => 1]),
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
    public function actionProductSort()
    {

        $product =  Product::getProduct(['sort_quantity' => 1]);
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
