@extends('front.app')

@section('content-wrapper')


<!-- Start Featured Slider -->

  <section id="mu-hero">
    <div class="container">
      <div class="row">

        <div class="col-sm-offset-2 col-sm-8 text-center">
          <h4 style="color: #FFF;" >
@if(Session::get('error', false))
    <?php $data = Session::get('error'); ?>
    @if (is_array($data))
        
        @foreach ($data as $msg)
           
                @if (is_array($msg))
                    @foreach ($msg as $m)
                    {{ $m }}
                    @endforeach
                @else
                {{ $msg }}
                @endif
               
            
        @endforeach
    @else
        
            {{ $data }}
            
       
    @endif
@endif
          </h4>
        </div> 
 

      </div>
    </div>
  </section>
  
  <!-- Start Featured Slider -->
  
  <!-- Start main content -->
    

  
  <!-- End main content --> 


@endsection


@section('javascript')
  <script type="text/javascript" src="assets/js/counter.js"></script>
@endsection