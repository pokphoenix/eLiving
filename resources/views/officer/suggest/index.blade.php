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
	.img-responsive {
		margin:0 auto !important;
	}
	.content > .row {
  overflow-x: auto;
  white-space: nowrap;
}
</style>
@endsection
@section('content-wrapper')
<!-- data-toggle="modal" data-target="#modal-card-content" -->
	

	
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      <i class="fa fa-circle-o"></i> @lang('sidebar.suggest_system')
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">@lang('sidebar.suggest_system')</li>
      </ol>
    </section>
   
	
    <!-- Main content -->
    <section class="content">
      
		<input type="hidden" id="current_card_id" >
		<input type="hidden" id="first_open_card" value="false">
		<input type="hidden" id="task_created_by" value="">
		<input type="hidden" id="asset_url" value="{{ asset('public/img/icon/') }}" >
	<div class="row">
		<div class="col-sm-12">
	         @include('widgets.card.filter')
	 	</div>
	</div>
	 	
		

      <!-- Main row -->
      <div class="row" >

	
		<section class="col-md-3">
			
			<div class="box box-solid bg-rm-new-active box-parent tab-panel" >
				
		            	<div class="box-header">
		            		<img class="icon-title" src="{{ asset('public/img/icon/icon_new.png') }}">
		            	   <h3 class="box-title">
		            	   		@lang('task.title_task_new') 
		            	   		<span class="title-new"></span>
		            	   </h3>
		            	  
		            	</div>
			            <div class="box-body bg-rm-new" id="card_new">
							<div class="append-card">
								
							@if(count($tasks)>0)
								@foreach($tasks as $task)
									@if($task['status']==1)
									<div class="box box-solid card show-content" >
										@if(isset($task['file_path']))
										<div><img src="{{ $task['file_path'] }}" class="img-responsive"></div>
										@endif
							            <div class="box-header">
							              	<h3 class="box-title">{{ $task['title'] }}</h3>
							              	<input type="hidden" class="box-id" value="{{  $task['id'] }}">
							              	<div class="box-tools pull-right card-btn-edit">
								                <button type="button" class="btn btn-box-tool" >
								                	<i class="fa fa-edit"></i>
								                </button>
								            </div>
							            </div>
							            <div class="box-body">
							             
						                  
						                   	@if ($task['category_id']!=0)
						                  	<small class="label category-label" data-id="{{$task['category_id']}}" style="color:#FFF; background:{{$task['category_color']}} " >
						                  	{{ $task['category_name'] }}</small>
						                  
						               	  	@endif
						                 
											@if(isset($task['members']))
											 <div class="card-member pull-right">
												@foreach ($task['members'] as $member)
													<img data-id="{{ $member['member_id'] }}" src="{{ $member['member_img'] }}" class="img-circle" height="25"  title="{{ $member['member_name']}}" >
												@endforeach
											</div>
											@endif
											
						         			@if (isset($task['doned_at']))
						                   	<small class="label label-success done-at-label">
						                  	{{  date('d/m/Y',strtotime($task['doned_at'])) }}</small>
						               		@endif
						               		
							                @if (isset($task['due_dated_at']))
							                <BR>
							                <small class="due-date-label label {{ labelClass($task) }}">
						                  	<i class="fa {{($task['due_dated_complete']==1) ? 'fa-check-square-o':'fa-clock-o'}}"></i> 
						                  	{{ dueDateTime($task) }}</small>
						               		@endif
											
											
										
						          
						               		
											
							        	</div>
							        </div>
							        @endif
								@endforeach
							@endif
							</div>
			            </div>
			            <!-- /.chat -->
			            <div class="box-footer bg-rm-new">
			              	&nbsp;
			            </div>

		    </div>
		
          	
		</section>
		
		<section class="col-md-3">
			<div class="box box-solid bg-rm-accept-active box-parent">
				
            	<div class="box-header">
            		<img class="icon-title" src="{{ asset('public/img/icon/icon_accept.png') }}" >
            	   <h3 class="box-title">
            	   		@lang('task.title_task_accept')
            	   		<span class="title-accept"></span>
            	   </h3>
            	  
            	</div>
	            <div class="box-body bg-rm-accept" id="card_accept">
					<div class="append-card">

						@if(count($tasks)>0)
						@foreach($tasks as $task)
							@if($task['status']==3 )
							<div class="box box-solid card show-content" >
								@if(isset($task['file_path']))
										<div><img src="{{ $task['file_path'] }}" class="img-responsive"></div>
										@endif
					            <div class="box-header">
					              	<h3 class="box-title">{{ $task['title'] }}</h3>
					              	<input type="hidden" class="box-id" value="{{  $task['id'] }}">
					              	<div class="box-tools pull-right card-btn-edit">
						                <button type="button" class="btn btn-box-tool" >
						                	<i class="fa fa-edit"></i>
						                </button>
						            </div>
					            </div>
					            <div class="box-body">
					             
				                  
				                   	@if ($task['category_id']!=0)
				                  	<small class="label category-label" data-id="{{$task['category_id']}}" style="color:#FFF; background:{{$task['category_color']}} " >
				                  	{{ $task['category_name'] }}</small>
				                  
				               	  	@endif
				                 
									@if(isset($task['members']))
									 <div class="card-member pull-right">
										@foreach ($task['members'] as $member)
											<img data-id="{{ $member['member_id'] }}" src="{{ $member['member_img'] }}" class="img-circle" height="25"  title="{{ $member['member_name']}}" >
										@endforeach
									</div>
									@endif
									
				         			@if (isset($task['doned_at']))
				                   	<small class="label label-success done-at-label">
				                  	{{  date('d/m/Y',strtotime($task['doned_at'])) }}</small>
				               		@endif
				               		
					                @if (isset($task['due_dated_at']))
					                <BR>
					                <small class="due-date-label label {{ labelClass($task) }}">
				                  	<i class="fa {{($task['due_dated_complete']==1) ? 'fa-check-square-o':'fa-clock-o'}}"></i> 
				                  	{{ dueDateTime($task) }}</small>
				               		@endif
									
								
				          
				               		
									
					        	</div>
					        </div>
					        @endif
						@endforeach
					@endif
					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer bg-rm-accept">
	              	&nbsp;
	            </div>

          </div>
		</section>
		
		<section class="col-md-3">
			<div class="box box-solid bg-rm-cancel-active box-parent">
				
            	<div class="box-header">
            		<i class="fa fa-window-close-o"></i>
            	   <h3 class="box-title">
            	   		@lang('task.title_task_cancel')
            	   		<span class="title-cancel"></span>
            	   </h3>
            	  
            	</div>
	            <div class="box-body bg-rm-cancel" id="card_reject">
					<div class="append-card">
						
					@if(count($tasks)>0)
						@foreach($tasks as $task)
							@if($task['status']==4)
							<div class="box box-solid card show-content" >
								@if(isset($task['file_path']))
										<div><img src="{{ $task['file_path'] }}" class="img-responsive"></div>
										@endif
					            <div class="box-header">
					              	<h3 class="box-title">{{ $task['title'] }}</h3>
					              	<input type="hidden" class="box-id" value="{{  $task['id'] }}">
					              	<div class="box-tools pull-right card-btn-edit">
						                <button type="button" class="btn btn-box-tool" >
						                	<i class="fa fa-edit"></i>
						                </button>
						            </div>
					            </div>
					            <div class="box-body">
					             
				                  
				                   	@if ($task['category_id']!=0)
				                  	<small class="label category-label" data-id="{{$task['category_id']}}" style="color:#FFF; background:{{$task['category_color']}} " >
				                  	{{ $task['category_name'] }}</small>
				                  
				               	  	@endif
				                 
									@if(isset($task['members']))
									 <div class="card-member pull-right">
										@foreach ($task['members'] as $member)
											<img data-id="{{ $member['member_id'] }}" src="{{ $member['member_img'] }}" class="img-circle" height="25"  title="{{ $member['member_name']}}" >
										@endforeach
									</div>
									@endif
									
				         			@if (isset($task['doned_at']))
				                   	<small class="label label-success done-at-label">
				                  	{{  date('d/m/Y',strtotime($task['doned_at'])) }}</small>
				               		@endif
				               		
					                @if (isset($task['due_dated_at']))
					                <BR>
					                <small class="due-date-label label {{ labelClass($task) }}">
				                  	<i class="fa {{($task['due_dated_complete']==1) ? 'fa-check-square-o':'fa-clock-o'}}"></i> 
				                  	{{ dueDateTime($task) }}</small>
				               		@endif
									
									
									
				               		
									
					        	</div>
					        </div>
					        @endif
						@endforeach
					@endif

					

					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer bg-rm-cancel">
	              	&nbsp;
	            </div>

          </div>
		</section>
		
		

	
		

      </div>

		@include('officer.suggest.card')	
	
		<div id="modal_task_filter" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">@lang('task.filter')</h4>
      </div>
      <div class="modal-body">
      		<div>
      			<input type="text" class="form-control" id="search_filter">
      		</div>
          <hr>
          <h4 class="title">@lang('task.category')</h4>
          <div class="task-no-category" >
            <div class="media">
              <div class="media-left media-middle">
                <div style="background:#ccc;" class="img-rounded"></div>
              </div>
              <div class="media-body">

                <h5 class="media-heading">
                @lang('task.no_category')</h5>
                
              </div>
            </div>
          </div>
    			<div id="filter_category_list">
            
    			</div>
           <div class="break-line"></div>
          
           
          <div>
            <div class="media clear-filter">
              <div class="media-left media-middle">
               <div style="background:#ccc;" class="img-rounded"></div>
              </div>
              <div class="media-body">
                <h5 class="media-heading">@lang('task.clear_filter')</h5>
                <input type="hidden" class="search-id" value="1">
              </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('main.close')</button>
      </div>
    </div>

  </div>
</div>
		
		
		
		

		

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
<script type="text/javascript" src="{{ url('js/card/filter.js') }}"></script> 

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
<script >
socket.on('suggest', function(data){
	if (typeof data==='string'){
	    data = JSON.parse(data);
	}
   	// console.log('[task]',data);
   	var currentCardId =  $("#current_card_id").val();
   	if(data.suggest_id != null){
    	updateListCard(data);
    }

   	if(data.task!=null&&data.suggest_category!=null&&data.suggest_comments.length==0&&data.suggest_checklists.length==0&&data.suggest_members.length==0){
    	appendCard('#card_new',data.task.id,data.task.title,data.suggest_category.color,data.suggest_category.name_en);
    }

   	if(data.task!=null&&data.suggest_id==currentCardId){
    	createTaskCard(data.task);
    }
 	// if(data.suggest_lastest_category_id!=null){
 		if(data.suggest_category==null&&data.suggest_id==currentCardId){ //--- ถ้าข้อมูลถูกลบออก ต้องเอาออกจาก หน้า list
 			console.log('[task] task_category==null ');
 			$(".show-content").find('.box-id').each(function(index, el) {
	            if($(this).val()==data.suggest_id){
	                var ele = $(this).closest('.show-content').find('.category-label');
	                ele.hide();
	            }
	        });

 			if($("#task_category").length >0){
 				$("#task_category").hide();
 			}
 		}else{
 			createTaskCategory(data.suggest_category,data.suggest_id);
 		}
    // }
    
    if(data.suggest_attachs!=null&&data.suggest_id==currentCardId){
    	createTaskAttachment(data.suggest_attachs);
    }
   
    if(data.suggest_comments!=null&&data.suggest_id==currentCardId){
    	createTaskComment(data.suggest_comments);
    }
   
   
    
    if(data.delete_task_id!=null){
    	deleteTask(data);
    }
   


})
	
</script>



<script type="text/javascript">
	
  	$('#card_accept,#card_new,#card_reject').slimScroll({
    	height: '600px'
  	});
  	countCardMenu();

	$(function(){					
		@if(isset($taskId))
			$(".show-content").each(function(){
				if($(this).find(".box-id").val()== {{ $taskId }} ){
					$(this).click();
				}

			}) 
		@endif
	});

	var baseRoute = "/suggest/system/" ;
	var systemRoute = "/suggest/system/" ;
	var socketRoute = "suggest" ;


	


	  	// autosize(document.querySelectorAll('textarea'));
	  	// autosize(document.getElementById("description-edit-body-text"));


</script>

@endsection		
