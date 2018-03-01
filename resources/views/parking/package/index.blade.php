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
                  <th>@lang('main.tool')</th>
                </tr>
                 <tr class="thead-search">
                  <th></th>
                  <th class="input-filter">@lang('parking.name')</th>
                  <th class="input-filter">@lang('parking.hour')</th>
                  <th class="input-filter">@lang('parking.price')</th>
                  <th class="input-filter">@lang('parking.times_limit')</th>
                  <th class="input-filter">@lang('parking.created_at')</th>
                  <th class="input-filter">@lang('main.tool')</th>
                </tr>
                </thead>
                <tbody>
				
				        @foreach ($lists as $key=>$list)
                <tr >
                  <td>{{ $key+1 }}</td>
                  <td>{{ $list['name']}}</td>
                  <td>{{ $list['hour']}}</td>
                  <td>{{ $list['price'] }}</td>
                  <td>{{ $list['times_limit'] }}</td>
                  <td>{{ created_date_format($list['created_at']) }}
                  </td>
                  <td> <button class="btn btn-default btn-edit btn-xs" data-id="{{ $list['id'] }}"><i class="fa fa-edit"></i></button> </td>
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
                 <!--  <div class="form-group">
                    <label for="name">@lang('parking.name')</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="@lang('parking.name')" value="{{ (isset($edit)) ? $address['name'] : old('name') }}" >
                  </div> -->
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
  $("#modal-default").modal("toggle");
})

$(".btn-save").on("click",function(){
  $("#parking-form").submit();
})
$(".btn-edit").on("click",function(){
   $("#modal-default input").val('');
    var packageId = $(this).data('id');
    var route = "/parking/package/"+packageId+"/edit?api_token="+api_token ;
    ajaxPromise('GET',route,null).done(function(data){
        $("#name").val(data.parking_package.name);
        $("#hour").val(data.parking_package.hour);
        $("#price").val(data.parking_package.price);
        $("#times_limit").val(data.parking_package.times_limit);
        $("#modal-default modal-title").text((($("#app_local").val()=='th') ? 'แก้ไขแพ็คเกจ' : 'Edit Package' ));
        $("#parking-form").attr({'action': $("#apiUrl").val()+"/parking/package/"+packageId+"?api_token="+api_token });
        var html = '<input type="hidden" id="_method" name="_method" value="PUT">';
        $("#parking-form").append(html);

        $("#modal-default").modal("toggle");
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
