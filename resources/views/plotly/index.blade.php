@extends('layouts.app')

@section('content')
<ul class="nav nav-tabs" id="myTabs">
  <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" data-url="{{ route('plotly.list') }}" href="#tab1">Data List</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" data-url="{{ route('plotly.index') }}" href="#tab2">Visualise</a>
  </li>
</ul>

<div class="tab-content">
  <div class="tab-pane fade show active" id="tab1">
    Loading content...
  </div>
  <div class="tab-pane fade" id="tab2">
    Loading content...
  </div>
</div>




    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Plotly</h2>
            </div>
        </div>
    </div>
   
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
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
                        @foreach($allCols as $col)
                            <option value="{{ strtolower($col) }}">{{ $col }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" id="y-axis-field" style="display: none;">
                    <strong>Y-Axis:</strong>
                    <select id="y_axis" class="form-control" name="y_axis" required>
                    <option value="">-- Select y-axis product --</option>
                        @foreach($allCols as $col)
                            <option value="{{ strtolower($col) }}">{{ $col }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" id="z-axis-field" style="display: none;">
                    <strong>Z-Axis:</strong>
                    <select id="z_axis" class="form-control" name="z_axis" required>
                        <option value="">-- Select z-axis product --</option>
                        @foreach($allCols as $col)
                            <option value="{{ strtolower($col) }}">{{ $col }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 text-center">
                    <button type="submit" class="btn btn-primary" id="plotlyFormBtn">Generate Graph</button>
                    <button type="button" class="btn btn-danger" id="resetBtn">Reset</button>
                </div>                  
            </form>
        </div>
        <div class="col-md-6">
            <div id="graph-display">
                <div id="loader" style="display: none">
                    <img src="{{ asset('images/loader.gif') }}" alt="Loading...">
                </div>
            </div>
        </div>
    </div>

    <script>
        var csvData;
        $(document).ready(function() {
            // Load CSV data once on page load
            $.ajax({
                url: "{{ asset('storage/BostonHousing.csv') }}",
                dataType: 'text',
                success: function(data) {
                    csvData = data;
                    var rows = csvData.split('\n');
                    var columnsArray = [];
                    for (var i = 0; i < rows.length; i++) {
                        var columns = rows[i].split(',');
                        columnsArray.push(columns);
                    }
                    console.log(columnsArray);
                }
            });

            //reset the form
            $('#resetBtn').click(function() {
                $('#plotlyForm')[0].reset();
            });

            /*$('#myTabs a').on('click', function(e) {
                e.preventDefault();

                var target = $(this).attr('href'); // Get the target tab
                var url = $(this).data('url'); // Get the data-url attribute containing the URL of the content page

                // Make an AJAX request to fetch the content
                $.ajax({
                url: url,
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    $(target).html(response); // Insert the fetched content into the tab
                },
                error: function(xhr) {
                    $(target).html('Error loading content.'); // Display an error message if content cannot be loaded
                }
                });
            });

            // Trigger the first tab to load its content
            $('#myTabs a:first').trigger('click');*/

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
                var x_axis = y_axis = '';
                var data = {};

                var x_axis = $('#x_axis').val();
                data.x = extractColumnValues(csvData, x_axis);
                if(graphtype == 'bar' || graphtype == 'scatter'){                    
                    var y_axis = $('#y_axis').val();
                    data.y = extractColumnValues(csvData, y_axis);
                }
                $('#loader').show();
                console.log(data);
                plotGraph(graphtype, data);
            });
        });

        function plotGraph(graphtype, data){
            var trace = {};

            trace.x = data.x;
            trace.type = graphtype;

            if (graphtype === 'bar' || graphtype === 'scatter') {
                trace.y = data.y
            } 
            if (graphtype === 'scatter') {
                trace.mode = 'markers'
            }

            var layout = {
                title: graphtype.charAt(0).toUpperCase() + graphtype.slice(1) + ' Graph'
            };

            var chartData = [trace];
            $('#loader').hide();
            Plotly.newPlot('graph-display', chartData, layout);
        }

        // Function to extract column values from CSV data
        function extractColumnValues(csvData, columnName) {
            var rows = csvData.split('\n');
            var columnValues = [];

            // Find the index of the columnName
            var headerRow = rows[0].split(',');
            var columnIndex = headerRow.indexOf(columnName);

            if (columnIndex !== -1) {
                // Iterate through the rows and extract the column values based on the input value
                for (var i = 1; i < rows.length; i++) {
                    var row = rows[i].split(',');
                    //if (row[columnIndex] === inputValue) {
                        columnValues.push(row[columnIndex]);
                    //}
                }
            }
            return columnValues;
        }
    </script>     
@endsection