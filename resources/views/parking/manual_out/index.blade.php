@extends('main.layouts.main')


@section('style')
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
  <link href="{{ url('plugins/iCheck/square/red.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
   <style type="text/css">
    tr:hover {cursor: pointer;}
    .custom-checkbox {
      -webkit-appearance: none;
  background-color: #fafafa;
  border: 1px solid #cacece;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05), inset 0px -15px 10px -12px rgba(0,0,0,0.05);
  padding: 9px;
  border-radius: 3px;
  display: inline-block;
  position: relative;
  width: 50px; height: 50px;
    }
    .custom-checkbox:checked {
  background-color: #00c0ef;
  border: 2px solid #00a7d0;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05), inset 0px -15px 10px -12px rgba(0,0,0,0.05), inset 15px 10px -12px rgba(255,255,255,0.1);
  color: #99a1a7;
}
  .custom-checkbox:checked:after {
  content: '\2714';
  font-size: 36px;
  position: absolute;
  top: 0px;
  left: 50%;
  margin-left: -12px;
  color: #FFF;
  font-size: 30px;
  text-align: center;
}
.td-left-side{
  width: 100px; text-align: right; float: left;
}
.td-license-left-side{
  width: 100px;float:left; font-size: 36px; text-align: right; margin-right: 10px;
}

  </style>

@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       
        <i class="fa fa-user"></i>
       
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
               <table id="checkin" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                  <th>@lang('parking.license_plate')</th>
                  <th>@lang('parking.start_date')</th>
                  <th>@lang('main.tool')</th>
                </tr>
                <tr class="thead-search">
                  <th></th> 
                  <th class="input-filter">@lang('parking.license_plate')</th>
                  <th class="input-filter">@lang('parking.start_date')</th>
                  <th ></th>
                </tr>
                </thead>
                <tbody>
        
                @foreach ($listIns as $key=>$checkin)
                <tr>
                  <td>{{ $key+1 }}</td>
                  <td>{!! $checkin['license_plate_category']." ".$checkin['license_plate']."<BR>".$checkin['province_name'] !!}</td>
                  <td>{{ created_date_format($checkin['created_at']) }}</td>
                 
                  <td> 
                      <button class="btn btn-success btn-setuse btn-xs" data-id="{{ $checkin['id'] }}" data-license-plate="{{$checkin['license_plate']}}"
                      data-license-plate-category="{{$checkin['license_plate_category']}}"
                      data-province-id="{{$checkin['province_id']}}"
                      data-start-date="{{ $checkin['created_at'] }}"
                       ><i class="fa fa-clock-o"></i></button> 
                  </td>
                 
                </tr>
                @endforeach
               
              </table>
            </div>
            <!-- /.box-body -->
      </div>

      <div class="box">
            <div class="box-header">
              <h3 class="box-title"></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              
              <table id="check_out" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                  <th>@lang('parking.license_plate')</th>
                  <th>@lang('parking.hour_use')</th>
                  <th>@lang('parking.start_date')</th>
                  <th>@lang('parking.end_date')</th>
                  <th>@lang('main.tool')</th>
                </tr>
                <tr class="thead-search">
                  <th></th> 
                  <th class="input-filter">@lang('parking.license_plate')</th>
                  <th class="input-filter">@lang('parking.hour_use')</th>
                  <th class="input-filter">@lang('parking.start_date')</th>
                  <th class="input-filter">@lang('parking.end_date')</th>
                  <th ></th>
                </tr>
                </thead>
                <tbody>
        
                @foreach ($listOuts as $key=>$checkin)
                <tr>
                  <td>{{ $key+1 }}</td>
                  <td>{!! $checkin['license_plate_category']." ".$checkin['license_plate']."<BR>".$checkin['province_name'] !!}</td>
                  <td>{{$checkin['hour_use']}}</td>
                  <td>{{ created_date_format($checkin['created_at']) }}</td>
                  <td>{{ created_date_format($checkin['outed_at']) }}</td>

                  <td></td>
                 
                </tr>
                @endforeach
               
              </table>
            
            </div>
            <!-- /.box-body -->
      </div>
    </section>
    <!-- /.content -->
<div class="modal fade" id="modal-default">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h3 class="debt-title"></h3>
                <h5 class="debt-text"></h5>
              </div>
              <div class="modal-body">
                  <div class="form-group">

                    <label for="name">@lang('parking.manual_time_out')</label>
                    <div class="row">
                       <div class="col-xs-2"> 
                        <input type="text" class="form-control" id="send_date_day"  placeholder="@lang('parcel.send_date_day')" value="{{ date('d') }}" >
                       </div>
                      <div class="col-xs-2">  
                        <input type="text" class="form-control" id="send_date_month"  placeholder="@lang('parcel.send_date_month')" value="{{ date('m') }}" >
                      </div>
                      <div class="col-xs-2">
                        <input type="text" class="form-control" id="send_date_year"  placeholder="@lang('parcel.send_date_year')" value="{{ date('Y') }}" >
                      </div>
                      <div class="col-xs-2">
                        <input type="text" class="form-control" id="send_date_hour"  placeholder="@lang('parcel.send_date_hour')" value="{{ date('H') }}" >
                      </div>
                      <div class="col-xs-2">
                        <input type="text" class="form-control" id="send_date_minute"  placeholder="@lang('parcel.send_date_minute')" value="{{ date('i') }}" >
                      </div>
                    </div>
                    <input type="hidden" id="parking_checkin_id" value="" >
                   
                        
                  </div>
                 
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
                <button type="button" class="btn btn-primary btn-save-out"> @lang('main.btn_save')
                   <i class="fa fa-spinner fa-spin fa-fw" style="display:none;"></i>
                </button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="modal-debt">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="debt-title"></h3>
          <h5 class="debt-text"></h5>
        </div>
        <div class="modal-body">
           <select id="debt_type" name="debt_type" class="form-control">
               <option value="0">@lang('parking.please_select_debt_type')</option>
            @if(isset($debtType))
              @foreach($debtType as $d)
                 <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
              @endforeach
            @endif
          </select> 
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
          <button type="button" class="btn btn-primary btn-save"> @lang('parking.accept_pay_debt')
             <i class="fa fa-spinner fa-spin fa-fw" style="display:none;"></i>
          </button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>




@endsection

@section('javascript')
<!-- DataTables -->

<script src="{{ url('plugins/iCheck/icheck.js') }} "></script>
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src=" {{ url('js/utility/data_table.js') }}"></script>
<script type="text/javascript">
$(function () {

  var table = $('#checkin').DataTable(
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


$(".btn-setuse").on("click",function(){
  $("#modal-default input").val('');

  // $("#debt_type").val(0).trigger('change');
  $("#parking_checkin_id").val($(this).data('id'));
  
  
  var d = new Date();
  $("#send_date_year").val( d.getFullYear() );
  $("#send_date_month").val( d.getMonth()+1 );
  $("#send_date_day").val( d.getDate() );
  $("#send_date_hour").val( d.getHours() );
  $("#send_date_minute").val( d.getMinutes() );

  $("#modal-default").modal("toggle");


})
$(".btn-save-out").on("click",function(){
    $(this).find('fa-spin').show();
    var pad = "00";
    var dateMinute = $("#send_date_minute").val() ;
    var dateHour = $("#send_date_hour").val() ;
    var dateDay = $("#send_date_day").val() ;
    var dateMonth = $("#send_date_month").val() ;
    var day = pad.substring(0, pad.length - dateDay.length) + dateDay ;
    var month = pad.substring(0, pad.length - dateMonth.length) + dateMonth ;
    var min = pad.substring(0, pad.length - dateMinute.length) + dateMinute ;
    var hour = pad.substring(0, pad.length - dateHour.length) + dateHour ;
    var date = $("#send_date_year").val()+"-"+month+"-"+day+" "+hour+":"+min ;
   
    var data = { id : $("#parking_checkin_id").val()
                ,check_out_date : date
               
              };
    checkDebtHour(data)
    .done(function(data){
       console.log(data);
       data.manual_out = 1 ;
        var route = "/parking/guard?api_token="+api_token ;
        ajaxPromise('POST',route,data).done(function(data){
          swal({
                title: "@lang('parking.checkout_success')" ,
                type: 'success',
                showCancelButton: false,
                confirmButtonText: "@lang('main.ok')"
              }).then((result) => {
                if (result.value) {
                  location.reload();
                }
              })
             
             
        });

       return false;

       
    }).fail(function(error){
        swal(
          'Error...',
         error,
          'error'
        )
    })




    
  

    // var route = "/parking/manual-out?api_token="+api_token ;
    // ajaxPromise('POST',route,data).done(function(data){
    //     $("#parking_checkin_id").val('');
    //     $(this).find('fa-spin').hide();
    //    swal({
    //     title:(($("#app_local").val()=='th') ? 'บันทึกสำเร็จ' : 'Create Success' ) ,
    //     type: 'success',
    //     showCancelButton: false,
    //     confirmButtonText: "@lang('main.ok')"
    //   }).then((result) => {
    //     if (result.value) {
    //         location.reload();
    //     }
    //   })
    // });
})


function checkDebtHour(send_data){
  var dfd = $.Deferred();
  var route = "/parking/guard/check-hour?api_token="+api_token ;
  ajaxPromise('POST',route,send_data).done(function(data){
       console.log(data);
        if(data.total_debt_hour>0){ 
          var debt = data.total_debt_hour;
          var maxDate = moment.unix(data.max_end_date).format("DD/MM/YYYY HH:mm");
          var nowDate = moment.unix(data.now_date).format("DD/MM/YYYY HH:mm");

          var title=(($("#app_local").val()=='th') ? 'คูปองใช้สิทธิ์ได้ถึงเวลา?'+maxDate : 'Are you sure?' ) ;
          var text=(($("#app_local").val()=='th') ? 'โดยขณะนี้ '+nowDate+' ต้องจ่ายเงินเพิ่ม จำนวน '+debt+' ชั่วโมง ' : "You want to paid "+debt+" hour !" ) ;

          if(data.previus_month_debt_hour>0){
              var previusDebt = data.previus_month_debt_hour;
              var currentDebt = data.debt_hour;
            text+= (($("#app_local").val()=='th') ? 'โดยแบ่งเป็นของเดือนก่อนหน้า '+previusDebt+' ชั่วโมง และ เดือนปัจจุบัน '+currentDebt+' ชั่วโมง ' : "it is previous month "+previusDebt+" hour and current month "+currentDebt+" hour !" ) ;
          }


          $(".debt-title").text(title);
          $(".debt-text").text(text);

          $("#modal-debt").modal('show');

          return false;

          // swal({
          //         title:(($("#app_local").val()=='th') ? 'คูปองใช้สิทธิ์ได้ถึงเวลา?'+maxDate : 'Are you sure?' ) ,
          //         text:(($("#app_local").val()=='th') ? 'โดยขณะนี้ '+nowDate+' ต้องจ่ายเงินเพิ่ม จำนวน '+debt+' ชั่วโมง' : "You want to paid "+debt+" hour !" ) ,
          //         type: 'warning',
          //         showCancelButton: true,
          //         confirmButtonText: (($("#app_local").val()=='th') ? 'ยืนยันจ่ายเงินเพิ่ม' : 'Ok' ),
          //         cancelButtonText: (($("#app_local").val()=='th') ? 'ยกเลิก' : 'Cancel' ),
          //         confirmButtonClass: 'btn btn-danger',
          //         cancelButtonClass: 'btn btn-default',
          //         buttonsStyling: false,
          //         reverseButtons: true
          //   }).then((result) => {
            
          //           if (result.value) {
          //               dfd.resolve(send_data);

          //           } else if (result.dismiss === 'cancel') {
          //             console.log('cancel');
          //              location.reload();

          //               // dfd.reject("");
          //           }
          //         })

       }else{
          dfd.resolve(send_data);
       }
   });
  return dfd.promise();
}


$(".btn-save").on("click",function(){
  console.log("save");
    $(this).find('fa-spin').show();
    var pad = "00";
    var dateMinute = $("#send_date_minute").val() ;
    var dateHour = $("#send_date_hour").val() ;
    var dateDay = $("#send_date_day").val() ;
    var dateMonth = $("#send_date_month").val() ;
    var day = pad.substring(0, pad.length - dateDay.length) + dateDay ;
    var month = pad.substring(0, pad.length - dateMonth.length) + dateMonth ;
    var min = pad.substring(0, pad.length - dateMinute.length) + dateMinute ;
    var hour = pad.substring(0, pad.length - dateHour.length) + dateHour ;
    var date = $("#send_date_year").val()+"-"+month+"-"+day+" "+hour+":"+min ;
   
    var data = { id : $("#parking_checkin_id").val()
                ,check_out_date : date
                ,debt_type : $("#debt_type").val()
                ,manual_out:1
              };
    var route = "/parking/guard?api_token="+api_token ;
    ajaxPromise('POST',route,data).done(function(data){

         $(".debt-title").text('');
         $(".debt-text").text('');
         $("#debt_type").val(0);
         $("#modal-debt").modal('hide');
         $("#modal-default").modal('hide');
         location.reload();
    });

   return false;
})

</script>
@endsection		
