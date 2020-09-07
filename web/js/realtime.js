let realtimeOptions = {

    actions: new Map([
        [ 'showGraphs', [ 'updateGraphsList' ] ],
        [ 'showVertices', [ 'updateVerticesList' ] ],
        [ 'showEdges', [ 'updateEdgesList' ] ],
        [ 'findShortestPath', [ 'updateShortestPath' ] ]
    ]),


    updateShortestPath: function(response) {
        const fieldCommonInfo = '#ShortestPath_CommonInfo';
        $(fieldCommonInfo).text('');
        const fieldAmountEdges = '#ShortestPath_AmountEdges';
        $(fieldAmountEdges).text('');
        const fieldLength = '#ShortestPath_Length';
        $(fieldLength).text('');
        const fieldVerticesSequence = '#ShortestPath_VerticesSequence'
        $(fieldVerticesSequence).text('');

        if ( ! response.graphExists ) {
            $(fieldCommonInfo).append(`<p><b>Graph '${response.graphName}' doesn't exist</b></p>`);
            return;
        }

        if ( !response.startVertexExists ) {
            $(fieldCommonInfo).append(`<p><b>Start vertex with id '${response.startVertex}' in graph '${response.graphName}' doesn't exist</b></p>`);
            return;
        }

        if ( !response.endVertexExists ) {
            $(fieldCommonInfo).append(`<p><b>End vertex with id '${response.endVertex}' in graph '${response.graphName}' doesn't exist</b></p>`);
            return;
        }

        if ( response.amountEdges === 'empty' ) {
            $(fieldCommonInfo).append(`<p><b>Path from '${response.startVertex}' to '${response.endVertex}' in graph '${response.graphName}' doesn't exist</b></p>`);
            return;
        }


        $(fieldCommonInfo).append(`<p><b>Graph: '${response.graphName}'; Start Vertex: '${response.startVertex}'; End Vertex: '${response.endVertex}';</b></p>`);

        $(fieldAmountEdges).append(`<p>Amount edges: ${response.amountEdges}</p>`);

        $(fieldLength).append(`<p>Length (in terms weight): ${response.pathLength}</p>`);

        let verticeSequence = response.verticesSequence.join(" -> ");

        $(fieldVerticesSequence).append(`<p>Path: ${verticeSequence}</p>`);
    },

    updateEdgesList: function(response) {
        const nameListElem = '#edgesList';

        $(nameListElem).text('');

        if ( ! response.graphExists ) {
            $(nameListElem).append(`<p><b>Graph '${response.graphName}' doesn't exist</b></p>`);
            return;    
        }

        $(nameListElem).append('<p><b>Graph: ' + response.graphName + '</b></p>');

        const edgesList = response.data;

        if ( edgesList.length === 0 ) {
            $(nameListElem).append('<p><b>empty</b></p>');
            return;
        }

        $(nameListElem).append('<p><b>Format: (start_vertex_id, end_vertex_id, weight)</b></p>');

        let stringVertices = "";
        const separator = ', ';
        for ( const edge of edgesList ) {
            stringVertices += `(${edge.start_vertex_id}, ${edge.end_vertex_id}, ${edge.weight})` + separator;
        }
        $(nameListElem).append('<p>' + stringVertices.substr(0, stringVertices.length - separator.length ) + '</p>');

        $('#EdgesList_GraphName').val('');
    },


    updateVerticesList: function(response) {
        const nameListElem = '#verticesList';

        $(nameListElem).text('');

        if ( ! response.graphExists ) {
            $(nameListElem).append(`<p><b>Graph '${response.graphName}' doesn't exist</b></p>`);
            return;    
        }

        $(nameListElem).append('<p><b>Graph: ' + response.graphName + '</b></p>');

        const verticesList = response.data;

        if ( verticesList.length === 0 ) {
            $(nameListElem).append('<p><b>empty</b></p>');
            return;
        }

        let stringVertices = "";
        const separator = ', ';
        for ( const vertex of verticesList ) {
            stringVertices += vertex.id + separator;
        }
        $(nameListElem).append('<p>' + stringVertices.substr(0, stringVertices.length - separator.length ) + '</p>');

        $('#VerticesList_GraphName').val('');
    },


    updateGraphsList: function(response) {
        $('#graphsList').text('');

        const graphsList = response.data;

        for ( const graph of graphsList ) {
            $('#graphsList').append('<p>' + graph.name + '</p>');
        }

        $('#Graphs_Name').val('');
    },




    run: function(response) {
        let handlers = this.actions.get(response.action);
        
        for ( let handler of handlers ) {
            this[ handler ](response);
        }
    }


};




$(function() {
    var chat = new WebSocket('ws://localhost:8080');
    chat.onmessage = function(e) {                
        let response = JSON.parse(e.data);

        if ( response.message ) {
            alert(response.message);
            return;
        }
        
        realtimeOptions.run(response);
    };
    chat.onopen = function(e) {
        $('#response').text("Connection established! Please, set your username.");
    };
    $('.btnSend').click(function(event) {
        event.preventDefault();

        const action = event.target.id;

        let inputsNames = event.target.dataset.input;

        inputsNames = inputsNames.split(",");

        let inputData = {};

        for ( let inputName of inputsNames ) {
            let input = $('#' + inputName);

            inputData[ inputName ] = input.val();
        }
       
        if ( ! ( Object.keys(inputData).length === 0 ) ) {
            chat.send( JSON.stringify({'action' : action, 'message' : inputData}) );
        }
        else {
            alert('Enter the message');
        }                
    })

    $('#btnSetUsername').click(function() {
        if ($('#username').val()) {
            chat.send( JSON.stringify({'action' : 'setName', 'name' : $('#username').val()}) );
        } else {
            alert('Enter username')
        }
    })
})