@extends('main.layouts.main')


@section('style')
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ url('public/css/input.css') }}">
  <style type="text/css">
    tr:hover {cursor: pointer;}
  </style>
@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-key"></i>@lang('main.room_management')
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('main.room_management')</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
  
       @include('layouts.error')

    	<div class="box">
          @if(!isset($noCreate))
            <div class="box-header">
              <h3 class="box-title"></h3>
               <a href=" {{ url($route.'/create') }}" class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> @lang('main.room_new')</a>
            </div>
            @endif
            <!-- /.box-header -->
            <div class="box-body">
              <table id="data-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('main.room_id')</th>
                  <th>@lang('main.room_display')</th>
                  <th>@lang('main.room_prefix')</th>
                  <th>@lang('main.room_number')</th>
                  <th>@lang('main.room_surfix')</th>
                  <th>@lang('main.room_user')</th>
                </tr>
                <tr class="thead-search" >
                  <th class="input-filter">@lang('main.room_id')</th>
                  <th class="input-filter">@lang('main.room_display')</th>
                  <th class="input-filter">@lang('main.room_prefix')</th>
                  <th class="input-filter">@lang('main.room_number')</th>
                  <th class="input-filter">@lang('main.room_surfix')</th>
                  <th class="input-filter">@lang('main.room_user')</th>
                </tr>
                </thead>
                <tbody>
				
				        @foreach ($rooms as $key=>$room)
               
                <tr onclick="goRoom({{$room['id']}})" title="click to edit" >
                  <td>{{ $room['id'] }}</td>
                  <td>{{ $room['name_prefix'].$room['name'].$room['name_surfix'] }}</td>
                  <td class="text-right">{{ $room['name_prefix'] }}</td>
                  <td>{{ $room['name'] }}</td>
                  <td>{{ $room['name_surfix'] }}</td>
                  
                  <td>{{ $room['room_cnt']}}</td>
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
// 	$.extend( true, $.fn.dataTable.defaults, {
//     "searching": false,
//     "ordering": false
// } );

  $(function () {
	   
    var table = $('#data-table').DataTable(
      {
        "bSortCellsTop": true
        ,"order": [[ 0, "desc" ]]
        ,"sDom": '<"H"flp>rt<"F"p><"clear">'
      }
      );
    
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
function goRoom(roomId){
   window.location.href= "{{ url($route) }}/"+roomId+'/edit';
}

</script>
@endsection		
