
    @if(count($domains)>0)

      <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">@lang('domain.has_domain')</h3>

                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
            <!-- /.box-header -->
              <div class="box-body">
                <ul class="products-list product-list-in-box">
                  @foreach ($domains as $domain)
                  <li class="item" >
                    
                    <div class="product-img">
                      <img src=" {{ url('dist/img/default-50x50.gif') }} " alt="Product Image">
                    </div>
                    <div class="product-info">
                      <a href="javascript:void(0)" class="product-title"> {{ $domain['name'] }}
                          <span class="label label-warning pull-right">@lang('domain.unit_count') {{ $domain['unit'] }}</span>
                        </a>
                        <span class="pull-right" style="margin-right: 10px;">
                          @if($domain['approve'])

                          <a href="{{ url( $domain['url_name'].'/dashboard') }}" class="btn btn-default"> @lang('domain.click') </a>
                          @else
                           @lang('domain.wait_approve')
                          @endif
                        </span>
                        <span class="product-description">
                              {{ $domain['residence_name']  }}
                        </span>
                        
                
                    </div>
                     <div >
                     
                     </div>
                  </li>
                
                  @endforeach
                </ul>
              </div>
            <!-- /.box-body -->
              @if(count($domains)>1)
             <!--  <div class="box-footer text-center">
                <a href="javascript:void(0)" class="uppercase">View All Products</a>
              </div> -->
              @endif
            <!-- /.box-footer -->
            </div>
   
        @endif