<div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"> @lang('user.address_detail')</h3>
              <div class="box-tools pull-right">
                   
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
          
              
              <div class="box-body">
                <input type="hidden" id="api_search" value="{{ url('api/search')}}">
                
                <div class="col-sm-12">
                  @if(isset($showAddressName))
                  <div class="form-group">
                    <label for="exampleInputPassword1">@lang('user.address_name')</label>
                    <input type="text" class="form-control" id="address_name" name="address_name" placeholder="@lang('user.address_name')" value="{{ (isset($edit)) ? $address['address_name'] : old('address_name') }}" >
                  </div>
                  @endif
                  <div class="form-group">
                    <label for="exampleInputPassword1">@lang('user.address')</label>
                    <textarea class="form-control" rows=1 id="address" name="address" placeholder="@lang('user.address')" >{{ (isset($edit)) ? $address['address'] : old('address') }}</textarea>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">@lang('user.province')</label>
                    <select class="select2 form-control" id="province_id" name="province_id" >
                        <option value=""></option>
                        @if (isset($province))
                        
                        @foreach($province as $p)
                        <option value="{{ $p['id'] }}" @if(isset($edit)&&$address['province_id']==$p['id']) selected=""  @endif > {{ $p['name' ]}} </option>
                        @endforeach
                        @endif

                      </select>
                      
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">@lang('user.amphur')</label>
                    <select class="select2 form-control" id="amphur_id" name="amphur_id" >
                      <option value=""></option>
                        @if (isset($amphur))
                        
                        @foreach($amphur as $a)
                        <option value="{{ $a['id'] }}" @if(isset($edit)&&$address['amphur_id']==$a['id']) selected=""  @endif > {{ $a['name' ]}} </option>
                        @endforeach
                        @endif

                      </select>
                      
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">@lang('user.district')</label>
                    <select class="select2 form-control " id="district_id" name="district_id" >
                      <option value=""></option>
                        @if (isset($district))
                        
                        @foreach($district as $d)
                        <option value="{{ $d['id'] }}" @if(isset($edit)&&$address['district_id']==$d['id']) selected=""  @endif > {{ $d['name' ]}} </option>
                        @endforeach
                        @endif

                    </select>
                      
                  </div>

                 
                  <div class="form-group">
                    <label for="exampleInputPassword1">@lang('user.zipcode')</label>
                    <input type="text" class="form-control" id="zip_code" name="zip_code" placeholder="@lang('user.zipcode')" maxlength="5" minlength="5" value="{{ (isset($edit)) ? $address['zip_code'] : old('zip_code') }}" >
                  </div>
                </div>

              </div>
              <!-- /.box-body -->

              
            
          </div>