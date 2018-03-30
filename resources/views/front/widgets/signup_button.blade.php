<div class="row">
    <div class="col-sm-offset-4 col-sm-4 ">
		<div class="text-center" >
		    <button type="button" class="mu-order-btn btn-cancel-1" onclick="window.location.href='{{ url("/") }}' " ><span>@lang('main.btn_cancel') </span>
		    </button> 

			<button type="button" class="mu-order-btn back-to none" 
		       ><span>@lang('main.btn_back') </span>
		    </button> 

		    <button type="button" class="mu-send-msg-btn next-to"><i class="fa fa-spinner fa-spin fa-fw" style="display:none;float:left;"></i>@lang('main.next') 
		    </button>
		       
		    <button type="button" class="mu-send-msg-btn btn-submit none" ><i class="fa fa-spinner fa-spin fa-fw" style="display:none;float:left;"></i>@lang('main.submit') 
		    </button>
		</div>
	</div>
</div>

<!-- <div class="row btn-cancel-2 none" >
	<div class="col-sm-offset-2 col-sm-8" >
		<div class="row" style="margin:10px 0;color: #ccc;border-top: 1px solid #CCC;">
		</div>
		<div class="row">
			<div class="col-sm-offset-2 col-sm-8 text-center" >
				<button type="button" class="mu-order-btn " style="margin: 0 auto;" onclick="window.location.href='{{ url("/") }}' " ><span>@lang('main.btn_cancel') </span></button> 
			</div>
		</div>
	</div>
</div> -->