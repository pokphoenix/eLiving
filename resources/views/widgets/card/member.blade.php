<div class="row">
	<div class="col-sm-12" id="task_member" >
		<h4 class="title"><i class="fa fa-user"></i> 
		@if(isset($memberTitle)) 
		{{ $memberTitle }}  
		@else
		@lang('task.title_member')
		@endif</h4> 
		<div class="form-group">
			<div class="list fl" style="margin-right:10px; ">
				
			</div>
			<div class="btn-tass">
				@if(!isset($roomId))
				<button class="btn btn-sm btn-default btn-task-member-add">
				 	<i class="fa fa-plus"></i>
				</button>
				@endif
			</div>
			<div class="member-detail"></div>
			 
    	</div>
	</div>
</div>

