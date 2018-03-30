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
       
         @lang('sidebar.parking_cancel')
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('sidebar.parking_cancel')</li>
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
                  <th>@lang('parking.show_used')</th>
                  <th>@lang('main.tool')</th>
                </tr>
                <tr class="thead-search">
                  <th></th>
                 
                  <th class="input-filter">@lang('parking.room')</th>
                  <th class="input-filter">@lang('parking.license_plate')</th>
                  <th class="input-filter">@lang('parking.start_date')</th>
                  <th class="input-filter">@lang('parking.end_date')</th>
                  <th class="input-filter">@lang('parking.used_date')</th>
                  <th class="input-filter">@lang('parking.show_used')</th>
                  <th ></th>
                </tr>
                </thead>
                <tbody>
        
                @foreach ($lists as $key=>$list)
                <tr >
                  <td>{{ $key+1 }}</td>
                  <td>{{ $list['room_name'] }}</td>
                  <td>{!! $list['license_plate_category']." ".$list['license_plate']."<BR>".$list['province_name'] !!}</td>
                  <td>{{ created_date_format($list['created_at']) }}</td>
                  <td>@if(isset($list['outed_at']))
                  {{ created_date_format($list['outed_at'])}}
                  @else
                     @lang('parking.is_until_out')
                  @endif
                  </td>
                  <td>{{   (isset($list['used_at'])) ? created_date_format($list['used_at']) : '' }}</td>
                  <td>@if($list['free_park']==1)
                        @lang('parking.free')
                      @else
                        @lang('parking.not_free')
                      @endif
                 </td>
                  <td> 
                    
                    <button class="btn btn-danger btn-delete btn-xs" data-id="{{ $list['id'] }}"  ><i class="fa fa-close"></i></button>
                   
                   </td>
                </tr>
                @endforeach
               
              </table>
            </div>
            <!-- /.box-body -->
          </div>

      <div class="box">
            <div class="box-header">
              <h3 class="box-title">@lang('parking.cancel_list')</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example2" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                 
                  <th>@lang('parking.room')</th>
                  <th>@lang('parking.license_plate')</th>
                  <th>@lang('parking.cancel_date')</th>
                  <th>@lang('parking.cancel_by')</th>
                 
                 
                </tr>
                 <tr class="thead-search">
                  <th></th>
                 
                  <th class="input-filter">@lang('parking.room')</th>
                  <th class="input-filter">@lang('parking.license_plate')</th>
                  <th class="input-filter">@lang('parking.cancel_date')</th>
                  <th class="input-filter">@lang('parking.cancel_by')</th>
                 
                 
                </tr>
                </thead>
                <tbody>
        
                @foreach ($listHistorys as $key=>$list)
                <tr >
                  <td>{{ $key+1 }}</td>
                  <td>{{ $list['room_name'] }}</td>
                   <td>{!! $list['license_plate_category']." ".$list['license_plate']."<BR>".$list['province_name'] !!}</td>
                  <td>{{ created_date_format($list['created_at']) }}</td>
                  <td>{{ $list['first_name']." ".$list['last_name'] }}
                  </td>
                 
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

     var table = $('#example1').DataTable(
      {
        "bSortCellsTop": true
      
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

    var table2 = $('#example2').DataTable(
      {
        "bSortCellsTop": true
       
      }
      );
    
    $.each($('.input-filter', table2.table().header()), function () {
        var column = table2.column($(this).index());
        $( 'input', this).on( 'keyup change', function () {
            if ( column.search() !== this.value ) {
                column
                    .search( this.value )
                    .draw();
            }
        } );
    } );

 
 
})

$(".btn-delete").on("click",function(){
  var parent = $(this).closest('tr') ;
  var useId = $(this).data('id');
  swal({
        title: (($("#app_local").val()=='th') ? 'คุณแน่ใจไหม?' : 'Are you sure?' ) ,
        text: (($("#app_local").val()=='th') ? 'คุณต้องการยกเลิกการใช้คูปองของทะเบียนนี้ใช่หรือไม่' : "You want to cancel this coupon!" ) ,
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: (($("#app_local").val()=='th') ? 'ยืนยัน' : 'Accept' ),
        cancelButtonText: (($("#app_local").val()=='th') ? 'ยกเลิก' : 'Cancel' ),
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-default',
        buttonsStyling: false,
        reverseButtons: true
  }).then((result) => {
          if (result.value) {
              var route = "/parking/cancel/"+useId+"?api_token="+api_token ;
              ajaxPromise('POST',route,{'_method':'DELETE'}).done(function(data){
               parent.remove();
              }).fail(function(txt) {
                var error = JSON.stringify(txt);
                           swal(
                            'Error...',
                            error,
                            'error'
                          )
              });

          } else if (result.dismiss === 'cancel') {
            
          }
        })
})

</script>
@endsection   
