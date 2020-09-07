<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="body-content">

        <div class="row">
            <div class="col-lg-12">
                <div class="shortest-path-container">
                    <h2>Find Shortest Path</h2>
                    <div class="label-wrapper">
                        <label>Graph Name: <input id="ShortestPath_GraphName" type="text"></label>
                        <label>Start Vertex Id: <input id="ShortestPath_StartVertexId" type="text"></label>
                        <label>End Vertex Id: <input id="ShortestPath_EndVertexId" type="text"></label>
                    </div>
                    <div class="buttons-wrapper">
                        <button class="btnSend" id="findShortestPath" data-input="ShortestPath_GraphName,ShortestPath_StartVertexId,ShortestPath_EndVertexId">Find Shortest Path</button>
                    </div>
                    <div id="ShortestPath_CommonInfo"></div>
                    <div id="ShortestPath_AmountEdges"></div>
                    <div id="ShortestPath_Length"></div>
                    <div id="ShortestPath_VerticesSequence"></div>
                </div>
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <h2>Graphs</h2>
                <form>
                    <div class="label-wrapper">
                        <label>Name: <input id="Graphs_Name" type="text"></label><br /> 
                    </div>
                    <div class="buttons-wrapper">
                        <button class="btnSend" id="createGraph" data-input="Graphs_Name">Create</button>
                        <button class="btnSend" id="deleteGraph" data-input="Graphs_Name">Delete</button>
                    </div>
                </form>
                <hr>
                <h3>Graphs List</h3>
                <div id="graphsList">
                    <?php
                        foreach ( $this->params['graphs'] as $graph ) {
                            echo "<p>" . $graph->name . "</p>";
                        }    
                    ?>
                </div>
            </div>
            <div class="col-lg-4">
                <h2>Vertices</h2>
                <form>
                    <div class="label-wrapper">
                        <label>Graph Name: <input id="Vertices_GraphName" type="text"></label><br />
                        <label>Id: <input id="Vertices_Id" type="text"></label><br /> 
                    </div>
                    <div class="buttons-wrapper">
                        <button class="btnSend" id="createVertex" data-input="Vertices_GraphName,Vertices_Id">Create</button>
                        <button class="btnSend" id="deleteVertex" data-input="Vertices_GraphName,Vertices_Id">Delete</button>
                    </div>
                </form>
                <hr>
                <h3>Vertices List</h3>
                <form>
                    <div class="label-wrapper">
                        <label>Graph Name: <input id="VerticesList_GraphName" type="text"></label><br />
                    </div>
                    <div class="buttons-wrapper">
                        <button class="btnSend" id="showVertices" data-input="VerticesList_GraphName">Show Vertices</button>
                    </div>
                </form>
                <div id="verticesList"></div>
            </div>
            <div class="col-lg-4">
                <h2>Edges</h2>
                <form>
                <div class="label-wrapper">
                        <label>Graph Name: <input id="Edges_GraphName" type="text"></label><br />
                        <label>Start Vertex Id: <input id="Edges_StartVertexId" type="text"></label><br /> 
                        <label>End Vertex Id: <input id="Edges_EndVertexId" type="text"></label><br />
                        <label>Weight: <input id="Edges_Weight" type="text"></label><br />
                    </div>
                    <div class="buttons-wrapper">
                        <button class="btnSend" id="createEdge" data-input="Edges_GraphName,Edges_StartVertexId,Edges_EndVertexId,Edges_Weight">Create</button>
                        <button class="btnSend" id="deleteEdge" data-input="Edges_GraphName,Edges_StartVertexId,Edges_EndVertexId,Edges_Weight">Delete</button>
                        <button class="btnSend" id="updateEdge" data-input="Edges_GraphName,Edges_StartVertexId,Edges_EndVertexId,Edges_Weight">Update</button>
                    </div>
                </form>
                <hr>
                <h3>Edges List</h3>
                <form>
                    <div class="label-wrapper">
                        <label>Graph Name: <input id="EdgesList_GraphName" type="text"></label><br />
                    </div>
                    <div class="buttons-wrapper">
                        <button class="btnSend" id="showEdges" data-input="EdgesList_GraphName">Show Edges</button>
                    </div>
                </form>
                <div id="edgesList"></div>                
            </div>
        </div>

    </div>
</div>
