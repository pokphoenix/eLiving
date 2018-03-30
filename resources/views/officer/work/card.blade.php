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
	                	@include('widgets.card.job-type')
	                	@include('widgets.card.area-type')
	                	@include('widgets.card.category')
	                	@include('widgets.card.pioritize')
	                	@include('widgets.card.tower')	
	                	@include('widgets.card.floor')	
	                	
	                	@include('widgets.card.member')

						<div class="row">
							<div class="col-sm-12" id="work_accept_by" >
								<h4 class="title"><i class="fa fa-user"></i>  
								@lang('work.accept_by')</h4> 
								<div class="form-group">
									<div class="list fl" style="margin-right:10px; ">
										
									</div>
									<div class="btn-tass">
										<!-- <button class="btn btn-sm btn-default btn-task-member-add">
										 	<i class="fa fa-plus"></i>
										</button> -->
									</div>
									<!-- <div class="member-detail"></div> -->
									 
						    	</div>
							</div>
						</div>



	                	@include('widgets.card.description')
						@include('widgets.card.attachment')
	                	

	                	<div class="row">
							<div class="col-sm-4" >
								<h4 class="title"><i class="fa fa-circle-o"></i> @lang('work.result')</h4> 
								<div class="form-group">
									<input type="checkbox" id="result" > @lang('work.result_msg')
						        	
						    	</div>
							</div>
						</div>

						@include('widgets.card.action-taken')
						@include('widgets.card.incomplete')
						@include('widgets.card.recommend')
						
					

	                	
						
	                	

						@include('widgets.card.comment')	
	                </div>
	                
	                <div class="col-sm-2">
	                	
						@include('widgets.card.menu-flow')

	                	<div class="task-menu-add">
                                <h4>@lang('task.title_add') </h4>
                                <button type="button" class="btn btn-block btn-social btn_task_category btn-default" data-toggle="modal" data-target="#modal_task_category" >
                                    <i class="fa fa-tag"></i> @lang('task.btn_task_type') 
                                </button>
                                <button type="button" class="btn btn-block btn-social btn_task_member btn-default"  >
                                    <i class="fa fa-user"></i>{{ $memberTitle }}
                                </button> 
                                <label for="file-upload" class="btn btn-block btn-social btn-default">
                                    <i class="fa fa-paperclip"></i> @lang('task.btn_attachment')
                                </label>
                                <input id="file-upload" name='doc_file[]' type="file" style="display:none;">
                                <button type="button" class="btn btn-block btn-social btn-print btn-default"  >
                                    <i class="fa fa-print"></i>@lang('main.print')
                                </button> 
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