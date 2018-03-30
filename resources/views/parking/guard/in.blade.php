@extends('main.layouts.main')


@section('style')
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
  <link href="{{ url('plugins/iCheck/square/red.css') }}" rel="stylesheet">

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
  width: 70px; text-align: right; float: left;
}
.td-license-left-side{
  width: 70px;float:left; font-size: 36px; text-align: right; margin-right: 10px;
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
              <div class="col-sm-offset-3 col-sm-6">
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
                       
                        <button type="button" class="btn btn-primary btn-save-guard">
                          <i class="fa fa-spinner fa-spin fa-fw" style="display:none;float:left;"></i>
                          <i class="fa fa-save"></i>&nbsp;@lang('main.btn_save') </button>
                    </div>
                    
  


             

              
             </form>
              </div>
              
            </div>
            <!-- /.box-header -->
           
            <!-- /.box-body -->
          </div>
    </section>
    <!-- /.content -->

@endsection

@section('javascript')
<!-- DataTables -->

<script src="{{ url('plugins/iCheck/icheck.js') }} "></script>
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script src="{{ url('js/utility/autocomplete.js') }}"></script> 
<script src="{{ url('js/user/room.js') }}"></script> 
<script type="text/javascript">
$(function () {
	
 $("#province_id").select2();
  setTimeout(function() {
    $('#province_id').val(1).trigger('change');
  }, 500);
   $("#room_id").select2();

})


$(document).on("click touch","#search_room_list .my-autocomplete-li",function(e) { 
  var id = $.trim($(this).find('.search-id').val()) ;
  var text = $.trim($(this).find('.search-text').val()) ;
  
  var canAppend = true ;
   $("#user-in-room-table tbody tr").each(function(index, el) {
   
      if($(this).find('.room-id').val()==id){
        canAppend = false;
      }
   });

   if(!canAppend){
    $("#search_room").val('');
    $("#search_room_list").hide();
     return false;
   }
  var html = "<tr>"+
            "<td><button type=\"button\" "+
            " class=\"btn btn-danger btn-xs btn-user-in-room-del\" >"+
            "<i class=\"fa fa-close\"></i></button>"+
            "<input type=\"hidden\" class=\"room-id\" value=\""+(id)+"\">"+
            "<input type=\"hidden\" class=\"room-approve\" value=\""+(0)+"\">"+
            "</td>"+
            "<td></td><td>"+text+"</td>"+
           
            "</tr>";
  $("#user-in-room-table tbody").append(html);
  // $(this).parent().parent().parent().find("#search_room").val($(this).find('h5').text()) ;
  $(this).parent().remove();
  $("#user-in-room-table tbody tr td:nth-child(2)").each(function (i) {
      var j = ++i;
      $(this).text(j);
  });
  $("#search_room").val('');
  $("#search_room_list").hide();
});



$(".btn-save-guard").on("click",function(){
    
   getSubmitData().then(function(data){
   
     $('.fa-spinner').show();
    var route = "/guard/parking-in?api_token="+api_token ;
     ajaxFromData('POST',route,data).done(function(data){
       $('.fa-spinner').hide();
       $("#license_plate").val('');
       $("#license_plate_category").val('');
       $("#province_id").val($("#province_id option:first").val());
       $("#room_id").val($("#province_id option:first").val());
        swal({
              title: "@lang('main.create_success')" ,
              type: 'success',
              showCancelButton: false,
              confirmButtonText: "@lang('main.ok')"
            }).then((result) => {
              if (result.value) {
                
              }
            })
    });
     return false;
           
  }).fail();



   

})


function getSubmitData(){
  var dfd = $.Deferred();

  
  var form_data = new FormData($("#search_license_plate")[0]);
  form_data.append('no_room', ( $("#no_room").is(':checked') ? 1 : 0  )  );
  dfd.resolve(form_data);
  return dfd.promise();
}




</script>
@endsection		
