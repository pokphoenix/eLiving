<div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">@lang('user.information')</h3>
              <div class="box-tools pull-right">
                   
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
          
              
              <div class="box-body">
          
              
        
                <div class="col-sm-12">
                  
                  <div class="form-group">
                    <label >@lang('user.id_card')</label>
                    <input type="text" maxlength="13"  class="form-control" id="id_card" name="id_card" placeholder="@lang('user.id_card')"  value="{{ isset($edit) ? $data['id_card'] : old('id_card') }}" >
                  </div>
                  @if(isset($edit)&&isset($data['username']))
                  <div class="form-group">
                    <label >@lang('user.user_name')</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="@lang('user.user_name')" value="{{ isset($edit) ? $data['username'] : old('username') }}" readonly="">
                  </div>
                  @endif
                  <div class="form-group">
                    <label >@lang('main.email')</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="@lang('main.email')"  value="{{ isset($edit) ? $data['email'] : old('email') }}">
                  </div>
                  <div class="form-group">
                    <label >@lang('user.first_name')</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="@lang('user.first_name')" value="{{ isset($edit) ? $data['first_name'] : old('first_name') }}">
                  </div>
                  <div class="form-group">
                    <label >@lang('user.nick_name')</label>
                    <input type="text" class="form-control" id="nick_name" name="nick_name" placeholder="@lang('user.nick_name')" value="{{ isset($edit) ? $data['nick_name'] : old('nick_name') }}">
                  </div>
                  <div class="form-group">
                    <label >@lang('user.last_name')</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="@lang('user.last_name')" value="{{ isset($edit) ? $data['last_name'] : old('last_name') }}">
                  </div>
                  <div class="form-group">
                    <label >@lang('main.tel')</label>
                    <input type="text" class="form-control" id="tel" name="tel" placeholder="@lang('main.tel')" value="{{ isset($edit) ? $data['tel'] : old('tel') }}">
                  </div>
                  <div class="form-group">
                    <label >@lang('user.alert_text')</label>
                    <input type="text" class="form-control" id="alert_text" name="alert_text" placeholder="@lang('user.alert_text')" value="{{ (isset($edit)&&isset($data['alert_text'])) ? $data['alert_text'] : old('alert_text') }}">
                  </div>
                </div>
              </div>
             
            
          </div>