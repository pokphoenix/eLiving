@extends('main.layouts.main')


@section('style')
  <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-key"></i>@lang('main.room_management')
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('domain') }}"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('main.room_management')</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      
      <!-- Main row -->
     
         
        <form  id="signup-form" role="form" method="POST" action="{{ url($domainName.'/backend/move-server') }}" >
              {{ method_field('PUT') }}
             {{ csrf_field() }}
         
      <div class="row">
        <div class="col-sm-12">
           @include('layouts.error')
        </div>
      </div>

      <div class="row">
      	<div class="col-sm-6">
      		<div class="box box-primary">
              <div class="box-body">
                <div class="col-sm-12">
                   <div class="form-group single-room ">
                      <label for="">Url เก่า</label>
                      <input type="text"  class="form-control" id="old_url" name="old_url" placeholder="Url เก่า"  value="{{ old('old_url') }} " >
                    </div>
                    <div class="form-group ">
                      <label for="">Url ใหม่</label>
                      <input type="text"  class="form-control" id="new_url" name="new_url" placeholder="Url ใหม่"  value="{{ old('new_url') }}" >
                    </div>
                </div>
              </div>
          </div>
      	</div>

        <div class="col-sm-12" style="height: 50px;">
           <button type="submit" id="save" class="btn btn-primary">@lang('main.btn_save')</button>
           <button type="button" id="cancel" class="btn btn-danger" " >@lang('main.btn_cancel')</button>
        </div>
      
      </div>
      </form>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->

@endsection