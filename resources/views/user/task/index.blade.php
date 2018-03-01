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
      	<img class="icon-title" src="{{ asset('public/img/icon/icon_external_work_2.png') }}"> @lang('main.task_person')
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('main.task_person')</li>
      </ol>
    </section>
   
	
    <!-- Main content -->
    <section class="content">
      
		<input type="hidden" id="current_card_id" >
		<input type="hidden" id="room_id" value="{{ $roomId }}" >
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

		 @include('user.task.card')	

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

 <script type="text/javascript" src="{{ url('js/card/card.js') }}"></script>
 <script type="text/javascript" src="{{ url('js/user/action.js') }}"></script>
 <script type="text/javascript" src="{{ url('js/card/comment.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/due-date.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/attach.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/member.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/category.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/checklist.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/history.js') }}"></script> 
 
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
var baseRoute = "/user/"+roomId+"/task/" ;

</script>
<script >
socket.on('task', function(data){
	if (typeof data==='string'){
	    data = JSON.parse(data);
	}
   	console.log('[task]',data);
   	var currentCardId =  $("#current_card_id").val();

    if(data.task_id != null){
    	updateListCard(data)
    }

 	if(data.task_lastest_category_id!=null){
 		if(data.task_category==null){ //--- ถ้าข้อมูลถูกลบออก ต้องเอาออกจาก หน้า list
 			console.log('[task] task_category==null ');
 			$(".show-content").find('.box-id').each(function(index, el) {
	            if($(this).val()==data.task_id){
	                var ele = $(this).closest('.show-content').find('.category-label');
	                ele.hide();
	            }
	        });

 			if($("#task_category").length >0){
 				$("#task_category").hide();
 			}
 		}else{
 			createTaskCategory(data.task_category,data.task_id);
 		}
    }
    if(data.task!=null&&data.task_id==currentCardId){
    	createTaskCard(data.task);
    	$('.box-id').each(function(index, el) {
	            if($(this).val()==data.task.id){
	            	console.log(data.task.id,data.task.status_color);
	                var ele = $(this).closest('.show-content');
	                ele.css({'border-left': '5px solid '+data.task.status_color });
	                ele.find('small.label').css({ 'background':data.task.status_color }).text(data.task.status_txt);
	            }
	    });

    }
    if(data.task_attachs!=null&&data.task_id==currentCardId){
    	console.log('[task] task_attachs  ',data.task_attachs);
    	createTaskAttachment(data.task_attachs);
    }
    if(data.task_checklists!=null&&data.task_id==currentCardId){
    	createTaskChecklist(data.task_checklists,data.task_id);
    }
    if(data.task_comments!=null&&data.task_id==currentCardId){
    	createTaskComment(data.task_comments);
    }
    if(data.task_historys!=null&&data.task_id==currentCardId){
    	createTaskTableHistory(data.task_historys);
    }
    if(data.task_members!=null&&data.task_id==currentCardId){
    	createTaskMember(data.task_members,data.task_id);
    }
    // if(data.status!=null){
    // 	createTaskSetInit(data.status);
    // }
    
    if(data.delete_task_id!=null){
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
					$(this).click();
				}

			}) 
		@endif


	}); 

	var systemRoute = "/task/" ;
	var socketRoute = "task" ;
</script>

@endsection		
