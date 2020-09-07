<?php

namespace app\models;

class Vertex extends \yii\db\ActiveRecord implements \yii\db\ActiveRecordInterface
{
    const SCENARIO_CREATE = 'create';

    public static function tableName()
    {
        return 'vertex';
    }

    public function rules()
    {
        return [
            [['graph_name', 'id'], 'safe']
        ];
    }


    public static function findVertex($graphName, $id)
    {
        return Vertex::find()->where('graph_name=:graph_name AND id=:id')->addParams([':graph_name' => $graphName, ':id' => $id])->one();
    }


    public static function isVertexExists($graphName, $id)
    {
        $vertex = Vertex::findVertex($graphName, $id);

        return ( $vertex === null ? false : true );
    }


    public static function validateVertexId($id)
    {
        $limit = 32768;

        $idNumber = intval($id);

        $message = "";

        if ( ( ! preg_match('/^\d+$/', $id) ) || ( $idNumber <= 0 ) || ( $idNumber >= $limit ) ) {
            $message = "Vertex id must be positive integer less $limit";
            return $message;
        }

        return $message;
    }
}

