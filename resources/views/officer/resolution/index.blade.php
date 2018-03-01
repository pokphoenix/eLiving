@extends('main.layouts.main')
@section('style')
<link rel="stylesheet" href="{{ url('plugins/jquery-ui/jquery-ui.css') }}">

<style type="text/css">
tbody tr:nth-child(odd) {background: #DEDEDE!important}
tfoot tr:nth-child(odd) {background: #DEDEDE!important}
</style>
@endsection
@section('content-wrapper')
<!-- data-toggle="modal" data-target="#modal-card-content" -->
	

	
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <img class="icon-title" src="{{ asset('public/img/icon/icon_purchasing_bidding_2.png') }}"> @lang('resolution.title')
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">@lang('resolution.title')</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
     
		<input type="hidden" id="current_card_id" >
		<input type="hidden" id="first_open_card" value="false" >
		<input type="hidden" id="asset_url" value="{{ asset('public/img/icon/') }}" >
	
	@if(Auth()->user()->hasRole('head.user'))
	<div class="row">
		<div class="col-sm-12">
	         @include('widgets.card.filter')
	 	</div>
	</div>
	@endif
		

      <!-- Main row -->
      <div class="row">

		
		<section class="col-md-3"  style="@if(!Auth()->user()->hasRole('officer'))  display:none; @endif "  >
			<div class="box box-solid bg-rm-new-active box-parent">
            	<div class="box-header">
            		<img class="icon-title" src="{{ asset('public/img/icon/icon_new.png') }}" >
            	   <h3 class="box-title">
            	   		@lang('task.title_task_new')
            	   </h3>
            	</div>
	            <div class="box-body bg-rm-new" id="card_new">
					<div class="append-card">
						
					@if(count($lists)>0)
						@foreach($lists as $list)
							@if($list['status']==1)
							<div class="box box-solid card show-content" >
					            <div class="box-header">
					              	<h3 class="box-title">{{ $list['title'] }}</h3>
					              	<input type="hidden" class="box-id" value="{{  $list['id'] }}">
					              	<div class="box-tools pull-right card-btn-edit">
						                <button type="button" class="btn btn-box-tool" >
						                	<i class="fa fa-edit"></i>
						                </button>
						            </div>
					            </div>
					            <div class="box-body">
					            	<span class="vote-label"> <i class="fa fa-gavel"></i> ({{ $list['total_vote'].' / '. $list['total_can_vote'] }}) </span>
					        	</div>
					        </div>
					        @endif
						@endforeach
					@endif

					

					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer bg-rm-new addcard-hover">
	              	@lang('resolution.add_new_card')
	            </div>
            </div>

          	<div class="box box-solid bg-rm-cancel-active box-parent">
            	<div class="box-header">
            		<i class="fa fa-window-close-o"></i>
            	   <h3 class="box-title">
            	   		@lang('task.title_task_cancel')
            	   </h3>
            	</div>
	            <div class="box-body bg-rm-cancel" id="card_reject">
					<div class="append-card">
						
					@if(count($lists)>0)
						@foreach($lists as $list)
							@if($list['status']==4)
							<div class="box box-solid card show-content" >
					            <div class="box-header">
					              	<h3 class="box-title">{{ $list['title'] }}</h3>
					              	<input type="hidden" class="box-id" value="{{  $list['id'] }}">
					              	<div class="box-tools pull-right card-btn-edit">
						                <button type="button" class="btn btn-box-tool" >
						                	<i class="fa fa-edit"></i>
						                </button>
						            </div>
					            </div>
					            <div class="box-body">
					              <span class="vote-label"> <i class="fa fa-gavel"></i> ({{ $list['total_vote'].' / '. $list['total_can_vote'] }}) </span>
					        	</div>
					        </div>
					        @endif
						@endforeach
					@endif

					

					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer bg-rm-cancel">
	              	&nbsp;
	            </div>
            </div>
		</section>
	
		<section class="col-md-3" >
			<div class="box box-solid bg-rm-voting-active box-parent">
            	<div class="box-header">
            		<img class="icon-title" src="{{ asset('public/img/icon/icon_voting.png') }}" >
            	   <h3 class="box-title">
            	   		@lang('resolution.title_task_voting')
            	   </h3>
            	   
		           
            	</div>
	            <div class="box-body bg-rm-voting" id="card_voting" >
					<div class="append-card" >
						
					@if(count($lists)>0)
						@foreach($lists as $list)
							@if($list['status']==2)
							<div class="box box-solid card show-content" >
					            <div class="box-header">
					              	<h3 class="box-title">{{ $list['title'] }}</h3>
					              	<input type="hidden" class="box-id" value="{{  $list['id'] }}">
					              	<div class="box-tools pull-right card-btn-edit">
						                <button type="button" class="btn btn-box-tool" >
						                	<i class="fa fa-edit"></i>
						                </button>
						            </div>
					            </div>
					            <div class="box-body">
					              <span class="vote-label"> <i class="fa fa-gavel"></i> ({{ $list['total_vote'].' / '. $list['total_can_vote'] }}) </span>

					              	@if($hasHeaduser)
										<small class="label status-vote-label label-{{ ($list['user_has_vote']) ? 'success' : 'warning'  }}">
											@if($list['user_has_vote'])
												@lang('resolution.voted')
											@else
												@lang('resolution.wait_for_voting')
											@endif
						                  	<input type="hidden" class="status-vote-label-id" value="{{ $list['user_has_vote'] }}" >
						                </small>
									@endif
					        	</div>
					        </div>
					        @endif
						@endforeach
					@endif

					

					</div>
	            </div>
	            <div class="box-footer bg-rm-voting">
	              	&nbsp;
	            </div>
          	</div>
		</section>

		<section class="col-md-3" >
			<div class="box box-solid bg-rm-voted-active box-parent">
            	<div class="box-header">
            		<img class="icon-title" src="{{ asset('public/img/icon/icon_voted.png') }}" >
            	   <h3 class="box-title">
            	   		@lang('resolution.title_task_voted')
            	   </h3>
            	   
		           
            	</div>
	            <div class="box-body bg-rm-voted" id="card_voted">
					<div class="append-card">

						@if(count($lists)>0)
							@foreach($lists as $list)
								@if($list['status']==3)
								<div class="box box-solid card show-content" >
						            <div class="box-header">
						              	<h3 class="box-title">{{ $list['title'] }}</h3>
						              	<input type="hidden" class="box-id" value="{{  $list['id'] }}">
						              	<div class="box-tools pull-right card-btn-edit">
							                <button type="button" class="btn btn-box-tool" >
							                	<i class="fa fa-edit"></i>
							                </button>
							            </div>
						            </div>
						            <div class="box-body">
						              <span class="vote-label"> <i class="fa fa-gavel"></i> ({{ $list['total_vote'].' / '. $list['total_can_vote'] }}) </span>

						              	@if($hasHeaduser)
										<small class="label status-vote-label label-{{ ($list['user_has_vote']) ? 'success' : 'warning'  }}">
											@if($list['user_has_vote'])
												@lang('resolution.voted')
											@else
												@lang('resolution.wait_for_voting')
											@endif
						                  	<input type="hidden" class="status-vote-label-id" value="{{ $list['user_has_vote'] }}" >
						                </small>
									@endif
						        	</div>
						        </div>
						        @endif
							@endforeach
						@endif
					</div>
	            </div>
	            <div class="box-footer bg-rm-voted">
	              	&nbsp;
	            </div>
          	</div>
		</section>
	
		

		<section class="col-md-3" >
			<div class="box box-solid bg-rm-done-active box-parent">
            	<div class="box-header">
            		<i class="fa fa-check-square-o"></i>
            	   <h3 class="box-title">
            	   		@lang('task.title_task_done')
            	   </h3>
            	   
		           
            	</div>
	            <div class="box-body bg-rm-done" id="card_done">
					<div class="append-card">

						@if(count($taskDone)>0)
							@foreach($taskDone as $list)
								@if($list['status']==7)
								<div class="box box-solid card show-content" >
						            <div class="box-header">
						              	<h3 class="box-title">{{ $list['title'] }}</h3>
						              	<input type="hidden" class="box-id" value="{{  $list['id'] }}">
						              	<div class="box-tools pull-right card-btn-edit">
							                <button type="button" class="btn btn-box-tool" >
							                	<i class="fa fa-edit"></i>
							                </button>
							            </div>
						            </div>
						            <div class="box-body">
						              	<span class="vote-label"> <i class="fa fa-gavel"></i> ({{ $list['total_vote'].' / '. $list['total_can_vote'] }}) </span>

						              	@if (isset($list['doned_at']))
					                   	<small class="label label-success done-at-label">
					                  	@lang('task.done_at') {{ date('d/m/Y',strtotime($list['doned_at'])) }}</small>
					               		@endif
						        	</div>
						        </div>
						        @endif
							@endforeach
						@endif
					</div>
	            </div>
	            <div class="box-footer bg-rm-done">
	              	&nbsp;
	            </div>
          	</div>
		</section>

      </div>
      <!-- /.row (main row) -->

		<div class="modal fade" id="modal-card-content" >
          <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                	@include('widgets.card.title')
                	<div class="row">
								<div class="col-sm-12">
									<div class="user-can-vote">
										<h4 class="title"><i class="fa fa-user" ></i> @lang('resolution.title_user_can_vote')</h4>
							        	<div class="user-can-vote-list">
							        		
							        	</div>
							    	</div>
								</div>
							</div>
              </div>
              <div class="modal-body">
					<div class="row">
						<div class="col-sm-12 section-resolution-button-new" >
					 	 	<button type="button" class="btn btn-info btn-flat btn-resolution-new"  >Create Item</button>
						 
					 	</div>
				
		                <div class="col-sm-12" style="height: 20px;"></div>
		                <div class="col-sm-10" >
		                	<div class="row">
		                		<div class="col-sm-12" id="data-summary-resolution-table" style="overflow:auto;">
		                		</div>
		                	</div>
							
							
		                	@include('widgets.card.comment')	

							@include('widgets.card.history')	
							
		                </div>
		                
		                <div class="col-sm-2">
		                	<div class="menu-status" style="margin-bottom: 5px;">
		                		<h4>@lang('task.title_task_status')</h4>
								<button type="button" class="btn btn-block btn-social btn-default btn-status" disabled="" >
							        New
							    </button>
		                	</div>
							
							<div class="menu-flow">
								<h4>@lang('task.title_change_task_status')</h4>
								<button type="button" class="btn btn-block btn-social bg-rm-new" id="btn_resubmit" style="color: #FFF;">
									<div>
							            <img src="{{ asset('public/img/icon/icon_new.png') }}" class="icon-task-menu" >
							        </div>
				                     @lang('task.btn_re_submit')
				                </button>
			                	<button type="button" class="btn btn-block btn-social btn-default bg-rm-voting" id="start_vote">
			                		<div>
							            <img src="{{ asset('public/img/icon/icon_voting.png') }}" class="icon-task-menu" >
							        </div>
				                     @lang('resolution.btn_voting')
				                </button>
				                <button type="button" class="btn btn-block btn-social btn-default bg-rm-voting" id="btn_manual_voted">
			                		<div>
							            <img src="{{ asset('public/img/icon/icon_voted.png') }}" class="icon-task-menu" >
							        </div>
				                     @lang('resolution.btn_voted')
				                </button>
				                
				               	<button type="button" class="btn btn-block btn-social btn-success" id="btn_done" >
				                    <i class="fa fa-check-square-o"></i>@lang('task.btn_done')
				                </button>
				               
				                <button type="button" class="btn btn-block btn-social btn-default" id="cancel_vote">
				                    <i class="fa fa-window-close-o"></i>@lang('task.btn_cancel')
				                </button>
							</div>
							
							<div class="menu-action">
								<h4>@lang('task.title_action')</h4>
								
				                <button type="button" class="btn btn-block btn-social btn-danger" id="no_vote">
				                    <i class="fa fa-user-times"></i>@lang('resolution.btn_no_vote')
				                </button>
				                <button type="button" class="btn btn-block   btn-success" id="btn_voted" disabled="">
				                    @lang('resolution.btn_you_voted')
				                </button>
				                <button type="button" class="btn btn-block btn-info btn-show-set-company-winner" disabled="">
				                	@lang('resolution.btn_voted_no_result')
				                </button>
				                <button type="button" class="btn btn-block btn-social btn-default" id="btn_change_voted">
				                	 <i class="fa fa-refresh"></i>@lang('resolution.btn_cancel_last_vote')
				                </button>
				                <button type="button" class="btn btn-block  btn-info" id="success_vote" disabled="">
				                    โหวตแล้ว
				                </button>
							</div>
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
		
		<div class="modal fade" id="modal-resolution-item" >
          <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('resolution.title_item')</h4>
                <input  type="hidden" id="insert_new_item" value="true">
              </div>
              <div class="modal-body">
          		<div class="row">
          			<div class="col-sm-12">
          				<form id="btn-resolution-add-item" >
				 		<input type="text" id="quotaion_item_desc" style="height:30px;" class="insert-item-description" placeholder="@lang('resolution.description')">
				 	 	<button type="submit"  class="btn btn-info btn-sm btn-flat" style="margin-top: -3px;" >@lang('resolution.insert_item')</button>
				 	 	</form>
					 	<div class="row">
	                		<div class="col-sm-12" id="data-resolution-item-table">
	                			<table id="table-resolution" class="table table-bordered">
									<thead>
										<tr>
											<th width="50"></th>
											<th class="vm-ct" width="50">@lang('resolution.no')</th>
											<th class="vm-ct">@lang('resolution.description')</th>
											
										</tr>

									</thead>
									<tbody>
										
									</tbody>
									
								</table>
	                		</div>
	                	</div>
				 	</div>
					<div class="col-sm-12" style="height: 20px;">
						<input type="hidden" id="resolution-table-data">
					</div>
          		</div>
				 	
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat btn-save-resolution">@lang('task.btn_save')</button>
               
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
    </section>
    <!-- /.content -->

  <!-- /.content-wrapper -->
@endsection

@section('javascript')
 <script src="{{ url('plugins/jquery-ui/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ url('js/utility/main.js') }}"></script> 

<script type="text/javascript" src="{{ url('js/resolution/card.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/resolution/resolution-item.js') }}"></script> 

<script type="text/javascript" src="{{ url('js/resolution/resolution-vote.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/resolution/resolution-comment.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/resolution/resolution.js') }}"></script> 
  <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
 <script type="text/javascript" src="{{ url('js/resolution/filter.js') }}"></script> 
 
 <script type="text/javascript" src="{{ url('js/resolution/resolution-flow.js') }}"></script>

 <!-- <script type="text/javascript" src="{{ url('plugins/autosize/autosize.min.js') }}"></script>  -->
 <script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
	<!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->

<script>



	// $("#btn-add-company").on('click',function(){ 
	// 	$("#modal-resolution-company").modal("toggle");
	// 	$("#modal-card-content").modal("toggle");
	// });

	$(document)
    .on( 'hidden.bs.modal', '.modal', function() {
        $(document.body).removeClass( 'modal-scrollbar' );
    })
    .on( 'show.bs.modal', '.modal', function() {
        if ( $(window).height() < $(document).height() ) {
            $(document.body).addClass( 'modal-scrollbar' );
        }
    });

</script>
<script >
socket.on('resolution', function(data){
	if (typeof data==='string'){
	    data = JSON.parse(data);
	}
    // console.log('[resolution]',data);

    var currentCardId =  $("#current_card_id").val();
   	if(data.resolution_id != null){
   		
    	updateListCard(data);
    }


    if(data.resolution!=null&&data.resolution_id==currentCardId){
    	 createResolutionTable(data);
    }
    if(data.resolution_items!=null&&data.resolution_id==currentCardId){
    	createTableItem(data.resolution_items);
    } 
    if(data.resolution_historys!=null&&data.resolution_id==currentCardId){
    	createHistory(data.resolution_historys);
    }
    if(data.resolution!=null&&data.resolution_id==currentCardId){
    	createContentCard(data.resolution);
    }
    if(data.resolution_comments!=null&&data.resolution_id==currentCardId){
    	createComment(data.resolution_comments);
    }
     if(data.resolution_user_can_vote!=null&&data.resolution_id==currentCardId){
    	createUserCanVote(data.resolution_user_can_vote);
    }

})
function updateListCard(data){
	var cardId = data.resolution_id ;
	

	if(data.resolution!=null){
		var resolution  =data.resolution ;
		$('.box-id').each(function(index, el) {
	        if($(this).val()==cardId){
	            var ele = $(this).closest('.show-content').find('.box-title').text(resolution.title);
	        }
		});

		//--- Done at
		if(resolution.doned_at!=null){
			console.log(resolution.doned_at);
			$('.box-id').each(function(index, el) {
			    if($(this).val()==cardId){
			      	var ele = $(this).closest('.show-content').find('.done-at-label');
			      	var html = 'Done at '+moment.utc(resolution.doned_at).format('DD MMM YYYY');
			      	if (ele.length > 0 ){
			      		ele.html(html);
			      		 console.log('change doned_at');
			      	}else{
			      		var due =  "<BR><small class=\"label label-success done-at-label\">"+
						 html+"</small>";
						 $(this).closest('.show-content').find('.box-body').append(due);
						 console.log('append doned_at');
			      	}	
			    }
			});

		}else{
			$('.box-id').each(function(index, el) {
			    if($(this).val()==cardId){
			      	var ele = $(this).closest('.show-content').find('.done-at-label').remove();
			    }
			});
		}



		cardStatusMove(resolution);
	}

	if(data.resolution_votes!=null){
		var voting = data.resolution_votes ;
	    var userCanVote = data.resolution_user_can_vote ;

	    $('.box-id').each(function(index, el) {
	        if($(this).val()==cardId){

	        	var voteText = "("+voting.length+" / "+userCanVote.length+")";
	        	var html = "<span class=\"vote-label\"> <i class=\"fa fa-gavel\"></i> "+voteText+" </span>";
	        	var ele = $(this).closest('.show-content') ;
	        	if(ele.find('.vote-label').length <= 0){
	        		ele.find('.box-body').append(html);
	        	}else{
	        		ele.find('.vote-label').replaceWith(html);
	        	} 
	        }
	    });
	}
}	
</script>


<script type="text/javascript">
	$('#card_new,#card_reject').slimScroll({
    	height: '250px'
  	});
  	$('#card_voted,#card_voting,#card_done').slimScroll({
    	height: '600px'
  	});

	$(function(){					
		var $win = $(window); // or $box parent container
		var $box = $("#table-resolution tbody");

		// var $title = $("#modal-card-content .modal-title");
		
		$win.on("click.Bst", function(event){	
			if ( $box.has(event.target).length == 0 &&!$box.is(event.target)){
		      	// console.log("you clicked outside the box");
				hideInputItem();
			}
		
			
		});
	  
		@if(isset($Id))
			$(".show-content").each(function(){
				if($(this).find(".box-id").val()== {{ $Id }} ){
					$(this).click();
				}
			})
		@endif

	});
		


	  	// autosize(document.querySelectorAll('textarea'));
	  	// autosize(document.getElementById("description-edit-body-text"));
	  

function hideInputItem(){
	if($("#table-resolution tbody tr.active-edit").length>0){
		var ele = $("#table-resolution tbody tr.active-edit");
		$(ele).find('td:gt(1)').each (function() {
			var val = $(this).find('input').val() ;
			$(this).find('input').attr("type","hidden");
			$(this).find('span').text(val).show();
		});  
		ele.removeClass('active-edit');
	}
}	  	












 


	</script>






@endsection		
