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
                <h2 class="mu-heading-title">@lang('main.signup')</h2>
                <span class="mu-header-dot"></span>
                @include('layouts.error')
              </div>

<input type="hidden" id="api_search" value="{{ url('api/search')}}">
            	
              <div class="col-sm-offset-1 col-sm-10">

                <div id="form-messages"></div>
                  <section class="form-parent none" data-id="2">

                      <form action="" id="domain-form" method="post" class="mu-contact-form">
                          {{ csrf_field() }}
                          <div class="col-sm-offset-4 col-sm-4">
                              <div class="form-group">             
                                <select id="domain_id" name="domain_id" class="form-control" >
                                    <option value="">@lang('main.select_domain')</option>
                                    @foreach($domains as $domain)
                                    <option value="{{ $domain['id'] }}">{{ $domain['name'] }}</option>
                                    @endforeach
                                </select>
                              </div>
                          </div>
                      </form>

                  </section>


                	 <section class="form-parent" data-id="0">
                	 	  <form id="signup-form" method="post" action="{{url('api/signup')}}" class="mu-contact-form">
                	 {{ csrf_field() }}
                	   <input type="hidden" id="agree" name="agree" class="form-check-input" value="1" > 
                  		<div class="col-sm-offset-4 col-sm-4">
                  				<div class="form-group">             
			                  		<input type="text" class="form-control" placeholder="@lang('user.id_card')" id="id_card" name="id_card" maxlength="13" >
			                  	</div>
			                  	<div class="has-temp">
			                  		<div class="form-group">             
				                  		<input type="text" class="form-control" placeholder="@lang('user.first_name')" id="first_name" name="first_name" >
				                  	</div>
				                  	<div class="form-group">                
				                  		<input type="text" class="form-control" placeholder="@lang('user.last_name')" id="last_name" name="last_name" value="{{old('last_name')}}">
				                  	</div>
				                  	<div class="form-group">                
				                  		<input type="text" class="form-control" placeholder="@lang('main.email')" id="email" name="email" value="{{old('email')}}">
				                  	</div>
				                  	<div class="form-group">                
				                  		<input type="text" class="form-control" placeholder="@lang('main.tel')" id="tel" name="tel" value="{{old('tel')}}">
				                  	</div>  
			                  	</div>

			                  	<div class="form-group">                
			                  		<input type="text" class="form-control" placeholder="@lang('user.user_name')" id="username" name="username" value="{{old('username')}}"
			                  		>
			                  	</div>
			                  	<div class="form-group">                
			                  		<input type="password" class="form-control" placeholder="@lang('main.password')" id="password" name="password" value="{{old('password')}}"
									
			                  		>
			                  	</div>
			                  	<div class="form-group">                
			                  		<input type="password" class="form-control" placeholder="@lang('user.confirm_password')" id="password_confirmation" name="password_confirmation" value="{{old('password_confirmation')}}">
			                  	</div>   
			                	
			                
                  		</div>
                  		</form>
                	 </section>
                  	
                  	
					@include('front.widgets.address')
          @include('front.widgets.room')
					@include('front.widgets.signup_button')	
                  	
                  			<!-- <div class="form-check">
							    <label class="form-check-label">
							      <input type="checkbox" id="agree" name="agree" class="form-check-input" @if(old('agree'))  checked="" @endif >
							      I agree to the Terms of Service and Privacy Policy.

Give us a call and we'll help you get your account activated

This is where you and your employees will log in:

https://yourcompany.bamboohr.com
							    </label>
							</div> -->	
			                
			               
							
								
							
			                  	<!-- <div class="form-group">                
			                  		<input type="text" class="form-control" placeholder="ชื่อโครงการ" id="residence_name" name="residence_name"  value="{{old('residence_name')}}">
			                  	</div>  -->    
		               
		                       
							<!-- <div class="row">
			                	<div class="form-group col-sm-offset-4 col-sm-4">                
			                  		<input type="text" class="form-control" placeholder="ชื่อบริษัท" id="company_name" name="company_name" value="{{old('company_name')}}">
			                  	</div>	  
		                  	</div>  -->
                  		
                  		
                  	
					<!-- <div class="row row-facebook" >
						<div class="col-sm-offset-2 col-sm-8" >
							<div class="row" style="margin:10px 0;color: #ccc;border-top: 1px solid #CCC;">
								
							</div>
							<div class="row">
								<div class="col-sm-offset-2 col-sm-8">
									<a class="btn btn-primary btn-block btn-social btn-facebook" id="btn-facebook">
							            <i class="fa fa-facebook"></i> Sign Up with Facebook
							        </a>
							       
								</div>
							</div>
							
						</div>
			                	
			                  	
		            </div> 

           

               		
                 
				 
              </div>
              

            </div>
          </div> -->
        </div>
      </div>
    </section>
    <!-- End Contact -->

   

  </main>
  
  <!-- End main content --> 

@endsection


@section('javascript')

@include('front.widgets.signup_javascript')


<script type="text/javascript">
jQuery.validator.setDefaults({
  debug: true,
  success: "valid"
});




$(function() {

	
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
        // minlength: 13,
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
      username: {
        required: true,
        minlength: 2,
        remote: {
                  url: "{{ url('api/validate/username') }}",
                  type: "post",
                  data: {
                      username: function() {
                          return $("#username").val();
                      }
                  }
              }
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
    // ,submitHandler: function (form) {

    //    // console.log(form.action);
    //    $('.fa-spinner').show();
   //         $.ajax({
   //             type: "POST",
   //              url: "{{url('api/signup')}}",
   //             data: $(form).serialize(),
   //             success: function (data) {
   //                // console.log(data,typeof data.response);
   //                $('.fa-spinner').hide();
   //                if(data.result=="true"){
   //                 swal({
   //                      title: 'Thank you, '+$("#first_name").val()+" "+$("#last_name").val()+' You\'ve successfully signd up.',
   //                      type: 'success',
   //                      showCancelButton: false,
   //                      confirmButtonText: "@lang('main.ok')"
   //                    }).then((result) => {
   //                      if (result.value) {
   //                        window.location.href = "{{ url('login') }}";
   //                      }
   //                    })
   //                }else{
   //                 var error = JSON.stringify(data.errors);
   //                 // console.log(error);
   //                   swal(
    //            'Error...',
    //            error,
    //            'error'
    //          )
   //                }
   //             }
   //         });
   //         return false; // required to block normal submit since you used ajax
   //     }
    

  });



	

});
</script>

<script>
function statusChangeCallback(response) {
    //console.log('statusChangeCallback');
    //console.log(response);
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    if (response.status === 'connected') {
      // Logged into your app and Facebook.
      loginWithFacebook();
    } else {
     
    }
  }
</script>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : "{{ Service::FACEBOOK_APP_ID }}",
      cookie     : true,
      xfbml      : true,
      version    : 'v2.11'
    });
      
    // FB.AppEvents.logPageView();   
  
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
function checkBlockPopUp(){
	var popup = window.open("about:blank","","directories=no,height=100,width=100,menubar=no,resizable=no,scrollbars=no,status=no,titlebar=no,top=0,location=no");
	if(!popup || popup.closed || typeof popup == 'undefined' || typeof popup.closed=='undefined') 
	{ 
	   
	    return true;
	}

	window.focus();
	popup.blur();		
	if(navigator && (navigator.userAgent.toLowerCase()).indexOf("chrome") > -1)
	{			

		if(popup && popup.chrome_popups_permitted && popup.chrome_popups_permitted() == true)
		{			
			popup.close();
			return true;
		}
		return;
	}
	popup.close();
}	


$("#btn-facebook").on("click touch",function(){
	// if(checkBlockPopUp()){
	// 	alert('บราวเซอร์ของคุณ มีการบล๊อคเฟสบุคป๊อปอัพ \n กรุณาปิดก่อนใช้งานค่ะ');
	// }else{
	// 	signUpFacebook();
	// }
	window.location = "{{ url('/signup_facebook') }}" ;
})

function checkLoginStatus(){
	FB.getLoginStatus(function(response) {
	    if (response.status === 'connected') { 
	    	loginWithFacebook(response);
	    }else{
	    	signUpFacebook();
	    }
	});
}

	
function signUpFacebook() {
	//console.log('signUpFacebook');
	FB.login(function(response) {
		//console.log('signUpFacebook',response);
	 	if (response.status === 'connected') {
		    var uid = response.authResponse.userID;
		    var accessToken = response.authResponse.accessToken;
		    if (response.authResponse) {
			     FB.api('/me', {fields: 'email,name,first_name,last_name'}, function(response) {
			     	//console.log(response);
			     	// $("#id_card").val(response.id);
			     	$("#email").val(response.email);
			     	$("#first_name").val(response.first_name);
			     	$("#last_name").val(response.last_name);
			     	$("#username").val('FB'+response.id);
			     	var password = accessToken.substring(0,10); 
			     	$("#password").val(password);
			     	$("#password_confirmation").val(password);
			     	
			     	// $("#signup-form").submit();

			      
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

@endsection