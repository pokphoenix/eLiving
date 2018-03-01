<div class="task-menu-flow">
   
	<h4>@lang('task.title_task_status')</h4>
	<button type="button" class="btn btn-block btn-social btn-default btn-status" disabled="" >
        New
    </button>
    <h4 class="title-flow">@lang('task.title_change_task_status')</h4>
    <button type="button" class="btn btn-block btn-social btn-info btn_todo" >
        <div>
        <img src="{{ asset('public/img/icon/icon_todo.png') }}" class="icon-task-menu">
        </div>
         @lang('task.btn_todo')
    </button>
    <button type="button" class="btn btn-block btn-social btn-info btn_accept" >
        <div>
            <img src="{{ asset('public/img/icon/icon_accept.png') }}" class="icon-task-menu">
        </div>
        @lang('task.btn_accept')
    </button>
    <button type="button" class="btn btn-block btn-social bg-rm-new" id="btn_re_submit">
        <div>
            <img src="{{ asset('public/img/icon/icon_new_2.png') }}" class="icon-task-menu" >
        </div>
        @lang('task.btn_re_submit')
    </button>
   
    <button type="button" class="btn btn-block btn-social btn-success" id="btn_done" >
        <i class="fa fa-check-square-o"></i>  @lang('task.btn_done')
    </button>
    <button type="button" class="btn btn-block btn-social btn-warning" id="btn_in_progress" >
        <i class="fa fa-clock-o"></i>  @lang('task.btn_in_progress')
    </button> 

    <button type="button" class="btn btn-block btn-social bg-purple" id="btn_pending" >
        <i class="fa fa-hourglass"></i> @lang('task.btn_pending')
    </button>
	 <button type="button" class="btn btn-block btn-social btn-danger" id="btn_cancel">
        <i class="fa fa-window-close-o"></i> @lang('task.btn_cancel')
    </button>
    
</div>