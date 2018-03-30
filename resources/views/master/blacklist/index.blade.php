@extends('main.layouts.main')


@section('style')

  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

<link rel="stylesheet" href="{{ url('public/css/input.css') }}">


@endsection

@section('content-wrapper')
    <input type="hidden" id="route" value="{{ $route }}">

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

        <div class="row">
          <div class="col-xs-12">
            <div class="box">
            <div class="box-header">
              <h3 class="box-title"></h3>
               <button class="btn btn-success btn-sm btn-create" > <i class="fa fa-plus"></i> @lang('main.add')</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                
                  <th>@lang('user.no')</th>
                  <th>@lang('main.word')</th>
                
                 
              
                  <th >@lang('main.tool')
                  
                  </th>
                </tr>
                 <tr class="thead-search" >
                  <th></th>
                 
                  <th class="input-filter">@lang('main.word')</th>
                
                
                  <th ></th>
                 
                 
                </tr>
                </thead>

                <tbody>
        
                @foreach ($lists as $key=>$list)
                <tr >
                  
                  <td>{{ $key+1 }}</td>
                  <td>{{ $list['text']}}</td>
                 
                 
               
                  <td>
                   
                    <button class="btn btn-xs btn-default btn-edit" data-id="{{$list['id']}}"><i class="fa fa-edit"></i></button>
                     <button class="btn btn-xs btn-danger btn-delete" data-id="{{$list['id']}}"><i class="fa fa-trash-o"></i></button>
                  </td>
                  

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
 <div class="modal fade" id="modal-default">
          <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('main.add'){{ $title }}</h4>
              </div>
              <div class="modal-body">
                 <form  id="data-form" role="form" method="POST" action="{{$action}}" enctype="multipart/form-data"  >
                      <div class="form-group">
                          <label for="name">@lang('main.word')</label>
                          <input type="text" class="form-control" id="text" name="text" placeholder="@lang('main.word')"  >
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
<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
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


<script>

$(".btn-edit").on("click",function(){

   var text = (($("#app_local").val()=='th') ? 'แก้ไข' : 'Edit' )+" "+"{{$title}}" ;
   $(".modal-title").text(text);


   $("#modal-default input").val('');
    var dataId = $(this).data('id');
    var route =    "/"+$("#route").val()+"/"+dataId+"/edit?api_token="+api_token ;
    ajaxPromise('GET',route,null).done(function(data){
        var r = data.data ;
        $('#text').val(r.text);
         $("#data-form #_method").remove('');

        $("#modal-default modal-title").text((($("#app_local").val()=='th') ? 'แก้ไขรายการ' : 'Edit Contact' ));
        $("#data-form").attr({'action': $("#apiUrl").val()+"/"+$("#route").val()+"/"+dataId+"?api_token="+api_token });
         var html = '<input type="hidden" id="_method"  name="_method" value="PUT">';
        $("#data-form").append(html);
       

        $("#modal-default").modal("toggle");
    });
})

$(".btn-create").on("click",function(){
   var text = (($("#app_local").val()=='th') ? 'เพิ่ม' : 'Add' )+" "+"{{$title}}" ;
   $(".modal-title").text(text);
  $("#modal-default input,#modal-default textarea").val('');
  $("#status").attr('checked',true);
  $("#data-form #_method").remove('');
  $("#data-form").attr('action', "{{$action}}" );
  $("#modal-default").modal("toggle");
})




$(".btn-save").on("click",function(){
  $("#data-form").submit();
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
              var route = "/"+$("#route").val()+"/"+buyId+"?api_token="+api_token ;
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


$(function() {
    $("#data-form").validate({
      rules: {
        name_en: {
          required: true,
          maxlength: 255
        },
        name_th: {
          required: true,
          maxlength: 255
        }, 
        color: {
          required: true,
          maxlength: 255
        },
      
      
      },
      messages: {
        name_en: (($("#app_local").val()=='th') ? 'ชื่อภาษาไทยไม่ถูกต้อง' : 'Wrong Name (en)' ),
        name_th: (($("#app_local").val()=='th') ? 'ชื่อภาษาอังกฤษไม่ถูกต้อง' : 'Wrong Name (th)' ),
        color: (($("#app_local").val()=='th') ? 'ระบุสีให้ถูกต้อง' : 'Wrong Color' ),
      },
      highlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
      }
      ,submitHandler: function (form) {
        $(".btn-save").find('.fa-spinner').show();
       
        var form_data = new FormData($("#data-form")[0]);

        status = $("#status").is(":checked") ? 1 : 0  ;
        form_data.append('status',status);

        $.ajax({
                 type: form.method ,
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
