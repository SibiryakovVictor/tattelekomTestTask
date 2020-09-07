<?php

namespace app\controllers;

use yii\rest\ActiveController;

class GraphController extends ActiveController
{
    public $modelClass = 'app\models\Graph';

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

        $validateMessage = $this->modelClass::validateGraphName($id);

        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        if ( ! $this->modelClass::isGraphExists($id) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Graph '$id' doesn't exist" ];
        }

        $graph = \app\models\Graph::find()->where('name=:name')->addParams([':name' => $id])->one();

        $graph->delete();

        $response->setStatusCode(204);
    }

    public function actionView($id)
    {
        $response = \Yii::$app->getResponse();

        $validateMessage = $this->modelClass::validateGraphName($id);

        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        if ( ! $this->modelClass::isGraphExists($id) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Graph '$id' doesn't exist" ];
        }

        $graph = $this->modelClass::findOne($id);

        return [ 'success' => true, 'name' => $graph->name ];
    }

    public function actionCreate()
    {
        $fieldsValues = \Yii::$app->getRequest()->getBodyParams();

        $response = \Yii::$app->getResponse();

        if ( ! array_key_exists('name', $fieldsValues ) ) {
            $response->setStatusCode(400);

            return ["success" => false, "error" => "property 'name' not set"];
        }

        $graphName = $fieldsValues['name'];

        $message = $this->modelClass::validateGraphName($graphName);
        if ( ! empty($message) ) {
            $response->setStatusCode(400);

            return ["success" => false, "error" => $message];
        }

        $exist = $this->modelClass::isGraphExists($graphName);
        if ( $exist ) {
            $response->setStatusCode(400);

            return ["success" => false, "error" => "Graph '$graphName' already exists"];
        }

        $model = new $this->modelClass();
        $model->load($fieldsValues, '');

        if ( $model->save() ) {
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            return $this->actionView($id);
        }
    }


    public function actionShortestPath($id, $pairVertices)
    {
        $response = \Yii::$app->getResponse();

        $validateMessage = $this->modelClass::validateGraphName($id);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        if ( ! $this->modelClass::isGraphExists($id) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Graph '$id' doesn't exist" ];
        }

        list($startVertex, $endVertex) = explode(",", $pairVertices);

        $validateMessage = \app\models\Vertex::validateVertexId($startVertex);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        $validateMessage = \app\models\Vertex::validateVertexId($endVertex);
        if ( ! empty($validateMessage) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => $validateMessage ];
        }

        if ( ! \app\models\Vertex::isVertexExists($id, $startVertex) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "Start vertex with id '$startVertex' in graph '$id' doesn't exist" ];
        }

        if ( ! \app\models\Vertex::isVertexExists($id, $endVertex) ) {
            $response->setStatusCode(400);

            return [ 'success' => false, 'error' => "End vertex with id '$endVertex' in graph '$id' doesn't exist" ];
        }


        $shortestPathData = $this->modelClass::findShortestPath($id, $startVertex, $endVertex);
        
        $success = true;
        if ( $shortestPathData['amount_edges'] === 'empty' ) {
            $success = false;
        }

        return array_merge( 
            array(
                'success' => $success, 
                'start_vertex_id' => $startVertex, 
                'end_vertex_id' => $endVertex,
                'graph_name' => $id 
            ),
            $shortestPathData
        );
    }
}
