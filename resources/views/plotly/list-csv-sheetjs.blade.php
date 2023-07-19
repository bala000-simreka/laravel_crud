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
   
    <div id="gridctr" width=1600 height=2000></div>

    <script>
        (async() => {
            var container = document.getElementById('gridctr');
            /* replace with the URL of the file */
            const URL_TO_DOWNLOAD = "{{ asset('storage/50k-36.csv') }}";
            const ab = await (await fetch(URL_TO_DOWNLOAD)).arrayBuffer();

            /* Parse file and get first worksheet */
            const wb = XLSX.read(ab);

            var ws = wb.Sheets[wb.SheetNames[0]];
            var html = XLSX.utils.sheet_to_json(ws,{header:1});

            console.log(html);
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
            
            x_spreadsheet(container, options).loadData(stox(wb));
        })();
        
    </script>
      
@endsection