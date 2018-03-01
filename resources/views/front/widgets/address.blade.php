<section class="form-parent none" data-id="1" >
	<form id="address-form" method="post" action="{{ route('signup') }}" class="mu-contact-form">
		<input type="hidden" id="api_search" value="{{ url('api/search')}}">
    
		<div class="col-sm-offset-4 col-sm-4">
			<div class="form-group">
            	<textarea class="form-control" rows="1" id="address" name="address" placeholder="@lang('user.address')" style="height:50px; resize:none;overflow:hidden " >{{ (isset($edit)) ? $address['address'] : old('address') }}</textarea>
        	</div>
        	<div class="form-group">
	            <select class="select2 form-control" placeholder="@lang('user.province')" id="province_id" name="province_id" >
	                <option value="">@lang('user.province')</option>
	                @if (isset($province))
	                @foreach($province as $p)
	                <option value="{{ $p['id'] }}" @if(isset($edit)&&$address['province_id']==$p['id']) selected=""  @endif > {{ $p['name' ]}} </option>
	                @endforeach
	                @endif
	            </select>
        	</div>
	        <div class="form-group">
	            <select class="select2 form-control" placeholder="@lang('user.amphur')" id="amphur_id" name="amphur_id" >
	              <option value="">@lang('user.amphur')</option>
	                @if (isset($amphur))
	                @foreach($amphur as $a)
	                <option value="{{ $a['id'] }}" @if(isset($edit)&&$address['amphur_id']==$a['id']) selected=""  @endif > {{ $a['name' ]}} </option>
	                @endforeach
	                @endif
	            </select>
	        </div>
	        <div class="form-group">
	            <select class="select2 form-control " placeholder="@lang('user.district')" id="district_id" name="district_id" >
	              <option value="">@lang('user.district')</option>
	                @if (isset($district))
	                @foreach($district as $d)
	                <option value="{{ $d['id'] }}" @if(isset($edit)&&$address['district_id']==$d['id']) selected=""  @endif > {{ $d['name' ]}} </option>
	                @endforeach
	                @endif
	            </select>
	        </div>
	        <div class="form-group">
	            <input type="text" class="form-control" id="zip_code" name="zip_code" placeholder="@lang('user.zipcode')" maxlength="5" minlength="5" value="{{ (isset($edit)) ? $address['zip_code'] : old('zip_code') }}" >
	        </div> 
		</div>
	</form>
</section>