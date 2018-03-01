@extends('main.layouts.main')
@section('style')

<style type="text/css">
	.img-responsive {
		margin:0 auto !important;
	}
	textarea#description{
		resize:none;
	}
	.btn-hover:hover {
		color:#3c8dbc;cursor: pointer;
	}
	.btn-close-image {
		position:absolute;top:0;right:10px;background:#f56954;color:#FFF;
		display: none;
		border-radius: 50%;
		height:22px;line-height:21px;
		font-size: 16px;
		padding:0 4px;
		/*line-height:20px;*/
	}
	.parent-img:hover .btn-close-image{
		display: block;
	}
	.parent-img .img-responsive{
		width:100px;height:100px;
	}
	.img-resize-height img:hover{ cursor: pointer; }
.thumbnail {
  position: relative;
  width: 100%;
  /*height: 200px;*/
  overflow: hidden;
}
.thumbnail img {
  position: absolute;
  left: 50%;
  top: 50%;
  height: 100%;
  width: auto;
  -webkit-transform: translate(-50%,-50%);
      -ms-transform: translate(-50%,-50%);
          transform: translate(-50%,-50%);
          border:2px solid #FFF;
}
.thumbnail img.portrait {
  width: 100%;
  height: auto;
}
.nopadding{
	margin: 0;padding: 0;
}
</style>
@endsection
@section('content-wrapper')
<!-- data-toggle="modal" data-target="#modal-card-content" -->
	
	<input type="hidden" id="route" value="{{ $route }}" >
	
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      	<i class="fa fa-circle-o"></i>
      	@lang('sidebar.public_information')
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('sidebar.public_information')</li>
      </ol>
    </section>
	
    <!-- Main content -->
    <section class="content">
    	<div class="row" >
		 	<div class="col-sm-6">
          	<!-- Box Comment -->
          		<div class="row">
          			<div class="col-sm-12">
          	@if($canPost=="true")
          		
		          		<div class="box box-widget">
		            		<div class="box-header with-border">
						 		<label for="file_upload" class="btn-hover"  >
		                    		<i class="fa fa-camera" ></i>  @lang('post.picture')
		                  		</label>
		                  		<input id="file_upload" name='doc_file[]' type="file" style="display:none;" >

		              		
				            	<!-- <div class="box-tools">
					               
					                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					               
					            </div> -->
		            		</div>
		            <!-- /.box-header -->
		            		<div class="box-body">
		            			<div class="row">
		            				<div class="col-sm-1">
		            					<div class="user-block">
		                					<img class="img-circle" src="{{ auth()->user()->getProfileImg() }}" alt="User Image">
		               					</div>
		            				</div>
		            				<div class="col-sm-11">
		            					<textarea class="form-control" style="border:0;" id="description" rows="4" placeholder="@lang('post.content')"></textarea>
		            				</div>
		            		
		            			</div>
		            			<div class="row">
		            				<div class="col-sm-12">
		            					<div class="form-group" id="preview-img" style="display: none;" >
				                  	<!-- <div class="col-sm-3 parent-img" style="display: none;">
										<small class="pull-right badge btn-close-image"><i class="fa fa-close"></i></small>
					                    <img src="../dist/img/user3-128x128.jpg" class="img-responsive"  >
					                </div> -->

			            				</div>
			            			</div>
			               
			            		</div>
		            		</div>
		            <!-- /.box-footer -->
				            <div class="box-footer">
				              	<button  class="btn btn-primary btn-save-post" >
				              		@lang('post.post')
				              		<i class="fa fa-spinner fa-spin fa-fw none"></i>
				              	</button>
				            </div>
		            <!-- /.box-footer -->
		          		</div>
          	@else
			          	<div class="row">
				          	<div class="col-sm-12">
				            	<div class="callout callout-info">
				              		<h4>@lang('post.you_baned')</h4>
				            	</div>
				          	</div>
				      	</div>
          	@endif
          			</div>
          		</div>
          		@include('widgets.post.main')
        	</div>
			
			@if(Auth()->user()->hasRole('admin')&& count($members) > 0)
		    <div class="col-sm-3">
		        <div class="box box-primary" >
		            <div class="box-header">
		                <i class="fa fa-users"></i>
		                <h3 class="box-title">@lang('post.member_is_ban')</h3>
		            </div>
		            <div class="box-body chat member" id="member_baned_list" >
		            @if( count($members) > 0)
		                @foreach ($members as $member)
		                <div class="item">
		                    <div class="pull-right">
		                        <button class="btn btn-success btn-xs btn-unban-user" title="@lang('post.unban_user')" data-id="{{ $member['member_id'] }}" > <i class="fa fa-user-plus"></i> </button>
		                    </div>
		                    <img src="{{ $member['img'] }}" >
		                    
		                    <p class="message" >
		                        <a href="javascript:void(0)" class="name" style="margin-top:5px;">{{ $member['first_name']." ".$member['last_name'] }}
		                        </a>
		                    </p> 
		                </div>
		                @endforeach
		            @endif
		            </div>
		                  <!-- /.chat -->
		                  
		        </div>            
		    </div>  
		    @endif

       </div>

	
		
		

		
		

      <!-- Main row -->
     
      <!-- /.row (main row) -->

		
		

		

    </section>
    <!-- /.content -->

  <!-- /.content-wrapper -->
@endsection

@section('javascript')



 <script type="text/javascript" src="{{ url('js/utility/main.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/utility/autocomplete.js') }}"></script> 


<script type="text/javascript">
$('.btn_start_task').daterangepicker({
    "singleDatePicker": true,
    "timePicker": true,
    "timePicker24Hour":true,
    locale: {
        format: 'MM/DD/YYYY H:mm'
    },
    "opens": "left"
   
}, function(start, end, label) {
	var startDate = start.format('YYYY-MM-DD H:mm');
	var data = {start_task_at:startDate} ;
    UpdateTask(data).done(function(res){
    	
    })
});
</script>


<script type="text/javascript">
	$('#card_new,#card_new_2,#card_reject,#card_in_progress,#card_pending').slimScroll({
    	height: '250px'
  	});
  	$('#card_accept,#card_done').slimScroll({
    	height: '600px'
  	});

  	console.log($(".img-resize-height").innerWidth());

  	

	$(function(){		
		$(".thumbnail,.layout-plus").height($(".thumbnail").innerWidth());


		@if(isset($taskId))
			$(".show-content").each(function(){
				if($(this).find(".box-id").val()== {{ $taskId }} ){
					$(this).click();
				}

			}) 
		@endif
	});
		


	  	// autosize(document.querySelectorAll('textarea'));
	  	// autosize(document.getElementById("description-edit-body-text"));

</script>
 <script type="text/javascript" src="{{ url('js/post/comment.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/post/main.js') }}"></script> 

<script>
	var ApiUrl = $("#apiUrl").val() ;
	var RouteUrl = "/"+$("#route").val();
	var userId = {{ Auth()->user()->id }} ;
 var myStuff = {
        elements: {
            imgInPreview: function() {
                return $(document).find('.preview-img');
            },
            divAppended: function() {
                return $(document).find('#divAppended');
            }
        }
    }




$(document).on("click",".btn-close-image",function(){
	$(this).closest('.parent-img').remove();
});
$(document).on("click",".btn-edit-post",function(){
	var parent = $(this).closest('.box-widget');
	parent.find('.text-show').hide();
	parent.find('.text-edit').show();
	parent.find('.text-edit textarea').focus().select();
});
$(document).on("click",".btn-cancel-edit",function(){
	var parent = $(this).closest('.box-widget');
	parent.find('.text-show').show();
	parent.find('.text-edit').hide();
	
});
$(document).on("click",".btn-save-edit",function(){
	var parent = $(this).closest('.box-widget');
	var text = parent.find('.text-edit textarea').val();
	var data = { 'description':text  } ;
	var post_id = parent.find('.post-id').val();
	var route = "/post/"+post_id+"?api_token="+api_token ;
	ajaxPromise('PUT',route,data).done(function(data){
		parent.find('.text-show').text(text).show();

		parent.find('.text-edit').hide();
	})

	
	
});





	$(".btn-save-post").on("click",function(){
		$(this).find('.fa-spinner').show();
		var img = [] ;
		$(".file_upload").each(function(){
			var imgRow = JSON.parse($(this).val()) ;
			console.log($(this).val());
			img.push(imgRow);
		});
		// console.log(img);
		var form_data = new FormData();
		form_data.append('description', $("#description").val() );
		form_data.append('file_upload',JSON.stringify(img));
		$.ajax({
			url:  ApiUrl+RouteUrl+"?api_token="+api_token,
			type: 'POST',
			dataType: 'json',
			data:form_data,
			cache:false,
	        contentType: false,
	        processData: false,
		})
		.done(function(res) {
			// console.log(res);
			$('.fa-spinner').hide();
			if(res.result=="true"){
              swal({
                  title: "@lang('main.create_success')" ,
                  type: 'success',
                  showCancelButton: false,
                  confirmButtonText: "@lang('main.ok')"
              }).then((result) => {
                if (result.value) {
                  location.reload();
                }
              })
            }else{
              var error = JSON.stringify(res.errors);
              swal(
                'Error...',
                error,
                'error'
              )
            }
		})
		.fail(function() {
			// dfd.reject( "error");
			$(this).find('.fa-spinner').hide();
		})

	});
</script>

@endsection		
