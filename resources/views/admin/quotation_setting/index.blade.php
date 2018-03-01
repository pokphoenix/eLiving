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
         @lang('sidebar.quotation_setting')
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('sidebar.quotation_setting')</li>
      </ol>
    </section>



    <!-- Main content -->
    <section class="content">
  
       @include('layouts.error')

    	<div class="box">
           <div class="box-header">
          
              <h3 class="box-title"></h3>
            
              <button class="btn btn-success btn-sm btn-edit" 
               > <i class="fa fa-edit"></i> @lang('setting.edit')
             </button>
              <button class="btn btn-default btn-sm btn-preview" 
               > <i class="fa fa-edit"></i> @lang('setting.preview')
             </button>
            
          
            </div>
            <!-- /.box-header -->
            <div class="box-body" >
              <form id="create-form" action="{{$action}}">
                 <div class="col-sm-6">
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_header_th')</label>
                        <input type="text" class="form-control" id="header_th" name="header_th" placeholder="@lang('setting.quotation_header_th')"  value="{{$data['header_th']}}" >
                      </div>

                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_subject_th')</label>
                        <input type="text" class="form-control" id="subject_th" name="subject_th" placeholder="@lang('setting.quotation_subject_th')"  value="{{$data['subject_th']}}" >
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_inform_th')</label>
                        <input type="text" class="form-control" id="inform_th" name="inform_th" placeholder="@lang('setting.quotation_inform_th')"  value="{{$data['inform_th']}}">
                      </div>
                       <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_remark_th')</label>
                        <input type="text" class="form-control" id="remark_th" name="remark_th" placeholder="@lang('setting.quotation_remark_th')"  value="{{$data['remark_th']}}" >
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_sign_1_th')</label>
                        <input type="text" class="form-control" id="sign_1_th" name="sign_1_th" placeholder="@lang('setting.quotation_sign_1_th')"  value="{{$data['sign_1_th']}}">
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_sign_2_th')</label>
                        <input type="text" class="form-control" id="sign_2_th" name="sign_2_th" placeholder=">@lang('setting.quotation_sign_2_th')"  value="{{$data['sign_2_th']}}">
                      </div>
                     
                      
                    
                      
                  </div>
                  <div class="col-sm-6">
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_header_en')</label>
                        <input type="text" class="form-control" id="header_en" name="header_en" placeholder="@lang('setting.quotation_header_en')"  value="{{$data['header_en']}}" >
                      </div>

                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_subject_en')</label>
                        <input type="text" class="form-control" id="subject_en" name="subject_en" placeholder="@lang('setting.quotation_subject_en')"  value="{{$data['subject_en']}}" >
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_inform_en')</label>
                        <input type="text" class="form-control" id="inform_en" name="inform_en" placeholder="@lang('setting.quotation_inform_en')"  value="{{$data['inform_en']}}">
                      </div>
                       <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_remark_en')</label>
                        <input type="text" class="form-control" id="remark_en" name="remark_en" placeholder="@lang('setting.quotation_remark_en')"  value="{{$data['remark_en']}}" >
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_sign_1_en')</label>
                        <input type="text" class="form-control" id="sign_1_en" name="sign_1_en" placeholder="@lang('setting.quotation_sign_1_en')"  value="{{$data['sign_1_en']}}">
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_sign_2_en')</label>
                        <input type="text" class="form-control" id="sign_2_en" name="sign_2_en" placeholder=">@lang('setting.quotation_sign_2_en')"  value="{{$data['sign_2_en']}}">
                      </div>
                     
                      
                    
                      
                  </div>
                  <div class="col-sm-12">
                     <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_logo_left')</label>
                        <input type="file" class="form-control" id="logo_left" name="logo_left" placeholder=">@lang('setting.quotation_logo_left')"  value="{{$data['logo_left']}}"  >
                        <img src="{{ isset($data['logo_left']) ? $data['logo_left'] : '' }}" id="show_logo_left" width="100" height="100" @if(!isset($data['logo_left'])) class="none" @endif >
                        <input type="hidden" id="hidden_logo_left" >
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_logo_right')</label>
                        <input type="file" class="form-control" id="logo_right" name="logo_right" placeholder=">@lang('setting.quotation_logo_right')"  value="{{$data['logo_right']}}" >
                        <img src="{{ isset($data['logo_right']) ? $data['logo_right'] : '' }}" id="show_logo_right" width="100" height="100" 
                        @if(!isset($data['logo_right'])) class="none" @endif>
                        <input type="hidden" id="hidden_logo_right" >
                      </div>
                  </div>

                  <div class="col-sm-12" style="height: 50px;">
                     <button type="submit" id="save" class="btn btn-primary none">@lang('main.btn_save')
                       <i class="fa fa-spinner fa-spin fa-fw none" ></i>
           
                     </button>


                      <button type="button" id="cancel" class="btn btn-danger none">@lang('main.btn_cancel')
                      </button>
                    
                  </div>
              </form>
            </div>
            <!-- /.box-body -->
          </div>
    </section>
    <!-- /.content -->

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

  $("#create-form input").attr({'readonly':true});

  $(".btn-edit").on("click",function(){
     $("#create-form input").attr({'readonly':false});
     $("#save,#cancel").show();
  }) 
  $(".btn-cancel").on("click",function(){
     $("#create-form input").attr({'readonly':true});
     $("#save,#cancel").hide();
  })
})
function goEdit(idCard){
  window.location.href = "{{ url($route) }}/"+idCard+"/edit" ;
}


function readURL(input) {
  console.log($(input).attr('name'));
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    var file = input.files[0];
    var file_name = file.name;
    var file_ext = file_name.split('.').pop().toLowerCase();
    var file_size = file.size ;
    reader.onload = function(e) {

      var renderEle =  ($(input).attr('name')=="logo_left") ? "#show_logo_left" : "#show_logo_right" ;

      var hideEle = ($(input).attr('name')=="logo_left") ? "#hidden_logo_left" : "#hidden_logo_right" ;

      $(renderEle).attr('src', e.target.result);
      $(renderEle).show();
      var data = {
              name : file_name ,
              extension : file_ext ,
              size : file_size ,
              data : e.target.result ,
            }
      $(hideEle).val(JSON.stringify(data));
    }

    reader.readAsDataURL(input.files[0]);
  }
}


$("#logo_left,#logo_right").change(function() {
  readURL(this);
});

$(".btn-preview").on("click",function() {
    var route = $("#baseUrl").val()+"/purchase/quotation/print-preview";
    var wihe = 'width='+screen.availWidth+',height='+screen.availHeight; 
    window.open(route, 'ตัวอย่าง' , 'fullscreen=yes,'+wihe);
});


$(function() {

  $(document).on("click",".btn-save",function(event) {  
    $(this).find('.fa-spinner').show();
  
      
      var content = CKEDITOR.instances['editor1'].getData();
      console.log('text',content);
      var url = "{{ url('api/'.$domainId) }}/pre-welcome/1?api_token="+api_token ;
      $.ajax({
                 type: "PUT",
                 url: url,
                 data:{desc:content},
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

  $("#create-form").validate({
      rules: {
        header_th: {
          required: true,
          maxlength: 255
        },
        subject_th: {
          required: true,
          maxlength: 255
        },
        inform_th: {
          required: true,
          maxlength: 255
        },
        remark_th: {
          required: true,
          maxlength: 255
        },
        sign_1_th: {
          required: true,
          maxlength: 255
        },
        sign_2_th: {
          required: true,
          maxlength: 255
        },
        header_en: {
          required: true,
          maxlength: 255
        },
        subject_en: {
          required: true,
          maxlength: 255
        },
        inform_en: {
          required: true,
          maxlength: 255
        },
        remark_en: {
          required: true,
          maxlength: 255
        },
        sign_1_en: {
          required: true,
          maxlength: 255
        },
        sign_2_en: {
          required: true,
          maxlength: 255
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
        form_data.append('_method','PUT');
        var imgLeft = [];
        var imgRight = [];
        if($('#hidden_logo_left').val()!=null&&$('#hidden_logo_left').val()!=''){
           imgLeft.push($('#hidden_logo_left').val());
          form_data.append('hidden_logo_left',JSON.stringify( imgLeft));
        }
        if($('#hidden_logo_right').val()!=null&&$('#hidden_logo_right').val()!=''){
          imgRight.push($('#hidden_logo_right').val());
          form_data.append('hidden_logo_right',JSON.stringify( imgRight));
        }


        
       
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
