<div class="task-menu-add">
                                <h4>@lang('task.title_add') </h4>
                                <button type="button" class="btn btn-block btn-social btn_task_category btn-default" data-toggle="modal" data-target="#modal_task_category" >
                                    <i class="fa fa-tag"></i> @lang('task.btn_task_type') 
                                </button>
                                <button type="button" class="btn btn-block btn-social btn_start_task btn-default" >
                                    <i class="fa fa-clock-o"></i> @lang('task.btn_start_date') 
                                </button>
                                <button type="button" class="btn btn-block btn-social btn_duedate btn-default" >
                                    <i class="fa fa-clock-o"></i> @lang('task.btn_due_date') 
                                </button>
                                
                                <button type="button" class="btn btn-block btn-social btn_task_member btn-default"  >
                                    <i class="fa fa-user"></i>@lang('task.btn_member') 
                                </button> 
                                <button type="button" class="btn btn-block btn-social btn_task_checklist btn-default" >
                                    <i class="fa fa-check-square-o"></i> @lang('task.btn_checklist') 
                                </button>
                                <label for="file-upload" class="btn btn-block btn-social btn-default">
                                    <i class="fa fa-paperclip"></i> @lang('task.btn_attachment')
                                </label>
                                <input id="file-upload" name='doc_file[]' type="file" style="display:none;">
                                
                            </div>