@extends('main')
@section('content')
<div class="mb-3 col-md-6 align-self-center"><a href="{{url('/employees')}}" class="btn mybtn-red"><i class="fas fa-long-arrow-alt-left mr-1"></i>Back</a></div>
<div class="col-md-12">
  <div class="card card-widget widget-user">
    <div class="widget-user-header bg-blue">
      <h3 class="widget-user-username mb-2">{{ucfirst($user->first_name).' '.ucfirst($user->last_name).' ( '.$user->employee_eid.' ) '}}</h3>
      <h6 class="widget-user-desc font-weight-light">{{$user->email}}</h6>
    </div>
    <div class="widget-user-image">
      @if(empty($user->user->profile_photo_path) || !file_exists(config('app.dir').'/storage/'.$user->user->profile_photo_path))
      @php
      $fc = substr($user->user->first_name,0,1);
      $lc = substr($user->user->last_name,0,1);
      $name = $fc."+".$lc;
      @endphp
      <img class="img-circle" src="https://ui-avatars.com/api/?rounded=true&color=34282C&background=C0C0C0&bold=true&name=<?php echo $name; ?>" alt="">
      @else
      <img class="img-circle" src="{{asset('storage'). '/' .$user->user->profile_photo_path}}" class="img-circle elevation-2">
      @endif
    </div>
    <div class="card-footer">
      <div class="row">
        <div class="col-sm-3 border-right">
          <div class="description-block">
            <h5 class="description-header mb-2">Address</h5>
            <span class="description-text">{{$user->address}}</span>
          </div>
        </div>
        <div class="col-sm-3 border-right">
          <div class="description-block">
            <h5 class="description-header mb-2">Birth Date</h5>
            <span class="description-text">{{date_format(date_create($user->birth_date),config('view_date_format'))}}</span>
          </div>
        </div>
        <div class="col-sm-3 border-right">
          <div class="description-block">
            <h5 class="description-header mb-2">AADHAR</h5>
            <span class="description-text">{{$user->aadhar}}</span>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="description-block">
            <h5 class="description-header mb-2">PAN</h5>
            <span class="description-text">{{$user->pan}}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection