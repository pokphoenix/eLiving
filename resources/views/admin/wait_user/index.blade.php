@extends('main.layouts.main')


@section('style')
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ url('public/css/input.css') }}">

@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
         User wait for Approve
        
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">User Wait For Approve</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
  
       @include('layouts.error')

    	<div class="box">
            <!-- <div class="box-header">
              <h3 class="box-title"></h3>
               <a href=" {{ url($route.'/create') }}" class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> new</a>
            </div> -->
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  
                  <th>@lang('user.no')</th>
                  <th>@lang('user.id_card')</th>
                  <th>@lang('user.name')</th>
                  <th>@lang('user.role')</th>
                  <th>@lang('user.create_date')</th>
                  <th>@lang('user.tool')</th>
                 
                  
                </tr>
                </thead>
                <tbody>
				
				@foreach ($users as $key=>$user)
                <tr onclick="goEdit('{{$user['id_card']}}')">
                  <td>{{ $key+1 }}</td>
                  <td>{{ $user['id_card']}}</td>
                  <td>{{ $user['first_name']." ".$user['last_name'] }}
                  </td>
                  <td>@foreach ($user['role'] as $role)   {{ $role }}<BR> @endforeach</td>
                  <td>{{ created_date_format($user['created_at']) }}</td>
                 
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
<script src=" {{ url('js/utility/data_table.js') }}"></script>
<script type="text/javascript">
	$(function () {
      var table = $('#example1').DataTable({"bSortCellsTop": true, "order": [[ 4, 'desc' ]]});
      $.each($('.input-filter', table.table().header()), function () {
          var column = table.column($(this).index());
          $( 'input', this).on( 'keyup change', function () {
              if ( column.search() !== this.value ) {
                  column
                      .search( this.value )
                      .draw();
              }
          } );
      } );
	})

function goEdit(idCard){
  window.location.href = "{{ url($route) }}/"+idCard+"/edit" ;
}
</script>
@endsection		
