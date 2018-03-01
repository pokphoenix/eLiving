@extends('main.layouts.main')


@section('style')
<!-- Select2 -->
  <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">

@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{ $title }}
        <small>เข้าร่วมโครงการ</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Domain</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      
      <!-- Main row -->
      <div class="row">
      	<div class="col-sm-offset-3 col-sm-6">
      		<div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">เข้าร่วมโครงการใหม่</h3>

            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="POST" action=" {{ url($route.'/join') }}" >
            	 {{ csrf_field() }}
              <div class="box-body">
					
					 @include('layouts.error')

              	
                 <div class="input-group input-group-sm">
                     <select class="form-control select2" id="domain_id" name="domain_id" >
                    <option ></option>
                   
                  </select>
                      <span class="input-group-btn" >
                        <button type="submit" class="btn btn-info btn-flat" style="height: 34px;">เข้าร่วม</button>
                      </span>
                </div>
              </div>
              <!-- /.box-body -->

           
            </form>
          </div>
          
          @if(isset($domains))

            @include('main.widgets.domain_item')
          @endif
      </div>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->

@endsection

@section('javascript')
<!-- Select2 -->
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
	<script type="text/javascript">
	  	$(function() {
          $("#domain_id").select2({
              // minimumInputLength: 2,
              tags: [],
              ajax: {
                  url: " {{ url('/api/domain/search?api_token=').Auth()->user()->api_token }} ",
                  dataType: 'json',
                  type: "POST",
                  delay: 250,
                  data: function (params) {
                      return {
                          name: params.term
                      };
                  },
                  processResults: function (data) {
                    return {
                      results: data
                    };
                  },
              }
          });
	  	});

	</script>
@endsection		
