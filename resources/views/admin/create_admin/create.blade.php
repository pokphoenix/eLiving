@extends('main.layouts.main')


@section('style')
  <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       
        <i class="fa fa-user"></i>      
        {{ $title }}
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active"> {{ $title }}</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      
      <!-- Main row -->
      @if(isset($edit))
         
        <form  id="signup-form" role="form" method="POST" action="{{$route}}" enctype="multipart/form-data"  >
            {{ method_field('PUT') }}
             {{ csrf_field() }}
         
  
      @else
          <form  id="signup-form" role="form" method="POST" action="{{$route}}" enctype="multipart/form-data"  >
          {{ csrf_field() }}
      @endif



      
      <div class="row">
        <div class="col-sm-12">
           @include('layouts.error')
        </div>
      </div>

      <div class="row">
      	<div class="col-sm-12">
      		  <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">@lang('user.information')</h3>
              <div class="box-tools pull-right">
                   
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
          
              
              <div class="box-body">
          
              
        
                <div class="col-sm-6">
                  
                  <div class="form-group">
                    <label >@lang('user.id_card')</label>
                    <input type="text" maxlength="13"  class="form-control" id="id_card" name="id_card" placeholder="@lang('user.id_card')"  value="{{ isset($edit) ? $data['id_card'] : old('id_card') }}" @if(isset($edit)) readonly="" @endif >
                  </div>
                 
                  <div class="form-group">
                    <label >@lang('main.email')</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="@lang('main.email')"  value="{{ isset($edit) ? $data['email'] : old('email') }}">
                  </div>
                  <div class="form-group">
                    <label >@lang('user.first_name')</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="@lang('user.first_name')" value="{{ isset($edit) ? $data['first_name'] : old('first_name') }}">
                  </div>
                   <div class="form-group">
                    <label >@lang('user.nick_name')</label>
                    <input type="text" class="form-control" id="nick_name" name="nick_name" placeholder="@lang('user.nick_name')" value="{{ isset($edit) ? $data['nick_name'] : old('nick_name') }}">
                  </div>
                  <div class="form-group">
                    <label >@lang('user.last_name')</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="@lang('user.last_name')" value="{{ isset($edit) ? $data['last_name'] : old('last_name') }}">
                  </div>
                  <div class="form-group">
                    <label >@lang('main.tel')</label>
                    <input type="text" class="form-control" id="tel" name="tel" placeholder="@lang('main.tel')" value="{{ isset($edit) ? $data['tel'] : old('tel') }}">
                  </div>
                  
                </div>
                 <div class="col-sm-6">
                  
                  <div class="form-group">
                    <label >@lang('user.user_name')</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="@lang('user.user_name')" value="{{ isset($edit) ? $data['username'] : old('username') }}" @if(isset($edit)) readonly="" @endif >
                  </div>
  
                  @if(!isset($edit))

                  <div class="form-group">
                    <label >@lang('main.password')</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="@lang('main.password')"  value="{{ old('password') }}">
                  </div>
                  <div class="form-group">
                    <label >@lang('user.confirm_password')</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="@lang('user.confirm_password')" value="{{old('password_confirmation') }}">
                  </div>
                  @endif
                  
                </div>
              </div>
             
            
          </div>
      	</div>
        
        <div class="col-sm-12"></div>
        <div class="col-sm-6">
             @include('admin.widgets.role')
        </div>
        <div class="col-sm-6">
            @include('admin.widgets.ban')
            
        </div>
       
       
        <div class="col-sm-12" style="height: 50px;">
           <button type="submit" id="save" class="btn btn-primary">@lang('main.btn_save')
             <i class="fa fa-spinner fa-spin fa-fw" style="display:none;" ></i>
 
           </button>


            <a href="{{ url($domainName.'/'.$routePath) }}" id="cancel" class="btn btn-danger">@lang('main.btn_cancel')</a>
          
        </div>
        
        
      </div>
      </form>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->

@endsection

@section('javascript')
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ url('js/utility/main.js') }}"></script> 
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ url('js/utility/autocomplete.js') }}"></script> 

<script type="text/javascript">


// $("#is_ban").on("click",function(){
//     if($(this).is(':checked')){

//     }
// })



@if(!isset($edit))

$(document).on("input","#id_card",function(e) {
  // if($(this).val().length==13){
    var route = "{{ url('api/search/user-data') }}" ;
    var idCard = $(this).val();
    var data ={id_card:idCard} ;
      $.ajax({
        url: route,
        type: 'POST',
        dataType: 'json',
        data:data
      })
      .done(function(response) {
        console.log(response);
        if(response.result=="true"){
            var res = response.response.user ;
            if(response.response.user.length > 0 ) {
              swal({
                title: 'Edit Data / แก้ไขข้อมูล ?',
                text: " Duplicate Citizen id / เลขบัตรประชาชนซ้ำในระบบ",
                type: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm Edit / ยืนยันแก้ไข' ,
                cancelButtonText: 'Cancel / ยกเลิก'
              }).then((result) => {
                if (result.value) {
                  window.location = "{{ url($domainName.'/'.$routePath) }}"+"/"+idCard+"/edit" ;
                }
              })
            }
            
        }else{
          
        }
      })
      .fail(function() {
       
      })
  // }
  
});
@endif

 

$(function() {
    $("#signup-form").validate({
      rules: {
        first_name: {
          required: true,
          maxlength: 255
        },
        last_name: {
          required: true,
          maxlength: 255
        },
        id_card: {
          required: true,
        
          maxlength: 13

          @if(!isset($edit))
          ,remote: {
                    url: "{{ url('api/validate/idcard') }}",
                    type: "post",
                    data: {
                        id_card: function() {
                            return $("#id_card").val();
                        }
                    }
                }
          @endif
        },
        username: {
          required: true,
          minlength: 2
          @if(!isset($edit))
          ,remote: {
                    url: "{{ url('api/validate/username') }}",
                    type: "post",
                    data: {
                        username: function() {
                            return $("#username").val();
                        }
                    }
                }
          @endif
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
        user: {
          maxlength: 255
        },
        zip_code:{
          maxlength: 255
        },
        // tel:"required",
        // company_name:"required",
        // domain:"required",
        // tel:"required",
        email: {
          required: true,
          email: true,
          maxlength: 255
        },
      },
      messages: {
        first_name: (($("#app_local").val()=='th') ? 'ชื่อไม่ถูกต้อง' : 'Wrong firstname' ),
        last_name: (($("#app_local").val()=='th') ? 'นามสกุลไม่ถูกต้อง' : 'Wrong lastname' ),
        id_card:{
            required: (($("#app_local").val()=='th') ? 'เลขบัตรประชาชนไม่ถูกต้อง' : 'Wrong ID card' ),
            remote: (($("#app_local").val()=='th') ? 'เลขบัตรประชาชนซ้ำในระบบ' : 'ID card already exits' )
        },
        username: {
          required: "@lang('user.first_name_require')",
          minlength: "@lang('user.first_name_require')",
          remote: "@lang('user.user_name_repeat')"
        },
        password: {
          required: "@lang('user.password_require')",
          minlength: "@lang('main.password_least_character_long')",
          maxlength: "@lang('main.password_not_over_long')"
        },
        password_confirmation: {
          required: "@lang('user.password_require')",
          minlength: "@lang('main.password_least_character_long')",
          maxlength: "@lang('main.password_not_over_long')",
          equalTo: "@lang('user.password_confirm_require')"
        },
        email: (($("#app_local").val()=='th') ? 'อีเมลไม่ถูกต้อง' : 'Wrong email address' ),
      },
        highlight: function ( element, errorClass, validClass ) {
      
        $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
       
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
       
      }
      ,submitHandler: function (form) {
        var idCard =  $.trim($("#id_card").val())  ;
        var data = { user:[],'file_type':[] };
        $("#save").find('.fa-spinner').show();

      
        var form_data = new FormData($("#signup-form")[0]);
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
                            window.location.href = "{{ url($domainName.'/'.$routePath) }}";
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
