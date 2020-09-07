<?php
namespace app\commands;

use app\daemons\GraphServer;
use yii\console\Controller;

class GraphController extends Controller
{
    public function actionStart($port = null)
    {
        $server = new GraphServer();
        
        if ($port) {

            $server->port = $port;

        }
        $server->start();
    }
}