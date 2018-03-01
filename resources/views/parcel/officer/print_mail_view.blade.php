@extends('main.layouts.main')


@section('style')
<link rel="stylesheet" href="{{ url('public/css/quotation-print.css')}}" media="screen,print">
<style>
  .page-break{
    /*margin:50px 0;*/
    /*border-bottom:1px dotted #333;*/
  }
  .column-view {
       
        padding:0 10px; border-bottom: 1px dotted #000; 
    }
@media print {
  
  .wrapper{
    overflow: hidden !important;
  }
  /*.invoice { page-break-after: always;
        page-break-inside: avoid; }*/
  .page-break{
    display: block;
    page-break-after: always;
  }
  .invoice{padding: 0; margin-top: 0}

 /* .parent-wrapper{
   width: 100%;
    height: 100%;
   
    overflow: hidden !important;
 }

 .child-wrapper{
   width: 100%;
    height: 99%;
   
    overflow-y: auto !important;
 }*/

}

  </style>
@endsection

@section('content-wrapper')
  
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-send"></i>
         {{ $title }}
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">{{ $title }}</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
  
       @include('layouts.error')
       
        <div class="row">
          <div class="col-xs-12">
             <button onclick="window.history.back();" class="btn btn-danger" style="margin-left: 10px;" >@lang('main.back')</button>
             <button onclick="printContent('print_this')" class="btn btn-success" style="margin-left: 10px;" >Print</button>
          </div>
        </div>
        <div class="row">
            <section class="invoice" id="print_this" style="padding: 0">


  @if(isset($lists))
  <?php $page = 1 ?>
   @for($i=0;$i< count($lists) ;$i++ )
      
      @if($i % 4==0)
         @for($j=(($page-1)*4);$j<(($page-1)*4)+4;$j++ )
          @if(isset($lists[$j]))
              <div class="row">
                  <div class="col-xs-2 text-center">
                   
                  </div>
                 <div class="col-xs-8">
                    <h4 class="text-center">REGISTOR MAIL AND PARCEL POST/pick up form</h4>
                   <h4 class="text-center">@lang('sidebar.receive_mail_list') </h4>
                  
                 </div>
                 <div class="col-xs-2 text-right">
                 </div>
              </div>
            

              <div class="row">
                  <div class="col-xs-6">
                    <div class="col-xs-4"> No. </div>
                    <div class="col-xs-8 ">

                       {{ $lists[$j]['parcel_code'] }} 
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div class="col-xs-6"> Date. </div>
                    <div class="col-xs-5 column-view">
                       {{ date('d m ',strtotime($lists[$j]['send_date'])).(date('Y',strtotime($lists[$j]['send_date']))+543)." ".date('H:i ',strtotime($lists[$j]['send_date'])) }} 
                    </div>
                    
                  </div>
              </div>
              <div class="row" style="height: 20px;"></div>
              <div class="row">
                  <div class="col-xs-6">
                    <div class="col-xs-4"> Name / ชื่อ </div>
                    <div class="col-xs-8 column-view">
                       &nbsp;
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div class="col-xs-6"> Room No / ห้องเลขที่ </div>
                    <div class="col-xs-5 column-view">
                       {{ $lists[$j]['room_name'] }} 
                    </div>
                  </div>
              </div>
              <div class="row" style="height: 20px;"></div>
             <div class="row">
                  <div class="col-xs-6">
                    <div class="col-xs-4"> Ref No. / เลขทะเบียน </div>
                    <div class="col-xs-8 column-view">
                       {{ (isset($lists[$j]['supplies_code'])) ? $lists[$j]['supplies_code'] : '&nbsp;' }} 
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div class="col-xs-6"> Sender / ผู้ส่ง </div>
                    <div class="col-xs-5 column-view">
                       {{ (isset($lists[$j]['supplies_send_name'])) ? $lists[$j]['supplies_send_name'] : '&nbsp;' }} 
                    </div>
                  </div>
              </div>
             
              <div class="row">
                  <div class="col-xs-12">
                    <div class="col-xs-12">กรุณาแสดงเอกสารนี้เพื่อรับพัสดุ หรือ เอกสารอื่นๆ ได้ที่ สำนักงานนิติบุคคลฯ ตั้งแต่เวลา 08.00 - 19.00 น.<BR>
                    </div>
                  </div>
                  
                 
              </div>
              
              <div class="row">
                  <div class="col-xs-6">
                    <div class="col-xs-4 " >
                      <div class="col-xs-12 column-view"> &nbsp;</div>
                   </div>
                    <div class="col-xs-8 ">
                       Delivered by: (Signature)
                    </div>
                   
                  </div>
                  <div class="col-xs-6">
                    <div class="col-xs-6">ลายเซ็นต์ผู้จ่าย</div>
                    <div class="col-xs-4 column-view">
                       &nbsp; 
                    </div>
                    <div class="col-xs-1">Date</div>
                  </div>
                 
              </div>

          @else
              <div class="row">
                  <div class="col-xs-2 text-center">
                   
                  </div>
                 <div class="col-xs-8">
                    <h4 class="text-center">REGISTOR MAIL AND PARCEL POST/pick up form</h4>
                    <h5 class="text-center">@lang('sidebar.receive_mail_list') </h5>
                  
                 </div>
                 <div class="col-xs-2 text-right">
                 </div>
              </div>
            

              <div class="row">
                  <div class="col-xs-6">
                    <div class="col-xs-4"> No. </div>
                    <div class="col-xs-8 column-view">
                       &nbsp;
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div class="col-xs-6"> Date. </div>
                    <div class="col-xs-5 column-view">
                       &nbsp;
                    </div>
                  </div>
              </div>
              <div class="row" style="height: 20px;"></div>
              <div class="row">
                  <div class="col-xs-6">
                    <div class="col-xs-4"> Name / ชื่อ </div>
                    <div class="col-xs-8 column-view">
                       &nbsp;
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div class="col-xs-6"> Room No / ห้องเลขที่ </div>
                    <div class="col-xs-5 column-view">
                       &nbsp;
                    </div>
                  </div>
              </div>
              <div class="row" style="height: 20px;"></div>
             <div class="row">
                  <div class="col-xs-6">
                    <div class="col-xs-4"> Ref No. / เลขทะเบียน </div>
                    <div class="col-xs-8 column-view">
                       &nbsp;
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div class="col-xs-6"> Sender / ผู้ส่ง </div>
                    <div class="col-xs-5 column-view">
                      &nbsp;
                    </div>
                  </div>
              </div>
              
              <div class="row">
                  <div class="col-xs-12">
                    <div class="col-xs-12">กรุณาแสดงเอกสารนี้เพื่อรับพัสดุ หรือ เอกสารอื่นๆ ได้ที่ สำนักงานนิติบุคคลฯ ตั้งแต่เวลา 08.00 - 19.00 น.<BR>
                    </div>
                  </div>
                  
                 
              </div>
              
              <div class="row">
                  <div class="col-xs-6">
                    <div class="col-xs-4 " >
                      <div class="col-xs-12 column-view"> &nbsp;</div>
                   </div>
                    <div class="col-xs-8 ">
                       Delivered by: (Signature)
                    </div>
                   
                  </div>
                  <div class="col-xs-6">
                    <div class="col-xs-6">ลายเซ็นต์ผู้จ่าย</div>
                    <div class="col-xs-4 column-view">
                       &nbsp; 
                    </div>
                    <div class="col-xs-1">Date</div>
                  </div>
                 
              </div>  
          @endif
           <div class="row" style="height: 20px;"><hr></div>
        @endfor

         <div class="page-break"></div>
       
         <?php $page++ ?>
      @endif
    @endfor
  @endif
       
    
        
     
  

      
       
   

   </section>
        </div>
     

      
    </section>
    
@endsection

@section('javascript')
<script src=" {{ url('js/utility/print.js') }}"></script>
<script type="text/javascript">
$(".btn-search").on('click', function(event) {
  var start_date = $("#start_date_year").val()+"-"+$("#start_date_month").val()+"-"+$("#start_date_day").val()+" "+$("#start_date_hour").val()+":"+$("#start_date_minute").val();
  var end_date = $("#end_date_year").val()+"-"+$("#end_date_month").val()+"-"+$("#end_date_day").val()+" "+$("#end_date_hour").val()+":"+$("#end_date_minute").val();


  start_date = moment(start_date).format('x')/1000;
  end_date = moment(end_date).format('x')/1000;

  var url = $("#baseUrl").val()+'/parcel/print-mail?start_date='+start_date+'&end_date='+end_date ;
 
  window.location.href=url ;
});

</script>

<script>
  function urlBack();
</script>
@endsection   
