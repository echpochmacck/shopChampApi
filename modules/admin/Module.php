<?php

namespace app\modules\admin;

use Yii;
use yii\filters\AccessControl;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function behaviors()
    {

        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'controllers' => ['admin/default'],
                        'actions' => ['login'],
                    ],

                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => fn() => Yii::$app->user->identity->isAdmin
                    ],

                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return    Yii::$app->response->redirect('/admin/default/login');
                    }
                    return Yii::$app->response->redirect('/site');
                }
            ],
        ];
    }
}
