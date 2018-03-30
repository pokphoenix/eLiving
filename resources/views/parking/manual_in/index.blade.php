@extends('main.layouts.main')


@section('style')
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
  <link href="{{ url('plugins/iCheck/square/red.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
   <style type="text/css">
    tr:hover {cursor: pointer;}
    .custom-checkbox {
      -webkit-appearance: none;
  background-color: #fafafa;
  border: 1px solid #cacece;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05), inset 0px -15px 10px -12px rgba(0,0,0,0.05);
  padding: 9px;
  border-radius: 3px;
  display: inline-block;
  position: relative;
  width: 50px; height: 50px;
    }
    .custom-checkbox:checked {
  background-color: #00c0ef;
  border: 2px solid #00a7d0;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05), inset 0px -15px 10px -12px rgba(0,0,0,0.05), inset 15px 10px -12px rgba(255,255,255,0.1);
  color: #99a1a7;
}
  .custom-checkbox:checked:after {
  content: '\2714';
  font-size: 36px;
  position: absolute;
  top: 0px;
  left: 50%;
  margin-left: -12px;
  color: #FFF;
  font-size: 30px;
  text-align: center;
}
.td-left-side{
  width: 100px; text-align: right; float: left;
}
.td-license-left-side{
  width: 100px;float:left; font-size: 36px; text-align: right; margin-right: 10px;
}

  </style>

@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       
        <i class="fa fa-user"></i>
       
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
            <div class="box-header">
              <h3 class="box-title"></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="col-sm-6">
                
                <form id="search_license_plate" >
                <div class="form-group">
                    <label for="name">@lang('parking.license_plate')</label>
                    <div>
                       <input type="text" class="form-control" style="width:60px;float: left;margin-right: 10px;" id="license_plate_category" name="license_plate_category" maxlength="3" placeholder="@lang('parking.license_plate_category')" value="" >
                       <input type="text" class="form-control" style="width:100px;float: left;margin-right: 10px;" id="license_plate" name="license_plate" placeholder="@lang('parking.license_plate')" maxlength="4"  value="" >
                        <select class="select2 form-control"  id="province_id" name="province_id" style="width:220px;" >
                        
                        @if (isset($province))
                          @foreach($province as $key=> $p)
                          <option value="{{ $p['id'] }}"  > {{ $p['text'] }}</option>
                          @endforeach
                        @endif
                        </select>
                    </div>
                   
                </div> 
                <div class="form-group">
                    <label for="name">@lang('parking.manual_time_in')</label>
                    <div class="row">
                       <div class="col-xs-2"> 
                        <input type="text" class="form-control" id="send_date_day"  placeholder="@lang('parcel.send_date_day')" value="{{ date('d') }}" >
                       </div>
                      <div class="col-xs-2">  
                        <input type="text" class="form-control" id="send_date_month"  placeholder="@lang('parcel.send_date_month')" value="{{ date('m') }}" >
                      </div>
                      <div class="col-xs-2">
                        <input type="text" class="form-control" id="send_date_year"  placeholder="@lang('parcel.send_date_year')" value="{{ date('Y') }}" >
                      </div>
                      <div class="col-xs-2">
                        <input type="text" class="form-control" id="send_date_hour"  placeholder="@lang('parcel.send_date_hour')" value="{{ date('H') }}" >
                      </div>
                      <div class="col-xs-2">
                        <input type="text" class="form-control" id="send_date_minute"  placeholder="@lang('parcel.send_date_minute')" value="{{ date('i') }}" >
                      </div>
                    </div>
                </div>
                <div class="form-group">
                  <label for="">@lang('parking.room')</label>
                   <select id="room_id" name="room_id" class="select2 form-control">
                      @if(isset($room))
                        @foreach($room as $r)
                           <option value="{{$r['id']}}">{{$r['text']}}</option>
                        @endforeach
                      @endif
                    </select>
                </div>

                       
                <div class="form-check">
                    <label class="form-check-label">
                    <input type="checkbox" id="no_room" name="no_room" class="form-check-input" @if(old('agree'))  checked="" @endif >
                       @lang('room.no_room')
                    </label>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary btn-sm btn-add-car-not-in-system" >@lang('main.btn_save')</button>
                </div>

              </div>
              
                 
    

               
               
             </form>


            
            </div>
            <!-- /.box-body -->
      </div>

      <div class="box">
            <div class="box-header">
              <h3 class="box-title"></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              
              <table id="checkin" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                  <th>@lang('parking.license_plate')</th>
                  <th>@lang('parking.start_date')</th>
                  <th>@lang('main.tool')</th>
                </tr>
                <tr class="thead-search">
                  <th></th> 
                  <th class="input-filter">@lang('parking.license_plate')</th>
                  <th class="input-filter">@lang('parking.start_date')</th>
                  <th ></th>
                </tr>
                </thead>
                <tbody>
        
                @foreach ($lists as $key=>$checkin)
                <tr>
                  <td>{{ $key+1 }}</td>
                  <td>{!! $checkin['license_plate_category']." ".$checkin['license_plate']."<BR>".$checkin['province_name'] !!}</td>
                  <td>{{ created_date_format($checkin['created_at']) }}</td>
                 
                  <td> 
                     <!--  <button class="btn btn-success btn-setuse btn-xs" data-id="{{ $checkin['id'] }}" data-license-plate="{{$checkin['license_plate']}}"
                      data-license-plate-category="{{$checkin['license_plate_category']}}"
                      data-province-id="{{$checkin['province_id']}}"
                      data-start-date="{{ $checkin['created_at'] }}"
                       ><i class="fa fa-clock-o"></i></button>  -->
                  </td>
                 
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
                <h3 class="debt-title"></h3>
                <h5 class="debt-text"></h5>
              </div>
              <div class="modal-body">
                 <select id="debt_type" name="debt_type" class="form-control">
                     <option value="0">@lang('parking.please_select_debt_type')</option>
                  @if(isset($debtType))
                    @foreach($debtType as $d)
                       <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
                    @endforeach
                  @endif
                </select> 
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
                <button type="button" class="btn btn-primary btn-save"> @lang('parking.accept_pay_debt')
                   <i class="fa fa-spinner fa-spin fa-fw" style="display:none;" ></i>
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

<script src="{{ url('plugins/iCheck/icheck.js') }} "></script>
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src=" {{ url('js/utility/data_table.js') }}"></script>
<script type="text/javascript">
$(function () {
	$("#province_id").select2();
  var table = $('#checkin').DataTable(
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


$(".btn-add-car-not-in-system").on("click",function(){
    var date = $("#send_date_year").val()+"-"+$("#send_date_month").val()+"-"+$("#send_date_day").val()+" "+$("#send_date_hour").val()+":"+$("#send_date_minute").val() ;
    var noRoom = ( $("#no_room").is(':checked') ? 1 : 0  )  ;
  
    var data = { license_plate : $("#license_plate").val()
                ,license_plate_category : $("#license_plate_category").val()
                ,province_id : $("#province_id").val()
                ,date : date
                ,no_room : noRoom
                ,room_id : $("#room_id").val()
              };
  

    var route = "/parking/manual-in?api_token="+api_token ;
    ajaxPromise('POST',route,data).done(function(data){
        $("#license_plate").val('');
        $("#license_plate_category").val('');
        $("#province_id").val(1).trigger('change');

       swal({
        title:(($("#app_local").val()=='th') ? 'บันทึกสำเร็จ' : 'Create Success' ) ,
        type: 'success',
        showCancelButton: false,
        confirmButtonText: "@lang('main.ok')"
      }).then((result) => {
        if (result.value) {
            location.reload();
        }
      })
    });
   
})
</script>
@endsection		
