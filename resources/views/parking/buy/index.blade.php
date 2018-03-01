@extends('main.layouts.main')


@section('style')
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
               <button class="btn btn-success btn-sm btn-create" > <i class="fa fa-plus"></i> @lang('parking.sell_coupon')</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                  <th>@lang('parking.room')</th>
                  <th>@lang('parking.user_buy_name')</th>
                  <th>@lang('parking.buyer_tel')</th>
                  <th>@lang('parking.name')</th>
                  <th>@lang('parking.price')</th>
                  <th>@lang('parking.sell_by')</th>
                  <th>@lang('parking.created_at')</th>
                  <th>@lang('parking.period_at')</th>
                  <th>@lang('parking.expired_at')</th>
                  <th>@lang('main.tool')</th>
                </tr>
                <tr class="thead-search">
                  <th></th>
                  <th class="input-filter">@lang('parking.room')</th>
                  <th class="input-filter">@lang('parking.user_buy_name')</th>
                  <th class="input-filter">@lang('parking.buyer_tel')</th>
                  <th class="input-filter">@lang('parking.name')</th>
                  <th class="input-filter">@lang('parking.price')</th>
                  <th class="input-filter">@lang('parking.sell_by')</th>
                  <th class="input-filter">@lang('parking.created_at')</th>
                  <th class="input-filter">@lang('parking.period_at')</th>
                  <th class="input-filter">@lang('parking.expired_at')</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
				
				        @foreach ($lists as $key=>$list)
                <tr  @if(isset($list['deleted_at']))  class="text-delete" @endif >
                  <td>{{ $key+1 }}</td>
                  <td>{{ $list['room_name']}}</td>
                  <td>{{ $list['user_buy_name']  }} 
                  @if(isset($list['id_card_buyer']))
                  {{ " (".$list['id_card_buyer'].")" }}
                  @endif
                  </td>
                  <td>{{ $list['buyer_tel']}}</td>
                  <td>{{ $list['package_name']}}</td>
                  <td>{{ $list['package_price'] }}</td>
                  <td>{{ $list['first_name']." ".$list['last_name'] }}</td>
                  <td>{{ created_date_format($list['created_at']) }}
                  <td>{{ created_date_format($list['period_at']) }}
                  <td>{{ created_date_format($list['expired_at']) }}
                  </td>
                  <td> 
                    @if(!$list['coupon_used'] && !isset($list['deleted_at']) )
                   <button class="btn btn-default btn-edit btn-xs" data-id="{{ $list['id'] }}"  ><i class="fa fa-eye"></i></button>
                    <button class="btn btn-danger btn-delete btn-xs" data-id="{{ $list['id'] }}"  ><i class="fa fa-trash-o"></i></button>
                    @elseif(isset($list['deleted_at']))
                    @lang('parking.deleted') {{ $list['delete_first_name']." ". $list['delete_last_name'] }}
                    @else
                    @lang('parking.used')
                    @endif
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
                <h4 class="modal-title">@lang('parking.sell_package')</h4>
              </div>
              <div class="modal-body">
                 <form  id="parking-form" role="form" method="POST" action="{{$action}}" enctype="multipart/form-data"  >
                  <div class="form-group">
                    <label for="room_id">@lang('parking.room')</label>
                    <select class="select2 form-control" id="room_id" name="room_id" >
                        <option value=""></option>
                        @if (isset($room))
                          @foreach($room as $r)
                          <option value="{{ $r['id'] }}"  > {{ $r['text' ]}} </option>
                          @endforeach
                        @endif
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="package_id">@lang('parking.package')</label>
                    <select class="select2 form-control" id="package_id" name="package_id" >
                       
                        @if (isset($package))
                          @foreach($package as $key=> $p)
                          <option value="{{ $p['id'] }}" @if($key==0) selected="" @endif  > {{ $p['name']."[".$p['hour'] }} @lang('parking.hour') ] @lang('parking.price') {{ $p['price'] }} </option>
                          @endforeach
                        @endif
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="name">@lang('parking.buy_period')</label>
                     <select  id="month" name="month" >
                        <option value=""></option>
  
                        @for($d=1;$d<=12;$d++)

                        <option value="{{$d}}" @if( intval(date('m'))==$d) selected="selected" @endif >{{ month_date($d) }}</option>
                        @endfor
                       
                    </select>
                    <select  id="year" name="year" >
                        <option value=""></option>
                        <option value="{{ date('Y',strtotime('-1 year')) }}">{{ date('Y',strtotime('-1 year')) }}</option>
                        <option value="{{ date('Y') }}" selected >{{ date('Y') }}</option>
                         <option value="{{ date('Y',strtotime('+1 year')) }}">{{ date('Y',strtotime('+1 year')) }}</option>
                    </select>
                  </div> 
                 
                  <div class="form-group">
                    <label for="name">@lang('parking.user_buy_name')</label>
                    <input type="text" class="form-control" id="user_buy_name" name="user_buy_name" placeholder="@lang('parking.user_buy_name')"  >
                  </div>
                  <div class="form-group">
                    <label for="name">@lang('parking.id_card_buyer')</label>
                    <input type="text" class="form-control" id="id_card_buyer" name="id_card_buyer" maxlength="13" minlength="13" placeholder="@lang('parking.id_card_buyer')"  >
                  </div> 
                  <div class="form-group">
                    <label for="name">@lang('parking.buyer_tel')</label>
                    <input type="text" class="form-control" id="buyer_tel" name="buyer_tel" maxlength="20" placeholder="@lang('parking.buyer_tel')"  >
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
                <button type="button" class="btn btn-primary btn-save">@lang('main.btn_save')
                   <i class="fa fa-spinner fa-spin fa-fw none" ></i>
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
   $("#room_id").select2();
  
  $("#package_id").select2();
  setTimeout(function() {
    $('#package_id').val(1).trigger('change');
  }, 500);
   
   $(".select2-container").css('width',"100%");
})
function goEdit(idCard){
  window.location.href = "{{ url($route) }}/"+idCard+"/edit" ;
}

$(".btn-create").on("click",function(){
    $('#room_id').val('').trigger('change');
    $('#package_id').val(1).trigger('change');

    d = new Date();


    $('#month').val((d.getMonth()+1)).trigger('change');
    $('#year').val(d.getFullYear()).trigger('change');
  $("#modal-default input").val('');
  $("#parking-form #_method").remove('');
  $("#parking-form").attr('action', "{{$action}}" );
   $('.btn-save').show();
    $("#modal-default input,#modal-default select").attr('disabled',false);
  $("#modal-default").modal("toggle");
})
$(".btn-save").on("click",function(){
  $("#parking-form").submit();
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
              var route = "/parking/buy/"+buyId+"?api_token="+api_token ;
              ajaxPromise('DELETE',route).done(function(data){
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

$(".btn-edit").on("click",function(){
   $("#modal-default input").val('');
    var parkingId = $(this).data('id');
    // var roomId = $(this).data('room-id');
    // var packageId = $(this).data('package-id');
    // var userName = $(this).data('buy-name');
    // var period = $(this).data('period');

    // var periodDate = new Date(period);
 
    // $("#month").val(periodDate.getMonth()+1).trigger('change');
    // $("#year").val(periodDate.getFullYear()).trigger('change');
    // $('#user_buy_name').val(userName);
    // $('#room_id').val(roomId).trigger('change');
    // $('#package_id').val(packageId).trigger('change');
    var route = "/parking/buy/"+parkingId+"/edit?api_token="+api_token ;

    ajaxPromise('GET',route,null).done(function(data){
        console.log(data);
        var r = data.parking_buys ;
        $('#room_id').val(r.room_id).trigger('change');
        $('#package_id').val(r.package_id).trigger('change');

        var d = new Date(r.period_at);
        $("#year").val( d.getFullYear() ).trigger('change');
        $("#month").val( d.getMonth()+1 ).trigger('change');
        $("#user_buy_name").val( r.user_buy_name );
        $("#id_card_buyer").val( r.id_card_buyer );
        $("#buyer_tel").val( r.buyer_tel );

        $('.btn-save').hide();

        $("#modal-default modal-title").text((($("#app_local").val()=='th') ? 'แก้ไขรายการ' : 'Edit Parcel' ));
       
         var html = '<input type="hidden" id="_method"  name="_method" value="PUT">';
        $("#parking-form").append(html);
       
        $("#modal-default input,#modal-default select").attr('disabled',true);
        $("#modal-default").modal("toggle");
    });


    // $("#modal-default modal-title").text((($("#app_local").val()=='th') ? 'แก้ไขการขาย' : 'Edit parcel Buy' ));
    // $("#parcel-form").attr({'action': $("#apiUrl").val()+"/parcel/officer/"+buyId+"?api_token="+api_token });
    // $("#modal-default").modal("toggle");
    // var html = '<input type="hidden" id="_method"  name="_method" value="PUT">';
    // $("#parcel-form").append(html);

})

// $(".btn-edit").on("click",function(){
//    $("#modal-default input").val('');
//     var buyId = $(this).data('id');
//     var roomId = $(this).data('room-id');
//     var packageId = $(this).data('package-id');
//     var userName = $(this).data('buy-name');
//     var period = $(this).data('period');

//     var periodDate = new Date(period);
 
//     $("#month").val(periodDate.getMonth()+1).trigger('change');
//     $("#year").val(periodDate.getFullYear()).trigger('change');
//     $('#user_buy_name').val(userName);
//     $('#room_id').val(roomId).trigger('change');
//     $('#package_id').val(packageId).trigger('change');
//     var route = "/parking/buy/"+packageId+"/edit?api_token="+api_token ;
//     $("#modal-default modal-title").text((($("#app_local").val()=='th') ? 'แก้ไขการขาย' : 'Edit Parking Buy' ));
//     $("#parking-form").attr({'action': $("#apiUrl").val()+"/parking/buy/"+buyId+"?api_token="+api_token });
//     $("#modal-default").modal("toggle");
//     var html = '<input type="hidden" id="_method"  name="_method" value="PUT">';
//     $("#parking-form").append(html);

// })





$(function() {
    $("#parking-form").validate({
      rules: {
        room_id: {
          required: true,
          number: 255
        },
        package_id: {
          required: true,
          number: true
        }, 
        user_buy_name: {
          required: true,
          maxlength:1000
        },
         id_card_buyer: {
          minlength:13,
          maxlength:13
        },
      
      },
      messages: {
        room_id: (($("#app_local").val()=='th') ? 'ห้องไม่ถูกต้อง' : 'Wrong Room' ),
        package_id: (($("#app_local").val()=='th') ? 'แพ็คเกจไม่ถูกต้อง' : 'Wrong Package' ),
        user_buy_name: (($("#app_local").val()=='th') ? 'ชื่อผู้ซื้อไม่ถูกต้อง' : 'Wrong Buyer' ),
        id_card_buyer: (($("#app_local").val()=='th') ? 'เลขบัตรประชาชนไม่ถูกต้อง' : 'Wrong Id Card' ),
       
        
      },
      highlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
       
      }
      ,submitHandler: function (form) {

        if($("#_method").length >0) return false;


        $(".btn-save").find('.fa-spinner').show();
        var form_data = new FormData($("#parking-form")[0]);

        var month = $("#month").val();
        var year = $("#year").val();
        //console.log(month);
        var period = new Date(year,month-1,1);
        //console.log(period);
        period = moment(period).format("YYYY-MM-DD");
        form_data.append('period_at',period);

             $.ajax({
                 type: $("#parking-form").attr('method') ,
                 url: form.action ,
                 data: form_data ,
                 processData: false,
                 contentType: false,
                 success: function (data) {
                    if($("#_method").length >0 ){
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
             return false; // required to block normal submit since you used ajax
         }

    });

  });

</script>
@endsection		
