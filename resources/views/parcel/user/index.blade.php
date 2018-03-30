@extends('main.layouts.main')
@section('style')
<link rel="stylesheet" href="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
  <!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{ url('plugins/iCheck/all.css') }}">
<style>
	.append-card table tr.show-content td { padding:0;padding-left: 50px; line-height:32px;  }
	.append-card table tr.show-content td:hover{
		cursor:pointer;
	}
	.append-card table tr.show-content td:hover  .card-btn-edit{
		display: block;
	}
	.append-card table tr.title td { padding-left:10px; 
		font-weight: bold;
		 /*text-decoration: underline;*/
		 border-bottom: 2px solid #000;
		  }
	
</style>
@endsection
@section('content-wrapper')
<!-- data-toggle="modal" data-target="#modal-card-content" -->
	

	
    <!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
	<i class="fa fa-send"></i>
	@lang('sidebar.parcel')
	<small></small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
		<li class="active"> @lang('sidebar.parcel')</li>
	</ol>
</section>


<!-- Main content -->
<section class="content">
	
	<input type="hidden" id="current_card_id" >
	<input type="hidden" id="room_id" value="{{ $roomId }}" >
	<input type="hidden" id="first_open_card" value="false">
	<input type="hidden" id="asset_url" value="{{ asset('public/img/icon/') }}" >
	
	<!-- Main row -->
	<div class="row" >
		<section class="col-md-12">
			
			<div class="box box-solid bg-rm-user-task box-parent"  >
				
				<div class="box-header">
					
					<h3 class="box-title">
					<select id="filter-status" style="color: #000;" >
						<option value="0" >@lang('parcel.all') </option>
						<option value="1" >@lang('parcel.received')</option>
						<option value="2" >@lang('parcel.wait_receive')</option>
					</select>
					</h3>
					
				</div>
				<div class="box-body "  >
					
					<div class="append-card">
						
						
						@if(count($lists)>0)
						<?php $dateTxt = ""; ?>
						@foreach($lists as $list)
						
						<?php
						$createdAt = date('d M Y', strtotime($list['created_at']));
						if( $createdAt!= $dateTxt){
							$dateTxt = $createdAt ;
							echo "<h4 class=\"title\" >$dateTxt : </h4>" ;
						}
						
						?>
						
						<div class="box box-solid card show-content" style="border-left: 5px solid {{ $list['status_color'] }};">
							<div class="box-header">
								<h3 class="box-title">{{ $list['parcel_type_name'] }}
								@if($list['type']==2)
								({{ $list['supplies_type_name'] }})
								@endif
								</h3>
							</div>
							<div class="box-body "  >
								
								@if(isset($list['supplies_send_name']))
								@lang('parcel.supplies_send_name') :
								{{ $list['supplies_send_name'] }} <BR>
								@endif
								@if(isset($list['supplies_code']))
								@lang('parcel.supplies_code') :
								{{ $list['supplies_code'] }} <BR>
								@endif
								@if(isset($list['gift_receive_name']))
								@lang('parcel.gift_receive_name') :
								{{ $list['gift_receive_name'] }} <BR>
								@endif
								
								@lang('parcel.send_date') : {{ created_date_format($list['send_date']) }}
								<BR>
								@lang('parcel.received_status') :
								@if(isset($list['receive_at']))
								<span class="received_status">@lang('parcel.received')</span>
								@else
								<span class="received_status">@lang('parcel.wait_receive')</span>
								<button class="btn btn-default btn-receive btn-xs" data-id="{{ $list['id'] }}" data-room-id="{{ $list['room_id'] }}" title="@lang('parcel.receive')"><i class="fa fa-share"></i></button>
								@endif
							</div>
							<input type="hidden" class="box-id" value="{{ $list['id'] }}" >
						</div>
						@endforeach
						
						
						@endif
					</div>
				</div>
				<!-- /.chat -->
				
			</div>
		</section>
	</div>
	<!-- /.row (main row) -->
	
</section>
<!-- /.content -->
<!-- /.content-wrapper -->
<div class="modal fade" id="modal-receive">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">@lang('parcel.receive')</h4>
			</div>
			<div class="modal-body">
				<form  id="receive-form" role="form" method="POST" action="{{$action}}" enctype="multipart/form-data"  >
					<input type="hidden" id="parcel_id" >
					<div class="form-group">
						<label for="name">@lang('parcel.receiver_name')</label>
						<input type="text" class="form-control" id="receive_name" name="receive_name" placeholder="@lang('parcel.receiver_name')">
					</div>
					<div class="form-group">
						<label for="name">@lang('parcel.receive_tel')</label>
						<input type="text" class="form-control" id="receive_tel" name="receive_tel" placeholder="@lang('parcel.receiver_name')">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
				<button type="button" class="btn btn-primary btn-save-receive">@lang('main.btn_save')
				<i class="fa fa-spinner fa-spin fa-fw" style="display:none;"></i>
				</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<div class="modal fade " id="modal-card-content" >
	<div class="modal-dialog" style="width: 90%;">
		<div class="modal-content " style="background: #EEE;">
			<div class="cotent-cover text-center" style="display: none;background: #ccc;">
				<img src="" height="100">
			</div>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<div class="row">
					<div class="col-sm-12">
						<h4 class="modal-title">
						<div class="show-title">
							<i class="fa fa-address-card-o"></i>
							<span>Title</span>
							
						</div>
						</h4>
					</div>
				</div>
				
			</div>
			<div class="modal-body">
				
				
				
				
				<div class="col-sm-12" style="height: 20px;"></div>
				<div class="col-sm-10" >
					<div class="row row-supplies-send-name none">
						<div class="col-sm-4" >
							<h4 class="title">@lang('parcel.supplies_send_name')</h4>
							<div class="form-group">
								<span></span>
								
							</div>
						</div>
					</div>
					<div class="row row-supplies-code none">
						<div class="col-sm-4" >
							<h4 class="title">@lang('parcel.supplies_code')</h4>
							<div class="form-group">
								<span></span>
							</div>
						</div>
					</div>
					<div class="row row-supplies-type-name none">
						<div class="col-sm-4" >
							<h4 class="title">@lang('parcel.supplies_type_name')</h4>
							<div class="form-group">
								<span></span>
								
							</div>
						</div>
					</div>
					<div class="row row-gift-receive-name none">
						<div class="col-sm-4" >
							<h4 class="title">@lang('parcel.gift_receive_name')</h4>
							<div class="form-group">
								<span></span>
								
							</div>
						</div>
					</div>
					<div class="row row-gift-send-name none">
						<div class="col-sm-4" >
							<h4 class="title">@lang('parcel.gift_send_name')</h4>
							<div class="form-group">
								<span></span>
								
							</div>
						</div>
					</div>
					<div class="row row-gift-description none">
						<div class="col-sm-4" >
							<h4 class="title">@lang('parcel.gift_description')</h4>
							<div class="form-group">
								<span></span>
								
							</div>
						</div>
					</div>
					<div class="row row-send-date">
						<div class="col-sm-4" >
							<h4 class="title">@lang('parcel.send_date')</h4>
							<div class="form-group">
								<span></span>
								
							</div>
						</div>
					</div>
					<div class="row row-received-status">
						<div class="col-sm-4" >
							<h4 class="title">@lang('parcel.received_status')</h4>
							<div class="form-group">
								<span></span>
								
							</div>
						</div>
					</div>
					
				</div>
				
				<div class="col-sm-2">
					
					
					<button type="button" class="btn btn-block btn-social btn-default btn-receive"   data-id="" data-room-id="" id="btn-receive" >
					@lang('parcel.btn_receive')
					</button>
					<button type="button" class="btn btn-block btn-social btn-default "   data-id="" data-room-id="" id="btn-un-receive" >
					@lang('parcel.btn_un_receive')
					</button>
					
					
					
					
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

@endsection

@section('javascript')

  <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
  <!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
<script src="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ url('plugins/iCheck/icheck.min.js')}}"></script>
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
 <script type="text/javascript" src="{{ url('js/utility/main.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/utility/autocomplete.js') }}"></script> 

<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script>
	$(".btn-tap").each(function(){
		$(this).on("click",function(){
			 var ele = $(this).data('toggle');
			 $(".tab-panel").hide();
			 $(ele).show();
			 
			 $(".btn-tap").removeClass('btn-primary');
			 $(this).addClass('btn-primary');
		})
		
	})
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

<script type="text/javascript">
var roomId = $("#room_id").val();
var baseRoute = "/parcel/"+roomId+"/user/" ;

</script>
<script >
 $(".btn-receive").on("click",function(){
  
  

  $("#modal-receive input").val('');
  $("#parcel_id").val($(this).data('id'));
  $("#parcel-form #_method").remove('');
  $("#parcel-form").attr('action', "{{$action}}" );
  $("#modal-receive").modal("toggle");

  $("#modal-card-content").modal('hide');
})
$(".btn-save-receive").on("click",function(){
  $("#receive-form").submit();
})

$("#btn-un-receive").on("click",function(){

	var route = "/parcel/receive/"+$(this).data('id')+"?api_token="+api_token ;
	ajaxPromise('POST',route,{'_method':'DELETE'}).done(function(){
		$("#btn-receive").show();
    	$("#btn-un-receive").hide();
	});
  
})

$("#filter-status").on("change",function(){
	var filter = $("#filter-status option:selected").text();
	if($(this).val()=="0"){
		$('.show-content').show();
	}else{ 
		$('.show-content').hide();
		$(".received_status").each(function(){
			if($(this).text()==filter){
				$(this).closest('.show-content').show();
			}
		})
	}
})



     $("#receive-form").validate({
      rules: {
        receive_name: {
          required: true,
          maxlength:500
        },
        receive_tel: {
          maxlength:45
        }
      
      },
      messages: {
        receive_name: (($("#app_local").val()=='th') ? 'ชื่อผู้รับไม่ถูกต้อง' : 'Wrong Receiver Name' ),
        receive_tel: (($("#app_local").val()=='th') ? 'เบอร์โทรผู้รับไม่ถูกต้อง' : 'Wrong Receiver Telephone' ),
       
        
      },
      highlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
      }
      ,submitHandler: function (form) {
        $(".btn-save-receive").find('.fa-spinner').show();
        var form_data = new FormData($("#receive-form")[0]);
       

             $.ajax({
                 type: $("#receive-form").attr('method') ,
                 url: $("#apiUrl").val()+"/parcel/receive/"+$("#parcel_id").val()+"?api_token="+api_token ,
                 data: form_data ,
                 processData: false,
                 contentType: false,
                 success: function (data) {
                    if($("#_method").length >0 ){
                      title = "@lang('main.update_success')";
                    }else{
                      title = "@lang('main.create_success')";
                    }
                    $(".btn-save-receive").find('.fa-spinner').hide();
                    if(data.result=="true"){
                      swal({
                          title:title ,
                          type: 'success',
                          showCancelButton: false,
                          confirmButtonText: "@lang('main.ok')"
                        }).then((result) => {
                          if (result.value) {
                            location.reload();
                          }
                        })

                     
                      
                    }else{
                      var error = JSON.stringify(data.errors);
                      swal(
                        'Error...',
                        error,
                        'error'
                      )
                    }
                 }

             }).fail(function() {
              $(".btn-save-receive").find('.fa-spinner').hide();
                      swal(
                        'Error...',
                        "@lang('main.something_when_wrong')",
                        'error'
                      )
            });
             return false; // required to block normal submit since you used ajax
         }

    });
	
</script>



<script type="text/javascript">
	$(function(){					
		@if(isset($postId))
			$(".show-content").each(function(){
				if($(this).find(".box-id").val()== {{ $postId }} ){
					$(this).click();
				}

			}) 
		@endif
	}); 
</script>
<script>
$(document).on("click",".show-content",function(event) {
	var boxId = $(this).find(".box-id").val() ;
	
	window.history.pushState("object or string", '' , $("#baseUrl").val()+baseRoute+boxId);

	
	var route = baseRoute+boxId+"?api_token="+api_token ;
    var data = "" ;
    ajaxPromise('GET',route,data).done(function(data){
    	console.log(data);
    	var res = data.parcel_officer ;
    	if(res.supplies_code!= null){
    		$(".row-supplies-code").show().find('span').text(res.supplies_code);
    	}
    	if(res.supplies_send_name!= null){
    		$(".row-supplies-send-name").show().find('span').text(res.supplies_send_name);
    	}
    	if(res.gift_receive_name!= null){
    		$(".row-gift-receive-name").show().find('span').text(res.gift_receive_name);
    	}
    	if(res.gift_send_name!= null){
    		$(".row-gift-send-name").show().find('span').text(res.gift_send_name);
    	}	
    	if(res.gift_description!= null){
    		$(".row-gift-description").show().find('span').text(res.gift_description);
    	}
    	if(res.send_date!= null){
    		$(".row-send-date").show().find('span').text(  moment(res.send_date).format('D/MM/YYYY HH:mm'));
    	}
    	if(res.receive_at!= null){
    		$(".row-received-status").show().find('span').text("@lang('parcel.received')");
			$("#btn-receive").hide();
			$("#btn-un-receive").show();

    	}else{
    		$(".row-received-status").show().find('span').text("@lang('parcel.wait_receive')");
    		$("#btn-receive").show();
    		$("#btn-un-receive").hide();
    	}	

    	$("#btn-receive,#btn-un-receive").data('id',res.id);
    	$("#btn-receive,#btn-un-receive").data('room-id',res.room_id);

    	$(".show-title").find('span').text(res.parcel_type_name);
    	$("#modal-card-content").modal('toggle');
    	
    })
	// if($("#table-quatation").length){
	// 	$("#section-company").show();
	// }
});

</script>
@endsection		
