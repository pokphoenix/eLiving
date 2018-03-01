@extends('main.layouts.main')

@section('style')
  <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
@endsection
@section('content-wrapper')

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-upload"></i> @lang('user.attachment') 
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('domain') }}"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li><a href="{{ url('profile/show') }}">@lang('main.profile')</a></li>
        <li class="active">@lang('user.attachment') </li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
               @include('layouts.error')
            </div>
        </div>

        <form id="create-form" action="{{ $apiUpdate }}" method="" enctype="multipart/form-data" >
            @if(isset($edit))
               {{ method_field('PUT') }}
            @endif

            {{ csrf_field() }} 
           
        <div class="row">
            <div class="col-sm-12">
                 @include('admin.widgets.attachment')
            </div>
            <div class="col-sm-12">
                  @include('admin.widgets.attachment-list')
            </div>
        </div>
        
        

        <div class="row">
            <div class="col-sm-12">
                <button type="submit" id="btn_save" class="btn btn-primary" > <i class="fa fa-save"></i>
                    @lang('main.save')
                    <i class="fa fa-spinner fa-spin fa-fw" style="display:none;"></i>
                </button>
                <button type="button" id="btn_cancel" class="btn btn-danger" > <i class="fa fa-close"></i>
                    @lang('main.back')</button>
            </div>
        </div>
        </form>
    </section>

@endsection

@section('javascript')
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ url('js/utility/main.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/utility/validate.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/utility/autocomplete.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/user/attachment.js') }}"></script> 
<script type="text/javascript">
var idCard = "{{ auth()->user()->id_card }}";
var api_token = "{{ auth()->user()->api_token }}" ;
  $(function() {
  
    $("#btn_cancel").on("click",function(){
        window.location.href="{{ url('profile/show')}}";
    });  
    $(".btn-del-attach").on("click",function(){
      swal({
      title: (($("#app_local").val()=='th') ? 'คุณแน่ใจไหม?' : 'Are you sure?' ) ,
      text: (($("#app_local").val()=='th') ? 'คุณต้องการลบไฟล์นี้ใช่หรือไม่' : "You want to delete this file!" ) ,
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: "@lang('main.btn_delete')",
      cancelButtonText: "@lang('main.btn_cancel')",
      confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-default',
      buttonsStyling: false,
      reverseButtons: true
    }).then((result) => {
      if (result.value) {
           $.ajax({
                  type: "DELETE",
                  url: "{{ url('api/profile/attach/') }}/"+$(this).data('id')+"?api_token="+api_token ,
                  data: {"_token": "{{ csrf_token() }}"} ,
                 success: function (data) {
                    // console.log(data,typeof data.response);
                    if(data.result=="true"){
                      swal({
                          title: "@lang('main.delete_success')" ,
                          type: 'success',
                          showCancelButton: false,
                          confirmButtonText: "@lang('main.ok')"
                      }).then((result) => {
                        if (result.value) {
                          window.location.href = "{{ url('profile/attach') }}";
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
             });

      }
    })



          
    }); 

  
    $("#create-form").validate({
       rules: {
        address:"required",
        province_id:"required",
        amphur_id:"required",
        district_id:"required",
        zip_code:"required"
      },
      messages: {
        address:"Please enter your  address",
        district_name:"Please enter your  district",
        province_name:"Please enter your  province",
        amphur_name:"Please enter your  amphur",
        zip_code:"Please enter your  zipcode"
      },
      submitHandler: function (form) {
          $("#btn_save").find('.fa-spinner').show();
          var data = { 'file_type':[] , 'img':[] };
          $("#append_upload tr").each(function(){
            $(this).find('.upload-file-type').val() ;
            data.file_type.push($(this).find('.upload-file-type').val());
            var imgRow = JSON.parse($(this).find('.file_upload').val()) ;
            data.img.push(imgRow);
          })
          var form_data = new FormData($("#create-form")[0]);
          form_data.append('file-type',JSON.stringify(data.file_type));
          form_data.append('file_upload',JSON.stringify(data.img));
          console.log(data.img);

          $.ajax({
                  type: "POST",
                  url: form.action ,
                  data: form_data ,
                  processData: false,
                  contentType: false,
                 success: function (data) {
                    $("#btn_save").find('.fa-spinner').hide();
                    if(data.result=="true"){
                      swal({
                          title: @if(isset($edit)) "@lang('main.update_success')"  @else "@lang('main.create_success')" @endif ,
                          type: 'success',
                          showCancelButton: false,
                          confirmButtonText: "@lang('main.ok')"
                      }).then((result) => {
                        if (result.value) {
                          window.location.href = "{{ url('profile/attach') }}";
                        }
                      })
                    }else{
                      var error = JSON.stringify(data.errors);
                      console.log(error);
                      swal(
                        'Error...',
                        error,
                        'error'
                      )
                    }
                 }
             });
             return false; // required to block normal submit since you used ajax
         }
      

    });

    

  });





</script>
@endsection







