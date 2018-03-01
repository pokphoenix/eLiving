<div class="modal fade " id="modal-card-content" >
          <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content " style="background: #EEE;">
            	<div class="cotent-cover text-center" style="display: none;background: #ccc;">
            		<img src="" height="100">
            	</div>
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                	 @include('widgets.card.title')
              </div>
              <div class="modal-body">
					
		
				 	
			
	                <div class="col-sm-12" style="height: 20px;"></div>
	                <div class="col-sm-10" >
	                	
	                	@include('widgets.card.category')
	                	@include('widgets.card.description')
						@include('widgets.card.attachment')
	                	
	                	
						
	                	

						@include('widgets.card.comment')	
	                </div>
	                
	                <div class="col-sm-2">
	                	
						@include('widgets.card.menu-flow')

	                	<div class="task-menu-add">
                                <h4>@lang('task.title_add') </h4>
                                <button type="button" class="btn btn-block btn-social btn_task_category btn-default" data-toggle="modal" data-target="#modal_task_category" >
                                    <i class="fa fa-tag"></i> @lang('task.btn_task_type') 
                                </button>
                                <label for="file-upload" class="btn btn-block btn-social btn-default">
                                    <i class="fa fa-paperclip"></i> @lang('task.btn_attachment')
                                </label>
                                <input id="file-upload" name='doc_file[]' type="file" style="display:none;">
                                
                            </div>
										
						@include('widgets.card.menu-delete')
	                	

						


		               <!--  <button type="button" class="btn btn-block btn-social" id="btn_attach" >
		                    <i class="fa fa-paperclip"></i> Attach file
		                </button>
		                <input id="file-upload" name='doc_file[]' type="file" style="display:none;"> -->
	                </div>
              </div>
              <div class="modal-footer">
                	
                </div>
                
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
		


@include('widgets.card.modal.task-category')

@include('widgets.card.modal.task-member')

@include('widgets.card.modal.task-checklist')