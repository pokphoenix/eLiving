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
	                	@include('widgets.card.startdate')
	                	@include('widgets.card.duedate')
	                	@include('widgets.card.category')
	                	@include('widgets.card.member')
	                	
	                	@include('widgets.card.description')
						@include('widgets.card.attachment')
	                	
	                	
						
	                	<div class="row" id="task_checklist" style="margin-top:20px;display: none;">

	                	</div>

						@include('widgets.card.comment')	

						@include('widgets.card.history')	
	                	
						
					

	                	
						

	                </div>
	                
	                <div class="col-sm-2">
	                	<div class="task-menu-room">
	                		<h4>@lang('task.title_room_info')</h4>
	                		@lang('task.label-room-number') : <BR>
	                		<p id="room_number"></p>
	                		@lang('task.label-room-name') : <BR>
	                		<p id="room_name"></p>
	                		@lang('task.label-room-tel') : <BR>
	                		<p id="room_tel"></p>
	                		@lang('task.label-room-email') : <BR>
	                		<p id="room_email"></p>
						
						</div>
						@include('widgets.card.menu-flow')
	                	@include('widgets.card.menu-add')
						@include('widgets.card.menu-action')					
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