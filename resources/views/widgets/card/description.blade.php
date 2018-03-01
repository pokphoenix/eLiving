<div class="row">
	<div class="col-sm-12 " id="row-task-edit">
    	<div id="description" style="display: none;">
    		<h4 class="title" ><i class="fa fa-file-text-o"></i> @lang('task.title_description')</h4><a href="javascript:void(0)" id="btn_edit_description_body" >@lang('task.btn_edit')</a>
    		<div id="description-body-readonly"></div>
    		<div id="description-body"></div>
    	</div>
    	<div id="description-edit" style="display: none;">
    		<h4 class="title"><i class="fa fa-file-text-o"></i> @lang('task.title_description')</h4>
    		<div id="description-edit-body" style="overflow: hidden;">
    			<textarea class="form-control" rows="3" id="description-edit-body-text" style="overflow:auto;"></textarea>
    			<BR>
    			<button id="description-edit-body-add-btn" class="btn btn-primary">@lang('task.btn_save')</button>
            	<button class="btn" id="description-edit-body-close-btn" >@lang('task.btn_cancel')</button>
    		</div>
    	</div>
    	<div class="task-edit-description" style="display: none;">
    		<div id="task-edit-description-btn">
    			<h4 class="title"><i class="fa fa-file-text-o"></i> @lang('task.title_description')</h4>
    		</div>
            <div id="task-edit-description-body" >
            	<textarea class="form-control" rows="3" id="task-description" placeholder="@lang('task.holder_description')" style="overflow:auto;"></textarea>
            	<BR>
            	<button id="task-edit-description-add-btn" class="btn btn-primary">@lang('task.btn_save')</button>
            	<button class="btn" id="task-edit-description-clost-btn" >@lang('task.btn_cancel')</button>
            </div>
    	</div>
    		
    </div>
</div>