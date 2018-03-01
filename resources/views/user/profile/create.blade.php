@extends('main.layouts.main')

@section('style')
<style type="text/css">
.img-profile-btn-edit,.img-profile-btn-select{
  position: absolute; right: 0;display: none;
}
.profile_img:hover {
  cursor: pointer;
}
.profile_img:hover .img-profile-btn-edit{
  display: block;
}

.profile-img-list{
  width: 18%;float: left;margin:4px;
  position:relative;
}
.profile-img-list:hover{
  cursor: pointer; border: 3px solid #00a65a;
}
.profile-img-list:hover .img-profile-btn-select{
   display: block;
}
.profile-img-list.active {
  border: 3px solid #3c8dbc;
}

.cropit-preview {
        background-color: #f8f8f8;
        background-size: cover;
        border: 1px solid #ccc;
        border-radius: 3px;
        margin-top: 7px;
        width: 250px;
        height: 250px;
      }

      .cropit-preview-image-container {
        cursor: move;
      }

      .image-size-label {
        margin-top: 10px;
      }
</style>
@endsection
@section('content-wrapper')

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-user-o"></i> @lang('main.profile')
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('domain') }}"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('main.profile')</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
               @include('layouts.error')
            </div>
        </div>
        @if (isset($data['alert_text']))
        <div class="row">
            <div class="col-sm-12">
              <div class="callout callout-info">
                <h4>@lang('user.message_from_admin')</h4>
                <p>{{ $data['alert_text'] }} </p>
              </div>
            </div>
        </div>
        @endif
      
        <form id="create-form" action="" method="" enctype="multipart/form-data" >
            {{ csrf_field() }} 
           
        <div class="row">
            <div class="col-sm-8">
                <div class="box box-primary">
                  <div class="box-header with-border">
                    
                    
                  </div>
                <!-- /.box-header -->
                <!-- form start -->

                  <div class="box-body">
                        
                            
            
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('user.id_card')</label>
                        <input type="text" class="form-control" id="id_card" name="id_card" placeholder="@lang('user.id_card')"  value="{{ isset($edit) ? $data['id_card'] : old('id_card') }}" readonly="" >
                      </div>

                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('user.first_name')</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="@lang('user.first_name')"  value="{{ isset($edit) ? $data['first_name'] : old('first_name') }}" @if (isset($show)) readonly="" @endif >
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('user.last_name')</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="@lang('user.last_name')"  value="{{ isset($edit) ? $data['last_name'] : old('last_name') }}" @if (isset($show)) readonly="" @endif >
                      </div>
                       <div class="form-group">
                        <label for="exampleInputPassword1">@lang('user.displayname')</label>
                        <input type="text" class="form-control" id="displayname" name="displayname" placeholder="@lang('user.displayname')"  value="{{ isset($edit) ? $data['displayname'] : old('displayname') }}" @if (isset($show)) readonly="" @endif >
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('main.email')</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="@lang('main.email')"  value="{{ isset($edit) ? $data['email'] : old('email') }}" @if (isset($show)) readonly="" @endif >
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('main.tel')</label>
                        <input type="text" class="form-control" id="tel" name="tel" placeholder=">@lang('main.tel')"  value="{{ isset($edit) ? $data['tel'] : old('tel') }}" @if (isset($show)) readonly="" @endif >
                      </div>
                      
                    
                      
                    </div>
                  </div>
                  <!-- /.box-body -->

                  <div class="box-footer">
                   
                  </div>
                
                </div>
            </div> 
            <div class="col-sm-4">
                <div class="box box-primary">
                  <div class="box-header with-border"> 
                  </div>
              

                  <div class="box-body">
                      <div class="row ">
                         <div class="col-sm-12">
                            <div class="row">
                              <div class="col-sm-12 text-center">
                                <div class="profile_img" style="position:relative;width:100px;margin:0 auto; " >
                                   <div class="img-profile-btn-edit">
                                    <button type="button" class="btn btn-box-tool" style="color:#fff;" >
                                      <i class="fa fa-edit"></i>
                                    </button>
                                  </div>
                                   <img id="profile_img" src=" {{ $data['profile_img'] }}" height="100" width="100" >
                                  
                                </div>
                               
                              </div>
                            </div>
                            <br>

                           
                            <a href="{{ url('profile/address') }}" type="button"   class="btn btn-default btn-block btn-social" > <i class="fa fa-address-book-o"></i>
                              @lang('main.address')  @if($addressCnt==0) <small class="label label-danger pull-right">@lang('main.not_complete')</small>  @endif
                            </a>
                            <a href="{{ url('profile/attach') }}" type="button"  class="btn btn-default btn-block btn-social" > <i class="fa fa-upload"></i>
                              @lang('user.attachment')  @if($attachmentCnt==0) <small class="label label-danger pull-right">@lang('main.not_complete')</small>  @endif
                            </a>
                            <a href="{{ url('profile/changepass') }}" type="button"   class="btn btn-default btn-block btn-social" > <i class="fa fa-lock"></i>
                               @lang('user.change_password')
                            </a>
                            <!-- <button type="button" id="btn_edit"  class="btn btn-default btn-block btn-social" >
                              <i class="fa fa-edit"></i>
                              Edit Profile
                            </button> -->
                            <a href="{{ url('profile/room') }}" type="button"  class="btn btn-default btn-block btn-social" > <i class="fa fa-key"></i>
                              @lang('main.room')  @if($roomCnt==0&& Auth()->user()->hasRole('user') ) <small class="label label-danger pull-right">@lang('main.not_complete')</small>  @endif
                            </a>

                            
                         </div>
                       
                      </div>
                      
                  </div> 
                     

                  <div class="box-footer">
                   
                  </div>
                
                </div>
            </div>   
        </div>
        

        <div class="row">
            <div class="col-sm-12">
                <!-- <a href="{{ url($route) }}" class="btn btn-danger"> <i class="fa fa-reply"></i>
                    ยกเลิก</a> -->
               

                <button type="submit" id="btn_save" style="display: none;" class="btn btn-primary" > <i class="fa fa-save"></i>
                    @lang('main.save')</button>
               <!--  <button type="button" id="btn_cancel" style="display: none;"  class="btn btn-danger" > <i class="fa fa-close"></i>
                    @lang('main.cancel')</button> -->

                
            </div>
        </div>
        </form>
    </section>

<div id="modal_profile_picture" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">@lang('user.profile_picture')</h4>
      </div>
      <div class="modal-body">
          
          <div class="row" style="padding-left: 5px;">
            @if(isset($data['profile_url']))
            <div class="profile-img-list @if(isset($data['profile_url'])&&$data['profile_img']==$data['profile_url']) active  @endif  "  >
                <div class="img-profile-btn-select" @if(isset($data['profile_img'])&&$data['profile_img']==$data['profile_url'])  style="display:block;" @endif>
                  <span class="badge bg-green"><i class="fa fa-check"></i></span>
                 
                </div>

                <img  src="{{ $data['profile_img'] }}" class="img-responsive"  >
            </div>
            @endif
            @for ($i = 1; $i < 20; $i++)
              <div class="profile-img-list @if((auth()->user()->avartar_id==$i&&!isset($data['profile_img'])) || $data['profile_img']==url('public/img/profile/'.$i.'.png')  ) active  @endif  "  >
                <input type="hidden"  class="avartar-id" value="{{$i}}" >
                <div class="img-profile-btn-select" @if((auth()->user()->avartar_id==$i&&!isset($data['profile_img'])) || $data['profile_img']==url('public/img/profile/'.$i.'.png')  ) style="display:block;"  @endif>
                  <span class="badge bg-green"><i class="fa fa-check"></i></span>
                 
                </div>

                <img src="{{ url('public/img/profile/'.$i.'.png') }}" class="img-responsive"  >
              </div>
            @endfor

          </div>
          <div class="row" style="margin:10px 0;color: #ccc;border-top: 1px solid #CCC;">
                                        
                                    </div>
           <div class="row">
             <div class="col-sm-12">
                 <div class="form-group">
                 
                </div>
                <div class="form-group preview-img" style="display: none;" >
                  <p>
                    @lang('user.preview') <small >@lang('user.click_picture_to_upload')</small>
                  </p>
                 
                  <div class="profile-img-list" >
                      <div class="img-profile-btn-select">
                        <span class="badge bg-green"><i class="fa fa-check"></i></span>
                       
                      </div>
                    <input type="hidden" id="upload-manual" >
                    <img id="img_show" src="" class="img-responsive"  >
                </div>

                </div>
             </div>
           </div>
         
          
         

         <!--  <div>
            <input type="text" class="form-control" id="search_member">
          </div> -->
    
      </div>
      <div class="modal-footer">
       
        <form id="upload_img_profile">
                 

                  <label for="file_upload"  class="btn btn-primary btn-click-upload-image">
                    <i class="fa fa-cloud-upload"></i> @lang('user.upload_from_computer')
                     <i class="fa fa-spinner fa-spin fa-fw" style="display:none;" ></i>
                  </label>
                  <input id="file_upload" name='doc_file[]' type="file" style="display:none;" >
                  <button type="button" class="btn btn-default" data-dismiss="modal">@lang('main.close')</button>
                  </form>
        
      </div>
    </div>

  </div>
</div>

@endsection

@section('javascript')

<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<!-- <script type="text/javascript" src=" {{ url('plugins/cropit/jquery.cropit.js') }} "></script> -->
<script type="text/javascript" src="{{ url('js/utility/main.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/utility/validate.js') }}"></script> 
<script type="text/javascript">
  var  api_token = "{{ auth()->user()->api_token }}" ;

function readURL(input) {

  if (input.files && input.files[0]) {
    var reader = new FileReader();
    var file = input.files[0];
    var file_name = file.name;
    var file_ext = file_name.split('.').pop().toLowerCase();
    var file_size = file.size ;
    reader.onload = function(e) {

      $('#img_show').attr('src', e.target.result).height($('.profile-img-list').innerWidth());
      $('.preview-img').show();
      var data = {
              name : file_name ,
              extension : file_ext ,
              size : file_size ,
              data : e.target.result ,
            }
      $('#upload-manual').val(JSON.stringify(data));
    }

    reader.readAsDataURL(input.files[0]);
  }
}


$("#file_upload").change(function() {
  readURL(this);
});

  $(function() {
      $("#btn_save").show();
      $("#btn_cancel").show();
      $("#create-form input").attr({'readonly':false});
      $("#id_card,#displayname").attr({'readonly':true});

    $("#btn_edit").on("click",function(){
       
        // $("#btn_edit").hide();
        $("#btn_save").show();
        $("#btn_cancel").show();
    });

    $("#btn_cancel").on("click",function(){
        location.reload();
    }); 

   

    $(".profile-img-list").on("click",function(){

        $("#upload_img_profile").find('.fa-spinner').show();
        var vartarId = $(this).find('.avartar-id').val() ;
        if($(this).hasClass('active')){
          return false;
        }

        if($(this).find('.avartar-id').length <= 0 ){
          vartarId = 0 ;
        }

       if($(this).find('#upload-manual').length > 0 ){
          uploadProfileImg();
          return false;
        }


           $.ajax({
                 type: "POST",
                 url: "{{ url('api/profile/avatar?api_token=') }}"+api_token,
                 data: {avartar_id:vartarId  },
                 success: function (data) {
                    if(data.result=="true"){
                      swal({
                        type: 'success',
                        title:  "@lang('main.update_success')",
                        showConfirmButton: false,
                        timer: 1500
                      })
                        setTimeout(function(){ location.reload() }, 1600);
                        $("#upload_img_profile").find('.fa-spinner').hide();

                    }else{
                      var error = JSON.stringify(data.errors);
                        $("#upload_img_profile").find('.fa-spinner').hide();

                      swal(
                        'Error...',
                        error,
                        'error'
                      )
                    }
                 }
             });
    }); 

    $(".profile_img").on("click",function(){
        $("#file_upload").val('');
        $(".preview-img").hide();
        $("#modal_profile_picture").modal('show');
    }); 
    // $("#save_avatar").on("click",function(){
       
    // }); 

  
   
     $("#first_name,#last_name").on("input",function(){
        var display = $("#first_name").val()+" "+$("#last_name").val();
        $("#displayname").val(display);
     })


    $("#create-form").validate({
      rules: {
        first_name: "required",
        last_name: "required",
        tel:"required",
        email: {
          required: true,
          email: true
        },
        displayname: {
          required: true,
          minlength: 2,
          maxlength: 255,
          // remote: {
          //           url: "{{ url('api/search/displayname') }}",
          //           type: "post",
          //           data: {
          //               displayname: function() {
          //                   return $("#displayname").val();
          //               }
          //           }
          //       }
        },
        // unit: {
        //  required: true
        // },
        
        // agree: "required"
      },
      messages: {
        first_name: "Please enter your firstname",
        last_name: "Please enter your lastname",
        tel: "Please enter your telephone",
        email: "Please enter a valid email address",
        displayname:{
                  required: 'Please enter your  Display name',
                  remote: 'Display name already exits'
        },
        
      },
      submitHandler: function (form) {

        console.log(form.action);

             $.ajax({
                 type: "PUT",
                 url: "{{ $apiUpdate }}",
                 data: $(form).serialize(),
                 success: function (data) {
                    
                    if(data.result=="true"){
                      swal({
                        type: 'success',
                        title:  @if(isset($edit)) "@lang('main.update_success')" @else "@lang('main.create_success')" @endif,
                        showConfirmButton: false,
                        timer: 1500
                      })

                      setTimeout(function(){ window.location.href = "{{ url('domain') }}"; }, 1600);
                      
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
             return false; // required to block normal submit since you used ajax
         }
      

    });

    

  });

function uploadProfileImg(){

    var form_data = new FormData();
    form_data.append('file_upload',JSON.stringify( $('#upload-manual').val() ));
    form_data.append('avartar_id',0);
      $.ajax({
        type: "POST",
        url: "{{ url('api/profile/uploadimg?api_token=') }}"+api_token,
        data: form_data ,
        processData: false,
        contentType: false,
       success: function (data) {
         
          if(data.result=="true"){
            swal({
                title: "@lang('main.upload_success')" ,
                type: 'success',
                showCancelButton: false,
                confirmButtonText: "@lang('main.ok')"
            }).then((result) => {
              if (result.value) {
                location.reload();
              }
            })
              $("#upload_img_profile").find('.fa-spinner').hide();
          }else{
            var error = JSON.stringify(data.errors);
              $("#upload_img_profile").find('.fa-spinner').hide();

            swal(
              'Error...',
              error,
              'error'
            )
          }
       }
   });
   return false;   
}

</script>
@endsection







