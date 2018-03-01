 <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">@lang('user.attachment')</h3>
                <div class="box-tools pull-right">
                     
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                  </div>
              </div>
              <!-- /.box-header -->
              <!-- form start -->
            
              

                
                <div class="box-body">
                    <div class="row">
                      <div class="col-sm-12 upload-file">
                        <div class="form-group col-sm-4">
                            <label for="exampleInputFile">@lang('user.attachment_type')</label>
                            <select class="doc_type">
                                  
                                  <option value="1">@lang('user.attachment_id_card')</option>
                                  <option value="2">@lang('user.attachment_address')</option>
                                  <option value="3">@lang('user.attachment_other')</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-8">
                          <label for="file_upload" class="btn btn-info">
                            <i class="fa fa-cloud-upload"></i> @lang('user.browse_file')
                          </label>
                          <input id="file_upload" name='doc_file[]' type="file" style="display:none;" >
                        </div>
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-sm-12">
                          <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                              <th>@lang('user.no')</th>
                              <th>@lang('user.attachment_type')</th>
                              <th>@lang('user.thumbnail')</th>
                              <th>@lang('user.file_name')</th>
                              <th>@lang('user.file_size')</th>
                            </tr>
                            </thead>
                            <tbody id="append_upload">
                            </tbody>
                          
                           
                          </table>
                      </div>
                    </div>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                 <!--  <button type="button" class="btn btn-default" id="btn-upload-file">นำส่ง</button> -->
                </div>
              
            </div>
            