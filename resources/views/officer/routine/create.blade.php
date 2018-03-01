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
      	งานภายใน
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
		<input type="hidden" id="first_open_card" value="false">
		<input type="hidden" id="task_created_by" value="">

	 	<div>
		 	<button class="btn btn-flat btn-primary btn-tap" data-toggle="#tap_1" >ทำในอาทิตย์นี้</button>
	        <button class="btn btn-flat  btn-tap" data-toggle="#tap_2">งานทั้งหมด</button>
	 	</div>
		

      <!-- Main row -->
      <div class="row" >

	
		<section class="col-md-3">
			
			<div class="box box-solid bg-gray-active box-parent tab-panel" id="tap_1">
				
		            	<div class="box-header">
		            		<i class="fa fa-plus"></i>
		            	   <h3 class="box-title">
		            	   		New
		            	   </h3>
		            	   <span data-toggle="tooltip" title="3 New Messages" class="badge">3</span>
				           <div class="box-tools pull-right">
				           		 
				                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
				                </button>
				                <div class="btn-group">
				                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
				                    <i class="fa fa-wrench"></i></button>
				                  <ul class="dropdown-menu" role="menu">
				                    <li><a href="#">Action</a></li>
				                    <li><a href="#">Another action</a></li>
				                    <li><a href="#">Something else here</a></li>
				                    <li class="divider"></li>
				                    <li><a href="#">Separated link</a></li>
				                  </ul>
				                </div>
				            </div>
		            	</div>
			            <div class="box-body bg-gray" id="card_new">
							<div class="append-card">
								
							@if(count($tasks)>0)
								@foreach($tasks as $task)
									@if($task['status']==1 && (strtotime('monday this week')<=strtotime($task['start_task_at'])&&
									strtotime($task['start_task_at'])<=strtotime('sunday this week')
									))
									<div class="box box-solid card show-content" >
								<div><img src="{{ $task['file_path'] }}" class="img-responsive"></div>
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
												<img src="{{ $member['member_img'] }}" class="img-circle" height="25"  title="{{ $member['member_name']}}" >
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
			            <div class="box-footer bg-gray">
			              	&nbsp;
			            </div>

		    </div>
			<div class="box box-solid bg-gray-active box-parent tab-panel" id="tap_2" style="display: none;">
				
            	<div class="box-header">
            		<i class="fa fa-plus"></i>
            	   <h3 class="box-title">
            	   		New
            	   </h3>
            	  
		           <div class="box-tools pull-right">
		           		 
		                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		                </button>
		                <div class="btn-group">
		                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
		                    <i class="fa fa-wrench"></i></button>
		                  <ul class="dropdown-menu" role="menu">
		                    <li><a href="#">Action</a></li>
		                    <li><a href="#">Another action</a></li>
		                    <li><a href="#">Something else here</a></li>
		                    <li class="divider"></li>
		                    <li><a href="#">Separated link</a></li>
		                  </ul>
		                </div>
		            </div>
            	</div>
	            <div class="box-body bg-gray" id="card_new">
					<div class="append-card">
						
					@if(count($tasks)>0)
						@foreach($tasks as $task)
							@if($task['status']==1 &&  strtotime($task['start_task_at']) < time() )
							<div class="box box-solid card show-content" >
								<div><img src="{{ $task['file_path'] }}" class="img-responsive"></div>
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
												<img src="{{ $member['member_img'] }}" class="img-circle" height="25"  title="{{ $member['member_name']}}" >
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
	            <div class="box-footer bg-gray ">
	              	&nbsp;
	            </div>

            </div>

			
			

          	<div class="box box-solid bg-gray-active">
				
            	<div class="box-header">
            		<i class="fa fa-window-close-o"></i>
            	   <h3 class="box-title">
            	   		Cancel
            	   </h3>
            	   <span data-toggle="tooltip" title="3 New Messages" class="badge">3</span>
		           <div class="box-tools pull-right">
		           		 
		                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		                </button>
		                <div class="btn-group">
		                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
		                    <i class="fa fa-wrench"></i></button>
		                  <ul class="dropdown-menu" role="menu">
		                    <li><a href="#">Action</a></li>
		                    <li><a href="#">Another action</a></li>
		                    <li><a href="#">Something else here</a></li>
		                    <li class="divider"></li>
		                    <li><a href="#">Separated link</a></li>
		                  </ul>
		                </div>
		            </div>
            	</div>
	            <div class="box-body bg-gray" id="card_reject">
					<div class="">
						
					@if(count($tasks)>0)
						@foreach($tasks as $task)
							@if($task['status']==4)
							<div class="box box-solid card show-content" >
								<div><img src="{{ $task['file_path'] }}" class="img-responsive"></div>
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
												<img src="{{ $member['member_img'] }}" class="img-circle" height="25"  title="{{ $member['member_name']}}" >
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
	            <div class="box-footer bg-gray">
	              	&nbsp;
	            </div>

          </div>
		</section>
		
		<section class="col-md-3">
			<div class="box box-solid bg-gray-active">
				
            	<div class="box-header">
            		<i class="fa fa-signal"></i>
            	   <h3 class="box-title">
            	   		Accept
            	   </h3>
            	   <span data-toggle="tooltip" title="3 New Messages" class="badge">3</span>
		           <div class="box-tools pull-right">
		           		 
		                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		                </button>
		                <div class="btn-group">
		                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
		                    <i class="fa fa-wrench"></i></button>
		                  <ul class="dropdown-menu" role="menu">
		                    <li><a href="#">Action</a></li>
		                    <li><a href="#">Another action</a></li>
		                    <li><a href="#">Something else here</a></li>
		                    <li class="divider"></li>
		                    <li><a href="#">Separated link</a></li>
		                  </ul>
		                </div>
		            </div>
            	</div>
	            <div class="box-body bg-gray" id="card_accept">
					<div class="">

						@if(count($tasks)>0)
						@foreach($tasks as $task)
							@if($task['status']==3 )
							<div class="box box-solid card show-content" >
								<div><img src="{{ $task['file_path'] }}" class="img-responsive"></div>
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
												<img src="{{ $member['member_img'] }}" class="img-circle" height="25"  title="{{ $member['member_name']}}" >
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
	            <div class="box-footer bg-gray">
	              	&nbsp;
	            </div>

          </div>
		</section>
		
		
		

		<section class="col-md-3">
			<div class="box box-solid bg-gray-active">
				
            	<div class="box-header">
            		<i class="fa fa-road"></i>
            	   <h3 class="box-title">
            	   		In progress
            	   </h3>
            	   <span data-toggle="tooltip" title="3 New Messages" class="badge">3</span>
		           <div class="box-tools pull-right">
		           		 
		                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		                </button>
		                <div class="btn-group">
		                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
		                    <i class="fa fa-wrench"></i></button>
		                  <ul class="dropdown-menu" role="menu">
		                    <li><a href="#">Action</a></li>
		                    <li><a href="#">Another action</a></li>
		                    <li><a href="#">Something else here</a></li>
		                    <li class="divider"></li>
		                    <li><a href="#">Separated link</a></li>
		                  </ul>
		                </div>
		            </div>
            	</div>
	            <div class="box-body bg-gray" id="card_in_progress">
					<div class="">

						@if(count($tasks)>0)
						@foreach($tasks as $task)
							@if($task['status']==5)
							<div class="box box-solid card show-content" >
								<div><img src="{{ $task['file_path'] }}" class="img-responsive"></div>
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
												<img src="{{ $member['member_img'] }}" class="img-circle" height="25"  title="{{ $member['member_name']}}" >
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
	            <div class="box-footer bg-gray">
	              	&nbsp;
	            </div>

          </div>
			
		  <div class="box box-solid bg-gray-active">
				
            	<div class="box-header">
            		<i class="fa fa-hourglass"></i>
            	   <h3 class="box-title">
            	   		Pendding
            	   </h3>
            	   <span data-toggle="tooltip" title="3 New Messages" class="badge">3</span>
		           <div class="box-tools pull-right">
		           		 
		                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		                </button>
		                <div class="btn-group">
		                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
		                    <i class="fa fa-wrench"></i></button>
		                  <ul class="dropdown-menu" role="menu">
		                    <li><a href="#">Action</a></li>
		                    <li><a href="#">Another action</a></li>
		                    <li><a href="#">Something else here</a></li>
		                    <li class="divider"></li>
		                    <li><a href="#">Separated link</a></li>
		                  </ul>
		                </div>
		            </div>
            	</div>
	            <div class="box-body bg-gray" id="card_pending">
					<div class="">

						@if(count($tasks)>0)
						@foreach($tasks as $task)
							@if($task['status']==6)
							<div class="box box-solid card show-content" >
								<div><img src="{{ $task['file_path'] }}" class="img-responsive"></div>
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
												<img src="{{ $member['member_img'] }}" class="img-circle" height="25"  title="{{ $member['member_name']}}" >
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
	            <div class="box-footer bg-gray">
	              	&nbsp;
	            </div>

          </div>
		</section>

		<section class="col-md-3">
			<div class="box box-solid bg-gray-active">
				
            	<div class="box-header">
            		<i class="fa fa-check-square-o"></i>
            	   <h3 class="box-title">
            	   		Done
            	   </h3>
            	   <span data-toggle="tooltip" title="3 New Messages" class="badge">3</span>
		           <div class="box-tools pull-right">
		           		 
		                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		                </button>
		                <div class="btn-group">
		                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
		                    <i class="fa fa-wrench"></i></button>
		                  <ul class="dropdown-menu" role="menu">
		                    <li><a href="#">Action</a></li>
		                    <li><a href="#">Another action</a></li>
		                    <li><a href="#">Something else here</a></li>
		                    <li class="divider"></li>
		                    <li><a href="#">Separated link</a></li>
		                  </ul>
		                </div>
		            </div>
            	</div>
	            <div class="box-body bg-gray" id="card_done">
					<div class="">

						@if(count($tasks)>0)
						@foreach($tasks as $task)
							@if($task['status']==7)
							<div class="box box-solid card show-content" >
								<div><img src="{{ $task['file_path'] }}" class="img-responsive"></div>
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
												<img src="{{ $member['member_img'] }}" class="img-circle" height="25"  title="{{ $member['member_name']}}" >
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
	            <div class="box-footer bg-gray">
	              	&nbsp;
	            </div>

          </div>
		</section>

      </div>

		 @include('officer.task.card')	

		
		
		
		

		

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

 
 <script type="text/javascript" src="{{ url('js/card/comment.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/due-date.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/attach.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/member.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/category.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/checklist.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/card/history.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/user/card.js') }}"></script> 
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
		


	  	// autosize(document.querySelectorAll('textarea'));
	  	// autosize(document.getElementById("description-edit-body-text"));


</script>

@endsection		
