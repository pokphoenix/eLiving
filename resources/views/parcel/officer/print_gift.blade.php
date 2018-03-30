@extends('main.layouts.main')


@section('style')

<link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ url('public/css/input.css') }}">

<style>
.videoWrapper {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 */
  padding-top: 25px;
  height: 0;
}
.videoWrapper iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
  </style>
@endsection

@section('content-wrapper')
  

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       
        <i class="fa fa-send"></i>
       
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
      @include('widgets.search.search')
       

        <div class="row">
          <div class="col-xs-12">
            <div class="box">
            <div class="box-header">
              <h3 class="box-title"></h3>
               <button class="btn btn-success btn-sm btn-select-row" > <i class="fa fa-plus"></i> @lang('parcel.select')</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th> <input type="checkbox" class="select-row-all"  > </th>
                  <th>@lang('user.no')</th>
                  <th>@lang('parcel.room')</th>
                  <th>@lang('parcel.type')</th>
                  <th>@lang('parcel.created_at')</th>
                  <th>@lang('parcel.send_date')</th>
                  <th>@lang('parcel.received_at')</th>
                  <th>@lang('parcel.gift_receive_name')</th>
                  
                </tr>
                 <tr class="thead-search" >
                  <th></th>
                  <th></th>
                  <th class="input-filter">@lang('parcel.room')</th>
                  <th class="input-filter">@lang('parcel.type')</th>
                  <th class="input-filter">@lang('parcel.created_at')</th>
                  <th class="input-filter">@lang('parcel.send_date')</th>
                  <th class="input-filter">@lang('parcel.received_at')</th>
                  <th class="input-filter">@lang('parcel.gift_receive_name')</th>
                 
                 
                </tr>
                </thead>

                <tbody>
        
                @foreach ($lists as $key=>$list)
                <tr >
                  <td><input type="checkbox" class="select-row" value="{{ $list['id'] }}" ></td>
                  <td>{{ $key+1 }}</td>
                  <td>{{ $list['room_name']}}</td>
                  <td>{{ $list['parcel_type_name']}} 
                    @if($list['type']==2)
                      ( {{ $list['supplies_type_name']}} )
                    @endif
                  </td>
                  <td>{{ created_date_format($list['created_at']) }}
                  <td>{{ created_date_format($list['send_date']) }}
                  <td>@if(isset($list['receive_at']))
                    {{ created_date_format($list['receive_at'])  }} @lang('parking.by') {{ $list['receive_name'] }}
                    @else
                    @lang('parcel.wait_receive')
                    @endif
                  </td>
                  <td>{{ $list['gift_receive_name'] }}</td>
                  

                </tr>
                @endforeach
               
              </table>
            </div>
            <!-- /.box-body -->
      </div>
          </div>
        </div>

       




      
    

    </section>
    <!-- /.content -->
 
@endsection

@section('javascript')
<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script src=" {{ url('js/utility/data_table.js') }}"></script>
<script type="text/javascript">
$(function () {

  
 
    // DataTable
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
    

 
})


</script>

<script src=" {{ url('js/utility/print.js') }}"></script>
<script type="text/javascript">
$(".btn-search").on('click', function(event) {
  var start_date = $("#start_date_year").val()+"-"+$("#start_date_month").val()+"-"+$("#start_date_day").val()+" "+$("#start_date_hour").val()+":"+$("#start_date_minute").val();
  var end_date = $("#end_date_year").val()+"-"+$("#end_date_month").val()+"-"+$("#end_date_day").val()+" "+$("#end_date_hour").val()+":"+$("#end_date_minute").val();


  start_date = moment(start_date).format('x')/1000;
  end_date = moment(end_date).format('x')/1000;

  var url = $("#baseUrl").val()+'/parcel/print-gift?start_date='+start_date+'&end_date='+end_date ;
 
  window.location.href=url ;
});

</script>

<script>


$(document).on("change",".select-row-all",function(){
  if($(this).is(':checked')){
    $("#example1 input[type=checkbox]").attr('checked',true);
  }else{
     $("#example1 input[type=checkbox]").attr('checked',false);
  }
})
$(document).on("click",".btn-select-row",function(){
    getSelectData().done(function(data){
      var url = $("#baseUrl").val()+'/parcel/print-gift/view?id='+data.id ;
      window.location.href=url ;
      // console.log(url);
      //   $("#frame-print").src(url);
      //   $("#modal-print").modal('toggle');
    });
})

function getSelectData(){
  var dfd = $.Deferred();
  var data = {id:[]};
  $("#example1 tbody input[type=checkbox]:checked:enabled").each(function(index, el) {
     
      data.id.push($(this).val());
  });

  if(data.id.length>0){
      dfd.resolve(data);
  }else{
      dfd.reject("");
  }
  return  dfd.promise() ;
}
</script>
@endsection   
