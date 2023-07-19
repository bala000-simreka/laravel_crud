@extends('layouts.app')

@section('content')
    <style>
        .clusterize-scroll{
            max-height: 700px;
            overflow: auto;
        }
        table {
            height: 100%;
            border-collapse: collapse;
            width: 100%;
            margin: 10px;
            font-size: 0.8em;
        }

        thead {
            position: sticky;
            top: 0;
            background: #eee;
        }
    </style>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Table with Graph</h2>
            </div>
        </div>
    </div>

    <!-- <button id="loadButton">Load CSV Table</button> -->
   
    <div id="NoOfRows"></div>
    <div id="statusContainer"></div>
    <div id="TableContainer"></div>

    <!--HTML-->
    <div class="clusterize">        
        <div id="scrollArea" class="clusterize-scroll">
            <table id="clusterizeTable" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                <thead></thead>
                <tbody id="contentArea" class="clusterize-content">
                    <tr class="clusterize-no-data">
                        <td>Loading Table...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        (() => {
            //$('#loadButton').click(function(){
                var clusterizedData = [];
                var container = $('#TableContainer');
                var rowsContainer = $('#NoOfRows');
                var statusContainer = $('#statusContainer');
                /* URL of the csv file */
                const URL_TO_DOWNLOAD = "{{ asset('storage/50k-36.csv') }}";
                var loader = "{{asset('images/loader.gif')}}";
                var cnt = 0;

                const worker = new Worker("{{ asset('js/webworker/clusterize-with-graph-worker.js') }}");

                /* post a message to the worker with the URL to fetch */
                worker.postMessage({url: URL_TO_DOWNLOAD, loader:loader, xlsxscript: "{{ asset('js/xlsx.full.min.js') }}"});

                /* when the worker sends back data, add it to the DOM */
                worker.onmessage = function(e) {                    
                    if(e.data.error) return statusContainer.html(e.data.error);
                    else if(e.data.state) {
                        if(e.data.state == 'done'){
                            populateTable(e.data.headerHtml, e.data.graphHtml, e.data.graphData, e.data.clusterizedData, e.data.headers); 
                        }
                        return statusContainer.html(e.data.state);
                    }
                    cnt = cnt+1;
                    rowsContainer.html(cnt);
                };                

                function populateTable(headerHtml, graphHtml, graphData, clusterizedData, headers){                    
                    var clusterize = new Clusterize({
                        rows: clusterizedData,
                        scrollId: 'scrollArea',
                        contentId: 'contentArea'
                    });
                    $("#clusterizeTable thead").html(headerHtml+graphHtml);
                    for(var i=0; i<graphData.length; i++){
                        var data = {};
                        data.x = graphData[i];
                        data.xtitle = headers[i];
                        //custom changes with defaults
                        data.customLayout = {height:300, width:300};
                        data.defaultConfig = { displayModeBar: false };
                        //own custom changes
                        data.customNotitle = true;
                        $("#graph_"+i).html('');
                        plotGraph('histogram', data, 'graph_'+i);
                    }
                }
            //});      
        })();        
    </script>      
@endsection
