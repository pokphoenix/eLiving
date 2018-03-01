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
      	@lang('sidebar.notice')
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('sidebar.notice')</li>
      </ol>
    </section>
	
    <!-- Main content -->
    <section class="content">
    	<div class="row" >
		 	<div class="col-sm-6">
          	<!-- Box Comment -->
          		<div class="row">
          			<div class="col-sm-12">
        
          		
		          		<div class="box box-widget">
		            		<div class="box-header with-border">
						 		<label for="file_upload" class="btn-hover"  >
		                    		<i class="fa fa-camera" ></i>  @lang('post.picture')
		                  		</label>
		                  		<input id="file_upload" name='doc_file[]' type="file" style="display:none;" >

		              			<label for="public_date" class="btn-hover btn-public-date"  >
		                    		<i class="fa fa-clock-o" ></i>  @lang('post.public_date_set')
		                  		</label>

		                  		
				            	<!-- <div class="box-tools">
					               
					                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					               
					            </div> -->
		            		</div>
		            <!-- /.box-header -->
		            		<div class="box-body">
		            			<input type="hidden" id="public_start_date" >
		            			<input type="hidden" id="public_end_date" >
		            			<input type="hidden" id="public_is_never" >
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
			            		<div class="row row-public-date none ">
			            			<div class="col-sm-12">
			            				<div class="pull-right">
			            					<button class="btn btn-box-tool btn-cancel-public-date"><i class="fa fa-times"></i></button>
			            				</div>
			            				<i class="fa fa-clock-o" ></i>  @lang('post.public_date')
										
										<BR>
										<span id="start"></span> -
										<span id="end"></span>
			            			</div>
			            		</div>
			            		<div class="row row-public-role">
			            			<div class="col-sm-12">
			            				<i class="fa fa-eye" ></i>  @lang('post.public_role_set')
			            				  <?php 
                    foreach ($roles as $role){
                      echo "<br>".
                      "<input type=\"checkbox\"  name=\"role[]\" ".
                      " value=\"".$role['name']."\" ";

                         if(isset($edit)){

                         foreach($data['role'] as $userRole){ 
                          if(isset($edit) && $userRole==$role['name'] ){
                            echo " checked=\"\"";
                          }
                          
                         }
                       }
                      echo "> ".$role['display_name'];
                    }
                    ?>
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
          
          			</div>
          		</div>









          		@if(count($lists)>0)
			@foreach($lists as $list)
				 <div class="row" >
					 <div class="col-sm-12">
			          <!-- Box Comment -->
			          <div class="box box-widget ">
			            <div class="box-header with-border">
			            	<input type="hidden" class="post-id" value="{{ $list['id'] }}" >
			            	<input type="hidden" class="created-by" value="{{ $list['created_by'] }}" >
			              <div class="user-block">
			                <img class="img-circle" src="{{ $list['user_img'] }}" alt="User Image">
			                <span class="username"><a href="#">{{ $list['user_displayname']}} </a></span>
			                <span class="description"> {{ $list['created_at'] }}</span>
			              </div>
			              <!-- /.user-block -->
			              <div class="box-tools">
			              	@if($list['created_by']==Auth()->user()->id)
			                <button type="button" class="btn btn-box-tool btn-edit-post" >
			                  <i class="fa fa-edit"></i></button>
			                  @endif
			                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
			                </button>

			                @if($list['created_by']==Auth()->user()->id || Auth()->user()->hasRole('admin') )
			                <button type="button" class="btn btn-box-tool btn-delete-post"  data-id="{{$list['id']}}" title="@lang('post.delete_post')"><i class="fa fa-times"></i></button>
			                @endif

			                 <button type="button" class="btn btn-box-tool btn-delete-post"  data-id="{{$list['id']}}" title="@lang('post.delete_post')"><i class="fa fa-times"></i></button>
			               
			              </div>
			              <!-- /.box-tools -->
			            </div>
			            <!-- /.box-header -->
			            <div class="box-body">
			            	<p class="text-show">
			            		 {!! $list['description'] !!}
			            	</p>
			            	<div class="text-edit none">
			            		<textarea  class="form-control" style="border:none;" > {!! $list['description'] !!}</textarea>
			            		<div class="pull-right">
			            			<button class="btn btn-box-tool btn-cancel-edit">
			            			<i class="fa fa-times"></i>
			            		</button>
			            		<button class="btn btn-primary btn-sm btn-flat btn-save-edit">
			            			<i class="fa fa-save"></i>
			            		</button>
			            		</div>
			            		<BR>
			            		
			            	</div>
			            	
			            	<div class="row">
			            		<div class="col-sm-12">
			            			@if (count($list['attachments'])>0)
			            			<img class="img-responsive" src="{{ $list['attachments'][0]['file_path'] }}" alt="Photo">
									<div class="">
										@foreach($list['attachments'] as $key=>$a)
										@if ($key > 0 && $key < 3)
											<div class="img-resize-height col-sm-4 nopadding">
												<a href="{{ $a['file_path'] }}" target="_blank" >
												<div class="thumbnail">
													<img class="img-responsive" src="{{ $a['file_path'] }}" alt="Photo">
												</div>
												</a>
											</div>
										@endif
										@endforeach
										@if(count($list['attachments'])>4)
										<div class="img-resize-height  col-sm-4 nopadding" >
											<a href="{{ $list['attachments'][3]['file_path'] }}" target="_blank" >
											<div class="layout-plus" style=" position: absolute;z-index:2;  background:rgba(0,0,0,0.3) ; padding-bottom: 10px; width:100%;color:#FFF; display:table;"> 
												<p style="position:relative;text-align:center; vertical-align: middle; font-size: 2em;display:table-cell;">
													+ {{  count($list['attachments'])-3 }}
												</p>
												
											</div>
											<div class="thumbnail" >
												<img class="img-responsive" src="{{ $list['attachments'][3]['file_path'] }}" alt="Photo">
											</div>
											</a>
										</div>
										@elseif(count($list['attachments'])==4&&isset($list['attachments'][3]))
										<div class="img-resize-height  col-sm-4 nopadding">
											<a href="{{ $list['attachments'][3]['file_path'] }}" target="_blank" >
											<div class="thumbnail">
												<img class="img-responsive" src="{{ $list['attachments'][3]['file_path'] }}" alt="Photo">
											</div>
											</a>
										</div>
										@endif
									</div>	
					            		
										
					            	@endif
			            		</div>
			             	</div>
			             <!--  <button type="button" class="btn btn-default btn-xs"><i class="fa fa-share"></i> Share</button> -->
			              <button type="button" class="btn btn-default btn-xs btn-like"><i class="fa fa-thumbs-o-up"></i> <span>Like</span></button>
			              <span class="pull-right text-muted like-comment">
			              	{{ $list['post_like']." likes - ".$list['post_comment']." comments" }}
			              </span>
			            </div>
			            <!-- /.box-body -->
			         <div class="box-footer box-comments">
			         	@if(count($list['comments']))
			         		@foreach($list['comments'] as $key=>$comment)
 						<div class="box-comment">
			                <img class="img-circle img-sm" src="{{ $comment['img'] }}" alt="User Image">

			                <div class="comment-text">

			                      <span class="username">
			                        {{ $comment['user_name'] }}
			                        <span class="text-muted pull-right"> 
			                        {{ date('d/m/Y H:i',$comment['ts_created_at']) }}
			                        @if(Auth::user()->id==$comment['user_id'])
										<!-- <button type="button" class="btn btn-default btn-comment-delete-real btn-xs" title="Remove"><i class="fa fa-close"></i>
										</button> -->
										<div class="btn-group pull-right message-tools">
			                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			                                  <i class="fa fa-gear"></i>
			                                </button>
			                                <ul class="dropdown-menu pull-right" role="menu">
			                                  <li>
			                                    <a href="javascript:void(0)" class="btn-del-msg" >
			                                    @lang('chat.delete_message')
			                                    </a>
			                                  </li>
			                                  <li>
			                                    <a href="javascript:void(0)" class="btn-pin-msg" >
			                                    @lang('chat.pin_message')
			                                    </a>
			                                  </li>
			                                </ul>
			                            </div>
									@endif
			                    	</span>
			                      </span>
			                {!! $comment['comment_description'] !!}
			                </div>
			                
			            </div>
			            	@endforeach
			         	@endif
			          <!--       
			          
			              <div class="box-comment">
			                
			                <img class="img-circle img-sm" src="../dist/img/user4-128x128.jpg" alt="User Image">

			                <div class="comment-text">
			                      <span class="username">
			                        Luna Stark
			                        <span class="text-muted pull-right">8:03 PM Today</span>
			                      </span>
			                  It is a long established fact that a reader will be distracted
			                  by the readable content of a page when looking at its layout.
			                </div>
			               
			              </div> -->
			              
			            </div>
			          	<div class="box-footer">
			            	<i class="fa fa-clock-o"></i> @lang('post.public_date')
			             	<BR>
			             	{{ $list['public_start_at']." - "}}
			             	@if(isset($list['public_end_at']))
			             	{{ $list['public_end_at'] }}
							@else
								@lang('post.is_never')
			             	@endif
			             
			            </div>
			            <div class="box-footer">
			            	<i class="fa fa-eye" ></i>  @lang('post.public_role')
			             	<BR>
			             	@if(isset($list['public_role']))
			             	@foreach($list['public_role'] as $r)
								{{ $r }}<BR>
			             	@endforeach
			             	@endif
			             	
			             
			            </div>
			            <div class="box-footer">
			            	 
			              <form class="comment-form" action="#" method="post">
			                <img class="img-responsive img-circle img-sm" src="{{ auth()->user()->getProfileImg() }}" alt="Alt Text">
			                <!-- .img-push is used to add margin to elements next to floating images -->
			                <div class="img-push">
			                  <input type="text"  class="form-control input-sm comment-text" placeholder="@lang('post.press_enter_to_post')">
			                </div>
			              </form>
			             
			            </div>
			            
			            <!-- /.box-footer -->
			          </div>
			          <!-- /.box -->
			        </div>

			      </div>
			@endforeach
		@endif
        	</div>
			
			

       </div>

	
		
		

		
		

      <!-- Main row -->
     
      <!-- /.row (main row) -->

		
		

		

    </section>
    <!-- /.content -->

  <!-- /.content-wrapper -->
    <div class="modal fade" id="modal-default">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('post.public_date_set')</h4>
              </div>
              <div class="modal-body">
                 <form  id="parking-form" role="form" method="POST" action="{{$action}}" enctype="multipart/form-data"  >
                
                  <div class="form-group">
                    <label for="room_id">@lang('post.public_start')</label>
                   	<input type="text" class="form-control" id="public_start" >
                  </div>
                  <div class="form-group">
                    <label for="room_id">@lang('post.public_end')</label>
                   	<input type="text" class="form-control" id="public_end" >
                  </div>
                   <div class="form-check">
                      <label class="form-check-label">
                      <input type="checkbox" id="is_never" name="is_never" class="form-check-input" >
                         @lang('post.is_never') 
                      </label>
                    </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
                <button type="button" class="btn btn-primary btn-save-public">@lang('main.btn_save')
                   <i class="fa fa-spinner fa-spin fa-fw none" ></i>
                </button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
@endsection

@section('javascript')



 <script type="text/javascript" src="{{ url('js/utility/main.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/utility/autocomplete.js') }}"></script> 


<script type="text/javascript">
$('#public_start,#public_end').daterangepicker({
    "singleDatePicker": true,
    "timePicker": true,
    "timePicker24Hour":true,
    locale: {
        format: 'MM/DD/YYYY H:mm'
    },
    "opens": "left"
   
});


$("input[type=checkbox]").each(function(){
	if($(this).val()=="user"){
		$(this).attr("checked",true);
	}
})

$(".btn-public-date").on("click",function(){
    d = new Date();
    setTimeout(function() {
       $('#public_start').data('daterangepicker').setStartDate(new Date());
       $('#public_end').data('daterangepicker').setStartDate(new Date());
    }, 500);

  $("#modal-default").modal("toggle");

})
$(".btn-role").on("click",function(){
   
  $("#modal-default").modal("toggle");

})



</script>




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

function readURL(input) {
	// var dfd = $.Deferred();
    var newFile = $(input).clone();
    newFile.removeAttr("id");
    newFile.removeAttr("name");
    newFile.removeAttr("style");
	  // newFile.attr("name","upload_file[]");
	newFile.attr("class","file_upload");
	newFile.attr("type","hidden");

  if (input.files && input.files[0]) {
    var reader = new FileReader();
    var file = input.files[0];
    var file_name = file.name;
    var file_ext = file_name.split('.').pop().toLowerCase();
    var file_size = file.size ;
    reader.onload = function(e) {
    	 var data = {name : file_name ,
              extension : file_ext ,
              size : file_size ,
              data : e.target.result ,
            };
        newFile.val(JSON.stringify(data));
    	var html = "<div class=\"col-sm-3 parent-img\" >"+
			"<small class=\"pull-right badge btn-close-image\">"+
			"<i class=\"fa fa-close\"></i>"+
			"</small>"+
            
            "<img src=\""+e.target.result+"\" class=\"img-responsive\"  >"+
        "</div>";

       

        $('#preview-img').append(html).show();
        newFile.insertAfter($('.btn-close-image').last());
        // console.log($('.parent-img > .img-responsive').innerWidth());
        // $(".parent-img .img-responsive").height($('.parent-img .img-responsive').innerWidth());
    }

    reader.readAsDataURL(input.files[0]);

  }

  console.log($('.btn-close-image').last());

  

  // dfd.resolve();
  // return dfd.promise();
}


$("#file_upload").change(function() {




  readURL(this);
  $(this).val('');
});


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


$(".btn-save-public").on("click",function(){
	var public_start_date = moment($("#public_start").val()).format("YYYY-MM-DD HH:mm");
	var public_end_date = moment($("#public_end").val()).format("YYYY-MM-DD HH:mm");
	$("#public_start_date").val(public_start_date);
	$("#public_end_date").val(public_end_date);
	$("#start").text(public_start_date);
	$("#end").text(public_end_date);
	$("#public_is_never").val($("#is_never").is(":checked"));

	if($("#is_never").is(":checked")){
		$("#end").text((($("#app_local").val()=='th') ? 'ตลอด' : 'Alway' ));
	}

	$("#modal-default").modal("toggle");
	$(".row-public-date").show();

})

$(".btn-cancel-public-date").on('click', function(event) {
	$("#public_start_date").val('');
	$("#public_end_date").val('');
	$("#public_is_never").val('');
	$(".row-public-date").hide();
});


$(document).on("click",".btn-delete-post",function(){

	var parent = $(this).closest('.box-widget') ;
	
	swal({
        title: (($("#app_local").val()=='th') ? 'คุณแน่ใจไหม?' : 'Are you sure?' ) ,
        text: (($("#app_local").val()=='th') ? 'คุณต้องการลบข้อมูลนี้ใช่หรือไม่' : "You want to delete this data!" ) ,
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: (($("#app_local").val()=='th') ? 'ลบ' : 'Delete' ),
        cancelButtonText: (($("#app_local").val()=='th') ? 'ยกเลิก' : 'Cancel' ),
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-default',
        buttonsStyling: false,
        reverseButtons: true
  }).then((result) => {
          if (result.value) {
         	var post_id = parent.find('.post-id').val();
			var route = "/post/"+post_id+"?api_token="+api_token ;
			ajaxPromise('DELETE',route,null).done(function(data){
				parent.remove();
			})

          } else if (result.dismiss === 'cancel') {
            
          }
        })
	


	
});
$(document).on("click",".btn-ban-user",function(){

	var parent = $(this).closest('.box-widget') ;
	
	var created_by = parent.find('.created-by').val();
	var created_name = parent.find('.username a').text();
	var created_by_img = parent.find('.user-block img').attr('src');

	console.log(created_by,created_name,created_by_img);

	swal({
        title: (($("#app_local").val()=='th') ? 'คุณแน่ใจไหม?' : 'Are you sure?' ) ,
        text: (($("#app_local").val()=='th') ? 'คุณต้องการระงับใช้งานผู้ใช้คนนี้ใช่หรือไม่' : "You want to ban this user!" ) ,
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: (($("#app_local").val()=='th') ? 'ยืนยัน' : 'Sure' ),
        cancelButtonText: (($("#app_local").val()=='th') ? 'ยกเลิก' : 'Cancel' ),
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-default',
        buttonsStyling: false,
        reverseButtons: true
  }).then((result) => {
          if (result.value) {
         	var post_id = parent.find('.post-id').val();
			var route = "/post/"+post_id+"/ban?api_token="+api_token ;
			ajaxPromise('POST',route,null).done(function(data){
			var html =	"<div class=\"item\">"+
                    "<div class=\"pull-right\">"+
                        "<button class=\"btn btn-success btn-xs btn-unban-user\" "+
                        " title=\"@lang('post.unban_user')\" "+
                        " data-id=\""+created_by+"\" > <i class=\"fa fa-user-plus\"></i> "+
                        "</button>"+
                    "</div>"+
                    "<img src=\""+created_by_img+"\">"+
                    "<p class=\"message\" >"+
                        "<a href=\"javascript:void(0)\" class=\"name\" "+
                        " style=\"margin-top:5px;\">"+created_name+
                        "</a>"+
                    "</p>"+ 
                "</div>";
                console.log(html);
                var append = true ;
                $("#member_baned_list .item").each(function(){
                	if($(this).find('.btn-unban-user').data('id')==created_by){
                		append = false ;
                	}
                })

                if(append){
                	$("#member_baned_list").append(html);
                }
               
			})

          } else if (result.dismiss === 'cancel') {
            
          }
        })
	


	
});
$(document).on("click",".btn-unban-user",function(){

	var user_id = $(this).data('id') ;
	var parent = $(this).closest('.item');
	console.log(parent.length);
	swal({
        title: (($("#app_local").val()=='th') ? 'คุณแน่ใจไหม?' : 'Are you sure?' ) ,
        text: (($("#app_local").val()=='th') ? 'คุณต้องการปลดการระงับใช้งานผู้ใช้คนนี้ใช่หรือไม่' : "You want to un ban this user!" ) ,
        type: 'info',
        showCancelButton: true,
        confirmButtonText: (($("#app_local").val()=='th') ? 'ยืนยัน' : 'Sure' ),
        cancelButtonText: (($("#app_local").val()=='th') ? 'ยกเลิก' : 'Cancel' ),
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-default',
        buttonsStyling: false,
        reverseButtons: true
  }).then((result) => {
          if (result.value) {
         
			var route = "/post/"+user_id+"/unban?api_token="+api_token ;
			ajaxPromise('DELETE',route,null).done(function(data){
				parent.remove();
			})

          } else if (result.dismiss === 'cancel') {
            
          }
        })
	


	
});

$(document).on("click",".btn-like",function(){
	var ele = $(this) ;
	var parent = $(this).closest('.box-widget') ;
	var postId = parent.find('.post-id').val();
	var route = "/"+$("#route").val()+"/"+postId+"/like?api_token="+api_token;
	ajaxPromise('PUT',route,null).done(function(data){
		console.log(data);
		var text = data.post_like+" likes - "+data.post_comment+" comments"; 
		parent.find(".like-comment").text(text);
		
		var statusLike = (data.like_status) ? 'unlike' : 'like' ;
		ele.find('span').text(statusLike);
		
		if(data.like_status){
			ele.find('i').removeClass('fa-thumbs-o-up').addClass('fa-thumbs-o-down');
		}else{
			ele.find('i').removeClass('fa-thumbs-o-down').addClass('fa-thumbs-o-up');
		}

	});

});

$(".comment-form").submit(function(event) {
	var ele = $(this) ;
	var parent = $(this).closest('.box-widget') ;
	var postId = parent.find('.post-id').val();
	var route = "/"+$("#route").val()+"/"+postId+"/comment?api_token="+api_token;
	var text = $(this).find(".comment-text").val() ;
	if(text!=""||text==" "){

		var data = { description:text } ;
		ajaxPromise('POST',route,data).done(function(data){
			console.log(data);
			
			createComment(data.post_id,data.post_comments);
			ele.find(".comment-text").val('');
		});
	}
	
	return false;
});

function createComment(id,data){
	var html = '';
	if(data.length>0){
		for(var i=0;i<data.length;i++){

		html +="<div class=\"box-comment\">"+
                	"<img class=\"img-circle img-sm\" src=\""+data[i].img+"\" >"+
                	"<div class=\"comment-text\">"+
                     	"<span class=\"username\">"+
                        data[i].user_name+
                        "<span class=\"text-muted pull-right\">"+ 
                        moment.unix(data[i].ts_created_at).format("D/MM/YYYY HH:mm");
        if(data[i].user_id==userId){            	
        html += "<button type=\"button\" class=\"btn btn-default btn-comment-delete-real btn-xs\" title=\"Remove\">"+
                    		"<i class=\"fa fa-close\"></i>"+
						"</button>";
		}
		html +=			"</span>"+
                      "</span>"+
                data[i].comment_description+
                "</div>"+
            "</div>";
		}
		
	}
	console.log(html);

	$(".post-id").each(function(index, el) {
		if($(this).val()==id){
			$(this).closest('.box-widget').find(".box-comments").html(html);
		}
	});
	

	
}


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
		form_data.append('start',$("#public_start_date").val());
		form_data.append('end',$("#public_end_date").val());
		form_data.append('is_never',$("#public_is_never").val());

		 $('.row-public-role :checkbox:checked').each(function(i){
        	
          form_data.append('role[]',$(this).val());

        });

		

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
