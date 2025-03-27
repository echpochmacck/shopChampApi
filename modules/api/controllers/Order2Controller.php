<?php

namespace app\modules\api\controllers;

use app\models\Cart;
use app\models\Order;
use app\models\OrderComposition;
use app\models\Product;
use app\models\Status;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Order2Controller implements the CRUD actions for Order model.
 */
class Order2Controller extends ActiveController
{

    public $enableCsrfValidation = '';
    public $modelClass = '';

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();


        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => [(isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://' . $_SERVER['REMOTE_ADDR'])],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
            'actions' => [
                'make-order' => [
                    'Access-Control-Allow-Credentials' => true,
                ]
            ]
        ];
        $auth = [
            'class' => HttpBearerAuth::class,
            'optional' => ['index']
        ];
        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        return $behaviors;
    }

    /**
     * Lists all Order models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Order::find(),
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            */
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionMakeOrder()
    {

        $cart = Cart::findOne(['user_id' => Yii::$app->user->id]);
        $user = User::findOne(Yii::$app->user->id);
        if ($cart) {

            if ($cart->cart_sum <= $user->cash) {

                $cart_composition = Product::getProduct(['cart_id' => $cart->id]);
                if ($cart_composition) {
                    $order = new Order();
                    $order->load($cart->attributes, '');
                    $order->sum = $cart->cart_sum;
                    $user->cash -= $order->sum;
                    $order->status_id = Status::getStatusId('Новый');
                    $order->save(false);
                    $user->save(false);
                    foreach ($cart_composition as $product) {
                        $orderProduct = new OrderComposition();
                        $orderProduct->load($product, '');
                        $orderProduct->order_id = $order->id;
                        $orderProduct->product_id = $product['id'];
                        $orderProduct->quantity = $product['quantity_in_cart'];
                        $orderProduct->save(false);
                        $modelProduct = Product::findOne($product['id']);
                        $modelProduct->quantity -= $product['quantity_in_cart'];
                        $modelProduct->save(false);
                    }
                    $cart->delete(false);
                    return [
                        'data' => [
                            'order' => [
                                'id' => $order->id,
                                'created_at' => $order->date,
                                'products' => Product::getProduct(['order_id' => $order->id]),
                                'total_sum' => $order->sum
                            ],
                            'code' => 200,
                            'message' => 'Заказ оформлен'
                        ]
                    ];
                }
            } else {

                Yii::$app->response->statusCode = 403;
                return [
                    'error' => [
                        'code' => 403,
                        'message' => 'Недостаточно средств'
                    ]
                ];
            }
        } else {
            Yii::$app->response->statusCode = 404;
            return '';
        }
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
