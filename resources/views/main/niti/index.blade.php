@extends('main.layouts.main')
@section('style')
@endsection
@section('content-wrapper')
	<style type="text/css">
		
		.addcard-hover{  opacity:1;  }
		.addcard-hover:hover{  opacity:0.5;cursor: pointer;  }

		.card-btn-edit {
			display: none;
		}
		.card:hover {
			cursor: pointer;
		}
		.card:hover .card-btn-edit{
			display: block;
		}
		#task-edit-description-btn:hover { cursor:pointer;  }
	</style>


    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{ $title }}
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Quatation</li>
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
	            <div class="box-body bg-gray" id="card_new">
					<div class="append-card">
						
					@for ($i = 0; $i <= 1; $i++)
       					<div class="box box-solid card show-content" data-toggle="modal" data-target="#modal-card-content">
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
	
		 <div class="modal fade" id="modal-card-content" >
          <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Title</h4>
              </div>
              <div class="modal-body">
                <div class="col-sm-12 " id="row-task-edit">
                	<div id="description" style="display: none;">
                		<h5>Description</h5><a href="javascript:void(0)" >Edit</a>
                		<div id="description-body"></div>
                	</div>
                	<div id="description-edit" style="display: none;">
                		<h5>Description</h5>
                		<div id="description-edit-body" style="overflow: hidden;">
                			<textarea class="form-control" rows="3" id="description-edit-body-text" style="overflow:auto;background:#DDD;"></textarea>
                			<button id="description-edit-body-add-btn" class="btn btn-success">Add</button>
		                	<button class="btn" id="description-edit-body-close-btn" ><i class="fa fa-close"></i></button>
                		</div>
                	</div>
                		<p id="task-edit-description-btn">edit description...</p>
		                <div class="task-edit-description" style="display: none;">
		                	<textarea class="form-control" rows="3" id="task-description"></textarea>
		                	<BR>
		                	<button id="task-edit-description-add-btn" class="btn btn-success">Add</button>
		                	<button class="btn" id="task-edit-description-clost-btn" ><i class="fa fa-close"></i></button>
		                </div>
                </div>
	
				<div class="col-sm-12" style="height: 20px;">
					<input type="hidden" id="quatation-table-data">
				</div>
                <div class="col-sm-12">
                	<div class="row" id="section-add-item">
                		<div class="col-sm-4">
                			<input type="text" class="form-control" id="quotation-item-name" placeholder="ชื่อรายการ">
                		</div>
                		<div class="col-sm-4">
                			<input type="text" class="form-control" id="quotation-item-amount"  placeholder="จำนวน">
                		</div>
                		<div class="col-sm-4">
                			 <button type="button" id="btn-add-quotation" class="btn btn-info btn-flat">เพิ่มรายการของ</button>
                		</div>
                		
                	</div>
                	<div class="row" id="section-company" style="display: none;">
                		<div class="col-sm-8">
                			<input type="text" class="form-control" id="company-name" placeholder="ชื่อบริษัท">
                		</div>
                		<div class="col-sm-4">
                			 <button type="button" id="btn-add-company" class="btn btn-info btn-flat">เพิ่มรายการบริษัท</button>
                		</div>
                		
                	</div>
	

		<style>
			.vm-ct{
				vertical-align: middle !important ;text-align: center;
			}
		</style>

		            <div class="row">
                		<div class="col-sm-12" id="quotation">
							<!-- <table class="table table-bordered">
								<thead>
									<tr>
										<th rowspan="2" class="vm-ct">ลำดับ</th>
										<th rowspan="2" class="vm-ct">ชื่อรายการ</th>
										<th rowspan="2" class="vm-ct">จำนวน</th>
										<th colspan="3" class="text-center">ราคา</th>
									</tr>
									<tr>

										<th>บริษํท A</th>
										<th>บริษํท B</th>
										<th>บริษํท C</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td >0</td>
										<td >0</td>
										<td >0</td>
										<td >0</td>
										<td >0</td>
										<td >0</td>
										
	
									</tr>
								</tbody>
							</table> -->
                		</div>
                	</div>
                	<div class="row">
                		<button type="button" id="btn-save-quotation" class="btn btn-primary">save quotation</button>
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
		                	<textarea class="form-control" rows="1" id="task-comment-description"></textarea>
		                	<BR>
		                	<button id="task-comment-btn" class="btn btn-success">Add</button>
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


@endsection

@section('javascript')
 <script type="text/javascript" src="{{ url('plugins/autosize/autosize.min.js') }}"></script> 

	<script type="text/javascript">
		$('#card_new,#card_reject,#card_in_progress,#card_pending').slimScroll({
	    	height: '250px'
	  	});
	  	$('#card_accept,#card_done').slimScroll({
	    	height: '600px'
	  	});
	  	// autosize(document.querySelectorAll('textarea'));
	  	autosize(document.getElementById("description-edit-body-text"));
	  	$(function() {
	  		// $('textarea#description-edit-body-text').autogrow();
	  		$("#btn-save-quotation").on('click',function(){
	  			var text = $('#quotation').clone().html() ;
	  			console.log('q ',text);
	  			$("#quatation-table-data").val(text);
	  		});

	  		

	  		$("#btn-add-company").on('click',function(){
	  			var tdCount = $("#table-quatation thead tr:first td").length;
	  			var trCount = $("#table-quatation tbody tr").length;
	  			var tbCount = $("#table-quatation").length;
	  			if(tbCount>0){
	  				var colCount = $("#table-quatation thead tr:eq(1) th").length ;
	  				console.log('colCount',colCount);
	  				if(colCount==0 ){
	  					$("#table-quatation thead tr:first").append("<th colspan=\"1\" class=\"text-center\" >ราคา</th> ");
	  					$("#table-quatation thead").append("<tr><th> บริษัท "+$("#company-name").val()+"</th></tr>");
	  				}else{
	  					colCount++ ;
	  					$("#table-quatation thead tr:first th:eq(3)").attr("colspan",colCount);
	  					$("#table-quatation thead tr:eq(1)").append("<th> บริษัท "+$("#company-name").val()+"</th>");
	  				}

	  				
	  				
	  				$('#table-quatation tbody tr').each(function(){
					       $(this).append("<td >0</td>");
					});
	  			}
	  			$("#company-name").val('');
	  		});


	  		var quatationRow = 1 ;
	  		var quatationTable ='';
	  		$("#btn-add-quotation").on("click",function(){
	  			
	  			if(quatationRow ==1){
	  				quatationTable += "<table id=\"table-quatation\" class=\"table table-bordered\">"+
	  				"<thead><tr>"+
	  				"<th style=\"width:10px;\" rowspan=\"2\" class=\"vm-ct\">ลำดับ</th>"+
	  				"<th rowspan=\"2\" class=\"vm-ct\">ชื่อรายการ</th>"+
	  				"<th rowspan=\"2\" class=\"vm-ct\">จำนวน</th>"+
	  			
	  				"</tr></thead>"+
	  				"<tbody id=\"quotation-append-row\"><tr>"+
	  				"<td>1</td>"+
	  				"<td>"+$("#quotation-item-name").val()+"</td>"+
	  				"<td>"+$("#quotation-item-amount").val()+"</td>"+
	  				
	  				"</tr></tbody>"+
	  				"</table>";
	  				quatationRow++;
	  				$("#quotation").html(quatationTable);
	  				$("#section-company").show();
	  			}else{

	  				var tdCount = $("#table-quatation thead tr:first th").length;
	  				console.log('total [td]',tdCount);
	  				console.log('[td]',$("#table-quatation tbody tr:first td:gt(2)").length);

	  				quatationTable = "<tr><td>"+quatationRow+"</td>"+
	  				"<td>"+$("#quotation-item-name").val()+"</td>"+
	  				"<td>"+$("#quotation-item-amount").val()+"</td>" ;

	  				if(tdCount>3){
	  					$('#table-quatation tbody tr:first td:gt(2)').each(function(){
					      quatationTable += "<td>0</td>" ;
						});
	  				}

	  				quatationTable += "</tr>" ;
	  				

	  				$("#quotation-append-row").append(quatationTable);
	  				quatationRow++;
	  			}
	  			$("#quotation-item-name").val('');
	  			$("#quotation-item-amount").val('');
	  		});


	  		$(".addcard-hover").on("click",function(){
	  			var add_card = "<div class=\"addcard-box\"><div class=\"box box-solid\" >"+
	  						"<div class=\"box-header\">"+
	  						"<textarea class=\"txt-area-card-title form-control\" rows=\"2\" style=\"border: 0;\">"+
	  						"</textarea>"+"</div></div>"+
	  						"<button class=\"btn bg-olive margin btn-add-card\" >Add</button>"+
	  						"<button class=\"btn btn-close-card\" ><i class=\"fa fa-close\"></i></button></div>";
	  			var rows = $(this).parent(".box-solid") ; 
	  			rows.find(".append-card").append(add_card);
	  			rows.find(".box-footer").hide();
	  			
	  			console.log($(".addcard-box")[0].scrollHeight);
	  			console.log(rows.find(".append-card")[0].scrollHeight);
	  			var parentHeight = rows.find(".append-card")[0].scrollHeight -  $(".addcard-box")[0].scrollHeight ;
	  			console.log(parentHeight);

	  			rows.find(".box-body").animate({
				    scrollTop: parentHeight
				  },'fast');
	  			$(".txt-area-card-title").focus();
	  		})

	  		$('#task-edit-description-btn').click(function() {
	  			$('.task-edit-description').show();
	  			$('#task-edit-description-btn').hide();
	  		});
	  		$('#task-edit-description-clost-btn').click(function() {
	  			$('.task-edit-description').hide();
	  			$('#task-edit-description-btn').show();
	  		});
	  		$('#task-edit-description-add-btn').click(function() {
	  			var text = $('#task-description').val();
	  			console.log(text);
	  			$('#description-body').text(text);
	  			$('.task-edit-description').hide();
	  			$('#task-edit-description-btn').hide();
	  			$('#description').show(text);
	  		});		
			
			$('#description-body').click(function() {
				var ele = $('#description-body') ;
				console.log(ele.outerHeight(),ele.height(),ele.innerHeight());

				var height = $('#description-body').outerHeight();
				var width = $('#description-body').outerWidth();
	  			var text = $('#description-body').text();
	  			$('textarea#description-edit-body-text').val(text);
	  			$('textarea#description-edit-body-text').innerHeight(height);
	  			$('textarea#description-edit-body-text').innerWidth(width);
	  			
	  			$('#description').hide();
	  			$('#description-edit').show();
	  		});

			$('#description-edit-body-close-btn').click(function() {
	  			var text = $('#description-edit-body-text').val();
	  			$('#description-body').text(text);
	  			$('#description-edit').hide();
	  			$('#description').show();
	  		});

			$('#description-edit-body-add-btn').click(function() {
	  			var text = $('#description-edit-body-text').val();
	  			$('#description-body').text(text);
	  			$('#description-edit').hide();
	  			$('#description').show();
	  		});

	  	});
	  	
		$(".content-wrapper").on("click","#table-quatation tbody td:not(.input-edit)",function(event) {
	  			
				//---  another input change to text 
				if($("#table-quatation tbody td.input-edit").length>0){
					var ele = $("#table-quatation tbody td.input-edit");
		  			var val = ele.find('input').val() ;
		  			ele.removeClass('input-edit');
		  			ele.text(val) ;
				}

	  			var text = $(this).text();
	  			var input = "<input type=\"text\"  value=\""+text+"\">"
	  			$(this).addClass('input-edit').html(input);

	  	});

	
		$("#modal-card-content").on("click",function(event) {
	  		if($("#table-quatation tbody td.input-edit").length>0){
					var ele = $("#table-quatation tbody td.input-edit");
		  			var val = ele.find('input').val() ;
		  			ele.removeClass('input-edit');
		  			ele.text(val) ;
			}
	  	});

	  	$(".content-wrapper").on("click",".show-content",function(event) {
	  			console.log("click");
	  			var title = $(this).find(".box-title").text() ; 

	  			var modal = $('#modal-card-content') ;

	  			modal.find('.modal-title').text(title);

	  			var quatation = $("#quatation-table-data").val();
	  			if(quatation!=''){
	  				$("#quotation").html(quatation);
	  			}

	  			if($("#table-quatation").length){
	  				$("#section-company").show();
	  			}


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
	  			var card = "<div class=\"box box-solid card show-content\" data-toggle=\"modal\" data-target=\"#modal-card-content\">"+
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
