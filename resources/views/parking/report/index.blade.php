@extends('main.layouts.main')


@section('style')
<link rel="stylesheet" href="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ url('public/css/input.css') }}">

@endsection

@section('content-wrapper')
  

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       
        <i class="fa fa-circle-o"></i>
       
         {{ $title }}
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">{{ $title }}</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
  
       @include('layouts.error')

      <div class="box">
            <div class="box-header">
              <h3 class="box-title"></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                 
                  <th>@lang('parking.room')</th>
                  <th>@lang('parking.license_plate')</th>
                  <th>@lang('parking.start_date')</th>
                  <th>@lang('parking.end_date')</th>
                  <th>@lang('parking.used_date')</th>
                  <th>@lang('parking.used')</th>
                  <th>@lang('parking.debt_hour')</th>
                 
                </tr>
                <tr class="thead-search" >
                  <th>@lang('user.no')</th>
                  <th class="input-filter">@lang('parking.room')</th>
                  <th class="input-filter">@lang('parking.license_plate')</th>
                  <th class="input-filter">@lang('parking.start_date')</th>
                  <th class="input-filter">@lang('parking.end_date')</th>
                  <th class="input-filter">@lang('parking.used_date')</th>
                  <th class="input-filter">@lang('parking.used')</th>
                  <th class="input-filter">@lang('parking.debt_hour')</th>
                </tr>
                </thead>
                <tbody>
        
                @foreach ($lists as $key=>$list)
                <tr >
                  <td>{{ $key+1 }}</td>
                  <td>{{ $list['room_name'] }}</td>
                   <td>{!! $list['license_plate_category']." ".$list['license_plate']."<BR>".$list['province_name'] !!}</td>
                  <td>{{ created_date_format($list['start_date']) }}</td>
                  <td>@if(isset($list['end_date']))
                  {{ created_date_format($list['end_date'])}}
                  @else
                     @lang('parking.is_until_out')
                  @endif
                  </td>
                  <td>{{   (isset($list['used_date'])) ? created_date_format($list['used_date']) : '' }}</td>
                  <td>@if(is_null($list['used_date']))
                        @lang('parking.not_use')
                      @else
                        @lang('parking.use')
                      @endif
                 </td>
                 <td> {{ $list['debt_hour'] }} </td>
                  
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
<script src="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<!-- DataTables -->
<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script src=" {{ url('js/utility/data_table.js') }}"></script>
<script type="text/javascript">
$(function () {

   
 
    // DataTable
    var table = $('#example1').DataTable({"bSortCellsTop": true});
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


</script>
@endsection   
