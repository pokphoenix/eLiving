@extends('main.layouts.main')


@section('style')
@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{ $title }}
        <small>สร้างโครงการใหม่</small>
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
              <h3 class="box-title">สร้างโครงการ</h3>

            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" id="domain-form" method="POST" action=" {{ url($route) }}" >
            	 {{ csrf_field() }}
              <div class="box-body">
					
					 @include('layouts.error')

              	<div class="form-group">
                  <label for="exampleInputEmail1">ชื่อโครงการ</label>
                  <input type="text" class="form-control" id="name" name="name" placeholder="ชื่อโดเมนของโครงการ">
                </div>
               
                <div class="form-group">
                  <label for="exampleInputPassword1">ชื่อบริษัทบริหาร</label>
                  <input type="text" class="form-control" id="company_name" name="company_name" placeholder="ชื่อบริษัท">
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">จำนวน unit</label>
                  <select class="form-control" id="unit" name="unit" >
                  		@foreach($units as $key => $unit)
                  		<option value="{{ $key }}"> {{ $unit }} </option>
                  		@endforeach
                  </select>
                 
                </div>
                <!-- <div class="form-group">
                  <label for="exampleInputFile">File input</label>
                  <input type="file" id="exampleInputFile">

                  <p class="help-block">Example block-level help text here.</p>
                </div> -->
                
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Save</button>
              </div>
            </form>
          </div>
      	</div>
      </div>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->
@endsection

@section('javascript')
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>

	<script type="text/javascript">
		$('#card_new,#card_reject,#card_in_progress,#card_pending').slimScroll({
	    	height: '250px'
	  	});
	  	$('#card_accept,#card_done').slimScroll({
	    	height: '600px'
	  	});

	  	$(function() {
          $("#domain-form").validate({
              rules: {
                name: {
                  required: true,
                  maxlength: 255,
                  remote: {
                            url: "{{ url('api/validate/domain-name') }}",
                            type: "post",
                            data: {
                                name: function() {
                                    return $("#name").val();
                                }
                            }
                        }
                },
              
               
                company_name: "required",
              },
              messages: {
                name: {
                  required: 'กรุณากรอกชื่อโดเมน',
                  remote: 'ชื่อโดเมนซ้ำ'

                },
               
               
                company_name:"กรุณากรอกชื่อบริษัท",
              },
              errorElement: "em",
              errorPlacement: function ( error, element ) {

                  // Add the `help-block` class to the error element
                  error.addClass( "help-block" );

                  // Add `has-feedback` class to the parent div.form-group
                  // in order to add icons to inputs
                  element.parents( ".form-group" ).addClass( "has-feedback" );

                  if ( element.prop( "type" ) === "checkbox" ) {
                    error.insertAfter( element.parent( "label" ) );
                  } else {
                    error.insertAfter( element );
                  }
                  console.log(element.next( "span" ));

                  // Add the span element, if doesn't exists, and apply the icon classes to it.
                  if ( !element.next( "span" )[ 0 ] ) {
                    $( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
                  }
                },
              success: function ( label, element ) {
                  // Add the span element, if doesn't exists, and apply the icon classes to it.
                  if ( !$( element ).next( "span" )[ 0 ] ) {
                    $( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ) );
                  }
                  label.parent().removeClass('error');
                    label.remove(); 
                },
              highlight: function ( element, errorClass, validClass ) {
                $( element ).parents( ".form-group" ).find('em').remove();
                $( element ).parents( ".form-group" ).find('span').remove();
                $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
                $( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
              },
              unhighlight: function ( element, errorClass, validClass ) {
                $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
                $( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
              }

            });



	  		$(".addcard-hover").on("click",function(){
	  			var add_card = "<div class=\"addcard-box\"><div class=\"box box-solid\">"+
	  						"<div class=\"box-header\">"+
	  						"<textarea class=\"txt-area-card-title\" rows=\"2\" style=\"border: 0;\">"+
	  						"</textarea>"+"</div></div>"+
	  						"<button class=\"btn bg-olive margin btn-add-card\" >Add</button>"+
	  						"<button class=\"btn btn-close-card\" ><i class=\"fa fa-close\"></i></button></div>";
	  			var rows = $(this).parent(".box-solid") ; 
	  			rows.find(".append-card").append(add_card);
	  			rows.find(".box-footer").hide();
	  		})


	  	});
	  	
	  	$(".content-wrapper").on("click",".btn-close-card",function(event) {
	  			console.log("click");
	  			var rows = $(this).closest(".box-parent") ; 
	  			rows.find(".append-card").find(".addcard-box").remove();
	  			rows.find(".box-footer").show();
	  	});

	  	$(".content-wrapper").on("click",".btn-add-card",function(event) {
	  			var rows = $(this).closest(".box-parent") ; 
	  			var txt = $("textarea.txt-area-card-title").val();
	  			console.log(txt);
	  			var card = "<div class=\"box box-solid card\">"+
	  						"<div class=\"box-header\">"+
	  						"<h3 class=\"box-title\">"+txt
	  						"</h3>"+"</div></div>";
	  			rows.find(".append-card").find(".addcard-box").remove();
	  			rows.find(".append-card").append(card);
	  			rows.find(".box-footer").show();
	  	});

	  	$(".content-wrapper").on("click","#task-edit-description",function(event) {
	  			var add_description = "<div class=\"addcard-box\"><div class=\"box box-solid\">"+
	  						"<div class=\"box-header\">"+
	  						"<textarea class=\"txt-area-card-title\" rows=\"2\" style=\"border: 0;\">"+
	  						"</textarea>"+"</div></div>"+
	  						"<button class=\"btn bg-olive margin btn-add-card\" >Add</button>"+
	  						"<button class=\"btn btn-close-card\" ><i class=\"fa fa-close\"></i></button></div>";
	  			var rows = $(this).parent(".box-solid") ; 
	  			rows.find(".append-card").append(add_card);
	  			rows.find(".box-footer").hide();
	  	});

	  	

	  	// $(".content-wrapper").on("mouseover",".card",function(event) {
	  	// 		console.log("hover");
	  			
	  	// });
	  	

	</script>
@endsection		
