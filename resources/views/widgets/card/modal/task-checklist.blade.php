<div id="modal_task_checklist" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form id="checklist-form" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">@lang('task.add_checklist')</h4>
      </div>
      <div class="modal-body">
          <div>
            <input type="text" class="form-control" id="checklist_title">
          </div>
      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('main.close')</button>
        <button type="submit" class="btn btn-primary btn-checklist-add" >@lang('main.add')</button>
      </div>
      </form>
    </div>

  </div>
</div>