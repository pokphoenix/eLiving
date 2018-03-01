@extends('main.layouts.main')
@section('style')
<link rel="stylesheet" href="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
  <!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{ url('plugins/iCheck/all.css') }}">
<style>
	.append-card table tr.show-content td { padding:0;padding-left: 50px; line-height:32px;  }
	.append-card table tr.show-content td:hover{
		cursor:pointer;
	}
	.append-card table tr.show-content td:hover  .card-btn-edit{
		display: block;
	}
	.append-card table tr.title td { padding-left:10px; 
		font-weight: bold;
		 /*text-decoration: underline;*/
		 border-bottom: 2px solid #000;
		  }
	
</style>
@endsection
@section('content-wrapper')
<!-- data-toggle="modal" data-target="#modal-card-content" -->
	

	
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      	<i class="fa fa-circle-o"></i>
      	 @lang('sidebar.suggest_system')
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('sidebar.suggest_system')</li>
      </ol>
    </section>
   
	
    <!-- Main content -->
    <section class="content">
      
		<input type="hidden" id="current_card_id" >
		<input type="hidden" id="is_user" value="1">
		<input type="hidden" id="first_open_card" value="false">
		<input type="hidden" id="asset_url" value="{{ asset('public/img/icon/') }}" >
	 	
      <!-- Main row -->
      <div class="row" >

	
		<section class="col-md-12">
			
			<div class="box box-solid bg-rm-user-task box-parent"  >
				
		            	<div class="box-header">
		            		
		            	   <h3 class="box-title">
		            	   		<button class="btn btn-primary addcard-hover"><i class="fa fa-plus"></i> @lang('task.btn_new_task')
		            	   		</button>
		            	   </h3>
		            	  
		            	</div>

			            <div class="box-body "  >
			            	<div class="addcard-box" style="display: none;">
								<div class="box box-solid" >
									<div class="box-body">
										<label>@lang('task.category')</label>
										<select class="form-control" id="category">
											<option value=""></option>
											@foreach ($taskCategory as $category)
											<option value="{{ $category['id'] }}"> {{ $category['name'] }}</option>
											@endforeach
										</select>
									</div>
									<div class="box-header">
										<textarea class="txt-area-card-title form-control" rows="2" ></textarea>
									</div>
									
								</div>
								<button class="btn bg-olive margin btn-add-card" >@lang('task.next')</button>
								<button class="btn btn-close-card" ><i class="fa fa-close"></i></button>
							</div>
							<div class="append-card">
	

							
							@if(count($tasks)>0)
							 <?php $dateTxt = ""; ?>
								@foreach($tasks as $task)
									<?php $createdAt = date('d M Y',$task['created_at']);  
									if( $createdAt!= $dateTxt){
										$dateTxt = $createdAt ; 
										echo "<h4 class=\"title\" >$dateTxt : </h4>" ;
									}
									
									?>
									<!-- <tr class="show-content">
										<td>
											<input type="hidden" class="box-id" value="1">
							              	<div class="pull-right card-btn-edit">
								                <button type="button" class="btn btn-box-tool" >
								                	<i class="fa fa-edit"></i>
								                </button>
								            </div>
										{{$task['title']}}
										<small class="label"  style="color:{{$task['status_color']}} ;" >
					                  	{{ $task['status_text'] }}</small>
										</td>
									</tr> -->

								<div class="box box-solid card show-content" style="border-left: 5px solid {{ $task['status_color'] }};">
						            <div class="box-header">
						            	<small class="category-label" data-id="{{ $task['category_id'] }}" style="color:{{ $task['category_color'] }} ;font-weight: bold;" >
					                  	{{ ucfirst($task['category_name']) }}</small>
					                  	
					                  	
						              	<h3 class="box-title">{{ $task['title'] }}</h3>
						              	<br>
						              	<small class="label"  style="background:{{ $task['status_color']  }} ;" >
					                  	{{ ucfirst($task['status_text']) }}</small>
						            </div>
						            <input type="hidden" class="box-id" value="{{ $task['id'] }}" >
					        	</div>
								@endforeach
							
								
							@endif
							</div>
			            </div>
			            <!-- /.chat -->
			           

		    </div>
			
		</section>
      </div>
      <!-- /.row (main row) -->

		 @include('user.suggest.card')	

    </section>
    <!-- /.content -->

  <!-- /.content-wrapper -->
@endsection

@section('javascript')

  <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
  <!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
<script src="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ url('plugins/iCheck/icheck.min.js')}}"></script>
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
 <script type="text/javascript" src="{{ url('js/utility/main.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/utility/autocomplete.js') }}"></script> 

 <script type="text/javascript" src="{{ url('js/user/suggest/card.js') }}"></script>
 <script type="text/javascript" src="{{ url('js/user/suggest/action.js') }}"></script>
 <script type="text/javascript" src="{{ url('js/user/suggest/comment.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/user/suggest/attach.js') }}"></script>

 <script type="text/javascript" src="{{ url('js/card/category.js') }}"></script> 
 
<script>
	$(".btn-tap").each(function(){
		$(this).on("click",function(){
			 var ele = $(this).data('toggle');
			 $(".tab-panel").hide();
			 $(ele).show();
			 
			 $(".btn-tap").removeClass('btn-primary');
			 $(this).addClass('btn-primary');
		})
		
	})
	$(document)
    .on( 'hidden.bs.modal', '.modal', function() {
        $(document.body).removeClass( 'modal-scrollbar' );
    })
    .on( 'show.bs.modal', '.modal', function() {
        if ( $(window).height() < $(document).height() ) {
            $(document.body).addClass( 'modal-scrollbar' );
        }
    });

</script>

<script type="text/javascript">
var roomId = $("#room_id").val();
var baseRoute = "/user/suggest/system/" ;

</script>
<script >
socket.on('suggest', function(data){
	if (typeof data==='string'){
	    data = JSON.parse(data);
	}
   	console.log('[task]',data);
   	var currentCardId =  $("#current_card_id").val();

    if(data.suggest_id != null){
    	updateListCard(data)
    }

 	//if(data.suggest_lastest_category_id!=null){
 		if(data.suggest_category==null){ //--- ถ้าข้อมูลถูกลบออก ต้องเอาออกจาก หน้า list
 			console.log('[task] suggest_category==null ');
 			$(".show-content").find('.box-id').each(function(index, el) {
	            if($(this).val()==data.suggest_id){
	                var ele = $(this).closest('.show-content').find('.category-label');
	                ele.hide();
	            }
	        });

 			if($("#suggest_category").length >0){
 				$("#suggest_category").hide();
 			}
 		}else{
 			createTaskCategory(data.suggest_category,data.suggest_id);
 		}
    //}
    if(data.suggest!=null&&data.suggest_id==currentCardId){
    	createTaskCard(data.suggest);
    	$('.box-id').each(function(index, el) {
	            if($(this).val()==data.suggest.id){
	            	console.log(data.suggest.id,data.suggest.status_color);
	                var ele = $(this).closest('.show-content');
	                ele.css({'border-left': '5px solid '+data.suggest.status_color });
	                ele.find('small.label').css({ 'background':data.suggest.status_color }).text(data.suggest.status_txt);
	            }
	    });

    }
    if(data.suggest_attachs!=null&&data.suggest_id==currentCardId){
    	console.log('[task] suggest_attachs  ',data.suggest_attachs);
    	createTaskAttachment(data.suggest_attachs);
    }
    if(data.suggest_comments!=null&&data.suggest_id==currentCardId){
    	createTaskComment(data.suggest_comments);
    }
    if(data.delete_suggest_id!=null){
    	deleteTask(data);
    }
   


})
	
</script>
<script type="text/javascript">
$('.btn_start_task').daterangepicker({
    "singleDatePicker": true,
    "timePicker": true,
    "timePicker24Hour":true,
    showDropdowns: true,
   
    locale: {
        format: 'MM/DD/YYYY H:mm'
    },
    "opens": "left"
   
}, function(start, end, label) {
	var startDate = start.format('YYYY-MM-DD H:mm');
	var data = {start_task_at:startDate} ;
    UpdateTask(data).done(function(res){
    	
    })
});
</script>


<script type="text/javascript">
	$('#card_new,#card_reject,#card_in_progress,#card_pending').slimScroll({
    	height: '250px'
  	});
  	$('#card_accept,#card_done').slimScroll({
    	height: '600px'
  	});

	$(function(){					
		@if(isset($taskId))
			$(".show-content").each(function(){
				if($(this).find(".box-id").val()== {{ $taskId }} ){
					console.log("click");
					$(this).click();
				}

			}) 
		@endif
	}); 
</script>

@endsection		
