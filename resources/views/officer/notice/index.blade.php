@extends('main.layouts.main')
@section('style')
   <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ url('public/css/input.css') }}">
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
	.btn-close-image,.btn-delete-image {
		position:absolute;top:0;right:10px;background:#f56954;color:#FFF;
		display: none;
		border-radius: 50%;
		height:22px;line-height:21px;
		font-size: 16px;
		padding:0 4px;
		/*line-height:20px;*/
	}
	.parent-img:hover .btn-close-image,.parent-img:hover .btn-delete-image{
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
    	 <div class="box">
            <div class="box-header">
              <h3 class="box-title"></h3>
              <button class="btn btn-primary btn-hover btn-create"  >
		            <i class="fa fa-plus" ></i>  @lang('post.create_post')
		        </button>
               
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                  <th>@lang('post.description')</th>
                  <th>@lang('post.like')</th>
                  <th>@lang('post.public_date')</th>
                  <th>@lang('post.public_role')</th>
                  <th>@lang('post.prioritizes')</th>
                  <th>@lang('post.image')</th>
                  <th>@lang('main.tool')</th>
                </tr>
                <tr class="thead-search">
                  <th ></th>
                  <th class="input-filter">@lang('post.description')</th>
                  <th class="input-filter">@lang('post.like')</th>
                  <th class="input-filter">@lang('post.public_date')</th>
                  <th class="input-filter">@lang('post.public_role')</th>
                  <th class="input-filter">@lang('post.prioritizes')</th>
                  <th ></th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
        
                @foreach ($lists as $key=>$list)
                <tr >
                  <td>{{ $key+1 }}</td>
                  <td> {!! $list['description'] !!}</td>
                  <td>{{ $list['post_like']." likes - ".$list['post_comment']." comments" }}
                  </td>
                  <td>{{ $list['public_start_at']." - "}}
			             	@if(isset($list['public_end_at']))
			             	{{ $list['public_end_at'] }}
							@else
								@lang('post.is_never')
			             	@endif
			       </td>
                  <td>@if(isset($list['public_role']))
			             	@foreach($list['public_role'] as $r)
								{{ $r }}<BR>
			             	@endforeach
			             	@endif
			        </td>
			        <td>{{ $list['prioritize_name'] }}
			        	
			        </td>
					<td>
						@if (count($list['attachments'])>0)
							@foreach ($list['attachments'] as $a)
			            			<img class="img-responsive" src="{{ $a['file_path'] }}" width=50 height=50>
			            	@endforeach
			            @endif
					</td>
                  
                  <td> 
                   
                    <button class="btn btn-default btn-edit-post btn-xs" data-id="{{ $list['id'] }}" ><i class="fa fa-edit"></i></button>
                    <button class="btn btn-danger btn-delete-post btn-xs" data-id="{{ $list['id'] }}"  ><i class="fa fa-trash-o"></i></button>
                   
                  </td>

                </tr>
                @endforeach
               
              </table>
            </div>
            <!-- /.box-body -->
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
                 <form  id="post-form" role="form" method="POST" action="{{$action}}" enctype="multipart/form-data"  >
                  <div class="form-group">
                    <label for="room_id">@lang('post.content')</label>
                   	<textarea class="form-control" style="border:0;" id="description" rows="4" placeholder="@lang('post.content')"></textarea>
                  </div>

                  <div class="form-group">
                    <label for="room_id">@lang('post.public_start')</label>
                   	<input type="text" class="form-control" id="public_start" >
                  </div>
                  <div class="form-group">
                    <label for="room_id">@lang('post.public_end')</label>
                   	<input type="text" class="form-control" id="public_end" >
                  </div>

                  <div class="form-group">
                    <label for="room_id">@lang('post.prioritizes')</label>
                   	<select class="form-control" id="prioritize" name="prioritize">
                   		@if(isset($prioritizes))
	                   		@foreach($prioritizes as $p)
							<option value="{{$p['id']}}" >{{$p['name']}}</option>
	                   		@endforeach
                   		@endif
                   	</select>
                  </div>
                   <div class="form-check">
                      <label class="form-check-label">
                      <input type="checkbox" id="is_never" name="is_never" class="form-check-input" >
                         @lang('post.is_never') 
                      </label>
                    </div>
                    <input id="file_upload" name='doc_file[]' type="file"  >
                    <div class="row">
        				<div class="col-sm-12">
        					<div class="form-group" id="preview-img" style="display: none;" >
	                  	
            				</div>
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
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
                <button type="button" class="btn btn-primary  btn-save-post">@lang('main.btn_save')
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
 <script type="text/javascript" src="{{ url('js/post/comment.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/post/main.js') }}"></script> 

<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src=" {{ url('js/utility/data_table.js') }}"></script>	
<script type="text/javascript">
$(function() {
	var table = $('#example1').DataTable(
    {
        "bSortCellsTop": true
        ,"order": [[ 0, 'desc' ]]
    });
    
    $.each($('.input-filter', table.table().header()), function () {
        var column = table.column($(this).index());
        $( 'input', this).on( 'keyup change', function () {
            if ( column.search() !== this.value ) {
                column
                    .search( this.value )
                    .draw();
            }
        } );
    } );
});


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

$(".btn-create").on("click",function(){
	$("#description").val('');
	$("#prioritize").val(1).trigger('change');
	$("#preview-img").html('');
	$("#public_end").attr('disabled',true);
	$("#is_never").attr('checked',true);

	$(".row-public-role :checkbox").each(function(index, el) {
		$(this).attr('checked',false);
		if($(this).val()=="user"){
			$(this).attr('checked',true);
		}	
	});


    d = new Date();
    setTimeout(function() {
       $('#public_start').data('daterangepicker').setStartDate(new Date());
       // $('#public_end').data('daterangepicker').setStartDate(new Date());
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




$("#is_never").on("change",function(){
	if($(this).is(':checked')){
		$("#public_end").attr('disabled',true);
	}else{
		$("#public_end").attr('disabled',false);
	}
})


$(document).on("click",".btn-close-image",function(){
	$(this).closest('.parent-img').remove();
});
$(document).on("click",".btn-edit-post",function(){
	// var parent = $(this).closest('.box-widget');
	// parent.find('.text-show').hide();
	// parent.find('.text-edit').show();
	// parent.find('.text-edit textarea').focus().select();
	var id = $(this).data('id') ;
	var route = "/notice/"+id+"/edit?api_token="+api_token ;
	ajaxPromise('GET',route,null).done(function(data){
		console.log(data);
		var r = data.post ;
		$("#description").val(r.description);
		$("#prioritize").val(r.prioritize_id).trigger('change');
		$('#public_start').data('daterangepicker').setStartDate(new Date(r.public_start_at));
		

		$("#post-form").append("<input type=\"hidden\" id=\"has_edit\" value=\""+r.id+"\" >");

		if(r.public_end_at == null){
			$("#is_never").attr('checked',true);
			$("#public_end").data('daterangepicker').setStartDate(new Date());
			$("#public_end").attr('disabled',true);
		}else{
			$('#public_end').data('daterangepicker').setStartDate(new Date(r.public_end_at));
			$("#is_never").attr('checked',false);
			$("#public_end").attr('disabled',false);
		}



		$(".row-public-role :checkbox").each(function(index, el) {
			$(this).attr('checked',false);
			for(var i=0 ; i<r.public_role.length ;i++){
				if($(this).val()==r.public_role[i]){
					$(this).attr('checked',true);
				}
			}

			
		});
		if(r.attachments.length>0){
			var html = '';
			for (var i=0 ; i< r.attachments.length ; i++){
				html += "<div class=\"col-sm-3 parent-img\" >"+
				"<small class=\"pull-right badge  btn-delete-image\" data-id=\""+r.attachments[i].id+"\"  data-post-id=\""+r.attachments[i].post_id+"\" >"+
				"<i class=\"fa fa-close\"></i>"+
				"</small>"+
	            
	            "<img src=\""+r.attachments[i].file_path+"\" class=\"img-responsive\"  >"+
	        	"</div>";
			}
			$('#preview-img').append(html).show();
		}
		
		


		 $("#modal-default").modal("toggle");
	});

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
$(document).on("click",".btn-delete-image",function(){
	var id = $(this).data('id');
	var post_id = $(this).data('post-id');
	var parent = $(this).closest('.parent-img') ;
	var route = "/post/"+post_id+"/attachment/"+id+"?api_token="+api_token ;
	ajaxPromise('DELETE',route,null).done(function(data){
		parent.remove();
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

$(".btn-save-post").on("click",function(){
		$(this).find('.fa-spinner').show();
		var img = [] ;
		$(".file_upload").each(function(){
			var imgRow = JSON.parse($(this).val()) ;
			console.log($(this).val());
			img.push(imgRow);
		});
		var public_start_date = moment($("#public_start").val()).format("YYYY-MM-DD HH:mm");
		var public_end_date = moment($("#public_end").val()).format("YYYY-MM-DD HH:mm");

		var form_data = new FormData();
		form_data.append('description', $("#description").val() );
		form_data.append('prioritize', $("#prioritize").val() );
		form_data.append('file_upload',JSON.stringify(img));
		form_data.append('start',public_start_date);
		form_data.append('end',public_end_date);
		form_data.append('is_never',$("#is_never").is(":checked"));

		var count_role = 0 ;
		$('.row-public-role :checkbox:checked').each(function(i){
        		count_role++;
          form_data.append('role[]',$(this).val());

        });
        if(count_role==0){
        	$('.fa-spinner').hide();
        	swal(
                'Error...',
                (($("#app_local").val()=='th') ? 'กรุณาระบุกลุ่มคนเห็นข่าว' : 'Please select role' ),
                'error'
              )
        	return false;
        }
		var url = ApiUrl+RouteUrl+"?api_token="+api_token ;
		var title= "@lang('main.create_success')";
		if($("#has_edit").length>0){
			title= "@lang('main.update_success')";
			url = ApiUrl+"/post/"+$("#has_edit").val()+"?api_token="+api_token ;
			form_data.append('_method','PUT');
		}

		$.ajax({
			url:  url,
			type: "POST" ,
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
                  title: title ,
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
