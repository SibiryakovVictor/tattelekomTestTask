<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class RealtimeController extends Controller
{
    public function actionIndex()
    {
        $graphsList = \app\models\Graph::find()->all();

        $this->getView()->params['graphs'] = $graphsList;

        $this->getView()->registerCssFile("@web/css/realtime_interface.css");

        $this->getView()->registerJsFile(
            "@web/js/realtime.js",
            ['depends' => [\yii\web\JqueryAsset::class]]
        );

        return $this->render('index');
    }
}