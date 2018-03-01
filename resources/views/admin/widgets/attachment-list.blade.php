 <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">@lang('user.attachment_list')</h3>
                <div class="box-tools pull-right">
                     
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                  </div>
              </div>
              <!-- /.box-header -->
              <!-- form start -->
            
              

                
                <div class="box-body">
                    
                    <div class="row">
                      <div class="col-sm-12">
                          <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                              <th>@lang('user.no')</th>
                              <th>@lang('user.attachment_type')</th>
                              <th>@lang('user.thumbnail')</th>
                              <th>@lang('user.attachment_created_at')</th>
                              <th></th>
                            </tr>
                            </thead>
                            <tbody >
                              @if(isset($docs))
                              @foreach ($docs  as $key=>$doc)
                              <tr>
                                <td>{{ $key+1 }}</td>
                                <td>@if($doc['type']==1)
                                @lang('user.attachment_id_card')  
                                @elseif($doc['type']==3)
                                @lang('user.attachment_other')
                                @else
                                @lang('user.attachment_address')
                                @endif
                               </td>

                                <td> 
                                  @if(Auth()->user()->hasRole('admin'))
                                  <a href="{!! (isset($doc['file_code']))? env('APP_URL_SAVE_IMAGE').'/api/view/'.$doc['file_code'].'?api_token=33ae2f309f127ec78e051ba3075602fc' : $doc['img']  !!}" download="{{$doc['image']}}" title="click to download"> 
                                    <img src="{!! $doc['img'] !!}" height="50">
                                  </a>
                                  @endif
                                  {{ $doc['file_name'] }}
                                </td>
                                <td>{!! date('d/m/Y H:i',strtotime($doc['created_at'])) !!}</td>
                                <td>
                                  @if(isset($canDelAttach))
                                  <button type="button" class="btn btn-danger btn-xs btn-del-attach" data-id="{{$doc['id']}}" >
                                    <i class="fa fa-close"></i>
                                  </button>
                                  @endif
                                </td>
                              </tr>
                              @endforeach
                              @endif
                            </tbody>
                          
                           
                          </table>
                      </div>
                    </div>
                    

                    <!-- @for($i=1;$i<=4;$i++)
                    <div class="row">
                      <div class="col-sm-12 upload-file">
                        <div class="form-group col-sm-4">
                            <label for="exampleInputFile">ประเภทเอกสาร</label>
                            <select class="doc_type">
                                  <option value="0">บัตรประชาชน</option>
                                  <option value="1">บัตรประชาชน</option>
                                  <option value="2">ที่อยู่</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-8">
                          <input type="file"  id="input_file_{{ $i }}"  name="input_file[]"  >
                        </div>
                      </div>
                    </div>
                    @endfor -->


                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                 <!--  <button type="button" class="btn btn-default" id="btn-upload-file">นำส่ง</button> -->
                </div>
              
            </div>