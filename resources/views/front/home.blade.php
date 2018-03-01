@extends('front.app')

@section('content-wrapper')


<!-- Start Featured Slider -->

  <section id="mu-hero">
    <div class="container">
      <div class="row">

        <div class="col-md-6 col-sm-6 col-sm-push-6">
          <div class="mu-hero-right">
            <img src="assets/images/ebook.png" alt="Ebook img">
          </div>
        </div>

        <div class="col-md-6 col-sm-6 col-sm-pull-6">
          <div class="mu-hero-left">
            <h1>Perfect Landing Page Template to Present Your eBook</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam saepe, recusandae quidem nulla! Eveniet explicabo perferendis aut, ab quos omnis labore laboriosam quisquam hic deserunt ipsum maxime aspernatur velit impedit.</p>
            <a href="#" class="mu-primary-btn">Download Now!</a>
            <span>*Avaliable in PDF, ePUB, Mobi & Kindle.</span>
          </div>
        </div>  

      </div>
    </div>
  </section>
  
  <!-- Start Featured Slider -->
  
  <!-- Start main content -->
    
  <main role="main">

    <!-- Start Counter -->
    @include('front.home.counter')
    <!-- End Counter -->

    <!-- Start Book Overview -->
    @include('front.home.overview')
    <!-- End Book Overview -->

    

    <!-- Start Video Review -->
    @include('front.home.video')
    <!-- End Video Review -->

    <!-- Start Author -->
    @include('front.home.author')
    <!-- End Author -->

    <!-- Start Pricing -->
    @include('front.home.pricing')
    <!-- End Pricing -->

    <!-- Start Testimonials -->
    @include('front.home.testimonials')
    <!-- End Testimonials -->

  
    <!-- Start Contact -->
   <!--  <section id="mu-contact">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="mu-contact-area">

              <div class="mu-heading-area">
                <h2 class="mu-heading-title">Drop Us A Message</h2>
                <span class="mu-header-dot"></span>
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever</p>
              </div>

              
              <div class="mu-contact-content">

                <div id="form-messages"></div>
                <form id="ajax-contact" method="post" action="mailer.php" class="mu-contact-form">
                  <div class="form-group">                
                    <input type="text" class="form-control" placeholder="Name" id="name" name="name" required>
                  </div>
                  <div class="form-group">                
                    <input type="email" class="form-control" placeholder="Enter Email" id="email" name="email" required>
                  </div>              
                  <div class="form-group">
                    <textarea class="form-control" placeholder="Message" id="message" name="message" required></textarea>
                  </div>
                  <button type="submit" class="mu-send-msg-btn"><span>SUBMIT</span></button>
                    </form>

              </div>
              

            </div>
          </div>
        </div>
      </div>
    </section> -->
    <!-- End Contact -->

    <!-- Start Google Map -->
    @include('front.home.map')
    
    <!-- End Google Map -->

  </main>
  
  <!-- End main content --> 


@endsection


@section('javascript')
  <script type="text/javascript" src="assets/js/counter.js"></script>
@endsection