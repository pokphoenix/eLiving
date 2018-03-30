@extends('main.layouts.main')
@section('style')
<!-- DataTables -->
<link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection
@section('content-wrapper')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
  
  <i class="fa fa-upload"></i>
  
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
    <div class="box-body">
      @if($message = Session::get('success'))
      <div class="alert alert-info alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
        </button>
        <strong>Success!</strong> {{ $message }}
      </div>
      @endif
      {!! Session::forget('success') !!}
      <br />
      <!--   <a href="{{ URL::to('api/downloadExcel/xls') }}"><button class="btn btn-success">Download Excel xls</button></a>
      <a href="{{ URL::to('api/downloadExcel/xlsx') }}"><button class="btn btn-success">Download Excel xlsx</button></a>
      <a href="{{ URL::to('api/downloadExcel/csv') }}"><button class="btn btn-success">Download CSV</button></a> -->
      <!-- <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ URL::to('api/importExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data"> -->
      {{ csrf_field() }}
      <!-- <form id='input_form'>
        <table>
          <tr>
            <td>
              <input type="checkbox" name="sent_noti" value="1" id ="sent_noti" class="form-check-input" checked> Sent Notification<br>
            </td>
            <td width="80%">
              <input type="file"  class="form-control-file" name="import_file" id = "import_file"  />
            </td>
            <td>
              <button class="btn btn-primary btn-import" type="button">Import File
              <i class="fa fa-spinner fa-spin fa-fw none" ></i>
              </button> <br>
            </td>
            
          </tr>
        </table>
      </form> -->
      <!-- </form> -->
    </div>
    <!-- /.box-body -->
  </div>
  <div class="box">
    <!-- /.box-header -->
    <div class="box-header">
      <h3 class="box-title"></h3>
      <button class="btn btn-success btn-sm btn-create" > <i class="fa fa-plus"></i> @lang('im_day.im_insert')</button>
    </div>
    <div class="box-body">
      <table id="show_data" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>@lang('im_day.no')</th>
            <th>@lang('im_day.start_date')</th>
            <th>@lang('im_day.end_date')</th>
            <th>@lang('im_day.all_day')</th>
            <th>@lang('im_day.day_name')</th>
            <th>@lang('im_day.priority')</th>
            <th>@lang('im_day.role')</th>
            <th></th>
          </tr>
          <tr class="thead-search">
            <th ></th>
            <th class="input-filter">@lang('im_day.start_date')</th>
            <th class="input-filter">@lang('im_day.end_date')</th>
            <th class="input-filter">@lang('im_day.all_day')</th>
            <th class="input-filter">@lang('im_day.day_name')</th>
            <th class="input-filter">@lang('im_day.priority')</th>
            <th class="input-filter">@lang('im_day.role')</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          
          @foreach ($lists as $key=>$list)
          <tr >
            <td>{{ $key+1 }}</td>
            <td>{{ $list['start_date']}}</td>
            <td>{{ $list['end_date']}} </td>
            <td> <input type="checkbox" name="vehicle" value="Bike" disabled=""
              @if($list['all_day']==1)
              checked= ""
              @endif >
            </td>
            <td>{{ $list['day_name']}} </td>
            <td>{{ $list['priority']}} </td>
            <td>{{ $list['role_name']}} </td>
            <td>
              <button class="btn btn-default btn-edit btn-xs" data-id="{{ $list['id'] }}" ><i class="fa fa-edit"></i></button>
              <button class="btn btn-danger btn-delete btn-xs" data-id="{{ $list['id'] }}"  ><i class="fa fa-trash-o"></i></button>
            </td>
            <!--   <td>{{ date('Y-m-d',strtotime($list['start_date'])) }}</td>
            <td>{{ date('Y-m-d',strtotime($list['end_date'])) }}</td> -->
          </tr>
          @endforeach
          
        </table>
      </div>
      <!-- /.box-body -->
    </div>
  </div>
</section>
<!-- /.content -->
<div class="modal fade" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang('im_day.im_edit')</h4>
      </div>
      <div class="modal-body">
        <form  id="day-form" role="form" method="POST" action="{{$action}}" enctype="multipart/form-data"  >
          <div class="form-group">
            <div >
              <label for="name">@lang('im_day.day_name')</label>
              <input type="text" class="form-control" id="day_name" name="day_name" placeholder="@lang('im_day.day_name')" >
            </div>
            <div >
              <label for="name">@lang('im_day.start_date')</label>
              <input type="text" class="form-control" id="start_date" name="start_date" placeholder="@lang('im_day.start_date')"  >
            </div>
            <div >
              <label for="name">@lang('im_day.end_date')</label>
              <input type="text" class="form-control" id="end_date" name="end_date" placeholder="@lang('im_day.end_date')"  >
            </div>
            <div >
              <label for="priority">@lang('im_day.priority')</label>
              <select class="select2 form-control" id="priority" name="priority" >
                <option value="1">Important</option>
                <option value="2">Normal</option>
                <option value="3">Info</option>
              </select>
            </div>
            <div >
              <label for="all_day">@lang('im_day.all_day')</label>
              <input type="checkbox" name="all_day" id="all_day">
            </div>
            <div >
              <label for="role_id">@lang('im_day.role')</label>
              <select class="select2 form-control" id="role_id" name="role_id" >
                @if (isset($roles))
                @foreach($roles as $r)
                <option value="{{ $r['id'] }}"  > {{ $r['display_name' ]}} </option>
                @endforeach
                @endif
                <!-- <option value=""></option>
                @if (isset($room))
                @foreach($room as $r)
                <option value="{{ $r['id'] }}"  > {{ $r['text' ]}} </option>
                @endforeach
                @endif -->
                <!--   <option value="1">aaa</option>
                <option value="2">bba1</option>
                <option value="3">ccc3</option>
                <option value="4">ccc444</option> -->
              </select>
            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
              <button type="button" class="btn btn-primary btn-save">@lang('im_day.save')
              <i class="fa fa-spinner fa-spin fa-fw none" ></i>
              </button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
    </div>
  </div>
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
     var table = $('#show_data').DataTable(
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
                var route = "/important_day_manage/"+buyId+"?api_token="+api_token ;
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

  $(".btn-create").on("click",function(){
    $("#day-form #day_id").remove('');
    $("#start_date").val($.datepicker.formatDate('yy-mm-dd 00:00:00', new Date()));
    $("#end_date").val($.datepicker.formatDate('yy-mm-dd 22:59:59', new Date()));
    
    $("#day-form #_method").remove('');
    $("#day-form").attr('action', "{{$action}}" );
    $("#modal-default").modal("toggle");
  })




  $(".btn-save").on("click",function(){
    $("#day-form").submit();
  })

  $(".btn-edit").on("click",function(){
     $("#modal-default input").val('');
      var dayId = $(this).data('id');
      var route = "/important_day_manage/"+dayId+"/edit?api_token="+api_token ;
      console.log(route);
      ajaxPromise('GET',route,null).done(function(data){
          console.log(data.important_days);
          var r = data.important_days[0] ;
          console.log(r);
          $("#start_date").val( r.start_date );
          $("#end_date").val( r.end_date );
          $("#all_day").attr('checked',r.all_day==1);
          $("#day_name").val( r.day_name );
          $("#priority").val( r.priority ).trigger('change');;
          $("#role_id").val( r.role_id );

          $("#day-form #day_id").remove('');
          $("#day-form #_method").remove('');

          $("#day-form").attr({'action': "/important_day_manage/"+dayId+"?api_token="+api_token });
           var html = '<input type="hidden" id="_method"  name="_method" value="PUT">'+
           '<input type="hidden" id="day_id"  name="id" value="'+r.id+'">';
          $("#day-form").append(html);
          $("#day-form").attr({'action': $("#apiUrl").val()+"/important_day_manage/"+r.id+"?api_token="+api_token });
          $("#modal-default").modal("toggle");
      });
  })

  $(function() {
      $("#day-form").validate({
        rules: {
          start_date: {
            required: true,
            date: true
          },
          end_date: {
            required: true,
            date: true
          }, 
          day_name: {
            required: true,
            maxlength:1000
          },
        },
        messages: {
          start_date: (($("#app_local").val()=='th') ? 'วันเริ่มต้นม่ถูกต้อง' : 'Wrong Room' ),
          end_date: (($("#app_local").val()=='th') ? 'วันสิ้นสุดไม่ถูกต้อง' : 'Wrong Package' ),
          day_name: (($("#app_local").val()=='th') ? 'ชื่อวันไม่ถูกต้อง' : 'Wrong Package' ),
          
        },
        highlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
        },
        unhighlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
        }
        ,submitHandler: function (form) {

          eleSpin = ".btn-save" ;

          $(eleSpin).find('.fa-spinner').show();
          var form_data = new FormData($("#day-form")[0]);
          status = $("#all_day").is(":checked") ? 1 : 0  ;
          form_data.append('all_day',status);
          $.ajax({
            type: $("#day-form").attr('method') ,
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
                  $(eleSpin).find('.fa-spinner').hide();
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
              $(eleSpin).find('.fa-spinner').hide();
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
