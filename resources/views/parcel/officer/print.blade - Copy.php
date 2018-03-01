@extends('main.layouts.main')


@section('style')
<link rel="stylesheet" href="{{ url('public/css/quotation-print.css')}}" media="screen,print">
 <style>


@media print {
  
  
  .invoice { page-break-after: always;
        page-break-inside: avoid; }

}

  </style>
@endsection

@section('content-wrapper')
  

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       
        <i class="fa fa-send"></i>
       
          @lang('parcel.mailing_post')
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active"> @lang('parcel.mailing_post')</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
  
       @include('layouts.error')
    
      <div class="row">
          <div class="col-xs-12">
             <div class="row">
              <form id="search-form" method="GET" action="">
                <div class="form-group col-sm-4">
                    <label for="name">@lang('parcel.start_date')</label>
                    <div >
                      <div class="col-xs-2" style="padding:0px"> 
                      <input type="text" class="form-control" id="start_date_day"  placeholder="@lang('parcel.send_date_day')" value="{{ date('d',$startDate) }}" >
                     </div>
                    <div class="col-xs-2" style="padding:0px">  
                      <input type="text" class="form-control" id="start_date_month"  placeholder="@lang('parcel.send_date_month')" value="{{ date('m',$startDate) }}" >
                    </div>
                    <div class="col-xs-2" style="padding:0px">
                      <input type="text" class="form-control" id="start_date_year"  placeholder="@lang('parcel.send_date_year')" value="{{ date('Y',$startDate) }}" >
                    </div>
                    <div class="col-xs-2" style="padding:0px">
                      <input type="text" class="form-control" id="start_date_hour"  placeholder="@lang('parcel.send_date_hour')" value="{{ date('H',$startDate) }}" >
                    </div>
                    <div class="col-xs-2" style="padding:0px">
                      <input type="text" class="form-control" id="start_date_minute"  placeholder="@lang('parcel.send_date_minute')" value="{{ date('i',$startDate) }}" >
                    </div>
                    </div>
                    
                </div>
                <div class="form-group col-sm-4">
                    <label for="name">@lang('parcel.end_date')</label>
                    <div >
                      <div class="col-xs-2" style="padding:0px"> 
                      <input type="text" class="form-control" id="end_date_day"  placeholder="@lang('parcel.send_date_day')" value="{{ date('d',$endDate) }}" >
                     </div>
                    <div class="col-xs-2" style="padding:0px">  
                      <input type="text" class="form-control" id="end_date_month"  placeholder="@lang('parcel.send_date_month')" value="{{ date('m',$endDate) }}" >
                    </div>
                    <div class="col-xs-2" style="padding:0px">
                      <input type="text" class="form-control" id="end_date_year"  placeholder="@lang('parcel.send_date_year')" value="{{ date('Y',$endDate) }}" >
                    </div>
                    <div class="col-xs-2" style="padding:0px">
                      <input type="text" class="form-control" id="end_date_hour"  placeholder="@lang('parcel.send_date_hour')" value="{{ date('H',$endDate) }}" >
                    </div>
                    <div class="col-xs-2" style="padding:0px">
                      <input type="text" class="form-control" id="end_date_minute"  placeholder="@lang('parcel.send_date_minute')" value="{{ date('i',$endDate) }}" >
                    </div>
                    </div>
                    
                </div>
                 <div class="form-group col-sm-4">
                    <label for="name">&nbsp;</label>
                    <div>
                    <button type="button" class="btn btn-primary btn-search"><i class="fa fa-search"></i> @lang('main.search')</button>  
                    </div>
                    
                    
                </div>
              </form>
            </div>
          </div>

          <div class="col-xs-12">
             <button onclick="printContent('print_this')" class="btn btn-success" style="margin-left: 10px;" >Print</button>
          </div>
      <div class="row">
          <div class="col-xs-12" id="print_this">
  @if(isset($lists))
  <?php $page = 1 ?>
   @for($i=0;$i< count($lists) ;$i++ )
      
      @if($i % 20==0)
  <section class="invoice" style="padding: 0">
    <div class="row">
        <div class="col-xs-3 text-center">
          <div class="text-center" style="border:5px solid #CCC;margin-top: 10px;">
            <h2 style="margin-top: 10px;" > {{ date('d')."/".date('m')."/".( date('Y')+543) }}</h2>
          </div>
        </div>
       <div class="col-xs-6">
         <h3 class="text-center">@lang('sidebar.mailing_list') </h3>
         <h4 class="text-center">{{ $setting['header_officer'] }}</h4>
       </div>
       <div class="col-xs-3 text-right">
        <div class="col-xs-6" style="margin-top: 20px;"> <img src="{{ $setting['logo_domain'] }}" width="100" height="50" style="background: #CCC;" ></div>
        <div class="col-xs-6" style="margin-top: 20px;"> <img src="{{ $setting['logo_officer'] }}" height="50" style="background: #CCC;"></div>
       </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <table class="table table-bordered table-striped">
              <thead>
                <tr class="text-center">
                  <th rowspan="2" class="vm-ct" style="width: 100px;">@lang('parcel.no')</th>
                  <th rowspan="2" class="vm-ct" style="width: 100px;">@lang('parcel.room_id')</th>
                  <th rowspan="2" class="vm-ct" style="width: 200px;">@lang('parcel.supplies_send_name')</th>
                  <th colspan="3" class="vm-ct" >@lang('parcel.type')</th>
                  <th rowspan="2" class="vm-ct" style="width: 100px;">@lang('parcel.supplies_code')</th>
                  <th rowspan="2" class="vm-ct" style="width: 150px;">@lang('parcel.receiver')</th>
                  <th rowspan="2" class="vm-ct" style="width: 150px;">@lang('parcel.received_tel')</th>
                  <th rowspan="2" class="vm-ct" style="width: 150px;">@lang('parcel.received_at')</th>
                 
                </tr>
              
                <tr>
                  <th class="text-center" style="width: 50px;"><small>@lang('parcel.box')</small></th>
                  <th class="text-center" style="width: 50px;"><small>@lang('parcel.envelope')</small></th>
                  <th class="text-center" style="width: 50px;"><small>@lang('parcel.letter')</small></th>
                </tr>
               
              </thead>
              <tbody>

        @for($j=(($page-1)*20);$j<(($page-1)*20)+20;$j++ )
          @if(isset($lists[$j]))
          <tr>
                <!-- <td>{{ $j+1 }}</td> -->
                <td class="text-right"><span>{{ $lists[$j]['parcel_code'] }}</span></td>
                <td class="text-right">{{ $lists[$j]['room_name'] }}</td>
                <td><span>
                  {{ (isset($lists[$j]['supplies_send_name'])) ? $lists[$j]['supplies_send_name'] : '' }}
                  {{ (isset($lists[$j]['gift_send_name'])) ? $lists[$j]['gift_send_name'] : '' }}
                </span></td>
                <td class="text-center">@if($lists[$j]['type']==2&& $lists[$j]['supplies_type']==1 ) <i class="fa fa-check"></i> @endif</td>
                <td class="text-center">@if($lists[$j]['type']==2&& $lists[$j]['supplies_type']==2) <i class="fa fa-check"></i> @endif</td>
                <td class="text-center">@if($lists[$j]['type']==1 || ($lists[$j]['type']==2&& $lists[$j]['supplies_type']==3)) <i class="fa fa-check"></i> @endif</td>

               
                <td><span>{{ (isset($lists[$j]['supplies_code'])) ? $lists[$j]['supplies_code'] : '' }}</span></td>
                <td><span>{{ (isset($lists[$j]['receive_name'])) ? $lists[$j]['receive_name'] : '' }}</span></td>
                <td><span>{{ (isset($lists[$j]['receive_tel'])) ? $lists[$j]['receive_tel'] : '' }}</span></td>
                <td><span>{{ (isset($lists[$j]['receive_at'])) ? $lists[$j]['receive_at'] : '' }}</span></td>
               
              </tr>
          @else
           <tr>
                  @for($td=0;$td <  10 ; $td++  )
                  <td>&nbsp</td>
                   @endfor
              </tr>

          @endif
              
              
        @endfor

         
              </tbody>
            </table>
  

       
       </div>
    </div>
       <!--  <div class="page-break"></div> -->
  </section>
         <?php $page++ ?>
    

      @endif


   @endfor
   @endif
         </div>
      </div>
      </div>
    </section>
    <!-- /.content -->
 
@endsection

@section('javascript')

<script src=" {{ url('js/utility/print.js') }}"></script>
<script type="text/javascript">
$(".btn-search").on('click', function(event) {
  var start_date = $("#start_date_year").val()+"-"+$("#start_date_month").val()+"-"+$("#start_date_day").val()+" "+$("#start_date_hour").val()+":"+$("#start_date_minute").val();
  var end_date = $("#end_date_year").val()+"-"+$("#end_date_month").val()+"-"+$("#end_date_day").val()+" "+$("#end_date_hour").val()+":"+$("#end_date_minute").val();


  start_date = moment(start_date).format('x')/1000;
  end_date = moment(end_date).format('x')/1000;

  var url = $("#baseUrl").val()+'/parcel/print-list?start_date='+start_date+'&end_date='+end_date ;
 
  window.location.href=url ;
});

</script>
@endsection   
