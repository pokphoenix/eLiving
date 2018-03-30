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
        <i class="fa fa-circle-o"></i>
         @lang('sidebar.domain_setting')
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('sidebar.domain_setting')</li>
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
              
            
          
            </div>
            <!-- /.box-header -->
            <div class="box-body" >
              <form id="create-form" >
                 
             
                  <div class="col-sm-12">
                     <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.name_domain')</label>
                        <input type="text" class="form-control" id="name_domain" name="name_domain" placeholder=">@lang('setting.name_domain')"  value="{{ $data['name_domain'] }}"  >
                       
                      </div>
                     <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.logo_domain') <small>(100x50)</small></label>
                        <input type="file" class="form-control" id="logo_domain" name="logo_domain" placeholder=">@lang('setting.logo_domain')"  value=""  >
                        <img src="{{ isset($data['logo_domain']) ? $data['logo_domain'] : '' }}" id="show_logo_domain" width="100" height="50" @if(!isset($data['logo_domain'])) class="none" @endif >
                        <input type="hidden" id="hidden_logo_domain" >
                      </div>
                  </div>

                  <div class="col-sm-12" style="height: 50px;">
                     <button type="submit" id="save" class="btn btn-primary none">@lang('main.btn_save')
                       <i class="fa fa-spinner fa-spin fa-fw" style="display:none;"></i>
           
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
  $("#cancel").on("click",function(){
     $("#create-form input").attr({'readonly':true});
     $("#save,#cancel").hide();
  })
})


function readURL(input) {
  console.log($(input).attr('name'));
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    var file = input.files[0];
    var file_name = file.name;
    var file_ext = file_name.split('.').pop().toLowerCase();
    var file_size = file.size ;
    reader.onload = function(e) {

      var renderEle =  "#show_logo_domain" ; 

      var hideEle ="#hidden_logo_domain" ;

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


$("#logo_domain").change(function() {
  readURL(this);
});


$(function() {


  $("#create-form").validate({
      rules: {
        
       
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
        if($('#hidden_logo_domain').val()!=null&&$('#hidden_logo_domain').val()!=''){
          imgLeft.push($('#hidden_logo_domain').val());
          form_data.append('hidden_logo_domain',JSON.stringify(imgLeft));
        }
          
        var route = "/setting/domain?api_token="+api_token;

        ajaxFromData('POST',route,form_data).done(function(){
            swal({
                title: "@lang('main.update_success')",
                type: 'success',
                showCancelButton: false,
                confirmButtonText: "@lang('main.ok')"
              }).then((result) => {
                if (result.value) {
                  location.reload();
                }
              })
        })

        
       
             
             return false; // required to block normal submit since you used ajax
         }

    });

  });

</script>
@endsection		
