<?php
namespace app\daemons;

use consik\yii2websocket\events\WSClientEvent;
use consik\yii2websocket\WebSocketServer;
use Ratchet\ConnectionInterface;

class GraphServer extends WebSocketServer
{

    public function init()
    {
        parent::init();

        $this->on(self::EVENT_CLIENT_CONNECTED, function(WSClientEvent $e) {
            $e->client->graphShowVertices = null;
            $e->client->graphShowEdges = null;
            $e->client->graphShortestPath = null;
            $e->client->startVertexShortestPath = null;
            $e->client->endVertexShortestPath = null;
        });
    }


    protected function getCommand(ConnectionInterface $from, $msg)
    {
        $request = json_decode($msg, true);
        return !empty($request['action']) ? $request['action'] : parent::getCommand($from, $msg);
    }


    
    public function commandFindShortestPath(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);

        $message = $this->getMessageEmptyInput($request['message']);
        if ( ! $this->processEmptyInput($client, $message) ) {
            return;
        }

        $graphName = htmlentities($request['message']['ShortestPath_GraphName']);
        $startVertex = htmlentities($request['message']['ShortestPath_StartVertexId']);
        $endVertex = htmlentities($request['message']['ShortestPath_EndVertexId']);

        $client->graphShortestPath = $graphName;
        $client->startVertexShortestPath = $startVertex;
        $client->endVertexShortestPath = $endVertex;

        $this->updateShortestPath($client);
    }



    public function commandShowVertices(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);

        $message = $this->getMessageEmptyInput($request['message']);
        if ( ! $this->processEmptyInput($client, $message) ) {
            return;
        }

        $graphName = $request['message']['VerticesList_GraphName'];

        $client->graphShowVertices = $graphName;

        $this->updateVerticesList($client, $graphName);
    }



    public function commandCreateEdge(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);
        $action = $request['action'];
        
        $message = $this->getMessageEmptyInput($request['message']);
        if ( ! $this->processEmptyInput($client, $message) ) {
            return;
        }

        $graphName = htmlentities($request['message']['Edges_GraphName']);

        if ( ! $this->processIncorrectGraphName($client, $action, $graphName) ) {
            return;
        }

        $exist = \app\models\Graph::isGraphExists($graphName);
        if ( ! $exist ) {
            $message = "Graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'], $message);
            return;
        }        


        $startVertexId = htmlentities($request['message']['Edges_StartVertexId']);

        if ( ! $this->processIncorrectVertexId($client, $action . ', startVertexId', $startVertexId) ) {
            return;
        }

        $exist = \app\models\Vertex::isVertexExists($graphName, $startVertexId);
        if ( ! $exist ) {
            $message = "Vertex '$startVertexId' in graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'] . ', startVertexId', $message);
            return;
        }


        $endVertexId = htmlentities($request['message']['Edges_EndVertexId']);

        if ( ! $this->processIncorrectVertexId($client, $action . ', endVertexId', $endVertexId) ) {
            return;
        }

        $exist = \app\models\Vertex::isVertexExists($graphName, $endVertexId);
        if ( ! $exist ) {
            $message = "Vertex '$endVertexId' in graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'] . ', endVertexId', $message);
            return;
        }

        $weight = htmlentities($request['message']['Edges_Weight']);

        if ( ! $this->processIncorrectWeight($client, $action . ', weight', $weight) ) {
            return;
        }

        $exist = \app\models\Edge::isEdgeExists($graphName, $startVertexId, $endVertexId);
        if ( $exist ) {
            $message = "Edge from '$startVertexId' to '$endVertexId' in graph '$graphName' already exist";
            $this->sendError($client, $request['action'], $message);
            return;
        }

        $edge = new \app\models\Edge();
        $edge->graph_name = $graphName;
        $edge->start_vertex_id = $startVertexId;
        $edge->end_vertex_id = $endVertexId;
        $edge->weight = $weight;
        $edge->save();

        foreach ($this->clients as $c) {

            if ( $c->graphShowEdges == $graphName ) {
                $this->updateEdgesList($c, $graphName);
            }

            if ( $c->graphShortestPath == $graphName ) {
                $this->updateShortestPath($c);
            }
        }
    }



    public function commandDeleteEdge(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);
        $action = $request['action'];

        unset($request['message']['Edges_Weight']);
        
        $message = $this->getMessageEmptyInput($request['message']);
        if ( ! $this->processEmptyInput($client, $message) ) {
            return;
        }

        $graphName = htmlentities($request['message']['Edges_GraphName']);

        if ( ! $this->processIncorrectGraphName($client, $action, $graphName) ) {
            return;
        }

        $exist = \app\models\Graph::isGraphExists($graphName);
        if ( ! $exist ) {
            $message = "Graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'], $message);
            return;
        } 

        $startVertexId = htmlentities($request['message']['Edges_StartVertexId']);

        if ( ! $this->processIncorrectVertexId($client, $action . ', startVertexId', $startVertexId) ) {
            return;
        }

        $exist = \app\models\Vertex::isVertexExists($graphName, $startVertexId);
        if ( ! $exist ) {
            $message = "Vertex '$startVertexId' in graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'] . ', startVertexId', $message);
            return;
        }


        $endVertexId = htmlentities($request['message']['Edges_EndVertexId']);

        if ( ! $this->processIncorrectVertexId($client, $action . ', endVertexId', $endVertexId) ) {
            return;
        }

        $exist = \app\models\Vertex::isVertexExists($graphName, $endVertexId);
        if ( ! $exist ) {
            $message = "Vertex '$endVertexId' in graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'] . ', endVertexId', $message);
            return;
        }

        $exist = \app\models\Edge::isEdgeExists($graphName, $startVertexId, $endVertexId);
        if ( ! $exist ) {
            $message = "Edge from '$startVertexId' to '$endVertexId' in graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'], $message);
            return;
        }
        

        $edge = \app\models\Edge::findOne( ['graph_name' => $graphName, 'start_vertex_id' => $startVertexId, 'end_vertex_id' => $endVertexId ] );

        $edge->delete();

        foreach ($this->clients as $c) {

            if ( $c->graphShowEdges == $graphName ) {
                $this->updateEdgesList($c, $graphName);
            }

            if ( $c->graphShortestPath == $graphName ) {
                $this->updateShortestPath($c);
            }
        }
    }



    public function commandUpdateEdge(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);
        $action = $request['action'];
        
        $message = $this->getMessageEmptyInput($request['message']);
        if ( ! $this->processEmptyInput($client, $message) ) {
            return;
        }

        $graphName = htmlentities($request['message']['Edges_GraphName']);

        if ( ! $this->processIncorrectGraphName($client, $action, $graphName) ) {
            return;
        }

        $exist = \app\models\Graph::isGraphExists($graphName);
        if ( ! $exist ) {
            $message = "Graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'], $message);
            return;
        } 

        $startVertexId = htmlentities($request['message']['Edges_StartVertexId']);

        if ( ! $this->processIncorrectVertexId($client, $action . ', startVertexId', $startVertexId) ) {
            return;
        }

        $exist = \app\models\Vertex::isVertexExists($graphName, $startVertexId);
        if ( ! $exist ) {
            $message = "Vertex '$startVertexId' in graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'] . ', startVertexId', $message);
            return;
        }


        $endVertexId = htmlentities($request['message']['Edges_EndVertexId']);

        if ( ! $this->processIncorrectVertexId($client, $action . ', endVertexId', $endVertexId) ) {
            return;
        }

        $exist = \app\models\Vertex::isVertexExists($graphName, $endVertexId);
        if ( ! $exist ) {
            $message = "Vertex '$endVertexId' in graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'] . ', endVertexId', $message);
            return;
        }

        $weight = htmlentities($request['message']['Edges_Weight']);

        if ( ! $this->processIncorrectWeight($client, $action . ', weight', $weight) ) {
            return;
        }

        $exist = \app\models\Edge::isEdgeExists($graphName, $startVertexId, $endVertexId);
        if ( ! $exist ) {
            $message = "Edge from '$startVertexId' to '$endVertexId' in graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'], $message);
            return;
        }


        $edge = \app\models\Edge::findOne( ['graph_name' => $graphName, 'start_vertex_id' => $startVertexId, 'end_vertex_id' => $endVertexId ] );

        $edge->weight = $weight;

        $edge->update();

        foreach ($this->clients as $c) {

            if ( $c->graphShowEdges == $graphName ) {
                $this->updateEdgesList($c, $graphName);
            }


            if ( $c->graphShortestPath == $graphName ) {
                $this->updateShortestPath($c);
            }
        }
    }


    public function commandShowEdges(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);
        
        $message = $this->getMessageEmptyInput($request['message']);
        if ( ! $this->processEmptyInput($client, $message) ) {
            return;
        }

        $graphName = htmlentities($request['message']['EdgesList_GraphName']);

        $client->graphShowEdges = $graphName;

        $this->updateEdgesList($client, $graphName);
    }



    public function commandCreateVertex(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);
        
        $message = $this->getMessageEmptyInput($request['message']);
        if ( ! $this->processEmptyInput($client, $message) ) {
            return;
        }

        $graphName = htmlentities($request['message']['Vertices_GraphName']);

        $validateMessage = \app\models\Graph::validateGraphName($graphName);
        if ( ! empty($validateMessage) ) {
            $this->sendError($client, $request['action'], $validateMessage);
            return;
        }

        $exist = \app\models\Graph::isGraphExists($graphName);
        if ( ! $exist ) {
            $message = "Graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'], $message);
            return;
        }

        $vertexId = htmlentities($request['message']['Vertices_Id']);

        $validateMessage = \app\models\Vertex::validateVertexId($vertexId);
        if ( ! empty($validateMessage) ) {
            $this->sendError($client, $request['action'], $validateMessage);
            return;
        }

        $exist = \app\models\Vertex::isVertexExists($graphName, $vertexId);
        if ( $exist ) {
            $message = "Vertex '$vertexId' in graph '$graphName' already exist";
            $this->sendError($client, $request['action'], $message);
            return;
        }

        $vertex = new \app\models\Vertex();
        $vertex->graph_name = $graphName;
        $vertex->id = $vertexId;
        $vertex->save();

        foreach ($this->clients as $c) {

            if ( $c->graphShowVertices == $graphName ) {
                $this->updateVerticesList($c, $graphName);
            }

            if ( $c->graphShortestPath == $graphName ) {
                $this->updateShortestPath($c);
            }
        }
    }



    public function commandDeleteVertex(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);
        
        $message = $this->getMessageEmptyInput($request['message']);
        if ( ! $this->processEmptyInput($client, $message) ) {
            return;
        }

        $graphName = htmlentities($request['message']['Vertices_GraphName']);

        $validateMessage = \app\models\Graph::validateGraphName($graphName);
        if ( ! empty($validateMessage) ) {
            $this->sendError($client, $request['action'], $validateMessage);
            return;
        }

        $exist = \app\models\Graph::isGraphExists($graphName);
        if ( ! $exist ) {
            $message = "Graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'], $message);
            return;
        }

        $vertexId = htmlentities($request['message']['Vertices_Id']);

        $validateMessage = \app\models\Vertex::validateVertexId($vertexId);
        if ( ! empty($validateMessage) ) {
            $this->sendError($client, $request['action'], $validateMessage);
            return;
        }

        $exist = \app\models\Vertex::isVertexExists($graphName, $vertexId);
        if ( ! $exist ) {
            $message = "Vertex '$vertexId' in graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'], $message);
            return;
        }

        $vertex = \app\models\Vertex::findOne( ['graph_name' => $graphName, 'id' => $vertexId ] );

        $vertex->delete();
        
        foreach ($this->clients as $c) {

            if ( $c->graphShowVertices == $graphName ) {
                $this->updateVerticesList($c, $graphName);
            }

            if ( $c->graphShowEdges == $graphName ) {
                $this->updateEdgesList($c, $graphName);
            }

            if ( $c->graphShortestPath == $graphName ) {
                $this->updateShortestPath($c);
            }
        }
    }



    private function updateVerticesList(ConnectionInterface $client, $graphName)
    {
        $verticesList = \app\models\Graph::getVertices($graphName);

        $data = array_map( function($vertex) {
            return $vertex->getAttributes(array('id'));
        }, $verticesList );

        $client->send( json_encode([
            'action' => 'showVertices',
            'graphName' => $graphName,
            'graphExists' => \app\models\Graph::isGraphExists($graphName),
            'data' => $data
        ]) );
    }



    private function updateShortestPath(ConnectionInterface $client)
    {
        $graphName = $client->graphShortestPath;
        $startVertex = $client->startVertexShortestPath;
        $endVertex = $client->endVertexShortestPath;

        if ( ( $graphName == null ) || ( $startVertex == null ) || ( $endVertex == null ) ) {
            return;
        }

        $result = [
            'action' => 'findShortestPath',
            'graphName' => $graphName,
            'graphExists' => \app\models\Graph::isGraphExists($graphName),
            'startVertex' => $startVertex,
            'startVertexExists' => \app\models\Vertex::isVertexExists($graphName, $startVertex),
            'endVertex' => $endVertex,
            'endVertexExists' => \app\models\Vertex::isVertexExists($graphName, $endVertex)
        ];

        foreach ( $result as $exists ) {

            if ( ! $exists ) {
                
                $client->send(json_encode($result));
                return;
            }
        }

        $shortestPathData = \app\models\Graph::findShortestPath($graphName, $startVertex, $endVertex);

        $shortestPathFormatted = array();
        foreach ( $shortestPathData as $key => $value ) {
            $shortestPathFormatted[ lcfirst(\yii\helpers\Inflector::camelize($key)) ] = $value;
        }

        $result = array_merge($result, $shortestPathFormatted);

        $client->send(json_encode(($result)));
    }


    private function updateGraphsList(ConnectionInterface $client)
    {
        $graphsList = \app\models\Graph::find()->all();

        $data = array_map( function($graph) {
            return $graph->getAttributes(array('name'));
        }, $graphsList );

        $client->send( json_encode([ 
            'action' => 'showGraphs',
            'data' => $data
        ]) );
    }


    private function updateEdgesList(ConnectionInterface $client, $graphName)
    {
        $edgesList = \app\models\Graph::getEdges($graphName);

        $data = array_map( function($edge) {
            return $edge->getAttributes(array('start_vertex_id', 'end_vertex_id','weight'));
        }, $edgesList );

        $client->send( json_encode([
            'action' => 'showEdges',
            'graphName' => $graphName,
            'graphExists' => \app\models\Graph::isGraphExists($graphName),
            'data' => $data
        ]) );
    }



    public function commandCreateGraph(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);
        
        $message = $this->getMessageEmptyInput($request['message']);
        if ( ! $this->processEmptyInput($client, $message) ) {
            return;
        }

        $graphName = htmlentities($request['message']['Graphs_Name']);

        $validateMessage = \app\models\Graph::validateGraphName($graphName);
        if ( ! empty($validateMessage) ) {
            $this->sendError($client, $request['action'], $validateMessage);
            return;
        }

        $graph = new \app\models\Graph();

        $graph->name = $graphName;

        $graph->save();

        foreach ($this->clients as $c) {

            $this->updateGraphsList($c);
        }
    }


    public function commandDeleteGraph(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);
        
        $message = $this->getMessageEmptyInput($request['message']);
        if ( ! $this->processEmptyInput($client, $message) ) {
            return;
        }

        $graphName = htmlentities($request['message']['Graphs_Name']);

        $validateMessage = \app\models\Graph::validateGraphName($graphName);
        if ( ! empty($validateMessage) ) {
            $this->sendError($client, $request['action'], $validateMessage);
            return;
        }

        $exist = \app\models\Graph::isGraphExists($graphName);
        if ( ! $exist ) {
            $message = "Graph '$graphName' doesn't exist";
            $this->sendError($client, $request['action'], $message);
            return;
        }

        $graph = \app\models\Graph::find()->where('name=:name')->addParams([':name' => $graphName])->one();

        $graph->delete();

        foreach ($this->clients as $c) {

            $this->updateGraphsList($c);

            if ( $c->graphShowVertices == $graphName ) {
                $this->updateVerticesList($c, $graphName);
            }

            if ( $c->graphShowEdges == $graphName ) {
                $this->updateEdgesList($c, $graphName);
            }

            if ( $c->graphShortestPath == $graphName ) {
                $this->updateShortestPath($c);
            }
        }
    }


    private function sendError($client, $action, $message)
    {
        $client->send( json_encode([
            'message' => $action . ': ' . $message
        ]));
    }


    private function getMessageEmptyInput($input)
    {
        $message = "";

        foreach ( $input as $fieldName => $fieldValue ) {

            if ( empty($fieldValue) ) {

                $message .= "Fill input \"" . $fieldName . "\"\n";
            }
        }

        return $message;
    }


    private function processIncorrectGraphName($client, $action, $graphName)
    {
        $validateMessage = \app\models\Graph::validateGraphName($graphName);

        if ( ! empty($validateMessage) ) {
            $this->sendError($client, $action, $validateMessage);
            return false;
        }

        return true;
    }


    private function processIncorrectVertexId($client, $action, $vertexId)
    {
        $validateMessage = \app\models\Vertex::validateVertexId($vertexId);

        if ( ! empty($validateMessage) ) {
            $this->sendError($client, $action, $validateMessage);
            return false;
        }

        return true;
    }


    private function processIncorrectWeight($client, $action, $weight)
    {
        $validateMessage = \app\models\Edge::validateWeight($weight);

        if ( ! empty($validateMessage) ) {
            $this->sendError($client, $action, $validateMessage);
            return false;
        }

        return true;
    }


    private function processEmptyInput($client, $message)
    {
        if ( ! empty($message) ) {
            $result = [];
            $result['message'] = $message;

            $client->send( json_encode($result) );

            return false; 
        }

        return true;
    }


}