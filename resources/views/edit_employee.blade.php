@extends('main')
@section('content')
<div class="col-md-12">
  <!-- USERS LIST -->
  <form action="{{route('employee.update',$user->employee_id)}}" method="post" id="employee_form">
    @csrf
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <div class="col-md-6 align-self-center"><a href="{{url('/employees')}}" class="btn mybtn-red btn-sm"><i class="fas fa-arrow-left"></i><span class="ml-1">Back</span></a></div>
      </div>
      <!-- /.card-header -->
      <div class="card-body p-4 container">
        <div class="row">
          <div class="form-group col-md-6">
            <label for="employee_eid">Employee ID</label>
            <input type="text" readonly name="employee_eid" class="form-control @error('employee_eid') is-invalid @enderror" id="employee_eid" placeholder="Enter Employee ID" value="@if($user->employee_eid){{$user->employee_eid}}@endif">
            @error('employee_eid')
            <div class="text-danger">{{$message}}</div>
            @enderror
          </div>
          <div class="form-group col-md-6">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" id="first_name" placeholder="Enter First Name" value="@if($user->first_name){{$user->first_name}}@endif">
            @error('first_name')
            <div class="text-danger">{{$message}}</div>
            @enderror
          </div>
          <div class="form-group col-md-6">
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" id="last_name" placeholder="Enter Last Name" value="@if($user->last_name){{$user->last_name}}@endif">
            @error('last_name')
            <div class="text-danger">{{$message}}</div>
            @enderror
          </div>
          <div class="form-group col-md-6">
            <label for="email">Email address</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" aria-describedby="emailHelp" placeholder="Enter email" value="@if($user->email){{$user->email}}@endif">
            @error('email')
            <div class="text-danger">{{$message}}</div>
            @enderror
          </div>
          <div class="form-group col-md-6">
            <label for="birth_date">Birthdate</label>
            <input type="text" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" value="@if($user->birth_date){{date_format(date_create($user->birth_date),config('view_date_format'))}}@endif" placeholder="Select date" readonly>
            @error('birth_date')
            <div class="text-danger">{{$message}}</div>
            @enderror
          </div>
          <div class="form-group col-md-6">
            <label for="join_date">Join date</label>
            <input type="text" name="join_date" class="datepicker form-control @error('join_date') is-invalid @enderror" id="join_date" value="@if($user->join_date){{date_format(date_create($user->join_date),config('view_date_format'))}}@endif" placeholder="Select date" readonly>
            @error('join_date')
            <div class="text-danger">{{$message}}</div>
            @enderror
          </div>
          <div class="form-group col-md-6">
            <label for="pan">PAN</label>
            <input type="text" name="pan" class="form-control @error('pan') is-invalid @enderror" id="pan" placeholder="Enter PAN" value="@if($user->pan){{$user->pan}}@endif">
            @error('pan')
            <div class="text-danger">{{$message}}</div>
            @enderror
          </div>
          <div class="form-group col-md-6">
            <label for="aadhar">Aadhar</label>
            <input type="text" name="aadhar" class="form-control @error('aadhar') is-invalid @enderror" id="aadhar" placeholder="Enter AADHAR" value="@if($user->aadhar){{$user->aadhar}}@endif">
            @error('aadhar')
            <div class="text-danger">{{$message}}</div>
            @enderror
          </div>
          <div class="form-group col-md-12">
            <label for="address">Address</label>
            <textarea class="form-control @error('address') is-invalid @enderror" name="address" id="address" placeholder="Enter address">@if($user->address) {{$user->address}} @endif</textarea>
            @error('address')
            <div class="text-danger">{{$message}}</div>
            @enderror
          </div>
        </div>
      </div>
      <!-- /.card-body -->
      <div class="mb-3 text-center">
        <button type="submit" class="btn mybtn-red btn-sm">Update Employee</button>
      </div>
    </div>
  </form>
  <!--/.card -->
</div>
<input type="hidden" id="config" name="config" value="{{config('date_format_javascript')}}">
@endsection
@section('js')
{!! $js['datepicker'] !!}
<script src="{{ asset('js/validation.js') }}"></script>
@endsection