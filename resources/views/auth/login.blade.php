@extends('front.app')

@section('content-wrapper')
<div style="height:50px;"></div>
<main role="main">

    

    <!-- Start Contact -->
    <section id="mu-contact">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="mu-contact-area">

              <div class="text-center">

                 
                <h2 class="mu-heading-title">@lang('main.login')</h2>
                <span class="mu-header-dot"></span>
                 @include('layouts.error')
              </div>

                
              <div class="col-sm-offset-1 col-sm-10">

                <div id="form-messages"></div>
                <form id="signin-form" method="post" action="{{ route('signin') }}" class="mu-contact-form">
                     {{ csrf_field() }}
                    @if (session()->has('message'))
  <h3 class="alert alert-danger text-center">{!!session()->get('message')!!}</h3>
@endif
                    <input type="hidden" id="fb" name="fb"  >

                    <div class="row" >
                       
                        <div class="col-sm-offset-4 col-sm-4">
                            <div class="row">
                                <div class="form-group">             
                                    <input type="text" class="form-control" placeholder="@lang('user.user_name')" id="username" name="username" value="{{old('username')}}" >
                                </div>
                                <div class="form-group">                
                                    <input type="password" class="form-control" placeholder="@lang('main.password')" id="password" name="password" value="{{old('password')}}">
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> @lang('main.remember')
                                    </label>
                                </div>

                              <!--   <a class="btn btn-link" href="{{ url('/signup') }}">
                                    
                                    @lang('main.signup')
                                </a> -->
                                 
                            </div>
                              <button type="submit" class="mu-send-msg-btn"><span>@lang('main.submit')</span>
                                 </button><BR><BR>
                                 <div class="row" style="margin:10px 0;color: #ccc;border-top: 1px solid #CCC;">
                                        
                                 </div>
                            
                        </div>
                        
                    </div>
                    <div class="row">
                               <div class="col-sm-offset-3 col-sm-6">
                                 
                                  <a class="mu-order-btn" href="{{ route('password.request') }}">
                                    @lang('main.forgot_password')
                                </a>
                                <a  href="{{ url('/signup') }}" class="mu-success-btn" ><span>@lang('main.signup') </span></a>
                               </div>
                             </div> 
                            <div class="row">
                                <div class="col-sm-offset-4 col-sm-4" >
                                    <div class="row" style="margin:10px 0;color: #ccc;border-top: 1px solid #CCC;">
                                        
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <a class="btn btn-primary btn-block btn-social btn-facebook" id="btn-facebook">
                                                <i class="fa fa-facebook"></i> Login with Facebook
                                            </a>
                                           <input type="submit" formnovalidate id="cancelsubmit" name="cancel" value="Cancel" style="display: none;">
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                      

                    
                  </form>
              </div>
              

            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End Contact -->

   

  </main>


@endsection
@section('javascript')
<script type="text/javascript" src="plugins/jquery-validate/jquery.validate.min.js"></script>
<script type="text/javascript">
    $(function() {

      $("#signin-form").validate({
          rules: {
            username: {
              required: true,
              minlength: 2,
            },
            password: {
              required: true,
              minlength: 5,
              maxlength: 40,
            }
          },
          messages: {
            username: {
              required: "Please enter a username",
              minlength: "Your username must consist of at least 2 characters"
            },
            password: {
              required: "Please provide a password",
              minlength: "Your password must be at least 5 characters long",
              maxlength: "Your password cannot over 40 characters long"
            }
          },
        });

        // $("#signin-form").validate({
        //     rules: {
        //         username: {
        //             required: true,
        //             minlength: 2
        //         },
        //         password: {
        //             required: true,
        //             minlength: 5
        //         }
        //     },
        //     messages: {
        //         username: {
        //             required: "Please enter a username",
        //             minlength: "Your username must consist of at least 2 characters"
        //         },
        //         password: {
        //             required: "Please provide a password",
        //             minlength: "Your password must be at least 5 characters long"
        //         }
        //     }
        // });

    });
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




$("#btn-facebook").on("click",function(){
    loginWithFacebook();
})

function loginWithFacebook(){
    FB.getLoginStatus(function(response) {
        console.log("loginWithFacebook",response);
        if (response.status === 'connected') { 
            var uid = response.authResponse.userID;
            var accessToken = response.authResponse.accessToken;

            $("#fb").val(uid);
            $("#signin-form").attr("action",'signin_facebook');
            $("#cancelsubmit").click();
          
        }else{
            window.location = "{{ url('/signup_facebook') }}" ;
        }
    });
}
</script>
@endsection