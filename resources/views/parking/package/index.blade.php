@extends('main.layouts.main')


@section('style')
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
   <style type="text/css">
    tr:hover {cursor: pointer;}
  </style>
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
               <button class="btn btn-success btn-sm btn-create" > <i class="fa fa-plus"></i>@lang('parking.new_package')</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                  <th>@lang('parking.name')</th>
                  <th>@lang('parking.hour')</th>
                  <th>@lang('parking.price')</th>
                  <th>@lang('parking.times_limit')</th>
                  <th>@lang('parking.created_at')</th>
                  <th>@lang('parking.public_start')</th>
                  <th>@lang('parking.public_end')</th>
                  <th>@lang('main.tool')</th>
                </tr>
                 <tr class="thead-search">
                  <th></th>
                  <th class="input-filter">@lang('parking.name')</th>
                  <th class="input-filter">@lang('parking.hour')</th>
                  <th class="input-filter">@lang('parking.price')</th>
                  <th class="input-filter">@lang('parking.times_limit')</th>
                  <th class="input-filter">@lang('parking.created_at')</th>
                  <th class="input-filter">@lang('parking.public_start')</th>
                  <th class="input-filter">@lang('parking.public_end')</th>
                  <th class="input-filter">@lang('main.tool')</th>
                </tr>
                </thead>
                <tbody>
				
				        @foreach ($lists as $key=>$list)
                <tr @if(isset($list['deleted_at']))  class="text-delete" @endif>
                  <td>{{ $key+1 }}</td>
                  <td>{{ $list['name']}}</td>
                  <td>{{ $list['hour']}}</td>
                  <td>{{ $list['price'] }}</td>
                  <td>{{ $list['times_limit'] }}</td>
                  <td>{{ created_date_format($list['created_at']) }}</td>
                  <td>{{ created_date_format($list['public_start']) }}</td>
                  <td>  @if(isset($list['public_end']))  
                        {{ created_date_format($list['public_end']) }} 
                        @else
                        @lang("parking.never_end")
                        @endif </td>
                  <td> 
                       @if(!isset($list['deleted_at'])) 
                       <button class="btn btn-default btn-edit btn-xs" data-id="{{ $list['id'] }}"><i class="fa fa-edit"></i></button> 
                        <button class="btn btn-danger btn-delete btn-xs" data-id="{{ $list['id'] }}"><i class="fa fa-trash"></i></button>
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
                <h4 class="modal-title">@lang('parking.new_package')</h4>
              </div>
              <div class="modal-body">
                 <form  id="parking-form" role="form" method="POST" action="{{$action}}" enctype="multipart/form-data"  >
                  <div class="form-group">
                    <label for="name">@lang('parking.name')</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="@lang('parking.name')" value="{{ (isset($edit)) ? $address['name'] : old('name') }}" >
                  </div>
                  <div class="form-group">
                    <label for="name">@lang('parking.hour')</label>
                    <input type="text" class="form-control" id="hour" name="hour" placeholder="@lang('parking.hour')" value="{{ (isset($edit)) ? $address['hour'] : old('hour') }}" >
                  </div>
                  <div class="form-group">
                    <label for="price">@lang('parking.price')</label>
                    <input type="text" class="form-control" id="price" name="price" placeholder="@lang('parking.price')" value="{{ (isset($edit)) ? $address['price'] : old('price') }}" >
                  </div>
                  <div class="form-group">
                    <label for="price">@lang('parking.times_limit')</label>
                    <input type="text" class="form-control" id="times_limit" name="times_limit" placeholder="@lang('parking.times_limit')" >
                  </div>
                  <div class="form-group ">
                    <table >
                      <tr>
                        <td > <label for="price">@lang('parcel.start_search')</label></td>
                        <td style="padding-left: 5px;"></td>
                      </tr>
                      <tr>
                        <td >
                          @lang('parcel.day')
                          <input type="text" style="width:50px;" id="start_date_day"  placeholder="@lang('parcel.send_date_day')" value="{{ date('d') }}" >
                          <input type="text" style="width:50px;" id="start_date_month"  placeholder="@lang('parcel.send_date_month')" value="{{ date('m') }}" >
                          <input type="text" style="width:50px;" id="start_date_year"  placeholder="@lang('parcel.send_date_year')" value="{{ date('Y') }}" >
                        </td>
                        <td style="padding-left: 5px;"> 
                          @lang('parcel.time') : 
                          <input type="text" style="width:50px;" id="start_date_hour"  placeholder="@lang('parcel.send_date_hour')" value="{{ date('H', strtotime('-30 minutes')  ) }}" >
                           <input type="text" style="width:50px;" id="start_date_minute"  placeholder="@lang('parcel.send_date_minute')" value="{{ date('i',strtotime('-30 minutes') ) }}" >
                        </td>
                      </tr>
                    </table>
                  </div>
                  <div class="form-group ">
                    <table >
                      <tr>
                        <td > <label for="price">@lang('parcel.end_search')</label></td>
                        <td style="padding-left: 5px;"></td>
                      </tr>
                      <tr class="row-end-date">
                        <td >
                          @lang('parcel.day')
                          <input type="text" style="width:50px;" id="end_date_day"  placeholder="@lang('parcel.send_date_day')" value="{{ date('d') }}" >
                          <input type="text" style="width:50px;" id="end_date_month"  placeholder="@lang('parcel.send_date_month')" value="{{ date('m') }}" >
                          <input type="text" style="width:50px;" id="end_date_year"  placeholder="@lang('parcel.send_date_year')" value="{{ date('Y') }}" >
                        </td>
                        <td style="padding-left: 5px;"> 
                          @lang('parcel.time') : 
                          <input type="text" style="width:50px;" id="end_date_hour"  placeholder="@lang('parcel.send_date_hour')" value="{{ date('H', strtotime('-30 minutes')  ) }}" >
                           <input type="text" style="width:50px;" id="end_date_minute"  placeholder="@lang('parcel.send_date_minute')" value="{{ date('i',strtotime('-30 minutes') ) }}" >
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2"> 
                        <input type="checkbox" id="never_end" >@lang('parking.never_end')
                        </td>
                      </tr>
                    </table>
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
<!-- DataTables -->
<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
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
})
function goEdit(idCard){
  window.location.href = "{{ url($route) }}/"+idCard+"/edit" ;
}

$(".btn-create").on("click",function(){
  $("#parking-form #_method").remove('');
  $("#parking-form").attr('action', "{{$action}}" );
  $("#modal-default input").val('');

  var d = new Date();

  $("#start_date_day,#end_date_day").val(d.getDate());
  $("#start_date_month,#end_date_month").val(d.getMonth()+1);
  $("#start_date_year,#end_date_year").val(d.getFullYear());
  $("#start_date_hour,#end_date_hour").val(d.getHours());
  $("#start_date_minute,#end_date_minute").val(d.getMinutes());

  $("#never_end").prop('checked', true);
  $(".row-end-date").hide();

  $("#modal-default").modal("toggle");
})

$("#never_end").on("change",function(){
   if($(this).is(':checked')){
      $(".row-end-date").hide();
   }else{
      $(".row-end-date").show();
      var d = new Date();
      $("#end_date_day").val(d.getDate());
      $("#end_date_month").val(d.getMonth()+1);
      $("#end_date_year").val(d.getFullYear());
      $("#end_date_hour").val(d.getHours());
      $("#end_date_minute").val(d.getMinutes());
   }
})


$(".btn-save").on("click",function(){
  $("#parking-form").submit();
})
$(".btn-edit").on("click",function(){
   $("#modal-default input").val('');
    var packageId = $(this).data('id');
    var route = "/parking/package/"+packageId+"/edit?api_token="+api_token ;
    ajaxPromise('GET',route,null).done(function(data){
      var res = data.parking_package ;
        $("#name").val(res.name);
        $("#hour").val(res.hour);
        $("#price").val(res.price);
        $("#times_limit").val(res.times_limit);

        if(res.public_start!=null){
          var dStart = new Date(res.public_start);
          $("#start_date_day").val(dStart.getDate());
          $("#start_date_month").val(dStart.getMonth()+1);
          $("#start_date_year").val(dStart.getFullYear());
          $("#start_date_hour").val(dStart.getHours());
          $("#start_date_minute").val(dStart.getMinutes());
        }
        if(res.public_end!=null){
          var dEnd = new Date(res.public_end);
          $("#end_date_day").val(dEnd.getDate());
          $("#end_date_month").val(dEnd.getMonth()+1);
          $("#end_date_year").val(dEnd.getFullYear());
          $("#end_date_hour").val(dEnd.getHours());
          $("#end_date_minute").val(dEnd.getMinutes());
          $("#never_end").prop("checked",false);
          $(".row-end-date").show();
        }else{
          $("#never_end").prop("checked",true);
          $(".row-end-date").hide();
        }
        $("#modal-default modal-title").text((($("#app_local").val()=='th') ? 'แก้ไขแพ็คเกจ' : 'Edit Package' ));
        $("#parking-form").attr({'action': $("#apiUrl").val()+"/parking/package/"+packageId+"?api_token="+api_token });
        var html = '<input type="hidden" id="_method" name="_method" value="PUT">';
        $("#parking-form").append(html);

        $("#modal-default").modal("toggle");
    });

})
$(".btn-delete").on("click",function(){
    var ele =$(this);
    var parent = $(this).closest('tr');
    var packageId = $(this).data('id');
    var route = "/parking/package/"+packageId+"?api_token="+api_token ;
    ajaxPromise('POST',route,{'_method':'DELETE'}).done(function(data){
        parent.addClass('text-delete');
        parent.find('td:last-child').html('');
    });

})


$(function() {
    $("#parking-form").validate({
      rules: {
        name: {
          required: true,
          maxlength: 255
        },
        hour: {
          required: true,
          number: true
        },
        price: {
          required: true,
          number: true
        },
        times_limit:{
          required: true,
          number: true
        }
      },
      messages: {
        name: (($("#app_local").val()=='th') ? 'ชื่อไม่ถูกต้อง' : 'Wrong Name' ),
        hour: (($("#app_local").val()=='th') ? 'ชั่วโมงไม่ถูกต้อง' : 'Wrong Hour' ),
        price:(($("#app_local").val()=='th') ? 'ราคาไม่ถูกต้อง' : 'Wrong Price' ),
        times_limit:(($("#app_local").val()=='th') ? 'จำนวนสิทธิ์ต่อเดือนไม่ถูกต้อง' : 'Wrong Limit' ),
        
      },
      highlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
       
      }
      ,submitHandler: function (form) {
        $(".btn-save").find('.fa-spinner').show();
        var form_data = new FormData($("#parking-form")[0]);

        var public_start = $("#start_date_year").val()+"-"+$("#start_date_month").val()+"-"+$("#start_date_day").val()+" "+$("#start_date_hour").val()+":"+$("#start_date_minute").val() ;

        form_data.append('public_start',public_start);

        var public_end = null ;
        if(!$("#never_end").is(':checked')){
          public_end = $("#end_date_year").val()+"-"+$("#end_date_month").val()+"-"+$("#end_date_day").val()+" "+$("#end_date_hour").val()+":"+$("#end_date_minute").val() ;
        }
        form_data.append('public_end',public_end);






             $.ajax({
                 type: $("#parking-form").attr('method') ,
                 url: form.action ,
                 data: form_data ,
                 processData: false,
                 contentType: false,
                 success: function (data) {
                    if($("#parking-form").attr('method')=="PUT"){
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
