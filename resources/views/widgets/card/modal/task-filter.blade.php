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
          <h4 class="title">@lang('task.title_member')</h4>
          <div class="task-unsign" >
            <div class="media">
              <div class="media-left media-middle">
                <img src="{{ getBase64Img(url('public/img/error-image.jpg')) }}" class="img-circle" height="25">
              </div>
              <div class="media-body">

                <h5 class="media-heading">
                @lang('task.unsign')</h5>
               
              </div>
            </div>
          </div>

          <div id="filter_member_list">
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