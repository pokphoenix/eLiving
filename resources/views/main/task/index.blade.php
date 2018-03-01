@extends('main.layouts.main')
@section('style')
<link rel="stylesheet" href="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
  <!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{ url('plugins/iCheck/all.css') }}">
<style type="text/css">
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
      	<img class="icon-title" src="{{ asset('public/img/icon/icon_internal_work_2_edit.png') }}">
      	@lang('task.title_internal_task')
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('task.title_internal_task')</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      
		<input type="hidden" id="current_card_id" >
		<input type="hidden" id="first_open_card" value="false">
		<input type="hidden" id="task_internal" value="1" >
		<input type="hidden" id="asset_url" value="{{ asset('public/img/icon/') }}" >

	 	<div>
		 	<button class="btn btn-flat btn-primary btn-tap" data-toggle="#tap_1" >@lang('task.this_week')</button>
	        <button class="btn btn-flat  btn-tap" data-toggle="#tap_2">@lang('task.all')</button>

	        @include('widgets.card.filter')
	        
	 	</div>
		

      <!-- Main row -->
      <div class="row" >

	
		<section class="col-md-3">
			
			<div class="box box-solid bg-rm-new-active box-parent tab-panel" id="tap_1">
				
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
									@if($task['status']==1 && (strtotime('monday this week')<=strtotime($task['start_task_at'])&&
									strtotime($task['start_task_at'])<=strtotime('sunday this week')
									))
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
													<img src="{{ $member['member_img'] }}" data-id="{{ $member['member_id'] }}" class="img-circle" height="25"  title="{{ $member['member_name']}}" >
												@endforeach
											</div>
											@endif
											
						         			@if (isset($task['doned_at']))
						                   	<small class="label label-success done-at-label">
						                  	{{  date('d M Y',strtotime($task['doned_at'])) }}</small>
						               		@endif
						               		
							                @if (isset($task['due_dated_at']))
							                <BR>
							                <small class="due-date-label label {{ labelClass($task) }}">
						                  	<i class="fa {{($task['due_dated_complete']==1) ? 'fa-check-square-o':'fa-clock-o'}}"></i> 
						                  	{{ dueDateTime($task) }}</small>
						               		@endif
											
											
											@if($task['checklist_total']>0)
											<small class="card-checklist">
												<i class="fa fa-check-square-o"></i> {{  $task['checklist_success']." / ".$task['checklist_total'] }}
											</small>
											@endif
						          
						               		
											
							        	</div>
							        </div>
							        @endif
								@endforeach
							@endif
							</div>
			            </div>
			            <!-- /.chat -->
			            <div class="box-footer bg-rm-new addcard-hover">
			              	@lang('task.btn_new_task')
			            </div>

		    </div>
			<div class="box box-solid bg-rm-new-active box-parent tab-panel" id="tap_2" style="display: none;">
				
            	<div class="box-header">
            		<img class="icon-title" src="{{ asset('public/img/icon/icon_new.png') }}">
            	   <h3 class="box-title">
            	   		@lang('task.title_task_new')
            	   		<span class="tab-2-title"></span>
            	   </h3>
            	  
		          
            	</div>
	            <div class="box-body bg-rm-new" id="card_new_2">
					<div class="append-card">
						
					@if(count($tasks)>0)
						@foreach($tasks as $task)
							@if($task['status']==1 
							&& (!(strtotime('monday this week')<=strtotime($task['start_task_at'])
							&& strtotime($task['start_task_at'])<=strtotime('sunday this week')) || is_null($task['start_task_at']) ) )
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
						                  	{{  date('d M Y',strtotime($task['doned_at'])) }}</small>
						               		@endif
						               		
							                @if (isset($task['due_dated_at']))
							                <BR>
							                <small class="due-date-label label {{ labelClass($task) }}">
						                  	<i class="fa {{($task['due_dated_complete']==1) ? 'fa-check-square-o':'fa-clock-o'}}"></i> 
						                  	{{ dueDateTime($task) }}</small>
						               		@endif
											
											
											@if($task['checklist_total']>0)
											<small class="card-checklist">
												<i class="fa fa-check-square-o"></i> {{  $task['checklist_success']." / ".$task['checklist_total'] }}
											</small>
											@endif
						          
						               		
											
							        	</div>
							        </div>
					        @endif
						@endforeach
					@endif
					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer bg-rm-new addcard-hover">
	              	@lang('task.btn_new_task')
	            </div>

            </div>

			
			

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
											 <div class="card-member  pull-right">
												@foreach ($task['members'] as $member)
													<img data-id="{{ $member['member_id'] }}" src="{{ $member['member_img'] }}" class="img-circle" height="25"  title="{{ $member['member_name']}}" >
												@endforeach
											</div>
											@endif
											
						         			@if (isset($task['doned_at']))
						                   	<small class="label label-success done-at-label">
						                  	{{  date('d M Y',strtotime($task['doned_at'])) }}</small>
						               		@endif
						               		
							                @if (isset($task['due_dated_at']))
							                <BR>
							                <small class="due-date-label label {{ labelClass($task) }}">
						                  	<i class="fa {{($task['due_dated_complete']==1) ? 'fa-check-square-o':'fa-clock-o'}}"></i> 
						                  	{{ dueDateTime($task) }}</small>
						               		@endif
											
											
											@if($task['checklist_total']>0)
											<small class="card-checklist">
												<i class="fa fa-check-square-o"></i> {{  $task['checklist_success']." / ".$task['checklist_total'] }}
											</small>
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
		
		<section class="col-md-3">
			<div class="box box-solid bg-rm-accept-active box-parent">
				
            	<div class="box-header">
            		<img class="icon-title" src="{{ asset('public/img/icon/icon_todo.png') }}" >
            		
            	   <h3 class="box-title">
            	   		@lang('task.title_task_todo')
            	   		<span class="title-accept"></span>
            	   </h3>
            	   
            	</div>
	            <div class="box-body bg-rm-accept" id="card_accept">
					<div class="append-card">

						@if(count($tasks)>0)
						@foreach($tasks as $task)
							@if($task['status']==2)
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
						                  	{{  date('d M Y',strtotime($task['doned_at'])) }}</small>
						               		@endif
						               		
							                @if (isset($task['due_dated_at']))
							                <BR>
							                <small class="due-date-label label {{ labelClass($task) }}">
						                  	<i class="fa {{($task['due_dated_complete']==1) ? 'fa-check-square-o':'fa-clock-o'}}"></i> 
						                  	{{ dueDateTime($task) }}</small>
						               		@endif
											
											
											@if($task['checklist_total']>0)
											<small class="card-checklist">
												<i class="fa fa-check-square-o"></i> {{  $task['checklist_success']." / ".$task['checklist_total'] }}
											</small>
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
			<div class="box box-solid bg-rm-inprogress-active box-parent">
				
            	<div class="box-header">
            		<i class="fa fa-clock-o"></i>
            	   <h3 class="box-title">
            	   		@lang('task.title_task_in_progress')
            	   		<span class="title-inprocess"></span>
            	   </h3>
            	   
            	</div>
	            <div class="box-body bg-rm-inprogress" id="card_in_progress">
					<div class="append-card">

						@if(count($tasks)>0)
						@foreach($tasks as $task)
							@if($task['status']==5)
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
						                  	{{  date('d M Y',strtotime($task['doned_at'])) }}</small>
						               		@endif
						               		
							                @if (isset($task['due_dated_at']))
							                <BR>
							                <small class="due-date-label label {{ labelClass($task) }}">
						                  	<i class="fa {{($task['due_dated_complete']==1) ? 'fa-check-square-o':'fa-clock-o'}}"></i> 
						                  	{{ dueDateTime($task) }}</small>
						               		@endif
											
											
											@if($task['checklist_total']>0)
											<small class="card-checklist">
												<i class="fa fa-check-square-o"></i> {{  $task['checklist_success']." / ".$task['checklist_total'] }}
											</small>
											@endif
						          
						               		
											
							        	</div>
							        </div>
					        @endif
						@endforeach
					@endif
					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer bg-rm-inprogress">
	              	&nbsp;
	            </div>

          </div>
			
		  <div class="box box-solid bg-rm-pending-active box-parent">
				
            	<div class="box-header">
            		<i class="fa fa-hourglass"></i>
            	   <h3 class="box-title">
            	   		@lang('task.title_task_pending')
            	   		<span class="title-pending"></span>
            	   </h3>
            	</div>
	            <div class="box-body bg-rm-pending" id="card_pending">
					<div class="append-card">

						@if(count($tasks)>0)
						@foreach($tasks as $task)
							@if($task['status']==6)
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
				                  
				         
				                  @if (isset($task['due_dated_at']))
				                  <small class="due-date-label label 
									{{ labelClass($task) }}
				                 	">
				                  	<i class="fa {{($task['due_dated_complete']==1) ? 'fa-check-square-o':'fa-clock-o'}}"></i> 
				                  	{{ dueDateTime($task) }}</small>
				                 
				               		@endif
									
									<div class="card-member pull-right">
										@if(isset($task['members']))
											@foreach ($task['members'] as $member)
												<img data-id="{{ $member['member_id'] }}" src="{{ $member['member_img'] }}" class="img-circle" height="25"  title="{{ $member['member_name']}}" >
											@endforeach
										@endif
									</div>
									
									@if($task['checklist_total']>0)
									<small class="card-checklist">
										<i class="fa fa-check-square-o"></i> {{  $task['checklist_success']." / ".$task['checklist_total'] }}
									</small>
									@endif
				               		
					        	</div>
					        </div>
					        @endif
						@endforeach
					@endif
					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer bg-rm-pending">
	              	&nbsp;
	            </div>

          </div>
		</section>

		<section class="col-md-3">
			<div class="box box-solid bg-rm-done-active box-parent">
				
            	<div class="box-header">
            		<i class="fa fa-check-square-o"></i>
            	   <h3 class="box-title">
            	   		@lang('task.title_task_done')
            	   		<span class="title-done"></span>
            	   </h3>
            	   
            	</div>
	            <div class="box-body bg-rm-done" id="card_done">
					<div class="append-card">

						@if(count($taskDone)>0)
						@foreach($taskDone as $task)
							@if($task['status']==7)
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
						                  	@lang('task.done_at'){{ date('d M Y',strtotime($task['doned_at'])) }}</small>
						               		@endif
						               		
							                @if (isset($task['due_dated_at']))
							                <BR>
							                <small class="due-date-label label {{ labelClass($task) }}">
						                  	<i class="fa {{($task['due_dated_complete']==1) ? 'fa-check-square-o':'fa-clock-o'}}"></i> 
						                  	{{ dueDateTime($task) }}</small>
						               		@endif
											
											
											@if($task['checklist_total']>0)
											<small class="card-checklist">
												<i class="fa fa-check-square-o"></i> {{  $task['checklist_success']." / ".$task['checklist_total'] }}
											</small>
											@endif
						          
						               		
											
							        	</div>
							        </div>
					        @endif
						@endforeach
					@endif
					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer bg-rm-done">
	              	&nbsp;
	            </div>

          </div>
		</section>

      </div>
      <!-- /.row (main row) -->

		 @include('main.task.card')	

		
		@include('widgets.card.modal.task-filter')
		
		
		

		

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
 <script type="text/javascript" src="{{ url('js/card/comment.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/due-date.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/attach.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/member.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/category.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/checklist.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/history.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/action.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/filter.js') }}"></script> 
<script >
socket.on('task', function(data){
	// return false;
	if (typeof data==='string'){
	    data = JSON.parse(data);
	}
   	console.log('[task]',data);
 	if(data.task_lastest_category_id!=null){
 		if(data.task_category==null){ //--- ถ้าข้อมูลถูกลบออก ต้องเอาออกจาก หน้า list
 			// console.log('[task] task_category==null ');
 			

 			if($("#task_category").length >0){
 				$("#task_category").hide();
 			}
 		}else{
 			// console.log('[task] task_category  ',data.task_category);
 			createTaskCategory(data.task_category,data.task_id);
 		}
    }

    var currentCardId =  $("#current_card_id").val();

    if(data.task_id != null){
    	console.log('updateListCard',data);
    	updateListCard(data)
    }

    if(data.task!=null&&data.task_id==currentCardId){
    	createTaskCard(data.task);
    }
    if(data.task_attachs!=null&&data.task_id==currentCardId){
    	// console.log('[task] task_attachs  ',data.task_attachs);
    	createTaskAttachment(data.task_attachs);
    }
    if(data.task_checklists!=null&&data.task_id==currentCardId){
    	createTaskChecklist(data.task_checklists);
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
    	$(".show-content").find('.box-id').each(function(index, el) {
	            if($(this).val()==data.delete_task_id){
	                var ele = $(this).closest('.show-content');
	                ele.remove();
	            }
	    });
    }
   


})
	
</script>

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

var baseRoute = "/task/" ;

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
	$('#card_new,#card_new_2,#card_reject,#card_in_progress,#card_pending').slimScroll({
    	height: '250px'
  	});
  	$('#card_accept,#card_done').slimScroll({
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
		
	var systemRoute = "/task/" ;
	var socketRoute = "task" ;


	  	// autosize(document.querySelectorAll('textarea'));
	  	// autosize(document.getElementById("description-edit-body-text"));

</script>

@endsection		
