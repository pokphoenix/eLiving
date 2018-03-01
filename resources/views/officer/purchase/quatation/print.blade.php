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
  <link rel="stylesheet" href="{{ url('public/css/quotation-print.css')}}" media="screen,print">
  <script src="{{url('bower_components/jquery/dist/jquery.min.js')}}"></script>
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
    $(".class-price").each(function(index, el) {
        var price = parseFloat($(this).text()).toFixed(2);
        if(price==0){
          price = "-";
        }else{
          price = ReplaceNumberWithCommas(price);
        }
        

        $(this).text(price);
    });
  });

  
  </script>
</head>
<body  @if(!isset($preview)) onload="window.print();"  @endif >
<!-- <body > -->
<div class="wrapper">
  <!-- Main content -->
  <section class="invoice" style="padding: 0">
    <!-- title row -->
    <div class="row">
        <div class="col-xs-3"><img src="{{ $setting['logo_left'] }}" height="50" ></div>
       <div class="col-xs-6">
         <h3 class="text-center">{{ $setting['header']." ".$data['quotation']['title'] }}</h3>
         <div class="row " style="">
            <label class="col-xs-2 text-right"> @lang('quotation.subject')</label>
            <span>{{ $setting['subject']." ".$data['quotation']['title']}}</span>
         </div>
         <div class="row ">
            <label class="col-xs-2 text-right">@lang('quotation.inform')</label>
            <span>{{ $setting['inform']}}</span>
         </div>
       </div>
       <div class="col-xs-3 text-right"><img src="{{ $setting['logo_right'] }}" height="50" ></div>
    </div>
  

    <div class="row">
        <div class="col-xs-12">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <!-- <th rowspan="2" class="vm-ct" width="50">@lang('quotation.no')</th> -->
                  <th rowspan="2" class="vm-ct">@lang('quotation.description_list')</th>
                  <!-- <th rowspan="2" class="vm-ct">@lang('quotation.amount')</th> -->
               @if(!isset($preview)&&count($data['quotation_companys'])>0)
                  @foreach($data['quotation_companys'] as $key=>$company)
                  <th colspan="2" class="vm-ct" >
                      <span class="company-name">{{ ($key+1).".".$company['name'] }}</span>
                  </th>
                  @endforeach
                </tr>
                @if(isset($data['quotation_companys'])&&count($data['quotation_companys'])>0)
                <tr>
                   @foreach($data['quotation_companys'] as $key=>$company)
                  <th class="text-center">@lang('quotation.price_per_unit')</th>
                  <th class="text-center">@lang('quotation.amount_per_bath')</th>
                  @endforeach
                </tr>
                @endif
                @endif
              </thead>
              <tbody>
              @if(isset($data['quotation_items']))
              @foreach($data['quotation_items'] as $j=> $item)
              <tr>
                <!-- <td>{{ $j+1 }}</td> -->
                <td><span>{{ ( $j+1 ).".".$item['name'] }}</span></td>
                <!-- <td class="text-right"><span>{{ $item['amount'] }}</span></td> -->

                  @foreach($data['quotation_companys'] as $key=>$company)
         
                    <?php $hasCompanyItem = false; ?>
                    @foreach($data['quotation_company_items'] as $key=>$companyItem)
                      @if($companyItem['company_id']==$company['company_id'] &&
            $companyItem['quotation_item_id']==$item['id'])    
                          <td class="text-right class-price">{{ $companyItem['price_per_unit'] }}</td>
                          <td class="text-right class-price"><span class="item-price-total">{{  $companyItem['price'] }}</span></td>

                          <?php $hasCompanyItem = true; ?>
                      @endif
                    @endforeach

                    @if(!$hasCompanyItem)
                      <td class="text-right" >-</td>
                      <td class="text-right"><span class="item-price-total">-</span></td>
                    @endif
                  @endforeach
              </tr>
               @endforeach
              @endif
              @for($i=0;$i < (7-count($data['quotation_items'])) ; $i++)
              <tr>
                  @for($j=0;$j <  ( (count($data['quotation_companys'])*2)+$col ) ; $j++  )
                  <td>&nbsp</td>
                   @endfor
              </tr>
              @endfor


              <tr>
                <td colspan="{{$col}}">@lang('quotation.price_before_vat')</td>
                @foreach($data['quotation_companys'] as $key=>$company)
                <td></td>
                <td class="class-price text-right">{{$company['price_b4_vat']}}</td>
                @endforeach
              </tr>
              <tr>
                <td colspan="{{$col}}">@lang('quotation.discount')</td>
                @foreach($data['quotation_companys'] as $key=>$company)
                <td></td>
                <td class="class-price text-right">{{$company['discount']}}</td>
                @endforeach
              </tr>
              <tr>
                <td colspan="{{$col}}">@lang('quotation.vat') 7%</td>
                @foreach($data['quotation_companys'] as $key=>$company)
                <td></td>
                <td class="class-price text-right">{{$company['vat']}}</td>
                @endforeach
              </tr>
              
              <tr>
                <td colspan="{{$col}}">@lang('quotation.net_price')</td>
                @foreach($data['quotation_companys'] as $key=>$company)
                <td></td>
                <td class="class-price text-right">{{$company['price_net']}}</td>
                @endforeach
              </tr>
              <tr>
                <td colspan="{{$col}}">@lang('quotation.term_of_payment')</td>
                @foreach($data['quotation_companys'] as $key=>$company)
                <td colspan="2" class="text-center">{{ (isset($company['payment_term']) ? $company['payment_term'] : '-' ) }}</td>
                @endforeach
              </tr>
              <tr>
                <td colspan="{{$col}}">@lang('quotation.warranty')</td>
                @foreach($data['quotation_companys'] as $key=>$company)
                <td colspan="2" class="text-center">{{ (isset($company['guarantee']) ? $company['guarantee'] : '-' ) }}</td>
                @endforeach
              </tr>
              </tbody>
            </table>
  

        {{ $setting['remark']." ".$data['quotation']['title']." ".$data['quotation']['description'] }} 
       </div>

    </div>

    <div class="row">
      <div class="col-xs-4"></div>
      <div class="col-xs-4 text-center" >
          <div class="col-xs-offset-3 col-xs-6 text-center" style="border-top:dotted ;margin-top: 50px;">
            {{ $setting['sign_1']}}
          </div>
      </div>
      <div class="col-xs-4 text-center" >
          <div class="col-xs-offset-3 col-xs-6 text-center" style="border-top:dotted ;margin-top: 50px;">
            {{ $setting['sign_1']}}
          </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
</html>
