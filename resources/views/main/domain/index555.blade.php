@extends('main.layouts.main')


@section('style')
@endsection

@section('content-wrapper')
	
	<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{ $title }}
        <small>Control panel</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      
      <!-- Main row -->
      <div class="row">
		<section class="col-lg-3">
			<div class="box box-solid bg-gray-active box-parent">
				
            	<div class="box-header">
            		<i class="fa fa-comments-o"></i>
            	   <h3 class="box-title">
            	   		New
            	   </h3>
            	   <span data-toggle="tooltip" title="3 New Messages" class="badge">3</span>
		           <div class="box-tools pull-right">
		           		 
		                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		                </button>
		                <div class="btn-group">
		                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
		                    <i class="fa fa-wrench"></i></button>
		                  <ul class="dropdown-menu" role="menu">
		                    <li><a href="#">Action</a></li>
		                    <li><a href="#">Another action</a></li>
		                    <li><a href="#">Something else here</a></li>
		                    <li class="divider"></li>
		                    <li><a href="#">Separated link</a></li>
		                  </ul>
		                </div>
		            </div>
            	</div>
	            <div class="box-body bg-gray" id="card_pending">
					<div class="append-card">
						
					@for ($i = 0; $i <= 3; $i++)
       					<div class="box box-solid card" data-toggle="modal" data-target="#modal-default">
				            <div class="box-header">
				              	<h3 class="box-title">{{  'card '.$i }}</h3>
				              	<div class="box-tools pull-right card-btn-edit">
					                <button type="button" class="btn btn-box-tool" >
					                	<i class="fa fa-edit"></i>
					                </button>
					            </div>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                </div>
				        	</div>
				        </div>
    				@endfor

					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer bg-gray addcard-hover">
	              	Add a card ...
	            </div>

            </div>

			

          	<div class="box box-solid box-danger">
				
            	<div class="box-header">
            		<i class="fa fa-comments-o"></i>
            	   <h3 class="box-title">
            	   		Reject
            	   </h3>
            	   <span data-toggle="tooltip" title="3 New Messages" class="badge">3</span>
		           <div class="box-tools pull-right">
		           		 
		                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		                </button>
		                <div class="btn-group">
		                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
		                    <i class="fa fa-wrench"></i></button>
		                  <ul class="dropdown-menu" role="menu">
		                    <li><a href="#">Action</a></li>
		                    <li><a href="#">Another action</a></li>
		                    <li><a href="#">Something else here</a></li>
		                    <li class="divider"></li>
		                    <li><a href="#">Separated link</a></li>
		                  </ul>
		                </div>
		            </div>
            	</div>
	            <div class="box-body bg-red-gradient" id="card_reject">
					<div class="">

						<div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>
				          <div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>

				          <div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>
					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer">
	              	Add a card ...
	            </div>

          </div>
		</section>

		<section class="col-lg-3">
			<div class="box box-solid box-info">
				
            	<div class="box-header">
            		<i class="fa fa-comments-o"></i>
            	   <h3 class="box-title">
            	   		Accept
            	   </h3>
            	   <span data-toggle="tooltip" title="3 New Messages" class="badge">3</span>
		           <div class="box-tools pull-right">
		           		 
		                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		                </button>
		                <div class="btn-group">
		                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
		                    <i class="fa fa-wrench"></i></button>
		                  <ul class="dropdown-menu" role="menu">
		                    <li><a href="#">Action</a></li>
		                    <li><a href="#">Another action</a></li>
		                    <li><a href="#">Something else here</a></li>
		                    <li class="divider"></li>
		                    <li><a href="#">Separated link</a></li>
		                  </ul>
		                </div>
		            </div>
            	</div>
	            <div class="box-body bg-aqua-gradient" id="card_accept" >
					<div class="">

						<div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>
				          <div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>

				          <div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>
					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer">
	              	Add a card ...
	            </div>

          </div>
		</section>
		
		

		<section class="col-lg-3">
			<div class="box box-solid box-warning">
				
            	<div class="box-header">
            		<i class="fa fa-comments-o"></i>
            	   <h3 class="box-title">
            	   		In progress
            	   </h3>
            	   <span data-toggle="tooltip" title="3 New Messages" class="badge">3</span>
		           <div class="box-tools pull-right">
		           		 
		                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		                </button>
		                <div class="btn-group">
		                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
		                    <i class="fa fa-wrench"></i></button>
		                  <ul class="dropdown-menu" role="menu">
		                    <li><a href="#">Action</a></li>
		                    <li><a href="#">Another action</a></li>
		                    <li><a href="#">Something else here</a></li>
		                    <li class="divider"></li>
		                    <li><a href="#">Separated link</a></li>
		                  </ul>
		                </div>
		            </div>
            	</div>
	            <div class="box-body bg-yellow-gradient" id="card_in_progress">
					<div class="">

						<div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>
				          <div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>

				          <div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>
					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer">
	              	Add a card ...
	            </div>

          </div>
			
		  <div class="box box-solid bg-purple-active color-palette">
				
            	<div class="box-header">
            		<i class="fa fa-comments-o"></i>
            	   <h3 class="box-title">
            	   		Pendding
            	   </h3>
            	   <span data-toggle="tooltip" title="3 New Messages" class="badge">3</span>
		           <div class="box-tools pull-right">
		           		 
		                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		                </button>
		                <div class="btn-group">
		                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
		                    <i class="fa fa-wrench"></i></button>
		                  <ul class="dropdown-menu" role="menu">
		                    <li><a href="#">Action</a></li>
		                    <li><a href="#">Another action</a></li>
		                    <li><a href="#">Something else here</a></li>
		                    <li class="divider"></li>
		                    <li><a href="#">Separated link</a></li>
		                  </ul>
		                </div>
		            </div>
            	</div>
	            <div class="box-body bg-purple disabled color-palette" id="card_pending">
					<div class="">

						<div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>
				          <div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>

				          <div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>
					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer">
	              	Add a card ...
	            </div>

          </div>
		</section>

		<section class="col-lg-3">
			<div class="box box-solid box-success">
				
            	<div class="box-header">
            		<i class="fa fa-comments-o"></i>
            	   <h3 class="box-title">
            	   		Done
            	   </h3>
            	   <span data-toggle="tooltip" title="3 New Messages" class="badge">3</span>
		           <div class="box-tools pull-right">
		           		 
		                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		                </button>
		                <div class="btn-group">
		                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
		                    <i class="fa fa-wrench"></i></button>
		                  <ul class="dropdown-menu" role="menu">
		                    <li><a href="#">Action</a></li>
		                    <li><a href="#">Another action</a></li>
		                    <li><a href="#">Something else here</a></li>
		                    <li class="divider"></li>
		                    <li><a href="#">Separated link</a></li>
		                  </ul>
		                </div>
		            </div>
            	</div>
	            <div class="box-body bg-green-gradient" id="card_done">
					<div class="">

						<div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>
				          <div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>

				          <div class="box box-solid">
				            <div class="box-header">
				              <i class="fa fa-comments-o"></i>

				              <h3 class="box-title">Chat</h3>
				            </div>
				            <div class="box-body">
				              <!-- drag handle -->
			                  <span class="handle">
			                        <i class="fa fa-ellipsis-v"></i>
			                        <i class="fa fa-ellipsis-v"></i>
			                      </span>
			                  <!-- checkbox -->
			                  <input type="checkbox" value="">
			                  <!-- todo text -->
			                  <span class="text">Design a nice theme</span>
			                  <!-- Emphasis label -->
			                  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
			                  <!-- General tools such as edit or delete-->
			                  <div class="tools">
			                    <i class="fa fa-edit"></i>
			                    <i class="fa fa-trash-o"></i>
			                  </div>
				            </div>
				            
				          </div>
					</div>
	            </div>
	            <!-- /.chat -->
	            <div class="box-footer">
	              	Add a card ...
	            </div>

          </div>
		</section>

		

		
        
       
      </div>
      <!-- /.row (main row) -->
	
		 <div class="modal fade" id="modal-default">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Title</h4>
              </div>
              <div class="modal-body">
                <div class="col-sm-12">
                		<p id="task-edit-description-btn">edit description...</p>
		                <div class="task-edit-description">
		                	<textarea class="form-control" rows="3" id="task-description"></textarea>
		                	<BR>
		                	<button id="task-description-btn" class="btn btn-success">Add</button>
		                	<button class="btn" ><i class="fa fa-close"></i></button>
		                </div>
                	</div>
                <div class="col-sm-12" style="height: 20px;"></div>

                <div class="col-sm-12">
                	<div class="history">
                		<h6>pok created </h6>
                		<h6>pok issued to แจ้งบปัญหา </h6>
                		<h6>pok move to pioritized Low: </h6> 
                	</div>
		                
                </div>
              </div>
              <div class="modal-footer">
                <div class="task-comment">
		                	<textarea class="form-control" rows="1" id="task-description"></textarea>
		                	<BR>
		                	<button id="task-description-btn" class="btn btn-success">Add</button>
		                	<button class="btn" ><i class="fa fa-close"></i></button>
		                </div>
                <button type="button" class="btn btn-primary">Save changes</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection

@section('javascript')
	<script type="text/javascript">
		$('#card_new,#card_reject,#card_in_progress,#card_pending').slimScroll({
	    	height: '250px'
	  	});
	  	$('#card_accept,#card_done').slimScroll({
	    	height: '600px'
	  	});

	  	$(function() {
	  		$(".addcard-hover").on("click",function(){
	  			var add_card = "<div class=\"addcard-box\"><div class=\"box box-solid\">"+
	  						"<div class=\"box-header\">"+
	  						"<textarea class=\"txt-area-card-title\" rows=\"2\" style=\"border: 0;\">"+
	  						"</textarea>"+"</div></div>"+
	  						"<button class=\"btn bg-olive margin btn-add-card\" >Add</button>"+
	  						"<button class=\"btn btn-close-card\" ><i class=\"fa fa-close\"></i></button></div>";
	  			var rows = $(this).parent(".box-solid") ; 
	  			rows.find(".append-card").append(add_card);
	  			rows.find(".box-footer").hide();
	  		})


	  	});
	  	
	  	$(".content-wrapper").on("click",".btn-close-card",function(event) {
	  			console.log("click");
	  			var rows = $(this).closest(".box-parent") ; 
	  			rows.find(".append-card").find(".addcard-box").remove();
	  			rows.find(".box-footer").show();
	  	});

	  	$(".content-wrapper").on("click",".btn-add-card",function(event) {
	  			var rows = $(this).closest(".box-parent") ; 
	  			var txt = $("textarea.txt-area-card-title").val();
	  			console.log(txt);
	  			var card = "<div class=\"box box-solid card\">"+
	  						"<div class=\"box-header\">"+
	  						"<h3 class=\"box-title\">"+txt
	  						"</h3>"+"</div></div>";
	  			rows.find(".append-card").find(".addcard-box").remove();
	  			rows.find(".append-card").append(card);
	  			rows.find(".box-footer").show();
	  	});

	  	$(".content-wrapper").on("click","#task-edit-description",function(event) {
	  			var add_description = "<div class=\"addcard-box\"><div class=\"box box-solid\">"+
	  						"<div class=\"box-header\">"+
	  						"<textarea class=\"txt-area-card-title\" rows=\"2\" style=\"border: 0;\">"+
	  						"</textarea>"+"</div></div>"+
	  						"<button class=\"btn bg-olive margin btn-add-card\" >Add</button>"+
	  						"<button class=\"btn btn-close-card\" ><i class=\"fa fa-close\"></i></button></div>";
	  			var rows = $(this).parent(".box-solid") ; 
	  			rows.find(".append-card").append(add_card);
	  			rows.find(".box-footer").hide();
	  	});

	  	

	  	// $(".content-wrapper").on("mouseover",".card",function(event) {
	  	// 		console.log("hover");
	  			
	  	// });
	  	

	</script>
@endsection		
