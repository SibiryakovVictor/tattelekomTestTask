<?php

namespace app\controllers;

use yii\rest\ActiveController;

class EdgeController extends ActiveController
{
    public $modelClass = 'app\models\Edge';

    public $createScenario = \app\models\Edge::SCENARIO_CREATE;
    public $updateScenario = \app\models\Edge::SCENARIO_UPDATE;

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['view'], $actions['create'], $actions['delete'], $actions['update']);

        return $actions;
    }

    public function actionDelete($id)
    {
        $response = \Yii::$app->getResponse();

        list($graphName, $startVertexId, $endVertexId) = explode(',', $id);

        $validateMessage = \app\models\Graph::validateGraphName($graphName);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        $validateMessage = \app\models\Vertex::validateVertexId($startVertexId);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        $validateMessage = \app\models\Vertex::validateVertexId($endVertexId);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        if ( ! \app\models\Graph::isGraphExists($graphName) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Graph '$graphName' doesn't exist" ];
        }

        if ( ! \app\models\Vertex::isVertexExists($graphName, $startVertexId) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Start vertex with id '$startVertexId' in graph '$graphName' doesn't exist" ];
        }

        if ( ! \app\models\Vertex::isVertexExists($graphName, $endVertexId) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "End vertex with id '$endVertexId' in graph '$graphName' doesn't exist" ];
        }

        if ( ! $this->modelClass::isEdgeExists($graphName, $startVertexId, $endVertexId) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Edge from '$startVertexId' to '$endVertexId' in graph '$graphName' doesn't exist" ];
        }

        $edge = $this->modelClass::findEdge($graphName, $startVertexId, $endVertexId);

        $edge->delete();

        $response->setStatusCode(204);
    }


    public function actionView($id)
    {
        $response = \Yii::$app->getResponse();

        list($graphName, $startVertexId, $endVertexId) = explode(',', $id);

        $validateMessage = \app\models\Graph::validateGraphName($graphName);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        $validateMessage = \app\models\Vertex::validateVertexId($startVertexId);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        $validateMessage = \app\models\Vertex::validateVertexId($endVertexId);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        if ( ! \app\models\Graph::isGraphExists($graphName) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Graph '$graphName' doesn't exist" ];
        }

        if ( ! \app\models\Vertex::isVertexExists($graphName, $startVertexId) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Start vertex with id '$startVertexId' in graph '$graphName' doesn't exist" ];
        }

        if ( ! \app\models\Vertex::isVertexExists($graphName, $endVertexId) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "End vertex with id '$endVertexId' in graph '$graphName' doesn't exist" ];
        }

        if ( ! $this->modelClass::isEdgeExists($graphName, $startVertexId, $endVertexId) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Edge from '$startVertexId' to '$endVertexId' in graph '$graphName' doesn't exist" ];
        }

        $edge = $this->modelClass::findEdge($graphName, $startVertexId, $endVertexId);

        return [ 'success' => true, 'graph_name' => $edge->graph_name, 'start_vertex_id' => $edge->start_vertex_id, 'end_vertex_id' => $edge->end_vertex_id, 'weight' => $edge->weight ];
    }



    public function actionCreate()
    {
        $fieldsValues = \Yii::$app->getRequest()->getBodyParams();

        $response = \Yii::$app->getResponse();

        $requiredProperties = ['graph_name', 'start_vertex_id', 'end_vertex_id', 'weight'];

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


        $startVertexId = $fieldsValues['start_vertex_id'];

        $validateMessage = \app\models\Vertex::validateVertexId($startVertexId);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        if ( ! \app\models\Vertex::isVertexExists($graphName, $startVertexId) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Start vertex with id '$startVertexId' in graph '$graphName' doesn't exist" ];
        }

        $endVertexId = $fieldsValues['end_vertex_id'];

        $validateMessage = \app\models\Vertex::validateVertexId($endVertexId);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        if ( ! \app\models\Vertex::isVertexExists($graphName, $endVertexId) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "End vertex with id '$endVertexId' in graph '$graphName' doesn't exist" ];
        }

        $weight = $fieldsValues['weight'];

        $validateMessage = $this->modelClass::validateWeight($weight);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        
        $model = new $this->modelClass();
        $model->load($fieldsValues, '');

        if ( $model->save() ) {
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            return $this->actionView($id);
        }
    }


    public function actionUpdate($id)
    {
        $response = \Yii::$app->getResponse();

        list($graphName, $startVertexId, $endVertexId) = explode(',', $id);

        $validateMessage = \app\models\Graph::validateGraphName($graphName);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        $validateMessage = \app\models\Vertex::validateVertexId($startVertexId);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        $validateMessage = \app\models\Vertex::validateVertexId($endVertexId);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        if ( ! \app\models\Graph::isGraphExists($graphName) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Graph '$graphName' doesn't exist" ];
        }

        if ( ! \app\models\Vertex::isVertexExists($graphName, $startVertexId) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Start vertex with id '$startVertexId' in graph '$graphName' doesn't exist" ];
        }

        if ( ! \app\models\Vertex::isVertexExists($graphName, $endVertexId) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "End vertex with id '$endVertexId' in graph '$graphName' doesn't exist" ];
        }

        if ( ! $this->modelClass::isEdgeExists($graphName, $startVertexId, $endVertexId) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Edge from '$startVertexId' to '$endVertexId' in graph '$graphName' doesn't exist" ];
        }


        if ( ! array_key_exists( 'weight', \Yii::$app->getRequest()->getBodyParams() ) ) {
            $response->setStatusCode(400);

            return ["success" => false, "error" => "property 'weight' not set"];
        }

        $weight = $fieldsValues = \Yii::$app->getRequest()->getBodyParams()['weight'];

        $validateMessage = $this->modelClass::validateWeight($weight);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        $edge = $this->modelClass::findEdge($graphName, $startVertexId, $endVertexId);

        $edge->weight = $weight;

        $edge->update();

        return $edge;
    }
}
