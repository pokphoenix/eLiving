@extends('main.layouts.main')


@section('style')
<style>
.product-click:hover{ cursor: pointer !important;  }
.product-click {border: 1px solid #F00 !important; }
</style>
@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-home"></i> @lang('main.home')
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
    
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">



      <!-- Small boxes (Stat box) -->
        <div class="row connectedSortable" >
          <div class="col-sm-12 ">
            <div class="" style="background: #ecf0f5;" >
                  <div class="box-body "  >
                    <div class="append-card">
                     

                      @if(!auth()->user()->checkApprove())
                      <div class="box box-solid  " style="border-left: 5px solid #00c0ef;">
                          <div class="box-header">
                              <h3 class="box-title">
                              
                                {!! $preWelcom !!}
                                
                              </h3>
                              <br>
                          </div>
                      </div>
                      <div class="box box-solid card " style="border-left: 5px solid #00c0ef;">
                          <div class="box-header">
                              <h3 class="box-title">
                               <!--  User  {{ getStatusText(Auth()->user()->checkStatusApprove()) }} . 
                                
                                Please fill your data at  <a href="{{ url('/profile/show') }}" >Click</a> or Contact juristic person. <BR> สถานะผู้ใช้งาน {{ getStatusText(Auth()->user()->checkStatusApprove(),'TH') }}  -->
                                
                               @if(Auth()->user()->checkStatusApprove()==5)
                                 กรุณาตรวจสอบอีเมล์ที่แจ้งไว้กับเจ้าหน้าที่เพื่อยืนยันการเข้าใช้งาน
                               @else
                                กรุณากรอกข้อมูลที่ <a href="{{ url('/profile/show') }}" >คลิก</a> หรือ ติดต่อนิติบุคคล 
                               @endif
                                
                              </h3>
                              <br>
                          </div>
                      </div>
                      @endif
                    </div>
                  </div>
                  <!-- /.chat -->
            </div>
          </div>

          @if(auth()->user()->checkApprove())

          @if(count($lists)>0)
           <div class="col-sm-6">     
                <div class="box box-primary">
                  <div class="box-header with-border" style="background: #FFF;">
                    <h3 class="box-title">@lang('sidebar.notice')</h3>


                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                     <!--  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                    </div>
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body bg-gray box-child">
                     @include('widgets.post.main')
                  </div>
                  <!-- /.box-body -->
                  <div class="box-footer text-center">
                    <!-- <a href="javascript:void(0)" class="uppercase">View All Products</a> -->
                  </div>
                  <!-- /.box-footer -->
                </div> 
          </div>


          
          @endif
           
          
          <!-- @if(count($notifications)>0)
          <div class="col-sm-6">
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">@lang('dash.recent_task')</h3>

                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                    
                    </div>
                  </div>
                
                  <div class="box-body">
                    <ul class="products-list product-list-in-box">
                      @foreach ($notifications as $noti)                    
                    
                      <li class="item">
                        <div class="product-img">
                          <i class="fa {{ getNotificationIcon($noti['type']) }} text-{{ getNotificationType($noti['status']) }}"></i>
                        </div>
                        <div class="product-info">
                          <span class="pull-right">{{ $noti['created_at'] }}</span>
                          <a href="{{ getNotificationUrl($noti) }}" class="product-title">{{ $noti['message']  }}</a>
                        </div>
                      </li>

                      @endforeach
                      
                    </ul>
                  </div>
                  
                  <div class="box-footer text-center">
                    
                  </div>
                
                </div>
          </div>
          @endif -->
          @if(count($quotations)>0)
          <div class="col-sm-6">     
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">@lang('dash.quotation')</h3>

                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                     <!--  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                    </div>
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body bg-gray box-child">
                    <ul class="products-list product-list-in-box">
                      @foreach ($quotations as $quotation)                    
                      <li class="item">
                        <div class="product-img">
                        </div>
                        <div class="product-info">
                           <a href="{{ url($domainName.'/purchase/quotation/'.$quotation['id']) }}" class="product-title">{{$quotation['title'] }}</a>
                          <span class="product-description">
                               {{ $quotation['created_at'] }}
                          </span>
                        </div>
                      </li>
                      @endforeach
                      
                    </ul>
                  </div>
                  <!-- /.box-body -->
                  <div class="box-footer text-center">
                    <!-- <a href="javascript:void(0)" class="uppercase">View All Products</a> -->
                  </div>
                  <!-- /.box-footer -->
                </div> 
          </div>
          @endif
           @if(count($tasksOfficer)>0)
          <div class="col-sm-6">     
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">@lang('dash.task_user')</h3>

                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                     <!--  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                    </div>
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body bg-gray box-child">
                    <ul class="products-list product-list-in-box">
                      @foreach ($tasksOfficer as $to)    
                      <li class="item">
                        <div class="product-img">
                        </div>
                        <div class="product-info">
                          @if($to['type']==1)
                          <a href="{{ url($domainName.'/task/'.$to['id']) }}" class="product-title">{{$to['title'] }}</a>
                          @else
                          <a href="{{ url($domainName.'/officer/task/'.$to['id']) }}" class="product-title">{{$to['title'] }}</a>
                          @endif
                          <span class="product-description">
                               {{ $to['created_at'] }}
                          </span>
                        </div>
                      </li>

                     
                      @endforeach
                      
                    </ul>
                  </div>
                  <!-- /.box-body -->
                  <div class="box-footer text-center">
                    <!-- <a href="javascript:void(0)" class="uppercase">View All Products</a> -->
                  </div>
                  <!-- /.box-footer -->
                </div> 
          </div>
          @endif
          @if(count($tasksUser)>0)
          <div class="col-sm-6">     
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">@lang('dash.task_user')</h3>

                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                     <!--  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                    </div>
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body box-child">
                    <ul class="products-list product-list-in-box">
                      @foreach ($tasksUser as $tu)                    
                      <li class="item">
                        <div class="product-img">
                        </div>
                        <div class="product-info">
                          <a href="{{ url($domainName.'/user/'.$tu['room_id'].'/task/'.$tu['id']) }}" class="product-title">{{$tu['title'] }}</a>
                          <span class="product-description">
                               {{ $tu['created_at'] }}
                          </span>
                        </div>
                      </li>
                      @endforeach
                      
                    </ul>
                  </div>
                  <!-- /.box-body -->
                  <div class="box-footer text-center">
                    <!-- <a href="javascript:void(0)" class="uppercase">View All Products</a> -->
                  </div>
                  <!-- /.box-footer -->
                </div> 
          </div>
          @endif
           @if(count($parcels)>0)
          <div class="col-sm-6">     
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">@lang('dash.parcels')</h3>

                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                     <!--  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                    </div>
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body box-child">
                    <ul class="products-list product-list-in-box">
                      @foreach ($parcels as $p)                    
                      <li class="item">
                        <div class="product-img">
                        </div>
                        <div class="product-info">
                          <a href="{{ url($domainName.'/parcel/'.$p['room_id'].'/user/'.$p['id']) }}" class="product-title">{{ $p['parcel_type_name'] }}</a>
                          <span class="product-description">
                               {{ $p['created_at'] }}
                          </span>
                        </div>
                      </li>

                      @endforeach
                      
                    </ul>
                  </div>
                  <!-- /.box-body -->
                  <div class="box-footer text-center">
                    <!-- <a href="javascript:void(0)" class="uppercase">View All Products</a> -->
                  </div>
                  <!-- /.box-footer -->
                </div> 
          </div>
          @endif
          
          @endif
        </div>
		    
  
    </section>
    <!-- /.content -->

@endsection

@section('javascript')
 <script type="text/javascript" src="{{ url('js/post/comment.js') }}"></script> 
 <script type="text/javascript" src="{{ url('js/post/main.js') }}"></script> 

	<script type="text/javascript">
		var userId = {{ Auth()->user()->id }} ;

$(function() {
   $('.connectedSortable').sortable({
    placeholder         : 'sort-highlight',
    connectWith         : '.connectedSortable',
    handle              : '.box-header, .nav-tabs',
    forcePlaceholderSize: true,
    zIndex              : 999999
  });
  $('.connectedSortable .box-header, .connectedSortable .nav-tabs-custom').css('cursor', 'move');


  $('.box-child').slimScroll({
      height: '250px'
  });

});

	</script>
@endsection		
