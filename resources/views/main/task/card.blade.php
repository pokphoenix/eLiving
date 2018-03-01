<div class="modal fade " id="modal-card-content" >
          <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content " style="background: #EEE;">
              	<div class="cotent-cover text-center" style="display: none;background: #ccc;">
            		<img src="" height="100">
            	</div>
              	<div class="modal-header">
                	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  		<span aria-hidden="true">&times;</span>
                  	</button>
                	 @include('widgets.card.title')
              	</div>
              	<div class="modal-body">
	                <div class="row">
		                <div class="col-sm-10" >
	                    	@include('widgets.card.startdate')
		                	@include('widgets.card.duedate')
		                	@include('widgets.card.category')
		                	@include('widgets.card.member')
		                	@include('widgets.card.description')
							@include('widgets.card.attachment')
		                	<div class="row"  id="task_checklist" style="margin-top:20px;display: none;">
		                	</div>
							@include('widgets.card.comment')
		                	@include('widgets.card.history')
		                </div>
		                
		                <div class="col-sm-2">
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
              	</div>
              	<div class="modal-footer">
                	
              	</div>
                
              
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
		


@include('widgets.card.modal.task-category')

@include('widgets.card.modal.task-member')

@include('widgets.card.modal.task-checklist')

<div id="modal-card-delete" class="modal modal-danger fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Error</h4>
      </div>
      <div class="modal-body">
          <h2>งานนี้ถูกลบโดนผู้ใช้คนอื่นค่ะ</h2>
      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
