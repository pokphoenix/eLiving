@extends('main.layouts.main')


@section('style')
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
   <style type="text/css">
    tr:hover {cursor: pointer;}
  </style>
@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-phone"></i>
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
              @if(Auth()->user()->hasRole('admin'))
              <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#exampleModal" 
               > <i class="fa fa-edit"></i> @lang('main.pre_welcome_edit')
                <i class="fa fa-spinner fa-spin fa-fw" style="display:none;"></i>
             </button>
             @endif
            
          
            </div>
            <!-- /.box-header -->
            <div class="box-body" id="phone-content">

                {!! $data !!}
            </div>
            <!-- /.box-body -->
          </div>
    </section>
    <!-- /.content -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">@lang('main.pre_welcome_new')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="save_phone">
            <textarea id="editor1" name="editor1" rows="10" cols="80">
                {{ $data }}
            </textarea>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('main.close')</button>
        <button type="button" class="btn btn-primary btn-save">@lang('main.btn_save')
           <i class="fa fa-spinner fa-spin fa-fw none"></i>
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<!-- DataTables -->
<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

<script src="{{ url('bower_components/ckeditor/ckeditor.js') }}"></script>


<script type="text/javascript">
$(function () {
	$('#example1').DataTable({
    "iDisplayLength": 100
  })

  CKEDITOR.replace('editor1');

})
function goEdit(idCard){
  window.location.href = "{{ url($route) }}/"+idCard+"/edit" ;
}

$(function() {

  $(document).on("click",".btn-save",function(event) {  
    $(this).find('.fa-spinner').show();
  
      
      var content = CKEDITOR.instances['editor1'].getData();
      console.log('text',content);
      var url = "{{ url('api/'.$domainId) }}/pre-welcome/1?api_token="+api_token ;
      $.ajax({
                 type: "POST",
                 url: url,
                 data:{'text':content,'_method':'PUT' },
                 success: function (data) {
                    $(".btn-save").find('.fa-spinner').hide();
                    if(data.result=="true"){
                      swal({
                          title:  "@lang('main.update_success')",
                          type: 'success',
                          showCancelButton: false,
                          confirmButtonText: "@lang('main.ok')"
                        }).then((result) => {
                           $("#phone-content").html(content);
                           $("#exampleModal").modal('toggle');
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

             })

  });


  $("#example1 tbody td:eq(1)").on("click",function(){
      var parent = $(this).closest('tr');
      var title = parent.find('td:eq(1)').text();
      var tel = parent.find('td:eq(2)').text();
     
       console.log(parent.find('td:eq(1)').find('input').length);
       console.log(parent.find('td:eq(2)').find('input').length);

       var insert = false;

      if(parent.hasClass('active')){
         return false;
      }

      if(parent.find('td:eq(1)').find('input').length <= 0 ){
         html1 = "<input class=\"form-control row-title\" value=\""+title+"\"  >" ;
         insert = true;
      }
      var html2 = tel ;
      if(parent.find('td:eq(1)').find('input').length <= 0 ){
        html2 = "<input class=\"form-control row-tel\" value=\""+tel+"\"  >" ;
        insert = true;
      }
      
      var id =  $.trim(parent.find('.delete-phone').data('id')) ;

      var html3 = "<button class=\"btn btn-info btn-sm btn-row-save\" data-id=\""+id+"\" >"+
                "<i class=\"fa fa-save\"></i> "+
                " <i class=\"fa fa-spinner fa-spin fa-fw none\" ></i>";
                "</button>";


      if(insert){
        parent.addClass('active');
        parent.find('td:eq(1)').html(html1);
        parent.find('td:eq(2)').html(html2);
        parent.find('td:eq(3)').append(html3);
      }

     

      console.log('title',title,tel);


  })


 
   


    $("#create-form").validate({
      rules: {
        title: {
          required: true,
          maxlength: 255
        },
        tel: {
          required: true,
          maxlength: 255
        }
      },
      messages: {
       
        title: {
          required: "@lang('phone.title_require')",
          maxlength: "@lang('phone.title_require_max')",
        },
        tel: {
          required: "@lang('phone.tel_require')",
          maxlength: "@lang('phone.tel_require_max')"
        },
      },
        highlight: function ( element, errorClass, validClass ) {
      
        $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
       
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
       
      }
      ,submitHandler: function (form) {
       
        $("#save").find('.fa-spinner').show();

      
        var form_data = new FormData($("#create-form")[0]);
             $.ajax({
                 type: "POST",
                 url: form.action ,
                 data: form_data ,
                 processData: false,
                 contentType: false,
                 success: function (data) {
                    $("#save").find('.fa-spinner').hide();
                    if(data.result=="true"){
                      swal({
                          title:  @if(isset($edit)) "@lang('main.update_success')" @else  "@lang('main.create_success')" @endif,
                          type: 'success',
                          showCancelButton: false,
                          confirmButtonText: "@lang('main.ok')"
                        }).then((result) => {
                          if (result.value) {
                            window.location.href = "{{ url($domainId.'/'.$route) }}";
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
              $("#save").find('.fa-spinner').hide();
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
