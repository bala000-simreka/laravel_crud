@extends('layouts.app')

@section('content')
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
   
    
    <div id="TableContainer"></div>
    <div id="gridctr"></div>

    <script>
        (async() => {
            var container = document.getElementById('gridctr');
            /* replace with the URL of the file */
            const URL_TO_DOWNLOAD = "{{ asset('storage/50k-36.csv') }}";
            const ab = await (await fetch(URL_TO_DOWNLOAD)).arrayBuffer();

            /* Parse file and get first worksheet */
            const wb = XLSX.read(ab);
            

            
            x_spreadsheet(container).loadData(stox(wb));
        setDone(true);
            return data;
        })();
        
    </script>
      
@endsection