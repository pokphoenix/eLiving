@extends('front.app')

@section('content-wrapper')
@include('front.widgets.signup_css')
    
  <!-- Start main content -->
    
  <main role="main">

    

    <!-- Start Contact -->
    <section id="mu-contact">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="mu-contact-area">

              <div class="text-center">
                <h2 class="mu-heading-title">สมัครสมาชิก</h2>
                <span class="mu-header-dot"></span>
                @include('layouts.error')
              </div>

                
              <div class="col-sm-offset-1 col-sm-10">

                <div id="form-messages"></div>
                 <section class="form-parent" data-id="0">
                   <form id="signup-form" method="post" action="{{ url('/api/facebook_signup') }}" class="mu-contact-form">
                     {{ csrf_field() }}

                     <input  id="facebook_id" name="facebook_id" type="hidden" >
                     <input  id="facebook_token" name="facebook_token" type="hidden" >
                     <input  id="profile_url" name="profile_url" type="hidden" >
                     <input type="hidden" id="agree" name="agree" class="form-check-input" value="1" >     
                    <div class="row">
                        <div class="col-sm-offset-4 col-sm-4">
                                
                                <div class="form-group">             
                                    <img id="profile_img" src="{{ url('public/img/profile/0.png') }}" width="100" height="100" >
                                </div>
                                <div class="form-group">             
                                    <input type="text" class="form-control" placeholder="@lang('user.id_card')" id="id_card" name="id_card" maxlength="13" value="{{old('id_card')}}" >
                                </div>
                                <div class="form-group">             
                                    <input type="text" class="form-control" placeholder="@lang('user.first_name')" id="first_name" name="first_name" >
                                </div>
                                <div class="form-group">                
                                    <input type="text" class="form-control" placeholder="@lang('user.last_name')" id="last_name" name="last_name" value="{{old('last_name')}}">
                                </div>
                                <div class="form-group">                
                                    <input type="email" class="form-control" placeholder="@lang('main.email')" id="email" name="email" value="{{old('email')}}">
                                </div>
                                <div class="form-group">                
                                <input type="text" class="form-control" placeholder="@lang('main.tel')" id="tel" name="tel" value="{{old('tel')}}">
                              </div> 
                                <div class="form-group" id="facebook_popup_block">                
                                    <div class="alert alert-danger">
                                        <h5>Please allow popup block</h5>
                                    </div>
                                </div>
                              
                        </div>
                        
                    </div>
                  </form>
                 </section>
                    @include('front.widgets.address')
                    @include('front.widgets.room')
                    @include('front.widgets.signup_button')
                  
              </div>
              

            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End Contact -->

   

  </main>
  
  <!-- End main content --> 

@endsection


@section('javascript')



<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : "{{ Service::FACEBOOK_APP_ID }}",
      cookie     : true,
      xfbml      : true,
      version    : 'v2.11'
    });
      
    FB.getLoginStatus(function(response) {
        signUpFacebook();
    });
  
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "https://connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
<script type="text/javascript">


$("#btn-facebook").on("click",function(){
    // if(checkBlockPopUp()){
    //  alert('บราวเซอร์ของคุณ มีการบล๊อคเฟสบุคป๊อปอัพ \n กรุณาปิดก่อนใช้งานค่ะ');
    // }else{
    //  signUpFacebook();
    // }
    signUpFacebook();
})
$("#btn-logout-facebook").on("click",function(){
    FB.logout(function(response) {
        signUpFacebook();
    });

   
})

$(function() {
    if($("#facebook_id").val()==''){
      $("#facebook_popup_block").show();
    }
});

    
function signUpFacebook() {
   
    FB.login(function(response) {
       
        if (response.status === 'connected') {
            var uid = response.authResponse.userID;
            var accessToken = response.authResponse.accessToken;
            if (response.authResponse) {
                 FB.api('/me', {fields: 'email,name,first_name,last_name,picture.width(150).height(150)',access_token:accessToken}, function(response) {
                   
                    $("#email").val(response.email);
                    $("#first_name").val(response.first_name);
                    $("#last_name").val(response.last_name);
                    $("#facebook_id").val(response.id);
                    $("#facebook_token").val(accessToken);
                    $("#profile_url").val(response.picture.data.url);
                    $("#profile_img").attr({"src":response.picture.data.url});
                    $("#facebook_popup_block").hide();
                 });
            } else {
             alert('เชื่อมต่อเฟสบุคผิดพลาด กรุณาลองใหม่ในภายหลัง');
            }
        } else {
            alert('เชื่อมต่อเฟสบุคผิดพลาด กรุณาลองใหม่ในภายหลัง');
        }
    }, {scope: 'email,public_profile'});
}

function loginWithFacebook(){

}
</script>


@include('front.widgets.signup_javascript')


<script type="text/javascript">
  $("#signup-form").validate({
    rules: {
      first_name: {
            required: true,
            maxlength: 255
          },
      last_name:{
            required: true,
            maxlength: 255
          },
      id_card: {
        required: true,
        minlength: 13,
        maxlength: 13,
        remote: {
                  url: "{{ url('api/validate/idcard') }}",
                  type: "post",
                  data: {
                      id_card: function() {
                          return $("#id_card").val();
                      }
                  }
              }
      },
      // job_title:"required",
      // tel:"required",
      // company_name:"required",
      // domain:"required",
      tel:"required",
      email: {
        required: true,
        email: true
      },
      // unit: {
      //  required: true
      // },
      
      // agree: "required"
    },
    messages: {
      first_name: "@lang('user.first_name_require')",
      last_name: "@lang('user.last_name_require')",
      tel: "@lang('user.tel_require')",
      id_card:{
              required: "@lang('user.id_card_require')",
              remote: "@lang('user.id_card_repeat')"
      },
      email: "@lang('user.email_require')",
      // agree: "Please accept our policy"
      
    }
    ,highlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".form-group" ).find('em').remove();
          $( element ).parents( ".form-group" ).find('span').remove();
          $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
        }
     ,unhighlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
            $( element ).parents( ".form-group" ).find('label').remove();
        }
  });
</script>

@endsection