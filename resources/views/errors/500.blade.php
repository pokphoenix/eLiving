@extends('front.app')
@section('style')

@endsection
@section('content-wrapper')
<!-- data-toggle="modal" data-target="#modal-card-content" -->
	

	
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        500 Error Page
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">500 error</li>
      </ol>
    </section>
   
	
    <!-- Main content -->
   <section class="content">

      <div class="error-page">
        <h2 class="headline text-red">500</h2>

        <div class="error-content">
          <h3><i class="fa fa-warning text-red"></i> Oops! Something went wrong.</h3>

          <p>
            We will work on fixing that right away.
            Meanwhile, you may <a href="{{ url('/') }}">return to home</a> or try using the search form.
          </p>
			
			<a href="{{ url('/') }}" class="btn btn-danger btn-flat" >return to home</a>

          
        </div>
      </div>
      <!-- /.error-page -->

    </section>
    <!-- /.content -->

  <!-- /.content-wrapper -->
@endsection

@section('javascript')


@endsection		
