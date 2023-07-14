@extends('layouts.app')

@section('content')
    <style>
        .clusterize-scroll{
            max-height: 700px;
            overflow: auto;
        }
    </style>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Plot Data 50k</h2>
            </div>
        </div>
    </div>
   
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <button id="loadButton">Load CSV Table</button>
   
    <div id="NoOfRows"></div>
    <div id="statusContainer"></div>
    <div id="TableContainer"></div>

    <!--HTML-->
    <div class="clusterize">
        
        <div id="scrollArea" class="clusterize-scroll">
            <table id="dtDynamicVerticalScrollExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                <tbody id="contentArea" class="clusterize-content">
                    <tr class="clusterize-no-data">
                        <td>Loading data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        (() => {
            $('#loadButton').click(function(){
                var clusterizedData = [];
                const worker = new Worker("{{ asset('js/webworker/csvreader-worker.js') }}");

                var container = $('#TableContainer');
                var rowsContainer = $('#NoOfRows');
                var statusContainer = $('#statusContainer');
                /* URL of the csv file */
                const URL_TO_DOWNLOAD = "{{ asset('storage/50k-36.csv') }}";
                var cnt = 0;

                /* when the worker sends back data, add it to the DOM */
                worker.onmessage = function(e) {                    
                    if(e.data.error) return statusContainer.html(e.data.error);
                    else if(e.data.state) {
                        if(e.data.state == 'done'){
                            populateTable(clusterizedData); 
                        }
                        return statusContainer.html(e.data.state);
                    }
                    
                    if((e.data.csv).includes("<tr>")){
                        clusterizedData.push(e.data.csv);
                    }
                    //container.html(e.data.csv);
                    cnt = cnt+1;
                    rowsContainer.html(cnt);
                };
                /* post a message to the worker with the URL to fetch */
                worker.postMessage({url: URL_TO_DOWNLOAD, xlsxscript: "{{ asset('js/xlsx.full.min.js') }}"});

                function populateTable(clusterizedData){
                    var clusterize = new Clusterize({
                        rows: clusterizedData,
                        scrollId: 'scrollArea',
                        contentId: 'contentArea'
                    });
                }
            });

            // $('#dtDynamicVerticalScrollExample').DataTable();        
        })();
        
    </script>
      
@endsection