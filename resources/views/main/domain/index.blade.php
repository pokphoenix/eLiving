@extends('main.layouts.main')


@section('style')
<style>
.product-click:hover{ cursor: pointer !important;  }
.product-click {border: 1px solid #F00 !important; }
</style>
@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{ $title }}
        <small>Control panel</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
		@if(count($domains)<=0)
			<div class="col-sm-offset-4 col-sm-4 text-center">
				<h1> คุณยังไม่มีโครงการ </h1>
				<div class="col-sm-6 ">
					<a class="btn btn-app" href="{{ route('domain.join') }}">
		                <i class="fa fa-play"></i> เข้าร่วม
		            </a>
				</div>
				<div class="col-sm-6 ">
					<a class="btn btn-info btn-app" href="{{ route('domain.create') }}">
		                <i class="fa fa-plus"></i> สร้าง
		            </a>
				</div>
			</div>

		@endif

		
    	<div class="row">
    		<div class="col-sm-6">
    	 	@include('main.widgets.domain_item')
    		</div>
    	</div>

    </section>
    <!-- /.content -->
@endsection

@section('javascript')
	<script type="text/javascript">
		

	</script>
@endsection		
