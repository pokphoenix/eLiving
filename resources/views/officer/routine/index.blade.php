@extends('main.layouts.main')
@section('style')
<link rel="stylesheet" href="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link href="{{ url('plugins/fullcalendar/fullcalendar.min.css') }}" rel='stylesheet' />
<link href="{{ url('plugins/fullcalendar/fullcalendar.print.min.css') }}" rel='stylesheet' media='print' />
<style>

  /*body {
    margin: 40px 10px;
    padding: 0;
    font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    font-size: 14px;
  }*/

  #calendar {
    max-width: 900px;
    margin: 0 auto;
  }

</style>
@endsection
@section('content-wrapper')
<!-- data-toggle="modal" data-target="#modal-card-content" -->
	

	
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      	<img class="icon-title" src="{{ asset('public/img/icon/icon_manage_2.png') }}"> Routine Schedule
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Task</li>
      </ol>
    </section>
   
	
    <!-- Main content -->
    <section class="content">
      	<input type="hidden" id="current_card_id" >
      	<input type="hidden" id="before_repeat_type" >
      	<div class="row">
      		<div class="col-sm-12">
      			<button class="btn btn-primary btn-flat btn-create-routine" data-toggle="modal" data-target="#modal_routine" >Create New Routine</button>
      			<BR>
      			<BR>
      		</div>
      	</div>
      	<div class="row">
      		<section class="col-md-3">
				<div class="box box-solid bg-rm-routine-active box-daily">
					
	            	<div class="box-header">
	            		<i class="fa fa-signal"></i>
	            	   <h3 class="box-title">
	            	   		Daily
	            	   </h3>
	            	</div>
		            <div class="box-body bg-rm-routine card-width" >
	

							@if(count($cards)>0)
							@foreach($cards as $card)
								@if($card['repeat_type']==1 )
								<div class="box box-solid card show-content" >
									
						            <div class="box-header">
						              	<h3 class="box-title">{{ $card['title'] }}</h3>
						              	<input type="hidden" class="box-id" value="{{  $card['id'] }}">
						              	<div class="box-tools pull-right card-btn-edit">
							                <button type="button" class="btn btn-box-tool" >
							                	<i class="fa fa-edit"></i>
							                </button>
							            </div>
						            </div>
						            <div class="box-body">
						        	</div>
						        </div>
						        @endif
							@endforeach
						@endif
					
		            </div>
		            <!-- /.chat -->
		            <div class="box-footer bg-rm-routine">
		              	&nbsp;
		            </div>

	          </div>
			</section>

			<section class="col-md-3">
				<div class="box box-solid bg-rm-routine-active box-weekly">
					
	            	<div class="box-header">
	            		<i class="fa fa-signal"></i>
	            	   <h3 class="box-title">
	            	   		Weekly
	            	   </h3>
	            	</div>
		            <div class="box-body bg-rm-routine card-width">
						<div class="">

							@if(count($cards)>0)
							@foreach($cards as $card)
								@if($card['repeat_type']==2 )
								<div class="box box-solid card show-content" >
						            <div class="box-header">
						              	<h3 class="box-title">{{ $card['title'] }}</h3>
						              	<input type="hidden" class="box-id" value="{{  $card['id'] }}">
						              	<div class="box-tools pull-right card-btn-edit">
							                <button type="button" class="btn btn-box-tool" >
							                	<i class="fa fa-edit"></i>
							                </button>
							            </div>
						            </div>
						            <div class="box-body">
						        	</div>
						        </div>
						        @endif
							@endforeach
						@endif
						</div>
		            </div>
		            <!-- /.chat -->
		            <div class="box-footer bg-rm-routine">
		              	&nbsp;
		            </div>

	          </div>
			</section>
			
			<section class="col-md-3">
				<div class="box box-solid bg-rm-routine-active box-monthly">
					
	            	<div class="box-header">
	            		<i class="fa fa-signal"></i>
	            	   <h3 class="box-title">
	            	   		Monthly
	            	   </h3>
	            	</div>
		            <div class="box-body bg-rm-routine card-width">
						<div class="">

							@if(count($cards)>0)
							@foreach($cards as $card)
								@if($card['repeat_type']==3 )
								<div class="box box-solid card show-content" >
									<div><img src="" class="img-responsive"></div>
						            <div class="box-header">
						              	<h3 class="box-title">{{ $card['title'] }}</h3>
						              	<input type="hidden" class="box-id" value="{{  $card['id'] }}">
						              	<div class="box-tools pull-right card-btn-edit">
							                <button type="button" class="btn btn-box-tool" >
							                	<i class="fa fa-edit"></i>
							                </button>
							            </div>
						            </div>
						            <div class="box-body">
						        	</div>
						        </div>
						        @endif
							@endforeach
						@endif
						</div>
		            </div>
		            <!-- /.chat -->
		            <div class="box-footer bg-rm-routine">
		              	&nbsp;
		            </div>

	          </div>
			</section>

			<section class="col-md-3">
				<div class="box box-solid bg-rm-routine-active box-yearly">
					
	            	<div class="box-header">
	            		<i class="fa fa-signal"></i>
	            	   <h3 class="box-title">
	            	   		Yearly
	            	   </h3>
	            	</div>
		            <div class="box-body bg-rm-routine card-width" >

						@if(count($cards)>0)
							@foreach($cards as $card)
								@if($card['repeat_type']==4 )
								<div class="box box-solid card show-content" >
						            <div class="box-header">
						              	<h3 class="box-title">{{ $card['title'] }}</h3>
						              	<input type="hidden" class="box-id" value="{{  $card['id'] }}">
						              	<div class="box-tools pull-right card-btn-edit">
							                <button type="button" class="btn btn-box-tool" >
							                	<i class="fa fa-edit"></i>
							                </button>
							            </div>
						            </div>
						            <div class="box-body">
						        	</div>
						        </div>
						        @endif
							@endforeach
						@endif
					
		            </div>
		            <!-- /.chat -->
		            <div class="box-footer bg-rm-routine">
		              	&nbsp;
		            </div>

	          </div>
			</section>
      	</div>
		

		
    </section>
    <!-- /.content -->

  <!-- /.content-wrapper -->

<div id="modal_routine" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Create New Routine</h4>
      </div>
      <div class="modal-body">
			<input type="hidden" id="card_insert" >
			<form id="form-routine">
				
             	
				
				<div class="box box-info">
		            <div class="box-header with-border">
		              <h3 class="box-title">ทั่วไป</h3>
		            </div>
		            <div class="box-body">
		                <div class="form-group">
		                    <label for="exampleInputPassword1">ชื่อ</label>
		                    <input type="text" class="form-control" id="title" name="title" placeholder="ชื่องาน"  value="{{ isset($edit) ? $data['name'] : old('name') }}" >
		                </div>
		                <div class="form-group">
		                    <label for="exampleInputPassword1">หมวดหมู่</label>
		                    <select class="form-control" id="category_id" name="category_id" >
								<option></option>
								@foreach($routineCategory as $category)
								<option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
								@endforeach
		                    </select>
		                </div>
		                <div class="checkbox">
		                  <label>
		                    <input type="checkbox" id="is_all_day" name="is_all_day"> All Day
		                  </label>
		                </div>
		             	<div class="row routine-time" >
		             		<div class="form-group col-sm-6">
			                    <label for="exampleInputPassword1">Starts</label>
			                    <input type="text" class="form-control" id="started_at" name="started_at"  value="" >
			                </div>
			                <div class="form-group col-sm-6">
			                    <label for="exampleInputPassword1">Ends</label>
			                    <input type="text" class="form-control" id="ended_at" name="ended_at"  value="" >
			                </div>
		             	</div>
		              </div>
		        </div>

		        <div class="box box-info">
		            <div class="box-header with-border">
		              <h3 class="box-title">Repeat</h3>
		            </div>
		            <div class="box-body">
		                <div class="form-group">
		                   
		                    <label class="radio-inline"><input type="radio" name="repeat_type" value="1"  >Daily</label>
							<label class="radio-inline"><input type="radio" name="repeat_type" value="2">Weekly</label>
							<label class="radio-inline"><input type="radio" name="repeat_type" value="3">Monthly</label>
							<label class="radio-inline"><input type="radio" name="repeat_type" value="4">Yearly</label>
		                </div>
		                
		             	<div class="row routine-repeat-time">
		             		
			                <div class="form-group col-sm-12">
			                    <label for="exampleInputPassword1">End Repeat</label>
			                    <div class="checkbox">
				                  <label>
				                    <input type="checkbox" id="is_never" name="is_never" checked="">Never
				                  </label>
				                </div>
			                    <input type="text" class="form-control" id="repeat_ended_at" name="repeat_ended_at" placeholder="ชื่อห้อง"  value="{{ isset($edit) ? $data['name'] : old('name') }}" >
			                </div>
		             	</div>
		              </div>
		        </div>


				<button type="submit" class="btn btn-primary " >Save</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</form>

        

      </div>
      <!-- <div class="modal-footer">
      	
        
      </div> -->
    </div>

  </div>
</div>


@endsection

@section('javascript')
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script src="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ url('plugins/fullcalendar/fullcalendar.min.js') }}"></script>
<script src="{{ url('plugins/fullcalendar/locale-all.js') }}"></script>
<script type="text/javascript">
$('#started_at,#ended_at').daterangepicker({
    "singleDatePicker": true,
    // showDropdowns: true,
    "timePicker": true,
    "timePicker24Hour":true,
    locale: {
        format: 'YYYY-MM-DD H:mm'
    },
    "opens": "left"
});
$('#repeat_ended_at').daterangepicker({
    "singleDatePicker": true,
    "showDropdowns": true,
    "timePicker": true,
    "timePicker24Hour":true,
    locale: {
        format: 'YYYY-MM-DD H:mm'
    },
    "opens": "left",
    "drops": "up"
});


var validator = $("#form-routine").validate({
      rules: {
        title: {
          required: true,
          maxlength: 255,
        },
        category_id: "required",
        'repeat_type': {
          required: true,
        },
      },
      messages: {
        title: "Please enter your firstname",
        category_id: "Please enter your lastname",
        repeat_type:"Please enter your  ID card",
      },
      errorElement: "em",
      errorPlacement: function ( error, element ) {

          // Add the `help-block` class to the error element
          error.addClass( "help-block" );

          // Add `has-feedback` class to the parent div.form-group
          // in order to add icons to inputs
          element.parents( ".form-group" ).addClass( "has-feedback" );
           if ( element.is(":radio") ) 
            {
                error.appendTo( element.parents('.container') );
            }else  if ( element.prop( "type" ) === "checkbox" ) {
	            error.insertAfter( element.parent( "label" ) );
	          } 
            else 
            { // This is the default behavior 
                error.insertAfter( element );
            }
         

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
      },
      submitHandler:function(form) {

      		var route = "/routine?api_token="+api_token ;
      		var method = 'POST';
      		if($("#card_insert").val()=="false"){
      			var cardId = $("#current_card_id").val();
      			route = "/routine/"+cardId+"?api_token="+api_token ;
      			method = 'PUT';
      		}

      		
      		data = $(form).serialize() ;
      		ajaxPromise(method,route,data).done(function(res){
      			console.log(res);
      			appendCard(res.routine);
      			$("#modal_routine").modal("toggle");
      		})

      	  
      }

    });

$(function() {
  

    $(document).on("click",".show-content",function(event) {
		var boxId = $(this).find(".box-id").val() ;
		console.log("click",boxId);
		var title = $(this).find(".box-title").text() ; 
		var modal = $('#modal-card-content') ;
		modal.find('.modal-title span').text(title);
		$("#first_open_card").val('false');
		window.history.pushState("object or string", title , $("#baseUrl").val()+'/routine/'+boxId);

		clearRoutine();

		$("#current_card_id").val(boxId);

		var route = "/routine/"+boxId+"?api_token="+api_token ;
		var data = "" ;
		ajaxPromise('GET',route,data).done(function(data){
			createRoutine(data.routine);
		})
		// if($("#table-quatation").length){
		// 	$("#section-company").show();
		// }
	});

	$(document).on( 'click', '.btn-create-routine', function() {
		console.log('btn-create-routine > click');
		$("#repeat_ended_at").hide();
		clearRoutine();
	});

  });

function clearRoutine(){
	$("#modal_routine .modal-title").text('Create New Routine');
	$("#form-routine")[0].reset();
	$("#before_repeat_type").val("");
	validator.resetForm();
	$("#is_never").attr("checked",true);
	$("#is_all_day").attr("checked",false);
	$(".routine-time").show();
	$("#card_insert").val("true");

	$("input[name=repeat_type]").attr("checked",false);

	var started_at = new Date();	
	$('#started_at').data('daterangepicker').setStartDate(started_at);
	$('#started_at').data('daterangepicker').setEndDate(started_at);
	var ended_at = new Date();
	$('#ended_at').data('daterangepicker').setStartDate(ended_at);
	$('#ended_at').data('daterangepicker').setEndDate(ended_at);

	var repeat_ended_at = new Date();	
	$('#repeat_ended_at').data('daterangepicker').setStartDate(repeat_ended_at);
	$('#repeat_ended_at').data('daterangepicker').setEndDate(repeat_ended_at);


	$("#form-routine .form-group").find('em').remove();
    $("#form-routine .form-group").find('span').remove();
    $("#form-routine .form-group").removeClass( "has-error" ).removeClass( "has-success" );
}

   

function createRoutine(data){

	$("#modal_routine .modal-title").text('Edit Routine');

	$("#title").val(data.title);

	$("#category_id").val(data.category_id);
	$("#before_repeat_type").val(data.repeat_type);

	var started_at = new Date();	
	if(data.started_at!=null){
		started_at = new Date(data.started_at);
	}
	$('#started_at').data('daterangepicker').setStartDate(started_at);
	$('#started_at').data('daterangepicker').setEndDate(started_at);

	var ended_at = new Date();	
	if(data.ended_at!=null){
		ended_at = new Date(data.ended_at);
	}
	$('#ended_at').data('daterangepicker').setStartDate(ended_at);
	$('#ended_at').data('daterangepicker').setEndDate(ended_at);

	var repeat_ended_at = new Date();	
	if(data.repeat_ended_at!=null){
		repeat_ended_at = new Date(data.repeat_ended_at);
	}
	$('#repeat_ended_at').data('daterangepicker').setStartDate(repeat_ended_at);
	$('#repeat_ended_at').data('daterangepicker').setEndDate(repeat_ended_at);

	$("#is_never").attr("checked",false);
	$("#repeat_ended_at").show();
	if(data.is_never){
		$("#is_never").attr("checked",true);
		$("#repeat_ended_at").hide();
	}

	$("#is_all_day").attr("checked",false);
	$(".routine-time").show();
	if(data.is_all_day){
		$("#is_all_day").attr("checked",true);
		$(".routine-time").hide();
	}

	$("input[name=repeat_type]").each(function(index, el) {
		if($(this).val()==data.repeat_type)	{
			$(this).attr("checked",true);
		}
	});

	$("#card_insert").val("false");
	


	// $("#title").val(data.title);
	// $("#title").val(data.title);
	// $("#title").val(data.title);
	// $("#title").val(data.title);

	$("#modal_routine").modal("toggle");
}

function appendCard(data){
	var repeat_type = data.repeat_type;
	var card = "<div class=\"box box-solid card show-content\" data-toggle=\"modal\" data-target=\"#modal-card-content\">"+
			"<div class=\"box-header\">"+
			"<h3 class=\"box-title\">"+data.title+
			"</h3>"+
			"<input type=\"hidden\" class=\"box-id\" value=\""+data.id+"\" >"+
			"</div></div>";

	var ele = repeatTypeElement(repeat_type);
	console.log($("#before_repeat_type").val(),repeat_type);
	var appendCard = true;
	if ($("#before_repeat_type").val()!=repeat_type){
		var beforeElement = repeatTypeElement($("#before_repeat_type").val());
		$(beforeElement).find(".show-content").each(function(index, el) {
			if($(this).find(".box-id").val()==data.id){
				$(this).remove();
			}
		});
	}else{
		console.log($(ele).find(".show-content"))
		$(ele).find(".show-content").each(function(index, el) {
			console.log($(this).find(".box-id"),$(this).find(".box-id").val());
			if($(this).find(".box-id").val()==data.id){
				appendCard = false;
			}
		});
	}

	console.log(ele);
	console.log(card);
	if(appendCard){
		$(ele).find(".card-width").append(card);
	}
	
}
function repeatTypeElement(repeat_type){
		var ele = "";		
		if (repeat_type==1){
			ele = ".box-daily" ;
		}else if (repeat_type==2){
			ele = ".box-weekly" ;
		}else if (repeat_type==3){
			ele = ".box-monthly" ;
		}else if (repeat_type==4){
			ele = ".box-yearly" ;
		}
		return ele ;
	}
</script>
<script type="text/javascript">
	var JsonData = [
      	{
          title: 'Meeting',
          start: '2017-12-13T11:00:00',
          constraint: 'availableForMeeting', // defined below
          color: '#257e4a',
          icon : "check" 
        },
        {
          title: 'All Day Event',
          start: '2017-12-01'
        },
        {
          title: 'Long Event',
          start: '2017-12-07',
          end: '2017-12-10'
        },
        {
          id: 999,
          title: 'Repeating Event',
          start: '2017-12-09T16:00:00',
          color: '#257e4a'
        },
        {
          id: 999,
          title: 'Repeating Event',
          start: '2017-12-16T16:00:00'
          ,color: '#257e4a'
        },
        {
          title: 'Conference',
          start: '2017-12-11',
          end: '2017-12-13'
        },
        {
          title: 'Meeting',
          start: '2017-12-12T10:30:00',
          end: '2017-12-12T12:30:00'
        },
        {
          title: 'Lunch',
          start: '2017-12-12T12:00:00',
          color:'#00a65a',
          category: 'Q & A',
          category_color:'#00c0ef',
          is_check : 0 ,
          routine_id : 1
        },
        {
          title: '55 Meeting',
          start: '2017-12-12T14:30:00',
          is_check : 0 ,
        },
        {
          title: 'Happy Hour',
          start: '2017-12-12T17:30:00'
        },
        {
          title: 'Dinner',
          start: '2017-12-12T20:00:00'
        },
        {
          title: 'Birthday Party',
          start: '2017-12-13T07:00:00'
        },
        {
          title: 'Click for Google',
          url: 'http://google.com/',
          start: '2017-12-28'
        },
         // areas where "Meeting" must be dropped
        {
          id: 'availableForMeeting',
          start: '2017-12-11T10:00:00',
          end: '2017-12-11T16:00:00',
          rendering: 'background'
        },
        {
          id: 'availableForMeeting',
          start: '2017-12-13T10:00:00',
          end: '2017-12-13T16:00:00',
          rendering: 'background'
        },
         // red areas where no events can be dropped
        {
          start: '2017-12-24',
          end: '2017-12-28',
          overlap: false,
          rendering: 'background',
          color: '#ff9f89'
        },
        {
          start: '2017-12-06',
          end: '2017-12-08',
          overlap: false,
          rendering: 'background',
          color: '#ff9f89'
        }
      ];

</script>
<script type="text/javascript">
	$('.card-width').slimScroll({
    	height: '600px'
  	});

	$("#is_all_day").on("click",function(){
		if($(this).is(':checked')){
			$(".routine-time").hide();
		}else{
			$(".routine-time").show();
		}
	})
	$("#is_never").on("click",function(){
		if($(this).is(':checked')){
			$("#repeat_ended_at").hide();
			
		}else{
			
			$("#repeat_ended_at").show();
		}
	})

	$(function(){					
		@if(isset($cardId))
			$(".show-content").each(function(){
				if($(this).find(".box-id").val()== {{ $cardId }} ){
					$(this).click();
				}

			}) 
		@endif
	});

	// $(".btn-save-routine").on("click",function(){
		
	

	// 	var data = JSON.stringify( $("#form-routine").serializeArray() );
	// 	console.log('ajax-form-data',data);

	// 	// var formData = {
	// 	// 	title: $("#title").val()
	// 	// 	,category:$("#category_id").val()
	// 	// 	,start: moment.utc($("#started_at").val()).format('YYYY-MM-DDTh:mm:ss')
	// 	// 	,end: moment.utc($("#ended_at").val()).format('YYYY-MM-DDTh:mm:ss') 
	// 	// 	,routine_id: JsonData.length++
	// 	// 	,is_check:0
	// 	// }
	// 	var cnt = JsonData.length ;
	// 	console.log('cnt',cnt);
	// 	var title = $("#title").val();
	// 	var boxId = cnt++;
	// 	var formData = {
 //          title: $("#title").val()
 //          ,start:  moment.utc($("#started_at").val()).format('YYYY-MM-DDTh:mm:ss') 
	// 	  ,end: moment.utc($("#ended_at").val()).format('YYYY-MM-DDTh:mm:ss') 
	// 	  ,is_check:0
 //        }
	
	// 	JsonData.push(formData);

	// 	var repeat_type = $('input[name=repeat_type]').val();


	// 	var card = "<div class=\"box box-solid card show-content\" data-toggle=\"modal\" data-target=\"#modal-card-content\">"+
	// 			"<div class=\"box-header\">"+
	// 			"<h3 class=\"box-title\">"+title+
	// 			"</h3>"+
	// 			"<input type=\"hidden\" class=\"box-id\" value=\""+boxId+"\" >"+
	// 			"</div></div>";

	// 	var ele = "";		
	// 	if (repeat_type==1){
	// 		ele = ".box-daily" ;
	// 	}else if (repeat_type==2){
	// 		ele = ".box-weekly" ;
	// 	}else if (repeat_type==3){
	// 		ele = ".box-monthly" ;
	// 	}else if (repeat_type==4){
	// 		ele = ".box-yearly" ;
	// 	}
	// 	console.log(ele);
	// 	console.log($(ele));
	// 	console.log($(ele).find(".card-width"));
	// 	$(ele).find(".card-width").append(card);

	

		

	// 	$("#form-routine")[0].reset();
		

	// 	$('#routine-calendar-day,#routine-calendar-week,#routine-calendar-month,#routine-calendar-year').fullCalendar( 'removeEventSource', JsonData );
	// 	$('#routine-calendar-day,#routine-calendar-week,#routine-calendar-month,#routine-calendar-year').fullCalendar( 'addEventSource', JsonData );

	// 	$("#modal_routine").modal("toggle");
		

	// })



</script>

@endsection		
