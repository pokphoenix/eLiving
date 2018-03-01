@extends('main.layouts.main')


@section('style')
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
   <style type="text/css">
    tr:hover {cursor: pointer;}
  </style>
@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        @if(isset($waitUser))
        <img class="icon-title" src="{{ asset('public/img/icon/icon_user_wait_for_approve_2.png') }}">
        @else
        <i class="fa fa-user"></i>
        @endif
         {{ $title }}
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">{{ $title }}</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
  
       @include('layouts.error')

    	<div class="box">
           <div class="box-header">
            
            
         
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                  <th>@lang('user.id_card')</th>
                  <th>@lang('user.user_name')</th>
                  <th>@lang('user.name')</th>
                 
                  <th>@lang('user.create_date')</th>
                
                  <th>@lang('user.active_status')</th>
                  
                </tr>
                </thead>
                <tbody>
				
				@foreach ($users as $key=>$user)
                <tr >
                  <td>{{ $key+1 }}</td>
                  <td>{{ $user['id_card']}}</td>
                  <td>{{ $user['username']}}</td>
                  <td>{{ $user['first_name']." ".$user['last_name'] }}
                  </td>
                  <td>{{ created_date_format($user['created_at']) }}</td>
                  <td >{{ $user['activity_text'] }}</td>
                </tr>
                @endforeach
               
              </table>
            </div>
            <!-- /.box-body -->
          </div>
    </section>
    <!-- /.content -->

@endsection

@section('javascript')
<!-- DataTables -->
<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script type="text/javascript">
$(function () {
	$('#example1').DataTable({
    "iDisplayLength": 100
  })
})

</script>
@endsection		
