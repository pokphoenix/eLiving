@extends('main.layouts.main')


@section('style')
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

  <link href="{{ url('plugins/iCheck/square/red.css') }}" rel="stylesheet">

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
  width: 70px; text-align: right; float: left;
}
.td-license-left-side{
  width: 70px;float:left; font-size: 36px; text-align: right; margin-right: 10px;
}

  </style>
}
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
              <form id="search_license_plate" >
              <input type="text" class="form-control" style="float: left;margin-right: 10px;width:70px;" id="license_plate_category" name="license_plate_category" placeholder="@lang('parking.license_plate_category')" maxlength="3">
               <input type="text"  id="license_plate" name="license_plate" class="form-control" style="float: left;margin-right: 10px;width:100px;" placeholder="@lang('parking.license_plate')" maxlength="4">

               <button  class="btn btn-success btn-sm btn-search" > <i class="fa fa-search"></i>@lang('parking.search')</button>
             </form>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="table_license" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th class="col-xs-2"></th>
                  <th> <button type="button" id="btn_used" class="btn btn-info btn-flat btn-block" >@lang('parking.btn_used')</button> </th>
                </tr>
                </thead>
                <tbody >
				
				        @foreach ($lists as $key=>$list)
                <tr >
                  <td class="vm-ct"><input class="custom-checkbox" type="checkbox" data-license-plate="{{$list['license_plate_category'].$list['license_plate']." ".$list['province_name']}}" 
                    data-start-date="{{$list['start_date']}}" 
                    data-end-date="{{$list['end_date']}}"  
                    data-id="{{ $list['id'] }}"></td>
                  <td> <div  class="td-license-left-side">{{ $list['license_plate_category'] }}</div> 
                     <div class=""  style="font-size: 36px;">{{ $list['license_plate']." ".$list['province_name'] }}
                     </div>
                     <div class="td-left-side" style="font-size: 22px;"> @lang('parking.room') : </div> 
                     <div class="" style="font-size: 22px;">{{ $list['room_name'] }}</div> 
                   
                      <div class="td-left-side"> @lang('parking.in') : </div> 
                      <div> {{  date('d',strtotime($list['start_date']))." ".month_date(date('m',strtotime($list['start_date'])))." ".date('Y H:i',strtotime($list['start_date']))}}</div>
                      <div class="td-left-side"> @lang('parking.out') : </div> 
                      <div> @if(isset($list['end_date']))
                      {{  date('d',strtotime($list['end_date']))." ".month_date(date('m',strtotime($list['end_date'])))." ".date('Y H:i',strtotime($list['end_date']))}}
                      @else
                      @lang('parking.is_until_out')
                      @endif
                    </div>
                  </td>
                 
                
                </tr>
                @endforeach
                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
    </section>
    <!-- /.content -->

@endsection

@section('javascript')
<!-- DataTables -->

<script src="{{ url('plugins/iCheck/icheck.js') }} "></script>

<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script type="text/javascript">
$(function () {
	

 

})

function getDataLicense(){
  var dfd = $.Deferred();
  
  var data = {id:[],license_plate:[],date:[],debt:0} ;
  $("#table_license input[type='checkbox']").each(function(){
       console.log($(this).is(':checked'),$(this).data('id'));
     
      if($(this).is(':checked')){
          data.id.push($(this).data('id'));
          console.log(data.license_plate.length,$.inArray( $(this).data('license-plate') , data.license_plate ));
          if(data.license_plate.length>0 && $.inArray( $(this).data('license-plate') , data.license_plate ) == -1 ){
             // dfd.reject((($("#app_local").val()=='th') ? 'เลขทะเบียนไม่ตรงกัน' : 'Wrong License Plate' ));
             // return  dfd.promise();
          }
          data.license_plate.push($(this).data('license-plate'));
          var dataDate = { start_date : $(this).data('start-date') , end_date: $(this).data('end-date') };
          data.date.push(dataDate);

      }

  })
  if (data.id.length>0){
    dfd.resolve(data);
  }else{
    dfd.reject("");
  }
  return dfd.promise();
  
}

function calculateHour(data){
  var dfd = $.Deferred();
  console.log('calculateHour',data);
  var hasHour = 0 ;
  var remainHour = 0 ;
  // data.date
  var startDate = 0 ;
  for (var i = 0 ; i< data.date.length ; i++){
      
      //--- หาค่าวันเวลาน้อยสุด
      var unixStartDate = new Date(data.date[i].start_date);
      if (startDate==0){
        startDate = unixStartDate ;
      }else if(startDate>unixStartDate){
        startDate = unixStartDate ;
      }
      //--- หา ชั่วโมงที่ใช้ 
      var start = new Date(data.date[i].start_date),
      end   = new Date();
      if(end.getMinutes()>1){

      }


      if(data.date[i].end_date!=null){
        end   = new Date(data.date[i].end_date);
        var diff = end.getTime()-start.getTime() ;
        var diffInHour = diff/1000/60/60 ;
        hasHour += diffInHour ;
      }

          // var endMinute = $("#end_minute").val();
        // if(endMinute>0){
        //   endHour++;
        // }

        // var end_date = endMonth+'-'+endDay+" "+endHour+":00";  


     
  } 

  console.log(startDate);
 //-- หาค่าเวลาที่ใช้จริง 
 var end   = new Date();

      console.log('real',end.getTime(),startDate.getTime());

      var diff = end.getTime()-startDate.getTime();
      var diffInHour = diff/1000/60/60 ;
    var useRealHour = Math.ceil(diffInHour);

    console.log('มีชั่วโมงในระบบ'+hasHour);
    console.log('ชั่วโมงใช้จริง'+useRealHour);


    if(useRealHour>hasHour){
      data.debt = parseFloat(useRealHour)-parseFloat(hasHour) ;
    }


  if (hasHour>0){
    dfd.resolve(data);
  }else{
    dfd.reject("");
  }
  return dfd.promise();
  
}

function checkDebtHour2(data){
  var dfd = $.Deferred();
  console.log('checkDebtHour',data);
   if(data.debt>0){ 
      var debt = data.debt;
      swal({
              title: (($("#app_local").val()=='th') ? 'คูปองใช้สิทธิ์ไม่ถึงเวลาปัจจุบัน?' : 'Are you sure?' ) ,
              text: (($("#app_local").val()=='th') ? 'ต้องจ่ายเงินเพิ่ม จำนวน '+debt+' ชั่วโมง' : "You want to paid "+debt+" hour !" ) ,
              type: 'warning',
              showCancelButton: true,
              confirmButtonText: (($("#app_local").val()=='th') ? 'ยืนยันจ่ายเงินเพิ่ม' : 'Ok' ),
              cancelButtonText: (($("#app_local").val()=='th') ? 'ยกเลิก' : 'Cancel' ),
              confirmButtonClass: 'btn btn-danger',
              cancelButtonClass: 'btn btn-default',
              buttonsStyling: false,
              reverseButtons: true
        }).then((result) => {
        
                if (result.value) {
                    dfd.resolve(data);

                } else if (result.dismiss === 'cancel') {
                  console.log('cancel');
                   location.reload();

                    // dfd.reject("");
                }
              })

   }else{
      dfd.resolve(data);
   }


  return dfd.promise();
  
}

function checkDebtHour(send_data){
  var dfd = $.Deferred();
  var route = "/parking/guard/check-hour?api_token="+api_token ;
  ajaxPromise('POST',route,send_data).done(function(data){
       console.log(data);
     
        if(data.debt_hour>0){ 
          var debt = data.debt_hour;
          var maxDate = moment.unix(data.max_end_date).format("DD/MM/YYYY HH:mm");
          var nowDate = moment.unix(data.now_date).format("DD/MM/YYYY HH:mm");

          swal({
                  title:(($("#app_local").val()=='th') ? 'คูปองใช้สิทธิ์ได้ถึงเวลา?'+maxDate : 'Are you sure?' ) ,
                  text:(($("#app_local").val()=='th') ? 'โดยขณะนี้ '+nowDate+' ต้องจ่ายเงินเพิ่ม จำนวน '+debt+' ชั่วโมง' : "You want to paid "+debt+" hour !" ) ,
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: (($("#app_local").val()=='th') ? 'ยืนยันจ่ายเงินเพิ่ม' : 'Ok' ),
                  cancelButtonText: (($("#app_local").val()=='th') ? 'ยกเลิก' : 'Cancel' ),
                  confirmButtonClass: 'btn btn-danger',
                  cancelButtonClass: 'btn btn-default',
                  buttonsStyling: false,
                  reverseButtons: true
            }).then((result) => {
            
                    if (result.value) {
                        dfd.resolve(send_data);

                    } else if (result.dismiss === 'cancel') {
                      console.log('cancel');
                       location.reload();

                        // dfd.reject("");
                    }
                  })

       }else{
          dfd.resolve(send_data);
       }
   });
  return dfd.promise();
}


$("#btn_used").on("click",function(){

    getDataLicense()
    // .then(calculateHour)
    .then(checkDebtHour)
    .done(function(data){
       console.log(data);
        var route = "/parking/guard?api_token="+api_token ;
        ajaxPromise('POST',route,data).done(function(data){
             $("#table_license input[type='checkbox']").each(function(){
               console.log($(this).is(':checked'),$(this).data('id'));
             
              if($(this).is(':checked')){
                  $(this).closest('tr').remove();

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

    
   
   

})

function getDateThai(date){
  var month = [
    {'th':'มกราคม','en':'January' }
    , {'th':'กุมภาพันธ์','en':'February' }
    , {'th':'มีนาคม','en':'March' }
    , {'th':'เมษายน','en':'April' }
    , {'th':'พฤษภาคม','en':'May' }
    , {'th':'มิถุนายน','en':'June' }
    , {'th':'กรกฎาคม','en':'July' }
    , {'th':'สิงหาคม','en':'August' }
    , {'th':'กันยายน','en':'September' }
    , {'th':'ตุลาคม','en':'October' }
    , {'th':'พฤษจิกายน','en':'November' }
    , {'th':'ธันวาคม','en':'December' }
  ];


  var d = new Date(date) ;
  console.log(d);
  var monthTxt =  ($("#app_local").val()=='th') ? month[d.getMonth()].th : month[d.getMonth()].en ;

   return d.getDate()+' '+monthTxt+' '+d.getFullYear()+' '+(d.getHours()<10?'0':'') +d.getHours()+':'+(d.getMinutes()<10?'0':'') + d.getMinutes();
}


$(function() {
    $("#search_license_plate").validate({
      rules: {
        license_plate_category: {
          maxlength: 3
        },
        license_plate: {
          maxlength: 4,
          number: true
        }
      },
      messages: {
        license_plate_category: (($("#app_local").val()=='th') ? 'หมวดหมู่ไม่ถูกต้อง' : 'Wrong License Plate' ),
        license_plate: (($("#app_local").val()=='th') ? 'ทะเบียนไม่ถูกต้อง' : 'Wrong License Plate' ),
       
      },
      highlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
       
      }
      ,submitHandler: function (form) {
        
        var form_data = new FormData($("#search_license_plate")[0]);
             $.ajax({
                 type: 'POST' ,
                 url:  $("#apiUrl").val()+"/parking/guard/search?api_token="+api_token ,
                 data: form_data ,
                 processData: false,
                 contentType: false,
                 success: function (data) {
                   
                    if(data.result=="true"){
                      console.log(data);
                      var res = data.response.parking_guard_search;
                      var html = '';
                      for(var i=0 ;i<res.length;i++){
                        html += "<tr >"+
                                "<td class=\"vm-ct\">"+
                                "<input class=\"custom-checkbox\"  data-id=\""+res[i].id+"\" "+
                                "data-license-plate=\""+res[i].license_plate_category+res[i].license_plate+" "+res[i].province_name+"\" "+
                                 "data-start-date=\""+res[i].start_date+"\" "+
                                "data-end-date=\""+res[i].end_date+"\" type=\"checkbox\">"+
                                "</td>"+
                                "<td>"+
                                "<div class=\"td-license-left-side\">"+
                                res[i].license_plate_category+"</div>"+
                                "<div style=\"font-size: 36px;\">"+
                                res[i].license_plate+" "+res[i].province_name+"</div>"+
                                "<div class=\"td-left-side\" style=\"font-size: 22px;\"> "+
                                "@lang('parking.room') : </div> "+
                                "<div style=\"font-size: 22px;\">"+res[i].room_name+"</div>"+ 
                                "<div class=\"td-left-side\">"+(($("#app_local").val()=='th') ? ' เวลาเข้า' : 'In' )+" : </div>"+ 
                                "<div> "+getDateThai(res[i].start_date)+"</div>"+
                                "<div class=\"td-left-side\">"+(($("#app_local").val()=='th') ? ' เวลาออก' : 'Out' )+" : </div>";
                      if(res[i].end_date!=null){
                        html += "<div> "+getDateThai(res[i].end_date)+"</div>";
                      }else{
                         html += (($("#app_local").val()=='th') ? 'จนกว่าจะออก' : 'Until out' )
                      }

                               
                      html += "</td>"+
                                "</tr>";
                      }
                      
                      $("#table_license tbody").html(html);
                        
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
                      swal(
                        'Error...',
                        "@lang('main.something_when_wrong')",
                        'error'
                      )
            });
             return false; // required to block normal submit since you used ajax
         }

    });

  });

</script>
@endsection		
