<?php

namespace app\controllers;

use yii\rest\ActiveController;

class VertexController extends ActiveController
{
    public $modelClass = 'app\models\Vertex';

    public $createScenario = \app\models\Graph::SCENARIO_CREATE;


    public function actions()
    {
        $actions = parent::actions();

        unset($actions['view'], $actions['create'], $actions['delete']);

        return $actions;
    }


    public function actionDelete($id)
    {
        $response = \Yii::$app->getResponse();

        list($graphName, $vertexId) = explode(',', $id);

        $validateMessage = \app\models\Graph::validateGraphName($graphName);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        $validateMessage = $this->modelClass::validateVertexId($vertexId);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        if ( ! \app\models\Graph::isGraphExists($graphName) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Graph '$graphName' doesn't exist" ];
        }

        if ( ! $this->modelClass::isVertexExists($graphName, $vertexId) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Vertex with id '$vertexId' in graph '$graphName' doesn't exist" ];
        }

        $vertex = $this->modelClass::findVertex($graphName, $vertexId);

        $vertex->delete();

        $response->setStatusCode(204);
    }

    public function actionView($id)
    {
        $response = \Yii::$app->getResponse();

        list($graphName, $vertexId) = explode(',', $id);

        $validateMessage = \app\models\Graph::validateGraphName($graphName);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        $validateMessage = $this->modelClass::validateVertexId($vertexId);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        if ( ! \app\models\Graph::isGraphExists($graphName) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Graph '$graphName' doesn't exist" ];
        }

        if ( ! $this->modelClass::isVertexExists($graphName, $vertexId) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Vertex with id '$vertexId' in graph '$graphName' doesn't exist" ];
        }

        $vertex = $this->modelClass::findVertex($graphName, $vertexId);

        return [ 'success' => true, 'graph_name' => $vertex->graph_name, 'vertex_id' => $vertex->id ];
    }


    
    public function actionCreate()
    {
        $fieldsValues = \Yii::$app->getRequest()->getBodyParams();

        $response = \Yii::$app->getResponse();

        $requiredProperties = ['graph_name', 'id'];

        foreach ( $requiredProperties as $prop ) {

            if ( ! array_key_exists( $prop, $fieldsValues ) ) {
                $response->setStatusCode(400);
    
                return ["success" => false, "error" => "property '$prop' not set"];
            }
        }

        $graphName = $fieldsValues['graph_name'];

        $message = \app\models\Graph::validateGraphName($graphName);
        if ( ! empty($message) ) {
            $response->setStatusCode(400);

            return ["success" => false, "error" => $message];
        }

        $exist = \app\models\Graph::isGraphExists($graphName);
        if ( ! $exist ) {
            $response->setStatusCode(400);

            return ["success" => false, "error" => "Graph '$graphName' doesn't exists"];
        }


        $vertexId = $fieldsValues['id'];

        $validateMessage = $this->modelClass::validateVertexId($vertexId);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        if ( $this->modelClass::isVertexExists($graphName, $vertexId) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Vertex with id '$vertexId' in graph '$graphName' already exist" ];
        }

        
        $model = new $this->modelClass();
        $model->load($fieldsValues, '');

        if ( $model->save() ) {
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            return $this->actionView($id);
        }
    }

}
