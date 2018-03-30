<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>eLiving</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/icon" href="{{ url('public/img/favicon.ico') }} "/>
    <!-- Font Awesome -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="{{ url('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Slick slider -->
    <link href="{{ url('assets/css/slick.css') }}" rel="stylesheet">
    <!-- Theme color -->
    <link id="switcher" href="{{ url('assets/css/theme-color/default-theme.css') }}" rel="stylesheet">

    <!-- Main Style -->
    <link href="{{ url('assets/css/style.css') }}" rel="stylesheet">

    <!-- Fonts -->

    <!-- Open Sans for body font -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,700,800" rel="stylesheet">
    <!-- Lato for Title -->
    <!-- <link href="{{ url('assets/fonts/lato.css') }}" rel="stylesheet">  -->
 
 
  
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    
    <!-- Start Header -->
  <header id="mu-header" class="" role="banner" >
    <div class="container">
      <nav class="navbar navbar-default mu-navbar" >
          <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
                        <div class="navbar-header">
             

              <!-- Text Logo -->
              <a class="navbar-brand" href="{{ url('/') }}"> <img src="{{ url('public/img/logo/logo_residence_management_ver_3.png') }}" style="display:inline-block; width: 25px !important; height: 25px;" > eLiving</a>

              <!-- Image Logo -->
              <!-- <a class="navbar-brand" href="index.html"><img src="assets/images/logo.png"></a> -->


            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav mu-menu navbar-right">
                  @if(Route::currentRouteName()=="home" )
                    <li><a href="#">HOME</a></li>
                    <li><a href="#mu-book-overview">OVERVIEW</a></li>
                    <li><a href="#mu-author">AUTHOR</a></li>
                    <li><a href="#mu-pricing">PRICE</a></li>
                    <li><a href="#mu-testimonials">TESTIMONIALS</a></li>
                  @else
                    <!-- <li><a href="{{ url('/#') }}">HOME</a></li> -->
                  @endif
                    <!-- <li><a href="#mu-contact">CONTACT</a></li> -->
                 <!--  <li><a href="{{ url('/signin') }}">LOGIN</a></li>
                     <li><a href="{{ url('/signup') }}">TRY IT FREE</a></li>  -->
                </ul>
            </div><!-- /.navbar-collapse -->

          </div><!-- /.container-fluid -->
      </nav>
    </div>
  </header>
  <!-- End Header -->
  <input type="hidden" id="app_local" value="{{ App::getLocale() }}">
  @yield('content-wrapper')
      
      
  <!-- Start footer -->
  <footer id="mu-footer" role="contentinfo">
    <div class="container">
      <div class="mu-footer-area">
        <!-- <div class="mu-social-media">
          <a href="#"><i class="fa fa-facebook"></i></a>
          <a href="#"><i class="fa fa-twitter"></i></a>
          <a href="#"><i class="fa fa-google-plus"></i></a>
          <a href="#"><i class="fa fa-linkedin"></i></a>
        </div> -->
        <p class="mu-copyright">&copy; Copyright <a rel="nofollow" href="http://ferretking.com/">ferretking.com</a>. All right reserved.</p>
      </div>
    </div>

  </footer>
  <!-- End footer -->

  
  
    <!-- jQuery library -->
   <script src="{{ url('assets/js/jquery.min.js') }}"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!-- Bootstrap -->
    <script src="{{ url('assets/js/bootstrap.min.js') }}"></script>
  <!-- Slick slider -->
    <script src="{{ url('assets/js/slick.min.js') }}"></script>
    <!-- Counter js -->
  
    <!-- Ajax contact form  -->
    <script src="{{ url('assets/js/app.js') }}"></script>
   
 
  
    <!-- Custom js -->
  <script src="{{ url('assets/js/custom.js') }}"></script>
   @yield('javascript')
    <script type="text/javascript">
      if (navigator.appName == 'Microsoft Internet Explorer' ||  !!(navigator.userAgent.match(/Trident/) || navigator.userAgent.match(/rv:11/)) || (typeof $.browser !== "undefined" && $.browser.msie == 1))
        {
          alert("ไม่รองรับ IE กรุณาเปลี่ยนไปใช้ chrome");
        }
    </script>

  </body>
</html>