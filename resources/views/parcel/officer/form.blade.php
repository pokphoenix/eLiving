@extends('main.layouts.main')


@section('style')
<link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

  <link rel="stylesheet" href="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ url('public/css/input.css') }}">

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

      <div class="box">
            <div class="box-header">
              <h3 class="box-title"></h3>
               <button class="btn btn-success btn-sm btn-create" > <i class="fa fa-plus"></i> @lang('parcel.insert')</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                  <th>@lang('parcel.room')</th>
                  <th>@lang('parcel.type')</th>
                  <th>@lang('parcel.created_at')</th>
                  <th>@lang('parcel.send_date')</th>
                  <th>@lang('parcel.received_at')</th>
                  <th>@lang('main.tool')</th>
                </tr>
                 <tr class="thead-search" >
                  <th></th>
                  <th class="input-filter">@lang('parcel.room')</th>
                  <th class="input-filter">@lang('parcel.type')</th>
                  <th class="input-filter">@lang('parcel.created_at')</th>
                  <th class="input-filter">@lang('parcel.send_date')</th>
                  <th class="input-filter">@lang('parcel.received_at')</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
        
                @foreach ($lists as $key=>$list)
                <tr >
                  <td>{{ $list['parcel_code'] }}</td>
                  <td>{{ $list['room_name']}}</td>
                  <td>{{ $list['parcel_type_name']}} 
                    @if($list['type']==2)
                      ( {{ $list['supplies_type_name']}} )
                    @endif
                  </td>
                  <td>{{ created_date_format($list['created_at']) }}
                  <td>{{ created_date_format($list['send_date']) }}
                  <td>@if(isset($list['received_at']))
                    {{ created_date_format($list['received_at']) }}
                    @else
                    รอมารับ
                    @endif
                  </td>
                  <td> 
                   
                    <button class="btn btn-default btn-edit btn-xs" data-id="{{ $list['id'] }}" data-room-id="{{ $list['room_id'] }}"><i class="fa fa-edit"></i></button>
                    <button class="btn btn-danger btn-delete btn-xs" data-id="{{ $list['id'] }}"  ><i class="fa fa-trash-o"></i></button>
                   
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
                <h4 class="modal-title">@lang('parcel.insert')</h4>
              </div>
              <div class="modal-body">
                 <form  id="parcel-form" role="form" method="POST" action="{{$action}}" enctype="multipart/form-data"  >
                  <div class="form-group">
                    <label for="room_id">@lang('user.room')</label>
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
                    <label for="name">@lang('parcel.send_date')</label>
                    <input type="text" class="form-control" id="send_date" name="send_date" placeholder="@lang('parcel.send_date')" value="02/19/2018 14:01" >

                  </div>
                  
                  <div class="form-group">
                    <label for="package_id">@lang('parcel.type')</label>
                    <select class="select2 form-control" id="type" name="type" >
                        <option value=""></option>
                        @if (isset($parcelTypes))
                          @foreach($parcelTypes as $p)
                          <option value="{{ $p['id'] }}"  > {{ $p['name'] }} </option>
                          @endforeach
                        @endif
                    </select>
                  </div>

                  <div id="row_supplies" class="parcel-row none">
                    <div class="form-group">
                      <label for="name">@lang('parcel.supplies_send_name')</label>
                      <input type="text" class="form-control" id="supplies_send_name" name="supplies_send_name" placeholder="@lang('parcel.supplies_send_name')"  >
                    </div>
                    <div class="form-group">
                      <label for="name">@lang('parcel.supplies_type')</label>
                      <select class="select2 form-control" id="supplies_type" name="supplies_type" >
                          <option value=""></option>
                        @if (isset($suppliesTypes))
                          @foreach($suppliesTypes as $st)
                          <option value="{{ $st['id'] }}"  > {{ $st['name'] }} </option>
                          @endforeach
                        @endif
                      </select>
                      
                    </div> 
                    <div class="form-group">
                      <label for="name">@lang('parcel.supplies_code')</label>
                      <input type="text" class="form-control" id="supplies_code" name="supplies_code" placeholder="@lang('parcel.supplies_code')"  >
                    </div>
                    
                  </div>
                  <div id="row_gift" class="parcel-row none">
                    <div class="form-group">
                      <label for="name">@lang('parcel.gift_receive_name')</label>
                      <input type="text" class="form-control" id="gift_receive_name" name="gift_receive_name" placeholder="@lang('parcel.gift_receive_name')"  >
                    </div>
                    <div class="form-group">
                      <label for="name">@lang('parcel.gift_send_name')</label>
                      <input type="text" class="form-control" id="gift_send_name" name="gift_send_name" placeholder="@lang('parcel.gift_send_name')"  >
                    </div>
                    <div class="form-group">
                      <label for="name">@lang('parcel.gift_description')</label>
                      <input type="text" class="form-control" id="gift_description" name="gift_description" placeholder="@lang('parcel.gift_description')"  >
                    </div>
                    
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

<script src="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src=" {{ url('js/utility/data_table.js') }}"></script>
<script type="text/javascript">
$(function () {

 
  var table = $('#example1').DataTable(
      {
        "bSortCellsTop": true
        ,"order": [[ 0, 'desc' ]]
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


  $("#room_id,#type").select2();
   $(".select2-container").css('width',"100%");


   $('#send_date').daterangepicker({
    "singleDatePicker": true,
    "timePicker": true,
    "timePicker24Hour":true,
    showDropdowns: true,
    "startDate": new Date(),
    locale: {
        format: 'MM/DD/YYYY H:mm'
    },
    "opens": "left"
   
    }, function(start, end, label) {
      var startDate = start.format('YYYY-MM-DD H:mm');
      var data = {start_task_at:startDate} ;
        
    });
  
   setTimeout(function() {
       $('#send_date').data('daterangepicker').setStartDate(new Date());
    }, 500);
})
function goEdit(idCard){
  window.location.href = "{{ url($route) }}/"+idCard+"/edit" ;
}

$("#type").on("change",function(){
  console.log($(this).val());
  var type= $(this).val() ;
  $(".parcel-row").hide();
  if(type==3){
    $("#row_gift").show();
  }else if(type==2||type==5){
    $("#row_supplies").show();
  }else{

  }
})

$(".btn-create").on("click",function(){
  $('#room_id').val('').trigger('change');
  $('#type').val('').trigger('change');
  $(".parcel-row").hide();
  // $('#send_date').data('daterangepicker').setStartDate(new Date());

   setTimeout(function() {
       $('#send_date').data('daterangepicker').setStartDate(new Date());
    }, 500);

  $("#modal-default input").val('');
  $("#parcel-form #_method").remove('');
  $("#parcel-form").attr('action', "{{$action}}" );
  $("#modal-default").modal("toggle");
})
$(".btn-save").on("click",function(){
  $("#parcel-form").submit();
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
              var route = "/parcel/officer/"+buyId+"?api_token="+api_token ;
              ajaxPromise('DELETE',route).done(function(data){
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
$(".btn-edit").on("click",function(){
   $("#modal-default input").val('');
    var buyId = $(this).data('id');
    var roomId = $(this).data('room-id');
    var packageId = $(this).data('package-id');
    var userName = $(this).data('buy-name');
    var period = $(this).data('period');

    var periodDate = new Date(period);
 
    $("#month").val(periodDate.getMonth()+1).trigger('change');
    $("#year").val(periodDate.getFullYear()).trigger('change');
    $('#user_buy_name').val(userName);
    $('#room_id').val(roomId).trigger('change');
    $('#package_id').val(packageId).trigger('change');
    var route = "/parcel/buy/"+packageId+"/edit?api_token="+api_token ;
    $("#modal-default modal-title").text((($("#app_local").val()=='th') ? 'แก้ไขการขาย' : 'Edit parcel Buy' ));
    $("#parcel-form").attr({'action': $("#apiUrl").val()+"/parcel/buy/"+buyId+"?api_token="+api_token });
    $("#modal-default").modal("toggle");
    var html = '<input type="hidden" id="_method"  name="_method" value="PUT">';
    $("#parcel-form").append(html);

})





$(function() {
    $("#parcel-form").validate({
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
      
      },
      messages: {
        room_id: (($("#app_local").val()=='th') ? 'ห้องไม่ถูกต้อง' : 'Wrong Room' ),
        package_id: (($("#app_local").val()=='th') ? 'แพ็คเกจไม่ถูกต้อง' : 'Wrong Package' ),
       
        
      },
      highlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
      }
      ,submitHandler: function (form) {
        $(".btn-save").find('.fa-spinner').show();
        var form_data = new FormData($("#parcel-form")[0]);
        period = moment($("#send_date").val()).format("YYYY-MM-DD HH:mm");
        form_data.append('send_date',period);

             $.ajax({
                 type: $("#parcel-form").attr('method') ,
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
