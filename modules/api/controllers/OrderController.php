<?php

namespace app\modules\api\controllers;

use app\models\Cart;
use app\models\CartComposition;
use app\models\Order;
use app\models\OrderComposition;
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
                'get-cart' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'make-order' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'get-order' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
            ]
        ];

        $auth = [
            'class' => HttpBearerAuth::class,
            'only' => ['add-product', 'remove-product', 'get-cart', 'make-order', 'get-order'],
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
            }

            $cart->total_quantity++;
            $cart->cart_sum += $product->price;
            $cart->save();

            if (!($cart_composition = CartComposition::findOne(['cart_id' => $cart->id, 'product_id' => $product->id]))) {
                $cart_composition = new CartComposition();
                $cart_composition->product_id = $product->id;
                $cart_composition->cart_id = $cart->id;
            }
            $cart_composition->quantity++;
            $cart_composition->poisition_sum += $product->price;

            $cart_composition->save();

            return [
                'data' => [
                    'cart' => [
                        'products' => Product::getProduct(['cart_id' => $cart->id]),
                        'total_sum' => $cart->cart_sum

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


    // уменьшение товара на единицу в корзине
    public function actionRemoveProduct($id)
    {
        $product = Product::findOne($id);
        if ($product) {
            if (!($cart = Cart::findOne(['user_id' => Yii::$app->user->id]))) {
                Yii::$app->response->statusCode = 404;
                return '';
            } else {


                $cart->total_quantity--;
                $cart->cart_sum -= $product->price;
                $cart->save();
                if ($cart->total_quantity <= 0) {
                    $cart->delete(false);
                    return [
                        'data' => [
                            'code' => 200,
                            'message' => 'Корзина очищена'
                        ]
                    ];
                } else {
                    $cart->cart_sum -= $product->price;
                }
                if (!($cart_composition = CartComposition::findOne(['cart_id' => $cart->id, 'product_id' => $product->id]))) {
                    Yii::$app->response->statusCode = 404;
                    return '';
                } else {

                    $cart_composition && $cart_composition->quantity--;

                    // убираем последний товар
                    if ($cart_composition->quantity <= 0) {
                        $cart_composition->delete(false);
                    } else {
                        $cart_composition->poisition_sum -= $product->price;
                    }


                    $cart_composition->save();

                    return [
                        'data' => [
                            'cart' => [
                                'products' => Product::getProduct(['cart_id' => $cart->id]),
                            ],
                            'code' => 200,
                            'message' => 'Товар убран из корзины'
                        ]
                    ];
                }
            }
        } else {
            Yii::$app->response->statusCode = 404;
            return '';
        }
    }
    public function actionGetCart()
    {
        $cart = Cart::findOne(['user_id' => Yii::$app->user->id]);
        if ($cart) {
            return [
                'data' => [
                    'cart' => [
                        'products' => Product::getProduct(['cart_id' => $cart->id]),
                        'total_sum' => $cart->cart_sum
                    ],
                    'code' => 200,
                    'message' => 'Информация о корзине получена'
                ]
            ];
        } else {
            Yii::$app->response->statusCode = 404;
            return '';
        }
    }

    public function actionMakeOrder()
    {
        $cart = Cart::findOne(['user_id' => Yii::$app->user->id]);
        if ($cart) {
            $cart_products =  Product::getProduct(['cart_id' => $cart->id]);
            if ($cart_products) {
                $model = new Order();
                $model->user_id = Yii::$app->user->id;
                $model->sum = $cart->cart_sum;
                $model->total_quantity = $cart->total_quantity;
                $user = User::findone([Yii::$app->user->id]);
                if ($model->sum <= $user->cash) {
                    $user->cash = $user->cash - $model->sum;
                    $user->save(false);
                    $model->status_id = 1;
                    $model->save(false);
                    foreach ($cart_products as $product) {
                        $orderProduct = new OrderComposition();
                        $orderProduct->load($product, '');
                        $orderProduct->order_id = $model->id;
                        $orderProduct->product_id = $product['id'];
                        $orderProduct->quantity = $product['quantity_in_cart'];
                        $orderProduct->save(false);
                        $modelProduct = Product::findOne($product['id']);
                        $modelProduct->quantity -= $product['quantity_in_cart'];
                        $modelProduct->save(false);
                    }
                    return [
                        'data' => [
                            'order' => [
                                'id' => $model->id,
                                'created_at' => $model->date,
                                'total_sum' => $model->sum,
                                'products' => $cart_products,
                            ],
                            'user' => [
                                'cash' => $user->cash,
                            ],
                            'code' => 200,
                            'message' => 'Заказ оформлен',
                        ]
                    ];
                } else {
                    Yii::$app->response->statusCode = 403;
                    return [
                        'data' => [

                            'code' => 403,
                            'message' => 'no money 😭'

                        ]
                    ];
                }
            }
        } else {
            Yii::$app->response->statusCode = 404;
            return '';
        }
    }

    public function actionGetOrder($id)
    {
        $order = Order::findOne($id);
        if ($order) {
            if ($order->user_id == Yii::$app->user->id) {
                return [
                    'data' => [
                        'order' => [
                            'id' => $order->id,
                            'sum' => $order->sum,
                            'created_at' => $order->date,
                            'products' => Product::getProduct(['order_id' => $order->id]),
                        ],
                        'code' => 200,
                        'message' => 'Информация по заказу получена'
                    ]
                ];
            } else {
                Yii::$app->response->statusCode = 403;
                return '';
            }
        } else {
            Yii::$app->response->statusCode = 404;
            return '';
        }
    }
}
