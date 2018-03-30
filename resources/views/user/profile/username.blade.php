@extends('main.layouts.main')

@section('style')

@endsection
@section('content-wrapper')

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-lock"></i> @lang('user.change_password')
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

        <form id="create-form" action="" method="" enctype="multipart/form-data" >
            {{ csrf_field() }} 
           
        <div class="row">
            <div class="col-sm-6">
                <div class="box box-primary">
                <div class="box-header with-border">
                  
                  
                </div>
                <!-- /.box-header -->
                <!-- form start -->

                  <div class="box-body">
                        
                            
            
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('user.username')</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="@lang('user.username')"  value="{{old('username')}}">
                      </div>

                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('user.password')</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="@lang('user.password')"  value="{{old('password') }}">
                      </div>
                      
                       <div class="form-group">
                        <label for="exampleInputPassword1">@lang('user.password_confirmation')</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="@lang('user.password_confirmation')"  value="{{old('password_confirmation') }}">
                      </div>
                      

                     <!--  <div class="form-group">
                        <label for="exampleInputPassword1">Confirm New Password</label>
                        <div class="input-group margin">
                          <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm New Password"  value="{{old('last_name')}}" >
                            <span class="input-group-btn">
                              <button type="button" class="btn btn-default btn-flat show-pass" title="show password"><i class="fa fa-eye"></i></button>
                            </span>
                      </div>
                      </div> -->
                      
                      
                    
                      
                    </div>
                  </div>
                  <!-- /.box-body -->

                  <div class="box-footer">
                   
                  </div>
                
              </div>
            </div>  
        </div>
        

        <div class="row">
            <div class="col-sm-12">
                <button type="submit" id="btn_save" class="btn btn-primary" > <i class="fa fa-save"></i>
                    @lang('main.btn_save')</button>
                <button type="button" id="btn_cancel" class="btn btn-danger" > <i class="fa fa-close"></i>
                    @lang('main.btn_cancel')</button>
            </div>
        </div>
        </form>
    </section>

@endsection

@section('javascript')
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ url('js/utility/main.js') }}"></script> 
<!-- <script type="text/javascript" src="{{ url('js/utility/validate.js') }}"></script>  -->
<script type="text/javascript">

  $(function() {
  

    // $(".show-pass").on("click",function(){
    //     var parent = $(this).closest('.input-group');
    //     var type =  parent.find('input').attr('type') ; 
    //     console.log(type);
    //     if(type=="password"){
    //         parent.find('input').attr('type','text') ; 
    //     }else{
    //        parent.find('input').attr('type','password') ; 
    //     }
    // }); 

    $("#btn_cancel").on("click",function(){
        window.location.href="{{ url('profile/show')}}";
    }); 

  
    $("#create-form").validate({
      rules: {
        username: {
          required: true,
          maxlength: 255,
        },
        password: {
        
          required: true,
          minlength: 5,
          maxlength: 40,
        },
        password_confirmation: {
          required: true,
          minlength: 5,
          maxlength: 40,
          equalTo: "#password"
        },
      },
      messages: {
        username: {
          required: (($("#app_local").val()=='th') ? 'กรุณากรอกรหัสผู้ใช้' : "Please provide a username" ) ,
          maxlength: (($("#app_local").val()=='th') ? 'รหัสผู้ใช้ไม่ควรเกิน 255 ตัวอักษร' : "Your new password cannot over 255 characters long" )
        },
        password: {

          required: (($("#app_local").val()=='th') ? 'กรุณากรอกรหัสผ่าน' : "Please provide a password" ),
          minlength: (($("#app_local").val()=='th') ? 'กรุณากรอกพาสเวิดอย่างน้อย 5 ตัวอักษร' : "Your password must be at least 5 characters long"),
          maxlength: (($("#app_local").val()=='th') ? 'กรุณากรอกพาสเวิดไม่เกิน 40 ตัวอักษร' : "Your password cannot over 40 characters long" )
        },
        password_confirmation: {
          required: (($("#app_local").val()=='th') ? 'กรุณากรอกรหัสผ่าน' : "Please provide a password" ),
          minlength: (($("#app_local").val()=='th') ? 'กรุณากรอกพาสเวิดอย่างน้อย 5 ตัวอักษร' : "Your password must be at least 5 characters long"),
          maxlength: (($("#app_local").val()=='th') ? 'กรุณากรอกพาสเวิดไม่เกิน 40 ตัวอักษร' : "Your password cannot over 40 characters long" ),
          equalTo: (($("#app_local").val()=='th') ? 'กรุณากรอกรหัสผ่านให้ตรงกัน' : "Please enter the same password as above" )
        },
        
      },
       highlight: function ( element, errorClass, validClass ) {
      
        $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
       
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
       
      },
      submitHandler: function (form) {

        // console.log(form.action);
         var formData = new FormData($("#create-form")[0]); 
             
              formData.append('_method','PUT');
              


             $.ajax({
                  type: "POST",
                  url: "{{ $apiUpdate }}",
                  data: formData ,
                  cache:false,
                  contentType: false,
                  processData: false,
                 success: function (data) {
                    console.log(data,typeof data.response);
                    if(data.result=="true"){
                      swal({
                        type: 'success',
                        title: "@lang('main.update_success')" ,
                        showConfirmButton: false,
                        timer: 1500
                      })

                      setTimeout(function(){ window.location.href = "{{ url('profile/show') }}"; }, 1600);
                      
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







