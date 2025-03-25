<?php

namespace app\modules\api\controllers;

use app\models\Cart;
use app\models\CartComposition;
use app\models\Product;
use app\models\Role;
use app\models\User;
use Yii;
use yii\filters\auth\HttpBearerAuth;

class OrderController extends \yii\rest\ActiveController
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
                'Origin' => [(isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://' . $_SERVER['REMOTE_ADDR'])],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => false,
            ],
            'actions' => [
                'add-product' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'remove-product' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
            ]
        ];

        $auth = [
            'class' => HttpBearerAuth::class,
            'only' => ['add-product', 'remove-product'],
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
    public function actionAddProduct($id)
    {
        $product = Product::findOne($id);
        if ($product) {
            if (!($cart = Cart::findOne(['user_id' => Yii::$app->user->id]))) {
                // создание карзины
                $cart = new Cart();
                $cart->user_id = Yii::$app->user->id;
                $cart->cart_sum = 0;
                $cart->total_quantity = 1;
                $cart->save(false);
            }
            if (!($cart_composition = CartComposition::findOne(['cart_id' => $cart->id, 'product_id' => $product->id]))) {
                $cart_composition = new CartComposition();
                $cart_composition->product_id = $product->id;
                $cart_composition->cart_id = $cart->id;
                $cart_composition->quantity = 1;
            }
            $cart_composition->quantity++;
            $cart_composition->poisition_sum = Product::getPosSum($cart_composition->quantity, $product->price);
            $cart->total_quantity++;
            $cart->cart_sum++;
            $cart->save(false);
            $cart_composition->save(false);

            return [
                'data' => [
                    'cart' => [
                        'products' => Product::getProduct(['cart_id' => $cart->id])
                    ],
                    'code' => 200,
                    'message' => 'Товар добвален в корзину'
                ]
            ];
        } else {
            Yii::$app->response->statusCode = 404;
            return '';
        }
    }
}
