@extends('layouts.app')

@section('content')
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Sheet View</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="graph-tab" data-bs-toggle="tab" data-bs-target="#graph" type="button" role="tab" aria-controls="graph" aria-selected="false">Visualise</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div id="sheetstatusContainer"></div>
            <div id="gridctr"></div>
        </div>
        <div class="tab-pane fade" id="graph" role="tabpanel" aria-labelledby="graph-tab">
            <div id="graphstatusContainer"></div>
            <div class="col-md-12">
                <div class="col-md-6">
                    <form id="plotlyForm" action="#" method="POST">
                    @csrf  
                    <div class="form-group">
                        <strong>Graph Type:</strong>
                        <select id="graphtype" class="form-control graphtype" name="graphtype" required>
                            <option value="">-- Select Graph type --</option>
                            <option value="histogram">Histogram</option>
                            <option value="bar">Bar</option>
                            <option value="scatter">Scatter</option>
                        </select>
                    </div>
                    <div class="form-group" id="x-axis-field" style="display: none;">
                        <strong>X-Axis:</strong>
                        <select id="x_axis" class="form-control" name="x_axis" required>
                            <option value="">-- Select x-axis product --</option>
                            <!-- @foreach($allCols as $col)
                                <option value="{{ strtolower($col) }}">{{ $col }}</option>
                                @endforeach -->
                        </select>
                    </div>
                    <div class="form-group" id="y-axis-field" style="display: none;">
                        <strong>Y-Axis:</strong>
                        <select id="y_axis" class="form-control" name="y_axis" required>
                            <option value="">-- Select y-axis product --</option>
                            <!-- @foreach($allCols as $col)
                                <option value="{{ strtolower($col) }}">{{ $col }}</option>
                                @endforeach -->
                        </select>
                    </div>
                    <div class="form-group" id="z-axis-field" style="display: none;">
                        <strong>Z-Axis:</strong>
                        <select id="z_axis" class="form-control" name="z_axis" required>
                            <option value="">-- Select z-axis product --</option>
                            <!-- @foreach($allCols as $col)
                                <option value="{{ strtolower($col) }}">{{ $col }}</option>
                                @endforeach -->
                        </select>
                    </div>
                    <div class="col-md-6 text-center">
                        <button type="submit" class="btn btn-primary" id="plotlyFormBtn">Generate Graph</button>
                        <button type="button" class="btn btn-danger" id="resetBtn">Reset</button>
                    </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <div id="graph-display"></div>
                </div>
            </div>
        </div>
    </div>    

    <script>
        var csvData;
        $(document).ready(function() {
            var container = document.getElementById('gridctr');
            var graphdisplaycontainer = document.getElementById('graph-display');
            var sheetstatusContainer = $('#sheetstatusContainer');
            var graphstatusContainer = $('#graphstatusContainer');
            var html = cols = units = '';
            var loader = "{{asset('images/loader.gif')}}";
            var loaderContainer = '<img height="70px" width="70px" src="'+loader+'">';
            container.innerHTML = loaderContainer;
            
            const worker = new Worker("{{ asset('js/webworker/sheet-visualise-worker.js') }}");            
            const URL_TO_DOWNLOAD = "{{ asset('storage/50k-36.csv') }}";

            /* post a message to the worker with the URL to fetch */
            worker.postMessage({type: 'get_html', url: URL_TO_DOWNLOAD, xlsxscript: "{{ asset('js/xlsx.full.min.js') }}"});
            
            /* when the worker sends back data, add it to the DOM */
            worker.onmessage = function(e) {                    
                    if(e.data.error) return sheetstatusContainer.html(e.data.error);
                    else if(e.data.state) {
                        if(e.data.state == 'done'){
                            html = e.data.html;
                            loadSpreadsheet(html, e.data.wb, container);
                            loadAxisDropdowns(html);
                            cols = html.shift();
                            units = html.shift();
                        }
                        return sheetstatusContainer.html(e.data.state);
                    }
                };
            

            function loadSpreadsheet(html, wb, container){
                const options = {
                    view: {
                        height: () => document.documentElement.clientHeight-150,
                        width: () => document.documentElement.clientWidth-233,
                    },
                    row: {
                        len: html.length
                    },
                    col: {
                        len: html[0].length
                    },
                };
                container.innerHTML = '';
                x_spreadsheet(container, options).loadData(stox(wb));
            }
            
            function loadAxisDropdowns(html){
                var options = html[0];
                //$('#x_axis, #y_axis, #z_axis').empty();
                $.each(options, function(i, p) {
                    $('#x_axis, #y_axis, #z_axis').append($('<option></option>').val(i).html(p));
                });
            }

            //reset the form
            $('#resetBtn').click(function() {
                $('#plotlyForm')[0].reset();
            });

            //show-hide products fields based on graph-type selection
            $(document).on('change', '.graphtype', function() {
                var selectedOption = $(this).val();

                // Hide all elements initially
                $('#x_axis').val('');
                $('#y_axis').val('');
                $('#z_axis').val('');
                $('#x-axis-field, #y-axis-field, #z-axis-field').hide();

                // Show the corresponding element based on the selected option
                if (selectedOption === 'histogram') {
                    $('#x-axis-field').show();
                    $('#y-axis-field, #z-axis-field').hide();                    
                } else if (selectedOption === 'bar' || selectedOption === 'scatter') {
                    $('#x-axis-field, #y-axis-field').show();
                    $('#z-axis-field').hide();
                } else if(selectedOption == ''){
                    $('#x-axis-field, #y-axis-field, #z-axis-field').hide();
                }
            });
            

            $('#plotlyFormBtn').click(function() {
                var graphtype = $('#graphtype').val();
                var xaxis = yaxis = zaxis = [];
                var data = {};                

                var xaxis = $('#x_axis').val();
                var yaxis = $('#y_axis').val();
                var zaxis = $('#z_axis').val();

                graphdisplaycontainer.innerHTML = loaderContainer;
                const gworker = new Worker("{{ asset('js/webworker/sheet-visualise-worker.js') }}");  
                gworker.postMessage({type:'get_graph_data', gftype:graphtype, xaxis:xaxis, yaxis:yaxis, zaxis:zaxis, html:html});
                gworker.onmessage = function(e){
                    if(e.data.error) return graphstatusContainer.html(e.data.error);
                    else if(e.data.state) {
                        if(e.data.state == 'done'){
                            data.x = e.data.xaxis;
                            data.xtitle = cols[xaxis];
                            if(graphtype == 'bar' || graphtype == 'scatter'){        
                                data.y = e.data.yaxis;
                                data.ytitle = cols[yaxis];
                            }
                            graphdisplaycontainer.innerHTML = '';
                            plotGraph(graphtype, data, 'graph-display');
                        }
                        return graphstatusContainer.html(e.data.state);
                    }
                }
            });
        });
    </script>     
@endsection