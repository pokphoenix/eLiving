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
             
               <a href="{{url('domain/create')}}" class="btn btn-success btn-sm btn-create" > <i class="fa fa-plus"></i> @lang('parcel.insert')</a>
    
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                  <th>@lang('domain.name')</th>
                  <th>@lang('domain.url_name')</th>
                  <th>@lang('domain.created_at')</th>
                  <th>@lang('main.tool')</th>
                </tr>
                <tr class="thead-search">
                  <th ></th>
                  <th class="input-filter">@lang('domain.name')</th>
                  <th class="input-filter">@lang('domain.url_name')</th>
                  <th class="input-filter">@lang('domain.created_at')</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
        
                @foreach ($lists as $key=>$list)
                <tr >
                  <td>{{ $key+1 }}</td>
                  <td>{{ $list['name'] }}</td>
                  <td>{{ $list['url_name']}}</td>
                  <td>{{ created_date_format($list['created_at']) }}</td>
                  <td> 
                    <button class="btn btn-default btn-edit btn-xs" 
                    data-id="{{ $list['id'] }}"
                    data-name="{{ $list['name'] }}"
                    data-url-name="{{ $list['url_name'] }}" ><i class="fa fa-edit"></i></button>
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
                  <input type="hidden" id="d_id" >
                  <div class="form-group">
                    <label for="room_id">@lang('domain.name')</label>
                    <input type="text" class="form-control" id="name"  placeholder="@lang('domain.name')" value="" >
                   
                  </div>
                  <div class="form-group">
                    <label for="room_id">@lang('domain.url_name')</label>
                    <input type="text" class="form-control" id="url_name"  placeholder="@lang('domain.url_name')" value="" >
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
                <button type="button" class="btn btn-primary btn-save">@lang('main.btn_save')
                   <i class="fa fa-spinner fa-spin fa-fw" style="display:none;" ></i>
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
  var domainId = $(this).data('id');
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
               $.ajax({
                   url: "{{url('/api')}}"+"/domain/"+domainId+"?api_token="+api_token ,
                   type: 'POST',
                   dataType: 'json',
                   data: {'_method':'DELETE'} ,
                 })
                 .done(function(res) {

                    if(res.result=="true"){
                 
                    }else{
                      // dfd.reject( res.errors );
                      var error = JSON.stringify(res.errors);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
                      
                    }

                     $(".fa-spinner").hide();
                     $("#modal-default input").val('');
                     $("#modal-default").modal("toggle");
                 })
                 .fail(function(res) {
                    var error = JSON.stringify(res.errors);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
                 })


              var route = "/parcel/officer/"+buyId+"?api_token="+api_token ;
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
$(".btn-edit").on("click",function(){
  $("#modal-default input").val('');
  var name = $(this).data('name');
  var urlName = $(this).data('url-name');
  var domainId = $(this).data('id');
  console.log($("#apiUrl").val());
  console.log(name,urlName);
  $("#name").val(name);
  $("#d_id").val(domainId);
  $("#url_name").val(urlName);
  $("#modal-default").modal('toggle');
})


$(".btn-save").on("click",function(){
    $(".fa-spinner").show();
   

    var domainId = $("#d_id").val();
    var data = { "name": $("#name").val()
                ,"url_name":$("#url_name").val()
                ,"_method":"PUT"
     }
     $.ajax({
       url: "{{url('/api')}}"+"/domain/"+domainId+"?api_token="+api_token ,
       type: 'POST',
       dataType: 'json',
       data: data ,
     })
     .done(function(res) {

        if(res.result=="true"){
     
        }else{
          // dfd.reject( res.errors );
          var error = JSON.stringify(res.errors);
           swal(
            'Error...',
            error,
            'error'
          )
          
        }

         $(".fa-spinner").hide();
         $("#modal-default input").val('');
         $("#modal-default").modal("toggle");
     })
     .fail(function(res) {
        var error = JSON.stringify(res.errors);
           swal(
            'Error...',
            error,
            'error'
          )
     })
     


    
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

        eleSpin = ".btn-save" ;
        if($("#save_and_continue").length > 0) {
          eleSpin = ".btn-save-continue" ;
        }

        $(eleSpin).find('.fa-spinner').show();
        var form_data = new FormData($("#parcel-form")[0]);
       

        var period = $("#send_date_year").val()+"-"+$("#send_date_month").val()+"-"+$("#send_date_day").val()+" "+$("#send_date_hour").val()+":"+$("#send_date_minute").val()
        console.log(period);
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
                    $(eleSpin).find('.fa-spinner').hide();
                    if(data.result=="true"){


                      swal({
                          title:title ,
                          type: 'success',
                          showCancelButton: false,
                          confirmButtonText: "@lang('main.ok')"
                        }).then((result) => {
                          if (result.value) {

                           
                            console.log($("#save_and_continue").length);


                            if( $("#type").val()==2 && $("#save_and_continue").length>0 ){
                              $("#supplies_code").val('');
                            }else{
                              location.reload();
                            }

                            
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
