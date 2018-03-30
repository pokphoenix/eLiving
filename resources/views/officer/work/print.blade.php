<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>eLiving</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ url('bower_components/bootstrap/dist/css/bootstrap.min.css')}}" media="screen,print">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ url('bower_components/font-awesome/css/font-awesome.min.css')}}" media="screen,print">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ url('bower_components/Ionicons/css/ionicons.min.css')}}" media="screen,print">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ url('dist/css/AdminLTE.min.css')}}" media="screen,print">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic" media="screen,print">
  <link rel="stylesheet" href="{{ url('public/css/work-print.css')}}" media="screen,print">
  <script src="{{url('bower_components/jquery/dist/jquery.min.js')}}"></script>
  <style>
  
    .page {
        width: 210mm;
        min-height: 297mm;
        padding: 1mm;
        margin: 10mm auto;
      
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
    .subpage {
        padding: 3mm;
        height: 277mm;
       
    }
    
    @page {
        size: A4;
        margin: 0;
    }
    @media print {
        html, body {
            width: 210mm;
            height: 297mm;        
        }
        .page {
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            height: 290mm; 
            page-break-after: always;
           
        }
         .subpage {
            padding: 3mm;
            height: 289mm;
          
        }
    }

    



    

  </style>
  <script src=" {{ url('js/utility/print.js') }}"></script>
  <script type="text/javascript">
    function ReplaceNumberWithCommas(yourNumber) {
    //Seperates the components of the number
    var n= yourNumber.toString().split(".");
    //Comma-fies the first part
    n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    //Combines the two sections
    return n.join(".");
}


  

  $(function() {
    printContent('print_this')
  });

  
  </script>
</head>
<body  @if(!isset($preview)) onload="window.print();"  @endif >
<!-- <body > -->
<div class="wrapper">
  <!-- Main content -->
  <section class="page" id="print_this" >
    <div class="subpage">
       <div class="row">
            <div class="col-xs-4">&nbsp;</div>
            <div class="col-xs-4 text-center">
              <h4>ใบรับแจ้งซ่อม</h4>
              <h4>WORK ORDER</h4>
            </div>
            <div class="col-xs-4">
              <div class="pull-right">
                <img src=" {{$data['logo_domain']}} " width="100" height="50">
              </div>
               
            </div>
        </div>
        <div class="row" style="font-size: 18px;">
            <div class="col-xs-6">
              อาคาร / Building
              <span style="border: 1px solid #CCC;width:150px;display: inline-block;  padding:5px; height:25px;">&nbsp;</span></div>
            <div class="col-xs-6">
              <div class="pull-right">
                 เลขที่ใบแจ้งซ่อม / Job No.  <span class="column-line text-right" style="width: 150px;">
                
                {{str_pad($work['id'],10,'0',STR_PAD_LEFT)}}
               
                 </span>
              </div>
             
            </div>
        </div>
        <div class="row" style="font-size: 18px;">
            <div class="col-xs-12">
              วันที่ออกใบแจ้งซ่อม / Date
              <span class="column-line">{{ date('d/m/Y') }}</span>
              ขนิดงาน / Job Type&nbsp;&nbsp;&nbsp;&nbsp;
              <span class="checkbox">
                @if($work['job_type']==1)
                <i class="fa fa-check"></i>
                 @else
                &nbsp;
                @endif
              </span>&nbsp;&nbsp;SR&nbsp;&nbsp;
              <span class="checkbox">
                @if($work['job_type']==2)
                <i class="fa fa-check"></i>
                @else
                &nbsp;
                @endif
              </span>&nbsp;&nbsp;EM&nbsp;&nbsp;
              <span class="checkbox">
                @if($work['job_type']==3)
                <i class="fa fa-check"></i>
                 @else
                &nbsp;
                @endif
              </span>&nbsp;&nbsp;PM&nbsp;&nbsp;
            </div>
        </div>
  
        <div class="row">
          <div class="col-xs-12">
            <div style="width: 100%;height:1px;border-bottom:1px solid #000;margin-bottom:5px; ">&nbsp;</div>
          </div>
        </div>
  

        <div class="row">
          <div class="col-xs-12" >
            <div style="border: 1px solid #000;padding: 5px;">
              <div class="row">
               
                <div class="col-xs-offset-2 col-xs-4">
                  <span class="checkbox checkbox-medium" >
                    @if($work['area_type']==1)
                    <i class="fa fa-check"></i>
                     @else
                    &nbsp;
                    @endif
                  </span>&nbsp;&nbsp;พื้นที่ส่วนกลาง / Commond Area
                </div>
                <div class="col-xs-4">
                  <span class="checkbox checkbox-medium">
                  @if($work['area_type']==2)
                <i class="fa fa-check"></i>
                 @else
                &nbsp;
                @endif
                  </span>&nbsp;&nbsp;พื้นที่ลูกค้า / Owner Area
                </div>
                <div class="col-xs-2"></div>
              </div>
              <div class="row">
                <div class="col-xs-12">
                  ผู้แจ้ง / Customer
                  <span class="column-line" style="width:100px;">{{ isset($work['creator_name']) ? $work['creator_name'] : '&nbsp;' }}</span>
                  เลขที่ / Address
                  <span class="column-line" style="width:70px;">{{ isset($work['room_name']) ? $work['room_name'] : '&nbsp;' }}</span>
                  อาคาร / Tower
                  <span class="column-line" style="width:50px;">&nbsp;</span>
                  ชั้น / Floor
                  <span class="column-line" style="width:40px;">&nbsp;</span>
                  เบอร์ติดต่อ / Tel.
                  <span class="column-line" style="width:40px;overflow: hidden;">{{ isset($work['creator_tel']) ? $work['creator_tel'] : '&nbsp;' }}</span>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12">
                  รายการที่แจ้ง / Problem
                  <div class="lined-form">
                      <div clsss="row-content-test" style=" position: absolute; display: block;display: -webkit-box;max-width: 100%;height: 50px; margin: 0 auto;font-size: 16;line-height: 16px;-webkit-line-clamp: 3;-webkit-box-orient: vertical;overflow: hidden;text-overflow: ellipsis;" >
                        <span>
                          {{  (isset($work['description'])) ? $work['description'] : '&nbsp;' }}
                        </span>
                      
                      </div>
                      <span class="lines">
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                      </span>
                  </div>
                  
                </div>
              </div>
              <div style="height: 50px;"></div>

              <div class="row">
                <div class="col-xs-12">
                  ประเภทงาน / System
                  <div class="row-checkbox">
                    <span class="checkbox checkbox-small" >
                    @if($work['category_id']==1)
                    <i class="fa fa-check"></i>
                    @else
                    <i></i>
                    @endif
                    </span>
                    <div class="column-text" style="">&nbsp;&nbsp;ไฟฟ้า / Electricity</div>
                  </div>
                  <div class="row-checkbox">
                    <span class="checkbox checkbox-small" >
                    @if($work['category_id']==2)
                    <i class="fa fa-check"></i>
                    @else
                    <i></i>
                    @endif
                    </span>
                    <div class="column-text" style="">&nbsp;&nbsp;ปรับอากาศ / Air Condition</div>
                  </div>
                  <div class="row-checkbox">
                    <span class="checkbox checkbox-small" >
                    @if($work['category_id']==3)
                    <i class="fa fa-check"></i>
                    @else
                    <i></i>
                    @endif
                    </span>
                    <div class="column-text" >&nbsp;&nbsp;สุขาภิบาล / Sanitary</div>
                  </div>
                  <div class="row-checkbox">
                    <span class="checkbox checkbox-small" >
                    @if($work['category_id']==4)
                    <i class="fa fa-check"></i>
                    @else
                     <i></i>
                    @endif
                    </span>
                    <div class="column-text" style="">&nbsp;&nbsp;อื่นๆ / Other</div>
                  </div>

                </div>
              </div>
              <div class="row">
                <div class="col-xs-12">
                  ความจำเป็น
                  <div class="" style="display: inline-block; width:50px;">&nbsp;</div>
                  <div class="row-checkbox">
                    <span class="checkbox checkbox-small" >
                     @if($work['pioritized']==1)
                    <i class="fa fa-check"></i>
                    @else
                     <i></i>
                    @endif
                    </span>
                    <div class="column-text" style="">&nbsp;&nbsp;ด่วน</div>
                  </div>
                  <div class="row-checkbox">
                    <span class="checkbox checkbox-small" >
                    @if($work['pioritized']==2)
                    <i class="fa fa-check"></i>
                    @else
                    <i></i>
                    @endif
                    </span>
                    <div class="column-text" style="">&nbsp;&nbsp;ปกติ</div>
                  </div>
                  <div class="row-checkbox">
                    <span class="checkbox checkbox-small" >
                    @if($work['pioritized']==3)
                    <i class="fa fa-check"></i>
                    @else
                    <i></i>
                    @endif
                    </span>
                    <div class="column-text" >&nbsp;&nbsp;อื่นๆ <span class="column-line" style="width:420px;">@if(isset($work['pioritized_desc']))
                        {{ $work['pioritized_desc'] }}
                    @else
                    &nbsp;
                    @endif</span>
                    </div>
                  </div>
                </div>
              </div>
               <BR>
              <div class="row">
                <div class="col-xs-12">
                  ผู้รับแจ้ง / Request by
                  <span class="column-line" style="width:285px;">{{ isset($work['requestor_name']) ? $work['requestor_name'] : '&nbsp;' }}</span>
                   วันที่ / Date
                  <span class="column-line" style="width:100px;">{{ isset($work['requested_at']) ? date('d/m/Y',strtotime($work['requested_at'])) : '&nbsp;' }}</span>
                   เวลา / Time
                  <span class="column-line" style="width:100px;">{{ isset($work['requested_at']) ? date('H:i',strtotime($work['requested_at'])) : '&nbsp;' }}</span>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12">
                  ผลการปฆิบัติงาน / Result
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12">
                  <span style="display:inline-block;width: 50px;" ></span>
                  <div class="row-checkbox">
                    <span class="checkbox checkbox-medium" >
                      @if($work['result']==1)
                     <i class="fa fa-check" ></i>
                     @else
                     &nbsp;
                     @endif
                    </span>
                    <div class="column-text" style="">&nbsp;&nbsp;ได้รับการแก้ไขเป็นที่เรียบร้อยแล้ว</div>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-xs-12">
                  วิธีการแก้ไข / Action Taken
                  <div class="lined-form">
                      <div clsss="row-content-test" style=" position: absolute; display: block;display: -webkit-box;max-width: 100%;height: 64px; margin: 0 auto;font-size: 16;line-height: 16px;-webkit-line-clamp: 4;-webkit-box-orient: vertical;overflow: hidden;text-overflow: ellipsis;" >
                        <span>
                        {{  (isset($work['action_taken'])) ? $work['action_taken'] : '&nbsp;' }}
                        </span>
                      
                      </div>
                      <span class="lines">
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                      </span>
                  </div>
                  
                </div>
              </div>
              <div style="height:74px;"></div>
              
              <div class="row">
                <div class="col-xs-12">
                  <span style="display:inline-block;width: 50px;" ></span>
                  <div class="row-checkbox">
                    <span class="checkbox checkbox-medium" >
                      @if(isset($work['incomplete_because']))
                     <i class="fa fa-check" ></i>
                     @else
                     &nbsp;
                     @endif
                    </span>
                    <div class="column-text" style="">&nbsp;&nbsp;งานยังไม่ได้รับการแก้ไขเนื่องจาก / Incomplete Because</div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12">
                  <div class="lined-form">
                      <div clsss="row-content-test" style=" position: absolute; display: block;display: -webkit-box;max-width: 100%;height: 50px; margin: 0 auto;font-size: 16;line-height: 16px;-webkit-line-clamp: 3;-webkit-box-orient: vertical;overflow: hidden;text-overflow: ellipsis;" >
                        <span>
                         {{  (isset($work['incomplete_because'])) ? $work['incomplete_because'] : '&nbsp;' }}
                        </span>
                      
                      </div>
                      <span class="lines">
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                      </span>
                  </div>
                  
                </div>
              </div>
              <div style="height: 50px;"></div>
              <div class="row">
                <div class="col-xs-12">
                  ผู้ปฏิบัติ / Technician
                  <span class="column-line" style="width:285px;">{{ isset($work['technician_name']) ? $work['technician_name'] : '&nbsp;' }}</span>
                  วันที่ / Date
                  <span class="column-line" style="width:100px;">{{ isset($work['technician_at']) ? date('d/m/Y',strtotime($work['technician_at'])) : '&nbsp;' }}</span>
                  เวลา / Time
                  <span class="column-line" style="width:100px;">{{ isset($work['technician_at']) ? date('H:i',strtotime($work['technician_at'])) : '&nbsp;' }}</span>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12">
                  ผู้ตรวจรับงาน / Checked by
                  <span class="column-line" style="width:250px;">{{ isset($work['checked_name']) ? $work['checked_name'] : '&nbsp;' }}</span>
                  วันที่ / Date
                  <span class="column-line" style="width:100px;">{{ isset($work['checked_at']) ? date('d/m/Y',strtotime($work['checked_at'])) : '&nbsp;' }}</span>
                  เวลา / Time
                  <span class="column-line" style="width:100px;">{{ isset($work['checked_at']) ? date('H:i',strtotime($work['checked_at'])) : '&nbsp;' }}</span>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12">
                  ข้อเสนอแนะ / Recommendation
                  <div class="lined-form">
                      <div clsss="row-content-test" style=" position: absolute; display: block;display: -webkit-box;max-width: 100%;height: 64px; margin: 0 auto;font-size: 16;line-height: 16px;-webkit-line-clamp: 4;-webkit-box-orient: vertical;overflow: hidden;text-overflow: ellipsis;" >
                        <span>
                          {{  (isset($work['recommendation'])) ? $work['recommendation'] : '&nbsp;' }}
                        </span>
                      
                      </div>
                      <span class="lines">
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                      </span>
                  </div>
                  
                </div>
              </div>
              <div style="height:74px;"></div>
          </div>
          </div>
        </div>
         <BR>
        <div class="row">
          <div class="col-xs-12">
            <h4 class="text-center">รายการเบิก / ส่งคืนอุปกรณ์ที่ใช่ในการซ่อม LIST AND TURN ACCESSORIES FOR SERVICE</h4>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12">
            <table style="border: 1px solid #000;">
              <thead>
                 <tr style="border-bottom:1px solid #000;">
                   <th class="text-center" style="width: 70px;">ลำดับ <BR> No.</th>
                   <th class="text-center" style="width: 70px;">หน่วย <BR>Item</th>
                   <th class="text-center" style="width: 280px;">รายการ <BR>Description</th>
                   <th class="text-center" style="width: 100px;">ยอดเบิก <BR> Requisition</th>
                   <th class="text-center" style="width: 70px;">ยอดใช้ <BR> Used</th>
                   <th class="text-center" style="width: 70px;">คืนซาก <BR> Turn</th>
                   <th class="text-center" style="width: 100px;">หมายเหตุ <BR> Remark</th>
                 </tr>
              </thead>
              <tbody>
                @for($i=1;$i<=5;$i++)
                <tr>
                    <td class="text-center">{{ $i }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                 </tr>
                @endfor
              </tbody>

            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12">
            ผู้เบิก / Requisition by
            <span class="column-line" style="width:70px;">&nbsp;</span>
            วัน / เวลา Date / Time
            <span class="column-line" style="width:60px;">&nbsp;</span>
            ผู้จ่าย / Give out by
            <span class="column-line" style="width:70px;">&nbsp;</span>
            วัน / เวลา Date / Time
            <span class="column-line" style="width:60px;">&nbsp;</span>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12">
            ผู้คืน/ Turn by
            <span class="column-line" style="width:117px;">&nbsp;</span>
            วัน / เวลา Date / Time
            <span class="column-line" style="width:60px;">&nbsp;</span>
            ผู้รับคืน / Receive by
            <span class="column-line" style="width:65px;">&nbsp;</span>
            วัน / เวลา Date / Time
            <span class="column-line" style="width:60px;">&nbsp;</span>
          </div>
        </div>
        <BR>
        <div class="row">
          <div class="col-xs-12" >
            <div class="pull-right">
              ลายเซ็น / signature
            <span class="column-line" style="width:150px;">&nbsp;</span>
            หัวหน้าช่าง / เจ้าหน้าที่อาคาร Supervisor / Building .Attn.
          
            </div>
            
          </div>
        </div>
    </div>
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
</html>
