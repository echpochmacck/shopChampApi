<?php

namespace app\modules\api;

use Yii;

/**
 * api module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\api\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        // Yii::configure(
        //     Yii::$app,
        //     [
        //         'components' => [
        //             'request' => [
        //                 'class' => yii\web\Request::class,
        //                 'cookieValidationKey' => 'asd',
        //                 'baseUrl' => '',
        //                 'parsers' => [
        //                     'application/json' => 'yii\web\JsonParser',
        //                     'multipart/form-data' => 'yii\web\MultipartFormDataParser'
        //                 ]

        //             ],
        //             'user' => [
        //                 'class' => yii\web\User::class,
        //                 'identityClass' => 'app\models\User',
        //                 'enableSession' => false,
        //             ],
        //             'response' => [
        //                 'class' => yii\web\Response::class,
        //                 'format' => yii\web\Response::FORMAT_JSON,
        //                 'charset' => 'UTF-8',
        //                 'class' => 'yii\web\Response',
        //                 'on beforeSend' => function ($event) {
        //                     $response = $event->sender;
        //                     if ($response->statusCode == 404) {
        //                         $response->data = [
        //                             'data' => [
        //                                 'message' => 'not found',
        //                                 'code' => 404
        //                             ]
        //                         ];
        //                     }
        //                 },
        //                 'formatters' => [
        //                     \yii\web\Response::FORMAT_JSON => [
        //                         'class' => 'yii\web\JsonResponseFormatter',
        //                         'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
        //                         'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        //                         // ...
        //                     ],
        //                 ],
        //                 // ...
        //             ],
        //             'urlManager' => [
        //                 'class' => yii\web\UrlManager::class,
        //                 'enablePrettyUrl' => true,
        //                 'enableStrictParsing' => true,
        //                 'showScriptName' => false,
        //                 'rules' => [
        //                     'api/<controller>/<action>' => 'api/<controller>/<action>',
        //                     ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
        //                 ],
        //             ]

        //         ],
        //     ]
        // );
    }
}
