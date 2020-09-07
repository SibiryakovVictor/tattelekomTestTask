<?php

namespace app\models;

class Graph extends \yii\db\ActiveRecord implements \yii\db\ActiveRecordInterface
{
    const SCENARIO_CREATE = 'create';

    public static function tableName()
    {
        return 'graph';
    }
    
    public function rules()
    {
        return [
            [['name'], 'safe'],
        ];
    }


    public static function findShortestPath($graphName, $startVertex, $endVertex)
    {
        $vertices = Vertex::find()->where('graph_name=:graph_name')->addParams([':graph_name' => $graphName])->all();

        $verticesListId = array();
        foreach ( $vertices as $vertex ) {
            array_push($verticesListId, $vertex->id);
        }

        $pathTo = array();
        foreach ( $verticesListId as $i ) {
            $pathTo[ $i ] = array();
            for ( $counter = 0; $counter != count($verticesListId); $counter++ ) {
                $pathTo[ $i ][ $counter ] = PHP_INT_MAX;
            }
        }

        $pathTo[ $startVertex ][ 0 ] = 0; 

        $edgesList = Edge::find()->where('graph_name=:graph_name')->addParams([':graph_name' => $graphName])->all();

        $prevInPath = array();

        for ( $counter = 1; $counter != count($verticesListId); $counter++ ) {
            
            foreach ( $edgesList as $edge ) {

                if ( $pathTo[ $edge->end_vertex_id ][ $counter ] > $pathTo[ $edge->start_vertex_id ][ $counter - 1 ] + $edge->weight ) {

                    $pathTo[ $edge->end_vertex_id ][ $counter ] = $pathTo[ $edge->start_vertex_id ][ $counter - 1 ] + $edge->weight;

                    $prevInPath[ $edge->end_vertex_id ][ $counter ] = $edge->start_vertex_id;
                }  
            }
        }

        if ( min($pathTo[$endVertex]) === PHP_INT_MAX ) {
            return [
                'amount_edges' => 'empty',
                'path_length' => 'empty',
                'vertices_sequence' => 'empty'
            ];
        }

        $edgesInShortestPath = array_search(min($pathTo[$endVertex]), $pathTo[$endVertex]);

        $amountEdges = $edgesInShortestPath;
        $verticesInPath = array();
        $currentVertex = $endVertex;

        array_push($verticesInPath, intval($endVertex));

        while ( $amountEdges > 0 ) {

            array_push($verticesInPath, $prevInPath[ $currentVertex ][ $amountEdges ]);

            $currentVertex = $prevInPath[ $currentVertex ][ $amountEdges ];

            $amountEdges--;
        }

        $verticesInPath = array_reverse($verticesInPath);

        return [
            'amount_edges' => $edgesInShortestPath,
            'path_length' => min($pathTo[$endVertex]),
            'vertices_sequence' => $verticesInPath
        ];
    }


    public static function isGraphExists($graphName)
    {
        $graph = Graph::find()->where('name=:name')->addParams([':name' => $graphName])->one();

        return ( $graph === null ? false : true );
    }


    public static function getVertices($graphName)
    {
        $verticesList = Vertex::find()->where('graph_name=:graph_name')->addParams([':graph_name' => $graphName])->all();

        return $verticesList;
    }


    public static function getEdges($graphName)
    {
        $edgesList = Edge::find()->where('graph_name=:graph_name')->addParams([':graph_name' => $graphName])->all();

        return $edgesList;
    }


    public static function validateGraphName($graphName)
    {
        $nameMinLength = 6;
        $nameMaxLength = 20;

        $message = "";

        if ( ! preg_match( '/^\w+$/', $graphName ) ) {
            $message = "Graph's name can only contain symbols: 'a-z', 'A-Z', '0-9', '_'.";
            return $message;
        }

        if ( strlen($graphName) < $nameMinLength ) {
            $message = "Minimal length graph's name is " . $nameMinLength . " symbols.";
            return $message;
        }

        if ( strlen($graphName) > $nameMaxLength ) {
            $message = "Maximum length graph's name is " . $nameMaxLength . " symbols.";
            return $message;
        }
    }
}

