<div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">@lang('user.ban')</h3>
                <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                  </div>
              </div>
              <div class="box-body">
                    <div class="row">
                      <div class="col-sm-12">
                       <div class="form-group">
                        <label for="exampleInputPassword1">@lang('user.ban')</label>
                       
                        <input type="checkbox" name="is_ban" @if(isset($data['is_ban'])&&$data['is_ban']==1) checked=""  @endif  >
                      </div>
                      </div>
                    </div>
              </div>
                

                <div class="box-footer">
                 <!--  <button type="button" class="btn btn-default" id="btn-upload-file">นำส่ง</button> -->
                </div>
              
            </div>