@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Employee Management</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('employees.create') }}"> Create New Employee</a>
            </div>
        </div>
    </div>
   
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
   
    @if($employees->count() > 0) 
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Email</th>
            <th>Designation</th>
            <th>Address 1</th>
            <th>Address 2</th>
            <th width="280px">Action</th>
        </tr>
        
           @foreach ($employees as $employee)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->details->designation }}</td>
                <td>{{ $employee->details->address_1 }}</td>
                <td>{{ $employee->details->address_2 }}</td>
                <td>
                    <form action="{{ route('employees.destroy',$employee->id) }}" method="POST">
    
                        <a class="btn btn-info" href="{{ route('employees.show',$employee->id) }}">Show</a>
        
                        <a class="btn btn-primary" href="{{ route('employees.edit',$employee->id) }}">Edit</a>
    
                        @csrf
                        @method('DELETE')
        
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
       
        
    </table>
    @else  
            <p class="bg-danger text-white p-1">no Employees added yet!</p>  
        @endif
  
    {!! $employees->links() !!}
      
@endsection