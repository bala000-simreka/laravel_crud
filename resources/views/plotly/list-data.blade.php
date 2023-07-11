@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Plot Data</h2>
            </div>
        </div>
    </div>
   
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
   
    @if(count($allData) > 0)
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col">#</th>
                @foreach($cols as $col)
                    <th scope="col">{{$col}}</th>
                @endforeach
            </tr>
        </thead>
    <tbody>
        @foreach($allData as $key => $data)
        <tr>
            <th scope="row">{{ $key+1 }}</th>
            @foreach($data as $key_1 => $row)
                <td>{{ $row }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
    </table>
    @else  
            <p class="bg-danger text-white p-1">no data found!</p>  
        @endif
      
@endsection