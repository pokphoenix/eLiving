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
						<div class="row">
							<div class="col-sm-12" id="task_attach_ment" >
								<h4 class="title"><i class="fa fa-paperclip"></i> @lang('task.title_attachment')</h4> 
								<div class="button-attachment" >
									<label for="file-upload" class="btn btn-primary">
						                <i class="fa fa-cloud-upload"></i> @lang('task.btn_attachment')
						            </label>
						            <input id="file-upload" name='doc_file[]' type="file" style="display:none;">
								</div>
								
								<div class="form-group attachment-list">
						    	</div>
							</div>
						</div>
						
	                	<div class="row" id="task_checklist" style="margin-top:20px;display: none;">

	                	</div>
						@include('widgets.card.comment')	

	                	
					

	                	
						

	                </div>
	                
	                <div class="col-sm-2">
						
						<h4>@lang('task.title_task_status')</h4>
						<button type="button" class="btn btn-block btn-social btn-default btn-status" disabled="" >
					        New
					    </button>
						
						<h4 class="title-flow">@lang('task.title_task_status')</h4>
					    <button type="button" class="btn btn-block btn-social bg-rm-new" id="btn_re_submit">
					        <div>
					            <img src="{{ asset('public/img/icon/icon_new_2.png') }}" class="icon-task-menu" >
					        </div>
					        @lang('task.btn_re_submit')
					    </button>			
							
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
