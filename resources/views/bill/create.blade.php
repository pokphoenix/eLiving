@extends('main.layouts.main')

@section('style')

 <!-- DataTables -->
<link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

@endsection

@section('content-wrapper')
  
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       
        <i class="fa fa-upload"></i>
       
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
  <div class="box">
    <div class="box-body">

  @if($message = Session::get('success'))
    <div class="alert alert-info alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
        <strong>Success!</strong> {{ $message }}
      </div>
  @endif
  {!! Session::forget('success') !!}
  <br />
<!--   <a href="{{ URL::to('api/downloadExcel/xls') }}"><button class="btn btn-success">Download Excel xls</button></a>
  <a href="{{ URL::to('api/downloadExcel/xlsx') }}"><button class="btn btn-success">Download Excel xlsx</button></a>
  <a href="{{ URL::to('api/downloadExcel/csv') }}"><button class="btn btn-success">Download CSV</button></a> -->
  <!-- <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ URL::to('api/importExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data"> -->
    {{ csrf_field() }}
    <form id='input_form'>
    <table>
      <tr>
        <td>
           <input type="checkbox" name="sent_noti" value="1" id ="sent_noti" class="form-check-input" checked> Sent Notification<br>
        </td>
        <td width="80%">
           <input type="file"  class="form-control-file" name="import_file" id = "import_file"  />
        </td>
        <td>
           <button class="btn btn-primary btn-import" type="button">Import File    
              <i class="fa fa-spinner fa-spin fa-fw" style="display:none;" ></i>
           </button> <br>
        </td>
        
      </tr>
    </table>
    </form>
  <!-- </form> -->
            </div>
            <!-- /.box-body -->
          </div>

            <div class="box">
              @lang('bill.last_import_data')
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('bill.no')</th>
                  <th>@lang('bill.bill_no')</th>
                  <th>@lang('bill.room')</th>
                  <th>@lang('bill.name')</th>
                  <th>@lang('bill.total')</th>
                  <th>@lang('bill.bf')</th>
                  <th>@lang('bill.net')</th>
                  <th>@lang('bill.desc_')</th>
                  <th>@lang('bill.rate')</th>
                  <th>@lang('bill.qty')</th>
                  <th>@lang('bill.amount')</th>
                  <th>@lang('bill.bill_date')</th>
                </tr>
                <tr class="thead-search">
                  <th ></th>
                  <th class="input-filter">@lang('bill.bill_no')</th>
                  <th class="input-filter">@lang('bill.room')</th>
                  <th class="input-filter">@lang('bill.name')</th>
                  <th class="input-filter">@lang('bill.total')</th>
                  <th class="input-filter">@lang('bill.bf')</th>
                  <th class="input-filter">@lang('bill.net')</th>
                  <th class="input-filter">@lang('bill.desc_')</th>
                  <th class="input-filter">@lang('bill.rate')</th>
                  <th class="input-filter">@lang('bill.qty')</th>
                  <th class="input-filter">@lang('bill.amount')</th>
                   <th class="input-filter">@lang('bill.bill_date')</th>
                </tr>
                </thead>
                <tbody>
        
                @foreach ($lists as $key=>$list)
                <tr >
                  <td>{{ $key+1 }}</td>
                  <td>{{ $list['bill_no'] }}</td>
                  <td>{{ $list['room']}}</td>
                  <td>{{ $list['name']}} </td>
                  <td>{{ $list['total']}} </td>
                  <td>{{ $list['bf']}} </td>
                  <td>{{ $list['net']}} </td>
                  <td>{{ $list['desc_']}} </td>
                  <td>{{ $list['rate']}} </td>
                  <td>{{ $list['qty']}} </td>
                  <td>{{ $list['amount']}} </td>
                  <td>{{ date('Y-m-d',strtotime($list['date'])) }}</td>

                </tr>
                @endforeach
               
              </table>
            </div>
            <!-- /.box-body -->
          </div>

    </section>
    <!-- /.content -->
  <div class="modal fade" id="modal-default">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('bill.insert')</h4>
              </div>
              <div class="modal-body">
                 <form  id="parcel-form" role="form" method="POST" action="{{$action}}" enctype="multipart/form-data"  >
                  <div class="form-group">
                    <label for="room_id">@lang('user.room')</label>
                    <select class="select2 form-control" id="room_id" name="room_id" >
                        <option value=""></option>
                        @if (isset($room))
                          @foreach($room as $r)
                          <option value="{{ $r['id'] }}"  > {{ $r['text' ]}} </option>
                          @endforeach
                        @endif
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="name">@lang('bill.send_date')</label>
                    
                    <div class="row">
                       <div class="col-xs-2"> 
                        <input type="text" class="form-control" id="send_date_day"  placeholder="@lang('bill.send_date_day')" value="{{ date('d') }}" >
                       </div>
                      <div class="col-xs-2">  
                        <input type="text" class="form-control" id="send_date_month"  placeholder="@lang('bill.send_date_month')" value="{{ date('m') }}" >
                      </div>
                      <div class="col-xs-2">
                        <input type="text" class="form-control" id="send_date_year"  placeholder="@lang('bill.send_date_year')" value="{{ date('Y') }}" >
                      </div>
                      <div class="col-xs-2">
                        <input type="text" class="form-control" id="send_date_hour"  placeholder="@lang('bill.send_date_hour')" value="{{ date('H') }}" >
                      </div>
                      <div class="col-xs-2">
                        <input type="text" class="form-control" id="send_date_minute"  placeholder="@lang('bill.send_date_minute')" value="{{ date('i') }}" >
                      </div>
                    </div>

                   
                    
                   
                     
                     
                     


                  </div>
                  
                  <div class="form-group">
                    <label for="package_id">@lang('bill.type')</label>
                    <select class="select2 form-control" id="type" name="type" >
                        <option value=""></option>
                        @if (isset($parcelTypes))
                          @foreach($parcelTypes as $p)
                          <option value="{{ $p['id'] }}"  > {{ $p['name'] }} </option>
                          @endforeach
                        @endif
                    </select>
                  </div>

                  <div id="row_supplies" class="parcel-row none">
                    <div class="form-group">
                      <label for="name">@lang('bill.supplies_send_name')</label>
                      <input type="text" class="form-control" id="supplies_send_name" name="supplies_send_name" placeholder="@lang('bill.supplies_send_name')"  >
                    </div>
                    <div class="form-group">
                      <label for="name">@lang('bill.supplies_type')</label>
                      <select class="select2 form-control" id="supplies_type" name="supplies_type" >
                          <option value=""></option>
                        @if (isset($suppliesTypes))
                          @foreach($suppliesTypes as $st)
                          <option value="{{ $st['id'] }}"  > {{ $st['name'] }} </option>
                          @endforeach
                        @endif
                      </select>
                      
                    </div> 
                    <div class="form-group">
                      <label for="name">@lang('bill.supplies_code')</label>
                      <input type="text" class="form-control" id="supplies_code" name="supplies_code" placeholder="@lang('bill.supplies_code')"  >
                    </div>
                    
                  </div>
                  <div id="row_gift" class="parcel-row none">
                    <div class="form-group">
                      <label for="name">@lang('bill.gift_receive_name')</label>
                      <input type="text" class="form-control" id="gift_receive_name" name="gift_receive_name" placeholder="@lang('bill.gift_receive_name')"  >
                    </div>
                    <div class="form-group">
                      <label for="name">@lang('bill.gift_send_name')</label>
                      <input type="text" class="form-control" id="gift_send_name" name="gift_send_name" placeholder="@lang('bill.gift_send_name')"  >
                    </div>
                    <div class="form-group">
                      <label for="name">@lang('bill.gift_description')</label>
                      <input type="text" class="form-control" id="gift_description" name="gift_description" placeholder="@lang('bill.gift_description')"  >
                    </div>
                    
                  </div>
                   <div class="form-group row-position none">
                      <label for="name">@lang('bill.position')</label>
                      <input type="text" class="form-control" id="position" name="position" placeholder="@lang('bill.position')"  >
                    </div>
                  
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
                <button type="button" class="btn btn-primary btn-save">@lang('bill.btn_save_and_close')
                   <i class="fa fa-spinner fa-spin fa-fw" style="display:none;" ></i>
                </button>
                <button type="button" class="btn btn-info btn-save-continue none">@lang('bill.btn_save_and_continue')
                   <i class="fa fa-spinner fa-spin fa-fw" style="display:none;"></i>
                </button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>


@endsection

@section('javascript')
<!-- DataTables -->
<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script src=" {{ url('js/utility/data_table.js') }}"></script>
<script type="text/javascript">
$(function () {

   var table = $('#example1').DataTable(
      {
        "bSortCellsTop": true
       
      }
      );
    
    $.each($('.input-filter', table.table().header()), function () {
        var column = table.column($(this).index());
        $( 'input', this).on( 'keyup change', function () {
            if ( column.search() !== this.value ) {
                column
                    .search( this.value )
                    .draw();
            }
        } );
    } );   
})

$(".btn-import").on("click",function(){
   eleSpin = ".btn-import" ;
  $(eleSpin).find('.fa-spinner').show();
  var sent_noti = $("#sent_noti").is(":checked")?1:0;
  var url = "{{$action}}"; 
  var data = { 'sent_noti': sent_noti};
  var import_file_data =  $("#import_file").file;
  var method = 'post';
  var title = '';
  var form_data = new FormData($("#input_form")[0]);
 /* form_data.append('sent_noti',JSON.stringify(sent_noti));
   form_data.append('import_file',JSON.stringify(import_file_data));*/

   $.ajax({
    url: url,
    type: method,
    dataType: 'json',
    data:form_data,
    processData: false,
    contentType: false,
  })
  .done(function(res) {
     $(eleSpin).find('.fa-spinner').hide();
    if(res.result=="true"){
      swal({
            title:title+res.response.result,
            type: 'success',
            showCancelButton: false,
            confirmButtonText: "@lang('main.ok')"
          }).then((result) => {
            location.reload();
          })
    }else{
      // dfd.reject( res.errors );
      var error = JSON.stringify(res.errors);
       swal(
        'Error...',
        error,
        'error'
      )
      
    }
  })
  .fail(function() {
    $(eleSpin).find('.fa-spinner').hide();
    swal(
        'Error...',
        'Some thing error',
        'error'
    );
  })
})
</script>

@endsection   
