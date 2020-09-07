<?php

namespace app\models;

class Edge extends \yii\db\ActiveRecord implements \yii\db\ActiveRecordInterface
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public static function tableName()
    {
        return 'edge';
    }
    
    public function rules()
    {
        return [
            [['graph_name', 'start_vertex_id', 'end_vertex_id', 'weight'], 'safe'],
            [['weight'], 'safe', 'on' => 'update']
        ];
    }

    public static function validateWeight($weight)
    {
        $limit = 2147483647;

        $weightNumber = intval($weight);

        $message = "";

        if ( ( ! preg_match('/^\d+$/', $weight) ) || ( $weightNumber <= 0 ) || ( $weightNumber >= $limit ) ) {
            $message = "Weight must be positive integer less $limit";
            return $message;
        }

        return $message;
    }


    public static function isEdgeExists($graphName, $startVertexId, $endVertexId)
    {
        $edge = Edge::findEdge($graphName, $startVertexId, $endVertexId);

        return ( $edge === null ? false : true );
    }

    public static function findEdge($graphName, $startVertexId, $endVertexId)
    {
        return Edge::find()->where('graph_name = :graph_name AND start_vertex_id = :start_vertex_id AND end_vertex_id = :end_vertex_id')->addParams([':graph_name' => $graphName, ':start_vertex_id' => $startVertexId, ':end_vertex_id' => $endVertexId ])->one();
    }
}

