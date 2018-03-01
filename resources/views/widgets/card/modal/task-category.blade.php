<div id="modal_task_category" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">@lang('task.category')</h4>
      </div>
      <div class="modal-body">
          @foreach($taskCategory as $category)
      <div class="alert btn-task-category-add" style="background:{{ $category['color'] }};color:#FFF;">
            <h5> {{ $category['name'] }}</h5>
            <input type="hidden" class="category-id" value="{{ $category['id'] }}">
          </div>
          @endforeach

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('main.close')</button>
      </div>
    </div>

  </div>
</div>