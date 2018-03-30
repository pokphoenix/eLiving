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
              <div>
                      
                       <input type="text" class="form-control" style="width:60px;float: left;margin-right: 10px;" id="s_license_plate_category" name="s_license_plate_category" maxlength="3" placeholder="@lang('parking.license_plate_category')" value="" >
                    <input type="text" class="form-control" style="width:100px;float: left;margin-right: 10px;" id="s_license_plate" name="s_license_plate" placeholder="@lang('parking.license_plate')" maxlength="4"  value="" >
                        <select class="select2 form-control"  id="s_province_id" name="s_province_id" style="width:190px;" >
                       
                        @if (isset($province))
                          @foreach($province as $key=> $p)
                          <option value="{{ $p['id'] }}" @if($key==0) selected="" @endif  > {{ $p['text'] }}</option>
                          @endforeach
                        @endif
                    </select>
                    <button type="button"  class="btn btn-primary btn-search"><i class="fa fa-search"></i></button>
                    </div>
              <!--  <button class="btn btn-success btn-sm btn-create" > <i class="fa fa-plus"></i> @lang('parking.use_coupon')</button>
              -->

               <input type="hidden" id="remain_hour" name="remain_hour" value="">
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="checkin" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                  <th>@lang('parking.license_plate')</th>
                  <th>@lang('parking.hour_use')</th>
                  <th>@lang('parking.start_date')</th>
                  <th>@lang('main.tool')</th>
                </tr>
                <tr class="thead-search">
                  <th></th> 
                  <th class="input-filter">@lang('parking.license_plate')</th>
                  <th class="input-filter">@lang('parking.hour_use')</th>
                  <th class="input-filter">@lang('parking.start_date')</th>
                  <th ></th>
                </tr>
                </thead>
                <tbody>
        
                @foreach ($checkins as $key=>$checkin)
                <tr >
                  <td>{{ $key+1 }}</td>
                  <td>{!! $checkin['license_plate_category']." ".$checkin['license_plate']."<BR>".$checkin['province_name'] !!}</td>
                   <td>{{ $checkin['set_used_hour'] }}</td>
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
            
              <!--  <button class="btn btn-success btn-sm btn-create" > <i class="fa fa-plus"></i> @lang('parking.use_coupon')</button> -->
             

               <input type="hidden" id="remain_hour" name="remain_hour" value="">
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                 
                  <th>@lang('parking.license_plate')</th>
             
                  <th>@lang('parking.hour_use')</th>
                  <th>@lang('parking.start_date')</th>
                  <th>@lang('parking.end_date')</th>
                  <th>@lang('parking.created_at')</th>
                  <th>@lang('parking.used')</th>
                  <th>@lang('main.tool')</th>
                  <th>@lang('parking.created_by')</th>
                </tr>
                <tr class="thead-search">
                  <th></th>
                 
                  <th class="input-filter">@lang('parking.license_plate')</th>
             
                  <th class="input-filter">@lang('parking.hour_use')</th>
                  <th class="input-filter">@lang('parking.start_date')</th>
                  <th class="input-filter">@lang('parking.end_date')</th>
                  <th class="input-filter">@lang('parking.created_at')</th>
                  <th class="input-filter">@lang('parking.used')</th>
                  <th ></th>
                  <th class="input-filter">@lang('parking.created_by')</th>
                </tr>
                </thead>
                <tbody>
				
				        @foreach ($lists as $key=>$list)
                <tr  @if(isset($list['deleted_at']))  class="text-delete" @endif>
                  <td>{{ $key+1 }}</td>
                  <td>{!! $list['license_plate_category']." ".$list['license_plate']."<BR>".$list['province_name'] !!}</td>
                
                  <td>@if($list['is_until_out']=="1"&& is_null($list['end_date']) ) 
                      @lang('parking.is_until_out') 
                      @else
                       {{ $list['hour_use']}}
                      @endif
                    </td>
                  <td>{{ created_date_format($list['start_date']) }}</td>
                  <td>@if(isset($list['end_date']))
                    {{ created_date_format($list['end_date']) }}
                  @elseif($list['is_until_out']==0)
                    -
                  @else
                    @lang('parking.is_until_out')
                  @endif 
                  </td>
                  <td>{{ created_date_format($list['created_at']) }} </td>
                  <td>@if(is_null($list['used_date']))
                        @lang('parking.not_use')
                      @else
                        @lang('parking.use')
                      @endif
                 </td>
                  <td> 
                    @if(isset($list['deleted_at']))
                      @lang('parking.cancel') @lang('parking.by') {{ $list['deleted_first_name']." ". $list['deleted_last_name'] }}
                   
                    @elseif(is_null($list['used_date']))
                     <button class="btn btn-danger btn-delete btn-xs" data-id="{{ $list['id'] }}"  ><i class="fa fa-trash-o"></i></button> 
                      <button class="btn btn-default btn-edit btn-xs" data-id="{{ $list['id'] }}"
                       data-license-plate="{{$list['license_plate']}}"
                       data-license-plate-category="{{$list['license_plate_category']}}"
                       data-province-id="{{$list['province_id']}}"
                       data-start-date="{{$list['start_date']}}"
                       data-checkin-id="{{$list['parking_checkin_id']}}"
                       data-is-until-out={{$list['is_until_out']}}>
                       <i class="fa fa-edit"></i></button> 
                    @endif
                   </td>
                  <td>
                    {{ $list['first_name']." ".$list['last_name'] }}
                  </td>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('parking.use_coupon')</h4>
              </div>
              <div class="modal-body">
                

                 <form id="parking-form" role="form" method="POST" action="{{$action}}" enctype="multipart/form-data"  >
                 
                  <div class="form-group none">
                    <label for="name">@lang('parking.license_plate')</label>
                    <div>
                       <input type="hidden" id="start_date" value="">
                       <input type="hidden" id="parking_checkin_id" name="parking_checkin_id" value="">
                       <input type="text" class="form-control" style="width:60px;float: left;margin-right: 10px;" id="license_plate_category" name="license_plate_category" maxlength="3" placeholder="@lang('parking.license_plate_category')" value="" >
                    <input type="text" class="form-control" style="width:100px;float: left;margin-right: 10px;" id="license_plate" name="license_plate" placeholder="@lang('parking.license_plate')" maxlength="4"  value="" >
                        <select class="select2 form-control"  id="province_id" name="province_id" style="width:190px;" >
                       
                        @if (isset($province))
                          @foreach($province as $key=> $p)
                          <option value="{{ $p['id'] }}" @if($key==0) selected="" @endif  > {{ $p['text'] }}</option>
                          @endforeach
                        @endif
                    </select>
                    </div>
                   
                  </div> 
                  
                  <div class="form-group">
                      <label for="name">@lang('parking.hour_limit')</label>
                      <input type="text" class="form-control"  id="hour_use" name="hour_use"  placeholder="@lang('parking.hour_limit')"  value="1" >
                  </div>
                  <div class="form-group">
                      <label for="name">@lang('parking.out_before')</label>
                      <span  id="end_date"  ></span>
                  </div>
                  <div class="form-check">
                      <label class="form-check-label">
                      <input type="checkbox" id="is_until_out" name="is_until_out" class="form-check-input" >
                         @lang('parking.is_until_out') 
                      </label>
                  </div>

                   
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
                <button type="button" class="btn btn-primary btn-save">@lang('main.btn_save')
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
<script src="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<!-- DataTables -->
<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src=" {{ url('js/utility/data_table.js') }}"></script>
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script type="text/javascript">
$(function () {

	 var table = $('#example1').DataTable(
      {
        "bSortCellsTop": true
        ,"order": [[ 1, 'asc' ]]
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
  $("#province_id").select2();
  $("#s_province_id").select2();
  setTimeout(function() {
    $('#province_id').val(1).trigger('change');
  }, 500);
})
// $('#end_date').daterangepicker({
//     "singleDatePicker": true,
//     "timePicker": true,
//     "timePicker24Hour":true,
//     showDropdowns: true,
//     locale: {
//         format: 'MM/DD/YYYY H:mm'
//     },
//     "opens": "left"
   
// });


 $(document).on("keypress","#start_hour,#start_minute,#end_hour,#end_minute",function(event) {
    if ((event.which != 46 || event.val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
      event.preventDefault();
    }

  });

// $('#start_date').daterangepicker({
//     "singleDatePicker": true,
//     "timePicker": true,
//     "timePicker24Hour":true,
//     showDropdowns: true,
//      "timePickerIncrement": 15,
   
//     locale: {
//         format: 'MM/DD/YYYY H:mm'
//     },
//      minDate: new Date() , 
//     "opens": "left"
   
// }, function(start, end, label) {
//   var startDate = start.format('YYYY-MM-DD H:mm');
//   var maxDate = new Date(startDate) ;
//   var hour = $("#remain_hour").val();
//   maxDate.setHours(maxDate.getHours()+hour);
//   $( "#end_date" ).daterangepicker({ 
//     "singleDatePicker": true,
//     "timePicker": true,
//     "timePicker24Hour":true,
//     showDropdowns: true,
//      "timePickerIncrement": 10,
    
//     locale: {
//         format: 'MM/DD/YYYY H:mm'
//     },
//     "opens": "left",
//     minDate: new Date(startDate) , 
//     maxDate: maxDate });
  
// });

function getLastDate(monthYear){
    var d = new Date(monthYear+'-01');
    var selectDate = new Date(d.getFullYear(),d.getMonth()+1,0);
    return selectDate.getDate();

}

$("#start_day").on("change",function(){
    var startDaY = $(this).val();
    var monthYear = $("#month-year").val();
    var lastDate = getLastDate(monthYear);
    var html = '';
    for(var i = startDaY ; i<= lastDate ; i++) {
        html += "<option value=\""+i+"\" >"+i+"</option>";
    }
    $("#end_day").html(html);
})

$("#hour_use").on("keyup",function(){
    GetDateEnd();
 
})

function GetDateEnd(){
  // var startMonth = $("#start_month").val();
  // var startDay = $("#start_day").val();
  // var startHour = $("#start_hour").val();
  // var startMinute = $("#start_minute").val();
  // var start_date = startMonth+'-'+startDay+" "+startHour+":"+startMinute;
  var startDate = $("#start_date").val();
  var start = new Date(startDate);
  var endDate = start.getTime()+($("#hour_use").val()*60*60*1000);
  var end = new Date(endDate);
  // console.log(start.getTime(),endDate, (endDate-start.getTime())/1000/60/60  )



  $("#end_date").text(moment(endDate).format("DD/MM/YYYY HH:mm"));
}


$("#start_day,#end_day").on("change",function(){
  GetDateEnd($(this));
})


$("#start_hour,#start_minute").on("keyup",function(){
  GetDateEnd($(this));
})





$("#month-year").on("change",function(){
    var monthYear = $(this).val() ;
    // var route = "/parking/{{$roomId}}/hour-remain?api_token="+api_token ;
    // var data = { 'month_year':monthYear } ;
    var monthYearTxt = $("#month-year option:selected").text();
    $("#start_month,#end_month").val(monthYear).trigger('change').attr('disabled', true);
    var lastDate = getLastDate(monthYear);
    var html = '';
    for(var i = 1 ; i<= lastDate ; i++) {
        html += "<option value=\""+i+"\" >"+i+"</option>";
    }
    $("#start_day,#end_day").html(html);


     var cd = new Date();
     var currentDay = cd.getDate();

      $("#start_day,#end_day").val(currentDay).trigger('change');
   

    // ajaxPromise('POST',route,data).done(function(data){
    //     console.log(data.remain_hour);
    //      $(".change-month").text(monthYearTxt);
    //     $(".remain-hour").text(data.remain_hour);
    // })
});


$(".btn-create").on("click",function(){
  $("#modal-default input").val('');
  
  var d = new Date();
  $("#start_hour").val(d .getHours());
  // $("#start_minute").val(d .getMinutes());
  $("#start_minute").val('00');
  // $("#end_hour").val(d .getHours()+1);
  // $("#end_minute").val(d .getMinutes());
  $("#hour_use").val(1).attr('disabled',false);
  GetDateEnd();
  $("#is_until_out").prop('checked', false);

  $("#month-year").val($("#month-year option:first").val()).trigger('change');

  $("#modal-default").modal("toggle");
})
$(document).on("click",".btn-setuse",function(){
  $("#modal-default input").val('');
  var licensePlate = $(this).data('license-plate');
  var licensePlateCategory = $(this).data('license-plate-category');
  var provinceId = $(this).data('province-id');
  var startDate = $(this).data('start-date');
  var checkinId = $(this).data('id');

  $("#license_plate").val(licensePlate);
  $("#license_plate_category").val(licensePlateCategory);
  $("#province_id").val(provinceId);
  $("#start_date").val(startDate);
  $("#parking_checkin_id").val(checkinId);
  
  $("#modal-default #parking_use_id").remove();
  $("#hour_use").val(1).attr('disabled',false);
  GetDateEnd();
  $("#is_until_out").prop('checked', false);

  $("#month-year").val($("#month-year option:first").val()).trigger('change');

  $("#modal-default").modal("toggle");
})
$(".btn-save").on("click",function(){
  $("#parking-form").submit();
})

$(".btn-edit").on("click",function(){
  console.log('btn-edit click');

   $("#modal-default #parking_use_id").remove();

   $("#modal-default input").val('');
  var licensePlate = $(this).data('license-plate');
  var licensePlateCategory = $(this).data('license-plate-category');
  var provinceId = $(this).data('province-id');
  var startDate = $(this).data('start-date');
  var checkinId = $(this).data('checkin-id');
  var useId = $(this).data('id');
  var isUntilOut = $(this).data('is-until-out');
  console.log(isUntilOut);

  $("#license_plate").val(licensePlate);
  $("#license_plate_category").val(licensePlateCategory);
  $("#province_id").val(provinceId);
  $("#start_date").val(startDate);
  $("#parking_checkin_id").val(checkinId);
  var html = "<input type=\"hidden\" id=\"parking_use_id\" value=\""+useId+"\">";
  
  $("#hour_use").val(1).attr('disabled',false);
  GetDateEnd();
  if(isUntilOut){
    $("#is_until_out").prop('checked', true);
    $("#hour_use").val('').attr('disabled',true);
  }else{
     $("#is_until_out").prop('checked', false);
     $("#hour_use").val(1).attr('disabled',false);
  }
  
  $("#month-year").val($("#month-year option:first").val()).trigger('change');
  $("#modal-default").append(html);
  $("#modal-default").modal("toggle");
})

$(".btn-delete").on("click",function(){
  var parent = $(this).closest('tr') ;
  var buyId = $(this).data('id');
  swal({
        title: (($("#app_local").val()=='th') ? 'คุณแน่ใจไหม?' : 'Are you sure?' ) ,
        text: (($("#app_local").val()=='th') ? 'คุณต้องการลบข้อมูลนี้ใช่หรือไม่' : "You want to delete this data!" ) ,
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: (($("#app_local").val()=='th') ? 'ลบ' : 'Delete' ),
        cancelButtonText: (($("#app_local").val()=='th') ? 'ยกเลิก' : 'Cancel' ),
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-default',
        buttonsStyling: false,
        reverseButtons: true
  }).then((result) => {
          if (result.value) {
              var route = "{{$route}}/"+buyId+"?api_token="+api_token ;
              ajaxPromise('POST',route,{'_method':'DELETE'}).done(function(data){
                parent.addClass('text-delete');
                parent.find('td:last-child').html((($("#app_local").val()=='th') ? 'ยกเลิกรายการ' : 'deleted' ));
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

$("#is_until_out").on("change",function(){
  if($(this).is(":checked")){
     $("#hour_use").val('').attr('disabled',true);
     $("#end_date").text('');
  }else{
    $("#hour_use").val(1).attr('disabled',false);
  GetDateEnd();
  }
})

 


// $(".btn-edit").on("click",function(){
//    $("#modal-default input").val('');
//     var buyId = $(this).data('id');
//     var roomId = $(this).data('room-id');
//     var packageId = $(this).data('package-id');
//     $('#room_id').val(roomId).trigger('change');
//     $('#package_id').val(packageId).trigger('change');
//     var route = "/parking/buy/"+packageId+"/edit?api_token="+api_token ;
//     $("#modal-default modal-title").text((($("#app_local").val()=='th') ? 'แก้ไขการขาย' : 'Edit Parking Buy' ));
//     $("#parking-form").attr({'action': $("#apiUrl").val()+"/parking/buy/"+buyId+"?api_token="+api_token });
//     $("#modal-default").modal("toggle");
//     var html = '<input type="hidden" name="_method" value="PUT">';
//     $("#parking-form").append(html)

// })

// function diffHour(ele){
//   console.log(ele.attr('id'))
//   var startMonth = $("#start_month").val();
//   var startDay = $("#start_day").val();
//   var startHour = $("#start_hour").val();
//   var start_date = startMonth+'-'+startDay+" "+startHour+":00";

//   var endMonth = $("#end_month").val();
//   var endDay = $("#end_day").val();
//   var endHour = $("#end_hour").val();
//   var endMinute = $("#end_minute").val();
//   console.log('End B4',endHour,endMinute);

//   if(ele.attr('id')=="start_hour" && (startHour>endHour)){
//     $("#end_hour").val( parseInt(startHour)+1);
//     endHour = parseInt(startHour)+1;
//   }
 

//   if(endHour>=23){
//     endHour=23 ;
//      $("#end_hour").val(23);
//   }

//   if(endMinute>0){
//       console.log('add hour');
//     endHour++;
//   }





//   console.log('End AF',endHour,endMinute);

//   var end_date = endMonth+'-'+endDay+" "+endHour+":00";      
//   console.log(start_date,end_date);

//   var start = new Date(start_date),
//       end   = new Date(end_date);
//   var diff = end.getTime()-start.getTime() ;
//   var diffInHour = diff/1000/60/60 ;
//   $("#diff_hour").text(diffInHour);
// }



$(function() {
    $("#parking-form").validate({
      rules: {
        license_plate: {
          required: true,
          number:true,
          maxlength:4
        },
        license_plate_category: {
          required: true,
          maxlength:3
        },
        province_id:"required"
      
      },
      messages: {
        license_plate: (($("#app_local").val()=='th') ? 'ทะเบียนรถไม่ถูกต้อง' : 'Wrong License Plate' ),
        license_plate_category: (($("#app_local").val()=='th') ? 'หมวด' : 'Wrong category' ),
        province_id: (($("#app_local").val()=='th') ? 'จังหวัดไม่ถูกต้อง' : 'Wrong Province' ),
       
        
      },
      highlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
       
      }
      ,submitHandler: function (form) {
        $(".btn-save").find('.fa-spinner').show();

        //var remainHour = $("#month-year option:selected").data('remain');
       
        // var remainHour = $(".remain-hour").text();
        // var diffInHour = parseInt($("#diff_hour").text());

        // var startMonth = $("#start_month").val();
        // var startDay = $("#start_day").val();
        // var startHour = $("#start_hour").val();
        // var startMinute = $("#start_minute").val();
        // var start_date = startMonth+'-'+startDay+" "+startHour+":"+startMinute;

        var startDate = $("#start_date").val();
        var diffInHour = parseInt($("#hour_use").val());
        var start = new Date(startDate);
        var endDate = start.getTime()+($("#hour_use").val()*60*60*1000);
        var end = new Date(endDate);
        // console.log(start.getTime(),endDate, (endDate-start.getTime())/1000/60/60  );
        var end_date =  moment(endDate).format("YYYY-MM-DD HH:mm");

        // console.log(start.getTime(),endDate, (endDate-start.getTime())/1000/60/60  );



        // var endMonth = $("#end_month").val();
        // var endDay = $("#end_day").val();
        // var endHour = $("#end_hour").val();
        // var endMinute = $("#end_minute").val();
        // if(endMinute>0){
        //   endHour++;
        // }

        // var end_date = endMonth+'-'+endDay+" "+endHour+":00";  

        // console.log(diffInHour);
        // console.log(remainHour,start_date,end_date);
        // console.log(remainHour,start,end,diff);
        // console.log(parseInt(remainHour),diffInHour,parseInt(remainHour) < diffInHour);
        // if(diffInHour==0){
        //    swal(
        //         'Error...',
        //         (($("#app_local").val()=='th') ? 'ระบุเวลาจอดรถให้ถูกต้อง' : 'Wrong Date time' ),
        //         'error'
        //       )
        //     $(".btn-save").find('.fa-spinner').hide();
        //   return false;
        // }else if(remainHour=="0"  || parseInt(remainHour) < diffInHour ){
        //   swal(
        //         'Error...',
        //         (($("#app_local").val()=='th') ? 'จำนวนชั่วโมงคงเหลือไม่เพียงพอ' : 'Remain Hour not enough' ),
        //         'error'
        //       )
        //    $(".btn-save").find('.fa-spinner').hide();
        //   return false;
        // }

       

       
        var UseUrl = form.action ;
        var form_data = new FormData($("#parking-form")[0]);
        if($("#is_until_out").is(":checked")){
            form_data.append('hour_use',1);
        }
        if($("#modal-default #parking_use_id").length > 0){
           form_data.append('except_id',$("#modal-default #parking_use_id").val());
        } 
        // console.log('url',UseUrl);
        // return false;
        // form_data.append('start_date',start_date);
        form_data.append('is_until_out',$("#is_until_out").is(":checked"));
        var route = "/parking/{{$roomId}}/use/check-debt-hour?api_token="+api_token;
        $.ajax({
         type: 'POST' ,
         url: $("#apiUrl").val()+route ,
         data: form_data ,
         processData: false,
         contentType: false,
         success: function (data) {
          
            if(data.result=="true"){
                saveSetUse(UseUrl,form_data);
            }else{
                var error = JSON.stringify(data.errors);
                swal({
                  title:error ,
                  type: 'warning',
                  showCancelButton: false,
                  confirmButtonText: "@lang('main.ok')"
                }).then((result) => {
                  if (result.value) {
                     saveSetUse(UseUrl,form_data);
                  }else{
                      $(".btn-save").find('.fa-spinner').hide();
                  }

                })
            }
         }

     })

             
             return false; // required to block normal submit since you used ajax
         }

    });

  });


function saveSetUse(form_action,form_data){
    $.ajax({
       type: $("#parking-form").attr('method') ,
       url: form_action ,
       data: form_data ,
       processData: false,
       contentType: false,
       success: function (data) {
          if($("#modal-default #parking_use_id").length){
            title = "@lang('main.update_success')";
          }else{
            title = "@lang('main.create_success')";
          }
          $(".btn-save").find('.fa-spinner').hide();
          if(data.result=="true"){
            swal({
                title:title ,
                type: 'success',
                showCancelButton: false,
                confirmButtonText: "@lang('main.ok')"
              }).then((result) => {
                if (result.value) {
                  location.reload();
                }
              })
          }else{
            var error = JSON.stringify(data.errors);
            swal(
              'Error...',
              error,
              'error'
            )
          }
       }

    }).fail(function() {
       $(".btn-save").find('.fa-spinner').hide();
        swal(
          'Error...',
          "@lang('main.something_when_wrong')",
          'error'
        )
    });
}

$(".btn-search").on("click",function(){

    var data = { 
      "license_plate" : $("#s_license_plate").val()
      ,"license_plate_category" : $("#s_license_plate_category").val()
      ,"province_id": $("#s_province_id").val()
    };

    var route = "/parking/search/checkin?api_token="+api_token;

    ajaxPromise('POST',route,data).done(function(data){
        console.log(data);

        var html = '';

        var max = data.parking_checkins.length;
        if (max>0){
            var res = data.parking_checkins;
            for(var i =0 ; i<max ;i++){
              html += "<tr>"+
                      "<td>"+(i+1)+"</td>"+
                      "<td>"+res[i].license_plate_category+
                      " "+res[i].license_plate+
                      "<BR> "+res[i].province_name+"</td>"+
                      "<td>"+moment(res[i].created_at).format("YYYY-MM-DD HH:mm")+"</td>"+
                      "<td>"+ 
                      "<button class=\"btn btn-success btn-setuse btn-xs\" "+
                      " data-id=\""+res[i].id+"\" "+
                      " data-license-plate=\""+res[i].license_plate+"\" "+ 
                      " data-license-plate-category=\""+res[i].license_plate_category+"\" "+
                      " data-province-id=\""+res[i].province_id+"\" "+
                      " data-start-date=\""+res[i].created_at+" \" "+
                      "><i class=\"fa fa-clock-o\"></i></button> "+
                      "</td>"+
                      "</tr>";
            }
            
        }

        $("#checkin tbody").html(html);

    })
})


</script>
@endsection		
