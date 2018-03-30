@extends('main.layouts.main')
@section('style')
<link rel="stylesheet" href="{{ url('plugins/jquery-ui/jquery-ui.css') }}">

<style type="text/css">
tbody tr:nth-child(odd) {background: #DEDEDE!important}
tfoot tr:nth-child(odd) {background: #DEDEDE!important}
.content > .row {
  overflow-x: auto;
  white-space: nowrap;
}
</style>
@endsection
@section('content-wrapper')
<!-- data-toggle="modal" data-target="#modal-card-content" -->
	

	
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <img class="icon-title" src="{{ asset('public/img/icon/icon_purchasing_bidding_2.png') }}"> @lang('quotation.title')
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">@lang('quotation.title')</li>
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

		
		<section class="col-md-2 {{ (Auth()->user()->hasRole('officer')) ? 'col-officer' : 'col-headuser' }}"  style="@if(!Auth()->user()->hasRole('officer'))  display:none; @endif "  >
			<div class="box box-solid bg-rm-new-active box-parent">
            	<div class="box-header">
            		<img class="icon-title" src="{{ asset('public/img/icon/icon_new.png') }}" >
            	   <h3 class="box-title">
            	   		@lang('task.title_task_new')
            	   		<span class="title-new"></span>
            	   </h3>
            	</div>
	            <div class="box-body bg-rm-new" id="card_new">
					<div class="append-card">
						
					@if(count($quotations)>0)
						@foreach($quotations as $quotation)
							@if($quotation['status']==1)
							<div class="box box-solid card show-content" >
					            <div class="box-header">
					              	<h3 class="box-title">{{ $quotation['title'] }}</h3>
					              	<input type="hidden" class="box-id" value="{{  $quotation['id'] }}">
					              	<div class="box-tools pull-right card-btn-edit">
						                <button type="button" class="btn btn-box-tool" >
						                	<i class="fa fa-edit"></i>
						                </button>
						            </div>
					            </div>
					            <div class="box-body">
					            	<span class="vote-label"> <i class="fa fa-gavel"></i> ({{ $quotation['total_vote'].' / '. $quotation['total_can_vote'] }}) </span>
					        	</div>
					        </div>
					        @endif
						@endforeach
					@endif

					

					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer bg-rm-new addcard-hover">
	              	@lang('quotation.add_new_card')
	            </div>
            </div>

          	<div class="box box-solid bg-rm-cancel-active box-parent">
            	<div class="box-header">
            		<i class="fa fa-window-close-o"></i>
            	   <h3 class="box-title">
            	   		@lang('task.title_task_cancel')
            	   		<span class="title-cancel"></span>
            	   </h3>
            	</div>
	            <div class="box-body bg-rm-cancel" id="card_reject">
					<div class="append-card">
						
					@if(count($quotations)>0)
						@foreach($quotations as $quotation)
							@if($quotation['status']==4)
							<div class="box box-solid card show-content" >
					            <div class="box-header">
					              	<h3 class="box-title">{{ $quotation['title'] }}</h3>
					              	<input type="hidden" class="box-id" value="{{  $quotation['id'] }}">
					              	<div class="box-tools pull-right card-btn-edit">
						                <button type="button" class="btn btn-box-tool" >
						                	<i class="fa fa-edit"></i>
						                </button>
						            </div>
					            </div>
					            <div class="box-body">
					              <span class="vote-label"> <i class="fa fa-gavel"></i> ({{ $quotation['total_vote'].' / '. $quotation['total_can_vote'] }}) </span>
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
	
		<section class="col-md-2 {{ (Auth()->user()->hasRole('officer')) ? 'col-officer' : 'col-headuser' }}" >
			<div class="box box-solid bg-rm-voting-active box-parent">
            	<div class="box-header">
            		<img class="icon-title" src="{{ asset('public/img/icon/icon_voting.png') }}" >
            	   <h3 class="box-title">
            	   		@lang('quotation.title_task_voting')
            	   		<span class="title-voting"></span>
            	   </h3>
            	   
		           
            	</div>
	            <div class="box-body bg-rm-voting" id="card_voting" >
					<div class="append-card" >
						
					@if(count($quotations)>0)
						@foreach($quotations as $quotation)
							@if($quotation['status']==2)
							<div class="box box-solid card show-content" >
					            <div class="box-header">
					              	<h3 class="box-title">{{ $quotation['title'] }}</h3>
					              	<input type="hidden" class="box-id" value="{{  $quotation['id'] }}">
					              	<div class="box-tools pull-right card-btn-edit">
						                <button type="button" class="btn btn-box-tool" >
						                	<i class="fa fa-edit"></i>
						                </button>
						            </div>
					            </div>
					            <div class="box-body">
					              <span class="vote-label"> <i class="fa fa-gavel"></i> ({{ $quotation['total_vote'].' / '. $quotation['total_can_vote'] }}) </span>

					              	@if($hasHeaduser)
										<small class="label status-vote-label label-{{ ($quotation['user_has_vote']) ? 'success' : 'warning'  }}">
											@if($quotation['user_has_vote'])
												@lang('quotation.voted')
											@else
												@lang('quotation.wait_for_voting')
											@endif
						                  	<input type="hidden" class="status-vote-label-id" value="{{ $quotation['user_has_vote'] }}" >
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

		<section class="col-md-2 {{ (Auth()->user()->hasRole('officer')) ? 'col-officer' : 'col-headuser' }}" >
			<div class="box box-solid bg-rm-voted-active box-parent">
            	<div class="box-header">
            		<img class="icon-title" src="{{ asset('public/img/icon/icon_voted.png') }}" >
            	   <h3 class="box-title">
            	   		@lang('quotation.title_task_voted')
            	   		<span class="title-voted"></span>
            	   </h3>
            	   
		           
            	</div>
	            <div class="box-body bg-rm-voted" id="card_voted">
					<div class="append-card">

						@if(count($quotations)>0)
							@foreach($quotations as $quotation)
								@if($quotation['status']==3)
								<div class="box box-solid card show-content" >
						            <div class="box-header">
						              	<h3 class="box-title">{{ $quotation['title'] }}</h3>
						              	<input type="hidden" class="box-id" value="{{  $quotation['id'] }}">
						              	<div class="box-tools pull-right card-btn-edit">
							                <button type="button" class="btn btn-box-tool" >
							                	<i class="fa fa-edit"></i>
							                </button>
							            </div>
						            </div>
						            <div class="box-body">
						              <span class="vote-label"> <i class="fa fa-gavel"></i> ({{ $quotation['total_vote'].' / '. $quotation['total_can_vote'] }}) </span>

						              	@if($hasHeaduser)
										<small class="label status-vote-label label-{{ ($quotation['user_has_vote']) ? 'success' : 'warning'  }}">
											@if($quotation['user_has_vote'])
												@lang('quotation.voted')
											@else
												@lang('quotation.wait_for_voting')
											@endif
						                  	<input type="hidden" class="status-vote-label-id" value="{{ $quotation['user_has_vote'] }}" >
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
	
		<section class="col-md-2 {{ (Auth()->user()->hasRole('officer')) ? 'col-officer' : 'col-headuser' }}" >
			<div class="box box-solid bg-rm-inprogress-active box-parent">
            	<div class="box-header">
            		<i class="fa fa-clock-o"></i>
            	   <h3 class="box-title">
            	   		@lang('task.title_task_in_progress')
            	   		<span class="title-inprocess"></span>
            	   </h3>
            	   
		           
            	</div>
	            <div class="box-body bg-rm-inprogress" id="card_in_progress">
					<div class="append-card">

						@if(count($quotations)>0)
							@foreach($quotations as $quotation)
								@if($quotation['status']==5)
								<div class="box box-solid card show-content" >
						            <div class="box-header">
						              	<h3 class="box-title">{{ $quotation['title'] }}</h3>
						              	<input type="hidden" class="box-id" value="{{  $quotation['id'] }}">
						              	<div class="box-tools pull-right card-btn-edit">
							                <button type="button" class="btn btn-box-tool" >
							                	<i class="fa fa-edit"></i>
							                </button>
							            </div>
						            </div>
						            <div class="box-body">
						              <span class="vote-label"> <i class="fa fa-gavel"></i> ({{ $quotation['total_vote'].' / '. $quotation['total_can_vote'] }}) </span>
						        	</div>
						        </div>
						        @endif
							@endforeach
						@endif
					</div>
	            </div>
	            <div class="box-footer bg-rm-inprogress">
	              	&nbsp;
	            </div>
          	</div>
		  	<div class="box box-solid bg-rm-pending-active box-parent">
            	<div class="box-header">
            		<i class="fa fa-hourglass"></i>
            	   <h3 class="box-title">
            	   		@lang('task.title_task_pending')
            	   		<span class="title-pending"></span>
            	   </h3>
            	   
		           
            	</div>
	            <div class="box-body bg-rm-pending" id="card_pending">
					<div class="append-card">

						@if(count($quotations)>0)
							@foreach($quotations as $quotation)
								@if($quotation['status']==6)
								<div class="box box-solid card show-content" >
						            <div class="box-header">
						              	<h3 class="box-title">{{ $quotation['title'] }}</h3>
						              	<input type="hidden" class="box-id" value="{{  $quotation['id'] }}">
						              	<div class="box-tools pull-right card-btn-edit">
							                <button type="button" class="btn btn-box-tool" >
							                	<i class="fa fa-edit"></i>
							                </button>
							            </div>
						            </div>
						            <div class="box-body">
						              <span class="vote-label"> <i class="fa fa-gavel"></i> ({{ $quotation['total_vote'].' / '. $quotation['total_can_vote'] }}) </span>
						        	</div>
						        </div>
						        @endif
							@endforeach
						@endif
					</div>
	            </div>
	            <div class="box-footer bg-rm-pending">
	              	&nbsp;
	            </div>
          </div>
		</section>

		<section class="col-md-2 {{ (Auth()->user()->hasRole('officer')) ? 'col-officer' : 'col-headuser' }}" >
			<div class="box box-solid bg-rm-done-active box-parent">
            	<div class="box-header">
            		<i class="fa fa-check-square-o"></i>
            	   <h3 class="box-title">
            	   		@lang('task.title_task_done')
            	   		<span class="title-done"></span>
            	   </h3>
            	   
		           
            	</div>
	            <div class="box-body bg-rm-done" id="card_done">
					<div class="append-card">

						@if(count($taskDone)>0)
							@foreach($taskDone as $quotation)
								@if($quotation['status']==7)
								<div class="box box-solid card show-content" >
						            <div class="box-header">
						              	<h3 class="box-title">{{ $quotation['title'] }}</h3>
						              	<input type="hidden" class="box-id" value="{{  $quotation['id'] }}">
						              	<div class="box-tools pull-right card-btn-edit">
							                <button type="button" class="btn btn-box-tool" >
							                	<i class="fa fa-edit"></i>
							                </button>
							            </div>
						            </div>
						            <div class="box-body">
						              	<span class="vote-label"> <i class="fa fa-gavel"></i> ({{ $quotation['total_vote'].' / '. $quotation['total_can_vote'] }}) </span>

						              	@if (isset($quotation['doned_at']))
					                   	<small class="label label-success done-at-label">
					                  	@lang('task.done_at') {{ date('d/m/Y',strtotime($quotation['doned_at'])) }}</small>
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
								<h4 class="title"><i class="fa fa-user" ></i> @lang('quotation.title_user_can_vote')
									
								</h4>
					        	<div class="user-can-vote-list" >
					        		
					        	</div>
					    	</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="user-instead-vote">
								<h4 class="title"><i class="fa fa-user" ></i> @lang('quotation.title_user_instead_vote')
								<small class="total_instead_vote"></small>
								</h4>
					        	<div class="user-instead-vote-list" >
					        		
					        	</div>
					    	</div>
						</div>
					</div>
              </div>
              <div class="modal-body">
					<div class="row">
						<div class="col-sm-12 section-quotation-button-new" >
					 	 	<button type="button" class="btn btn-info btn-flat btn-quotation-new"  >Create Item</button>
						 	<button type="button" class="btn btn-info btn-flat btn-quotation-company" >@lang('quotation.insert_company')</button>
					 	</div>
				
		                <div class="col-sm-12" style="height: 20px;"></div>
		                <div class="col-sm-10" >
		                	<div class="row">
		                		<div class="col-sm-12" id="data-summary-quotation-table" style="overflow:auto;">
		                		</div>
		                	</div>
							
							
		                	@include('widgets.card.comment')	

							@include('widgets.card.history')	
							
		                </div>
		                
		                <div class="col-sm-2">
		                	<div class="menu-status" style="margin-bottom: 5px;">
		                		<h4>@lang('task.title_task_status')</h4>
								<button type="button" class="btn btn-block btn-social btn-default btn-status" >
							        New
							    </button>
		                	</div>
							
							<div class="menu-flow">
								<h4>@lang('task.title_change_task_status')</h4>
								<button type="button" class="btn btn-block btn-social bg-rm-new-active" id="btn_resubmit" style="color: #FFF;">
									<div>
							            <img src="{{ asset('public/img/icon/icon_new.png') }}" class="icon-task-menu" >
							        </div>
				                     @lang('task.btn_re_submit')
				                </button>
			                	<button type="button" class="btn btn-block btn-social btn-default bg-rm-voting-active" id="start_vote">
			                		<div>
							            <img src="{{ asset('public/img/icon/icon_voting.png') }}" class="icon-task-menu" >
							        </div>
				                     @lang('quotation.btn_voting')
				                </button>
				                <button type="button" class="btn btn-block btn-social btn-default bg-rm-voting-active" id="btn_manual_voted">
			                		<div>
							            <img src="{{ asset('public/img/icon/icon_voted.png') }}" class="icon-task-menu" >
							        </div>
				                     @lang('quotation.btn_voted')
				                </button>
				                
				               	<button type="button" class="btn btn-block btn-social bg-rm-done-active" id="btn_done" >
				                    <i class="fa fa-check-square-o"></i>@lang('task.btn_done')
				                </button>
				                <button type="button" class="btn btn-block btn-social bg-rm-inprogress-active" id="btn_in_progress" >
				                    <i class="fa fa-clock-o"></i>@lang('task.btn_in_progress')
				                </button>

				                <button type="button" class="btn btn-block btn-social bg-rm-pending-active" id="btn_pending" >
				                    <i class="fa fa-hourglass"></i> @lang('task.btn_pending')
				                </button>
				                <button type="button" class="btn btn-block btn-social bg-rm-cancel-active" id="cancel_vote">
				                    <i class="fa fa-window-close-o"></i>@lang('task.btn_cancel')
				                </button>
							</div>
							
							<div class="menu-action">
								<h4>@lang('task.title_action')</h4>
								<button type="button" class="btn btn-block btn-social btn-default" id="voting">
				                    <i class="fa fa-pie-chart"></i> @lang('quotation.btn_vote')
				                </button>
				                <button type="button" class="btn btn-block btn-social btn-danger" id="no_vote">
				                    <i class="fa fa-user-times"></i>@lang('quotation.btn_no_vote')
				                </button>
				                <button type="button" class="btn btn-block   btn-success" id="btn_voted" disabled="">
				                    You Voted
				                </button>
				                <button type="button" class="btn btn-block btn-info btn-show-set-company-winner" disabled="">
				                	@lang('quotation.btn_voted_no_result')
				                </button>
				                <button type="button" class="btn btn-block btn-social btn-default" id="btn_change_voted">
				                	 <i class="fa fa-refresh"></i>@lang('quotation.btn_cancel_last_vote')
				                </button>
				                <button type="button" class="btn btn-block  btn-info" id="success_vote" disabled="">
				                    โหวตแล้ว
				                </button>
				                 <button type="button" class="btn btn-block btn-social btn-info" id="btn_vote_instead" >
				                 	<i class="fa fa-exchange"></i>
				                    @lang('quotation.btn_vote_instead')
				                </button>

				                <a href="javascript:void(0)" type="button" target="_blank" class="btn btn-default btn-social btn-block btn-print"><i class="fa fa-print"></i> Print</a>
							</div>

							@include('widgets.card.menu-delete')
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
		
		<div class="modal fade" id="modal-attachment" >
          <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">ไฟล์แนบ</h4>
              </div>
              <div class="modal-body">
				 	<img src="" class="img-responsive" data-img-path="{{ asset('public/storage')}}" >
              </div>
              <div class="modal-footer">
                
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
	
		<div class="modal fade" id="modal-voting" >
          <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('quotation.title_vote')</h4>
               
              </div>
              <div class="modal-body">
              		<input type="hidden" id="instead_vote" value="0">
              		<div class="none" id="instead_name">
              			<h4>@lang('quotation.instead_name')</h4>
						<div class="col-sm-4">
							<input type="text" class="form-control" placeholder="@lang('quotation.instead_first_name')" id="instead_first_name" >	
						</div>
						<div class="col-sm-4">
							<input type="text" class="form-control" placeholder="@lang('quotation.instead_last_name')"  id="instead_last_name" >
						</div>
						<div class="col-sm-12"><small>@lang('quotation.instead_name_desc')</small></div>
						
						
						
					</div>
					
				 	<table id="voting-table" class="table table-bordered">
									<thead>
										<tr>
											<th width="50"></th>
											<th class="vm-ct" width="50">@lang('quotation.no')</th>
											<th class="vm-ct">@lang('quotation.company_compare')</th>
											<th class="vm-ct">@lang('quotation.price_net')</th>
											<th class="vm-ct">@lang('quotation.vote_result')</th>
										</tr>

									</thead>
									<tbody>
										
									</tbody>
									
								</table>
					<button id="instead_novote" class="btn btn-xs btn-danger btn-flat none" >
						<i class="fa fa-user-times"></i>@lang('quotation.btn_no_vote')
					</button>
					

              </div>
              <div class="modal-footer">
               
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>

		<div class="modal fade" id="modal-quotation-item" >
          <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('quotation.title_item')</h4>
                <input  type="hidden" id="insert_new_item" value="true">
              </div>
              <div class="modal-body">
          		<div class="row">
          			<div class="col-sm-12">
				 		<input type="text" id="quotaion_item_desc" style="height:30px;" class="insert-item-description" placeholder="@lang('quotation.description')">
				 		<input type="text" id="quotaion_item_amt" style="height:30px;" class="insert-item-amount" placeholder="@lang('quotation.amount')">
				 	 	<button type="button" id="btn-quotation-add-item" class="btn btn-info btn-sm btn-flat" style="margin-top: -3px;" >@lang('quotation.insert_item')</button>
					 	<div class="row">
	                		<div class="col-sm-12" id="data-quotation-item-table">
	                			<table id="table-quatation" class="table table-bordered">
									<thead>
										<tr>
											<th width="50"></th>
											<th class="vm-ct" width="50">@lang('quotation.no')</th>
											<th class="vm-ct">@lang('quotation.description')</th>
											<th class="vm-ct">@lang('quotation.amount')</th>
										</tr>

									</thead>
									<tbody>
										
									</tbody>
									
								</table>
	                		</div>
	                	</div>
				 	</div>
					<div class="col-sm-12" style="height: 20px;">
						<input type="hidden" id="quatation-table-data">
					</div>
          		</div>
				 	
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat btn-save-quotation">@lang('task.btn_save')</button>
                <button type="button" class="btn btn-info btn-flat btn-quotation-company-new"  >@lang('quotation.btn_save_and_compare')</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>


		<div class="modal fade" id="modal-quotation-company" >
          <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> @lang('quotation.company_compare')</h4>
              </div>
              <div class="modal-body">
              	<div class="row">
              		<input type="hidden" id="company_id" >
	                <div class="col-sm-12 " >
	                	<div class="row">
	                		<div class="col-sm-1 pull-right">
				        		<button type="button" class="btn btn-info btn-flat
					                      " id="btn-search-company" title="@lang('quotation.company_compare_search')">
					                      	<i class="fa fa-search"></i>
					            </button>
				        	</div>
	                		<div class="col-sm-11">
	                			<input type="text" class="form-control" id="supplier_name" name="supplier_name"  placeholder="@lang('quotation.company_compare_name')">
				        	</div>
	                	</div>
	                	<div class="row">
	                		<div class="col-sm-6">
								<div class="form-group">
				                    <label for="exampleInputEmail1">@lang('main.address')</label>
				                    <textarea class="form-control" rows="4" placeholder="@lang('main.address')" id="supplier_address"></textarea>
				                </div>
	                		</div>
	                		<div class="col-sm-6">
	                			<div class="row">
	                				<div class="col-sm-6">
		                				<div class="form-group">
						                    <label for="exampleInputEmail1">@lang('main.name')</label>
						                    <input type="text" class="form-control" id="contact_name"  placeholder="@lang('main.name')">
						                </div>
		                			</div>
		                			<div class="col-sm-6">
		                				<div class="form-group">
						                    <label for="exampleInputEmail1">@lang('main.tel')</label>
						                    <input type="text" class="form-control" id="contact_tel"  placeholder="@lang('main.tel')">
						                </div>
		                			</div>
	                			</div>
	                			<div class="row">
	                				<div class="col-sm-12">
	                					<div class="form-group">
						                    <input type="text" class="form-control" id="contact_email"  placeholder="@lang('main.email')">
						                </div>
	                				</div>
	                			</div>	
	                		</div>
	                	</div>
						
						<div class="row">
							<div class="col-sm-12">
								<table id="table-quatation-company" class="table table-bordered">
								<thead>
									<tr>
										
										<th class="vm-ct" width="50">@lang('quotation.no')</th>
										<th class="vm-ct">@lang('quotation.description') </th>
										<th class="vm-ct">@lang('quotation.amount')</th>
										<th width="50">@lang('quotation.price_per_unit')</th>
										<th width="50">@lang('quotation.amount_per_bath')</th>
										
									</tr>

								</thead>
								<tbody>
									
								</tbody>
								<tfoot>
									<tr>
										<td colspan="3">@lang('quotation.price_before_vat')</td>
										<td ></td>
										<td class="total-price-before-vat">0</td>
									</tr>
									<tr>
										<td colspan="3">@lang('quotation.discount')</td>
										<td ><input type="text" class="discount"></td>
										<td class="price-discount">0</td>
									</tr>
									<tr>
										<td colspan="3">@lang('quotation.vat') 7%</td>
										<td ><input type="checkbox" id="cal_vat" checked="" >@lang('quotation.calculate')</td>
										<td class="price-vat">0</td>
									</tr>
									<tr>
										<td colspan="3">@lang('quotation.net_price')</td>
										<td ></td>
										<td class="total-price-net">0</td>
									</tr>
									<tr>
										<td colspan="3">@lang('quotation.term_of_payment')</td>
										<td colspan="2"><input type="text" class="payment_term"></td>
									
									</tr>
									<tr>
										<td colspan="3">@lang('quotation.warranty')</td>
										<td colspan="2"><input type="text" class="guarantee"></td>
									</tr>
									<tr>
										<td colspan="3">@lang('quotation.remark')</td>
										<td colspan="2"><textarea class="form-control company-remark"></textarea>
										</td>
									</tr>
									<tr>
										<td colspan="3">@lang('quotation.attachment')</td>
										<td colspan="2"> <input type="file" id="company_attach">
										</td>
									</tr>
								</tfoot>
							</table>
							</div>
							
						</div>
	                </div>
	                <div class="col-sm-12">
					 	<div class="row">
					 		<div class="col-sm-12">
					 			<div class="box box-primary">
					              <div class="box-header with-border">
					                <h3 class="box-title">@lang('quotation.attachment')</h3>
					                <div class="box-tools pull-right">
					                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					                      </button>
					                </div>
					              </div>
					              <!-- /.box-header -->
					              <!-- form start -->
					            
					              

					                
					                <div class="box-body">
					                    <div class="row">
					                      <div class="col-sm-12">
					                          <table id="table-upload-file" class="table table-bordered table-striped">
					                            <thead>
					                            <tr>
					                              <th>@lang('quotation.no')</th>
					                              <th>@lang('quotation.file_name')</th>
					                              <th>@lang('quotation.file_type')</th>
					                              <th>@lang('quotation.file_size')</th>
					                              <th></th>
					                            </tr>
					                            </thead>
					                            <tbody id="append_upload">
					                            </tbody>
					                          
					                           
					                          </table>
					                      </div>
					                    </div>
					                    

					                    <!-- @for($i=1;$i<=4;$i++)
					                    <div class="row">
					                      <div class="col-sm-12 upload-file">
					                        <div class="form-group col-sm-4">
					                            <label for="exampleInputFile">ประเภทเอกสาร</label>
					                            <select class="doc_type">
					                                  <option value="0">บัตรประชาชน</option>
					                                  <option value="1">บัตรประชาชน</option>
					                                  <option value="2">ที่อยู่</option>
					                            </select>
					                        </div>
					                        <div class="form-group col-sm-8">
					                          <input type="file"  id="input_file_{{ $i }}"  name="input_file[]"  >
					                        </div>
					                      </div>
					                    </div>
					                    @endfor -->


					                </div>
					                <!-- /.box-body -->

					                <div class="box-footer">
					                 <!--  <button type="button" class="btn btn-default" id="btn-upload-file">นำส่ง</button> -->
					                </div>
					              
					        </div>
					 		</div>
					 		
					 	</div>
			        </div>

			        <div class="col-sm-12">
					 	<div class="row">
					 		<div class="col-sm-12">
					 			<div class="box box-primary">
					              <div class="box-header with-border">
					                <h3 class="box-title">@lang('quotation.attachmented')</h3>
					                <div class="box-tools pull-right">
					                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					                      </button>
					                </div>
					              </div>
					              <!-- /.box-header -->
					              <!-- form start -->
					            
					              

					                
					                <div class="box-body">
					                    <div class="row">
					                      <div class="col-sm-12">
					                          <table id="table-attachmented" class="table table-bordered table-striped">
					                            <thead>
					                            <tr>
					                              <th>@lang('quotation.no')</th>
					                              <th>@lang('quotation.file_name')</th>
					                             
					                             
					                            </tr>
					                            </thead>
					                            <tbody >
					                            </tbody>
					                          
					                           
					                          </table>
					                      </div>
					                    </div>
					                    

					                    <!-- @for($i=1;$i<=4;$i++)
					                    <div class="row">
					                      <div class="col-sm-12 upload-file">
					                        <div class="form-group col-sm-4">
					                            <label for="exampleInputFile">ประเภทเอกสาร</label>
					                            <select class="doc_type">
					                                  <option value="0">บัตรประชาชน</option>
					                                  <option value="1">บัตรประชาชน</option>
					                                  <option value="2">ที่อยู่</option>
					                            </select>
					                        </div>
					                        <div class="form-group col-sm-8">
					                          <input type="file"  id="input_file_{{ $i }}"  name="input_file[]"  >
					                        </div>
					                      </div>
					                    </div>
					                    @endfor -->


					                </div>
					                <!-- /.box-body -->

					                <div class="box-footer">
					                 <!--  <button type="button" class="btn btn-default" id="btn-upload-file">นำส่ง</button> -->
					                </div>
					              
					        </div>
					 		</div>
					 		
					 	</div>
			        </div>
              	</div>
              	
				
              </div>
              <div class="modal-footer">
              	 <button type="button" id="btn-cancel-company" class="btn btn-danger btn-flat">@lang('main.btn_cancel')
				  
                 </button>
                 <button type="button" id="btn-add-company" class="btn btn-info btn-flat">@lang('task.btn_save')
				   <i class="fa fa-spinner fa-spin fa-fw" style="display:none;"></i>
                 </button>
                
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>

		<div class="modal fade" id="modal-user-voting" >
          <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">User voted list</h4>
              </div>
              <div class="modal-body">
				 	
              </div>
              <div class="modal-footer">
                
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

 <!-- <script type="text/javascript" src="{{ url('js/card/card.js') }}"></script>  -->

 
 <script type="text/javascript" src="{{ url('js/quotation/quotation-item.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/quotation/quotation-company.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/quotation/quotation-vote.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/quotation/quotation-comment.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/quotation/quotation.js') }}"></script> 
  <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
 <script type="text/javascript" src="{{ url('js/quotation/filter.js') }}"></script> 
  <script>


	

  $(document).on("click",".del-doc",function(event) {
      imgCount = $('#append_upload tr').length;
      var rows = $(this).closest("tr") ; 
      rows.remove();
      console.log("click",imgCount);
      $("#append_upload tr td:first-child").each(function (i) {
          var j = ++i;
          $(this).text(j);
      });
  });

  $('#company_attach').on('change',function() {
	  var newFile = $(this).clone();
	  console.log(newFile);
	  var file = $('#company_attach')[0].files[0];
	  var file_name = file.name.replace(/\ /g,'')  ;
	  var file_ext = file_name.split('.').pop().toLowerCase();
	  var file_size = file.size ;
	  var reader = new FileReader();
	  newFile.removeAttr("id");
	  // newFile.attr("name","upload_file[]");
	  newFile.attr("class","file_upload");
	  newFile.attr("type","hidden");

	    reader.onload = function(e) {
            var data = {
            	name : file_name ,
            	extension : file_ext ,
            	size : file_size ,
            	data : e.target.result ,
            }
            newFile.val(JSON.stringify(data));
        }   
        reader.readAsDataURL(file);



	  var img_render = "<tr>"+
	                  "<td></td>"+
	                  "<td>"+file.name+"</td>"+
	                  "<td>"+file_ext+"</td>"+
	                  "<td>"+convertByte(file.size)+"</td>"+
	                  "<td><button type=\"button\" class=\"btn btn-danger del-doc\" > <i class=\"fa fa-close\"></i> </button>"+
	                  "</td>"+
	                "</tr>";
	   

	   $("#append_upload").append(img_render);


	   newFile.insertAfter($('.del-doc').last());
	 
	   $("#append_upload tr td:first-child").each(function (i) {
	      var j = ++i;
	      $(this).text(j);
	  });
	   $('#company_attach').val('');
	});
  </script>



 <!-- <script type="text/javascript" src="{{ url('plugins/autosize/autosize.min.js') }}"></script>  -->
 <script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
	<!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->

<script>



	// $("#btn-add-company").on('click',function(){ 
	// 	$("#modal-quotation-company").modal("toggle");
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
socket.on('quotation', function(data){
	if (typeof data==='string'){
	    data = JSON.parse(data);
	}
    console.log('[quotation]',data);

    var currentCardId =  $("#current_card_id").val();
   	if(data.quotation_id != null){
    	updateListCard(data);
    }


    if(data.quotation!=null&&data.quotation_id==currentCardId){
    	 createQuatationTable(data);
    }
    if(data.quotation_items!=null&&data.quotation_id==currentCardId){
    	createQuatationTableItem(data.quotation_items);
    } 
    if(data.quotation_historys!=null&&data.quotation_id==currentCardId){
    	createQuatationTableHistory(data.quotation_historys);
    }
    if(data.quotation!=null&&data.quotation_id==currentCardId){
    	createQuatationCard(data.quotation);
    }
    if(data.quotation_comments!=null&&data.quotation_id==currentCardId){
    	createQuotationComment(data.quotation_comments);
    }
    if(data.quotation_user_can_vote!=null&&data.quotation_id==currentCardId){
    	createQuotationUserCanVote(data.quotation_user_can_vote);
    } 

   	if(data.quotation_votes_instead!=null&&data.quotation_id==currentCardId)
   		createQuotationUserInsteadVote(data.quotation_votes_instead,data.quotation_total_user_can_vote);

  
})
function updateListCard(data){

	var cardId = data.quotation_id ;
	

	if(data.quotation!=null){
		var quotation  =data.quotation ;
		$('.box-id').each(function(index, el) {
	        if($(this).val()==cardId){
	            var ele = $(this).closest('.show-content').find('.box-title').text(quotation.title);
	        }
		});

		//--- Done at
		if(quotation.doned_at!=null){
			console.log(quotation.doned_at);
			$('.box-id').each(function(index, el) {
			    if($(this).val()==cardId){
			      	var ele = $(this).closest('.show-content').find('.done-at-label');
			      	var html = 'Done at '+moment.utc(quotation.doned_at).format('DD MMM YYYY');
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



		cardStatusMove(quotation);
	}

	if(data.quotation_votes!=null){
		var voting = data.quotation_votes ;
	    var userCanVote = data.quotation_user_can_vote ;

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
	$('#card_new,#card_reject,#card_in_progress,#card_pending').slimScroll({
    	height: '250px'
  	});
  	$('#card_voted,#card_voting,#card_done').slimScroll({
    	height: '600px'
  	});
  	countCardMenu();

	$(function(){					
		var $win = $(window); // or $box parent container
		var $box = $("#table-quatation tbody");

		// var $title = $("#modal-card-content .modal-title");
		
		$win.on("click.Bst", function(event){	
			if ( $box.has(event.target).length == 0 &&!$box.is(event.target)){
		      	// console.log("you clicked outside the box");
				hideInputItem();
			}
		
			
		});
	  
		@if(isset($quotationId))
			$(".show-content").each(function(){
				if($(this).find(".box-id").val()== {{ $quotationId }} ){
					$(this).click();
				}
			})
		@endif

	});
		


	  	// autosize(document.querySelectorAll('textarea'));
	  	// autosize(document.getElementById("description-edit-body-text"));
	  	$(function() {
	  		$("#start_vote").on("click", function(event) {
	  			var cardId = $("#current_card_id").val();
				var status = 2 ;
				ajaxUpdateStatus(cardId,status);
	  		});
	  		$("#btn_resubmit").on("click", function(event) {
	  			var cardId = $("#current_card_id").val();
				var status = 1 ;
				ajaxUpdateStatus(cardId,status);
	  		});
	  		$("#btn_pending").on("click", function(event) {
	  			var cardId = $("#current_card_id").val();
				var status = 6;
				ajaxUpdateStatus(cardId,status);
	  		});
	  		$("#btn_in_progress").on("click", function(event) {
	  			var cardId = $("#current_card_id").val();
				var status = 5 ;
				ajaxUpdateStatus(cardId,status);
	  		});
	  		$("#btn_done").on("click", function(event) {
	  			var cardId = $("#current_card_id").val();
				var status = 7 ;
				ajaxUpdateStatus(cardId,status);
	  		});

	  		$("#close-edit-title").on("click",function(){
				$(".show-edit-title").hide();
				$(".show-title").show();
			});
	  			
	  		$("#no_vote").on("click", function(event) {
	  			var cardId = $("#current_card_id").val();
				var route = "/purchase/quotation/"+cardId+"/novote?api_token="+api_token ;
			    var data = "" ;
			    ajaxPromise('GET',route,data).done(function(data){
			      // console.log(data);
			     	socket.emit('quotation',data);
					createCard(data);
			    })
	  		});
         
	  		$("#btn-remove-task").on("click", function(event) {
				var cardId = $("#current_card_id").val();
				var route = "/purchase/quotation/"+cardId+"?api_token="+api_token ;

				swal({
				  title: 'Are you sure?',
				  text: "คุณต้องการลบงานนี้ทิ้งใช่หรือไม่!",
				  type: 'warning',
				  showCancelButton: true,
				  confirmButtonText: 'ลบ',
				  cancelButtonText: 'ยกเลิก',
				  confirmButtonClass: 'btn btn-danger',
			      cancelButtonClass: 'btn btn-default',
				  buttonsStyling: false,
				  reverseButtons: true
				}).then((result) => {
				  if (result.value) {
					    ajaxPromise('POST',route,{_method:'DELETE'}).done(function(data){
					    	socket.emit('task',data);

					    	location.reload();

					       
					    }).fail(function(txt) {
					    	var error = JSON.stringify(txt);
		                       swal(
		                        'Error...',
		                        error,
		                        'error'
		                      )
					    });

				  } else if (result.dismiss === 'cancel') {
				    
				  }
				})



				
			});


	  		$("#table-quatation tbody").sortable({
		      	placeholder: "list-group-item-info",
		        stop: function(event, ui) {
		            $("#table-quatation tbody tr td:nth-child(2)").each(function (i) {
	                  var j = ++i;
	                  $(this).text(j);
	              });
		        }
		    });

	  	
	  		$("#edit-title").on("click",function(){
	  			console.log("[edit-title] click");
				var title =  $(this).parent().find('span').text();
				$("#card_title").val(title);
				$(".show-title").hide();
				$(".show-edit-title").show();
	  		});



	  		$("#save-edit-title").on("click",function(){
	  			var cardId = $("#current_card_id").val();
				var title =  $("#card_title").val();
				var route = "/purchase/quotation/"+cardId+"?api_token="+api_token;
				var data={ title :title,'_method':'PUT' };

				ajaxPromise('POST',route,data).done(function(data){
					socket.emit('quotation',data);
			        $(".show-title").find('span').text(title);
					$(".show-edit-title").hide();
					$(".show-title").show();
			    });
	  		});

	  		$(".btn-print").on("click",function(){
	  			var cardId = $("#current_card_id").val();
				var title =  $("#card_title").val();
				var route = $("#baseUrl").val()+"/purchase/quotation/"+cardId+"/print";
				window.open(route, title , 'fullscreen=yes');
	  		});


	  	});

$(".addcard-hover").on("click",function(){
	var add_card = "<div class=\"addcard-box\"><div class=\"box box-solid\" >"+
				"<div class=\"box-header\">"+
				"<textarea class=\"txt-area-card-title form-control\" rows=\"2\" style=\"border: 0;\">"+
				"</textarea>"+"</div></div>"+
				"<button class=\"btn bg-olive margin btn-add-card\" >"+(($("#app_local").val()=='th') ? ' เพิ่ม' : ' Add ' )+"</button>"+
				"<button class=\"btn btn-close-card\" ><i class=\"fa fa-close\"></i></button></div>";
	var rows = $(this).parent(".box-solid") ; 
	rows.find(".append-card").append(add_card);
	rows.find(".box-footer").hide();
	
	
	var parentHeight = rows.find(".append-card")[0].scrollHeight -  $(".addcard-box")[0].scrollHeight ;
	
	rows.find(".box-body").animate({
    scrollTop: parentHeight
  },'fast');
	$(".txt-area-card-title").focus();
})

$(document).on("click",".btn-add-card",function(event) {
	var rows = $(this).closest(".box-parent") ; 
	var txt = $("textarea.txt-area-card-title").val();
	console.log('ajaxCreateQuatation',txt);
	ajaxCreateQuatation(txt).done(function(data){
		console.log(data);

		var boxId = (typeof data.quotation_id =="undefined") ? data.task_id : data.quotation_id ;

		var card = "<div class=\"box box-solid card show-content\" data-toggle=\"modal\" data-target=\"#modal-card-content\">"+
				"<div class=\"box-header\">"+
				"<h3 class=\"box-title\">"+txt+
				"</h3>"+
				"<input type=\"hidden\" class=\"box-id\" value=\""+boxId+"\" >"+
				"</div>"+
				"<div class=\"box-body\">"+
				"<span class=\"vote-label\"> <i class=\"fa fa-gavel\"></i> (0 / 3) </span>"+
				"</div>"+
				"</div>";
			console.log(card);
			rows.find(".append-card").find(".addcard-box").remove();
			rows.find(".append-card").append(card);
			rows.find(".box-footer").show();

			openCard(txt,boxId);

			

		// location.reload();
    }).fail(function(txt) {
    	var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
    });
});



function hideInputItem(){
	if($("#table-quatation tbody tr.active-edit").length>0){
		var ele = $("#table-quatation tbody tr.active-edit");
		$(ele).find('td:gt(1)').each (function() {
			var val = $(this).find('input').val() ;
			$(this).find('input').attr("type","hidden");
			$(this).find('span').text(val).show();
		});  

		ele.removeClass('active-edit');
	}
}	  	

function ajaxCreateQuatation(txt){
	var dfd = $.Deferred();
	var url = $("#apiUrl").val() ;
	$.ajax({
		url: $("#apiUrl").val()+'/purchase/quotation?api_token='+api_token,
		type: 'POST',
		dataType: 'json',
		data: { 'title': txt},
	})
	.done(function(res) {
		console.log(res);
		if(res.result=="true"){
			dfd.resolve(res.response);
		}else{
			dfd.reject( res.errors );
		}
	})
	.fail(function() {
		dfd.reject( "error");
	})
	return dfd.promise();
}










 


	</script>






@endsection		
