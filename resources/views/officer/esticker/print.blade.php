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
  <link rel="stylesheet" href="{{ url('public/css/sticker-print.css')}}" media="screen,print">
  <script src="{{url('bower_components/jquery/dist/jquery.min.js')}}"></script>
  <!-- <script src=" {{ url('plugins/html2canvas/html2canvas.min.js') }}"></script> -->
  <script src=" {{ url('js/utility/print.js') }}"></script>

  <script type="text/javascript">
    
   $(function() {

      
    // capture();

       printContent('print_this');


   });

 function capture() {

    // html2canvas(document.querySelector("#print_this")).then(canvas => {
    //     document.body.appendChild(canvas);
    //     console.log(canvas.toDataURL("image/png"));
    //     $('#img_val').attr('src',canvas.toDataURL("image/png"))
    //     // printContent('#img_val');


       

    // });


   
}


  </script>
  <style type="text/css">
  .text-shadow {
   text-shadow: rgb(255, 255, 255) 3px 0px 0px, rgb(255, 255, 255) 2.83487px 0.981584px 0px, rgb(255, 255, 255) 2.35766px 1.85511px 0px, rgb(255, 255, 255) 1.62091px 2.52441px 0px, rgb(255, 255, 255) 0.705713px 2.91581px 0px, rgb(255, 255, 255) -0.287171px 2.98622px 0px, rgb(255, 255, 255) -1.24844px 2.72789px 0px, rgb(255, 255, 255) -2.07227px 2.16926px 0px, rgb(255, 255, 255) -2.66798px 1.37182px 0px, rgb(255, 255, 255) -2.96998px 0.42336px 0px, rgb(255, 255, 255) -2.94502px -0.571704px 0px, rgb(255, 255, 255) -2.59586px -1.50383px 0px, rgb(255, 255, 255) -1.96093px -2.27041px 0px, rgb(255, 255, 255) -1.11013px -2.78704px 0px, rgb(255, 255, 255) -0.137119px -2.99686px 0px, rgb(255, 255, 255) 0.850987px -2.87677px 0px, rgb(255, 255, 255) 1.74541px -2.43999px 0px, rgb(255, 255, 255) 2.44769px -1.73459px 0px, rgb(255, 255, 255) 2.88051px -0.838247px 0px;
}
    

   

    
  </style>
   
</head>
<body>
<!-- <body > -->
<div class="wrapper">
  <img src="" id="img_val" >
  <div id="divhidden" ></div>
    <button onclick="printContent('print_this')" class="btn btn-success" style="margin-left: 10px;" >Print</button>
  <!-- Main content -->
  <section  id="print_this" style="padding: 0;">
    <!-- title row -->
         <div >
           <img src="{{ url('public/img/bg_watermark.jpg') }}" width="430" height="430">
         </div>
        <div class="main-sticker" style="margin-top: -430px;">
          <div class="row">
            <div class="col-sm-12">
               <div class="pull-right text-shadow" >No {{  str_pad($esticker['no'],5,'0',STR_PAD_LEFT)  }}</div>  
            </div>
            
          </div>
         
          <div class="row">
            <div class="col-sm-12" >
              <span class="domain-name text-shadow">{{ $esticker['name_sticker'] }}</span>
      
              <input type="text" class="form-control pull-right created-by" placeholder="ออกโดย" value="{{ 'ออกโดย ' }}" >
            </div>
            
          </div>

          <div class="row">
            <div class="col-sm-12" >
               <span class="license-plate text-shadow">
                 @foreach ($esticker['license_plate_list'] as $key=>$et)
                    @if($key>0)
                    ,
                    @endif
                    {{ $et['license_plate_category']." ".$et['license_plate'] }}
                 @endforeach
               </span>
               <span class="pull-right created-at">  {{ date('d',strtotime($esticker['created_at']))." ".month_date_short(date('m',strtotime($esticker['created_at'])))." ".date('Y',strtotime($esticker['created_at']))  }}</span>
            </div>
           
          </div>
          <div class="row">
            <div class="col-sm-12 qrcode-row" style="display:flex;justify-content:center;align-items:center; ">
              <img src="{{ $esticker['qrcode'] }}" 
              width="330" height="330" style="float:left;">
  
              <span class="text-shadow" style="writing-mode: vertical-rl;text-orientation: upright;font-size: 56px;" >
               {{ $esticker['year'] }}
              </span>

            </div>
            
          </div>
          
        </div>

        
  
  

    
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
</html>
