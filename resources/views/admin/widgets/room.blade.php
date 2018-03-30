<div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">@lang('user.room_list')</h3>
                <div class="box-tools pull-right">
                     
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                  </div>
              </div>
              <!-- /.box-header -->
              <!-- form start -->
            
              

                
                 <div class="box-body">
          
              
        
                <div class="col-sm-12">
                    <div class="input-group">
                       <input type="text" class="form-control" id="search_room" data-action="{{ url('/api/'.$domainId.'/search/room?api_token=').auth()->user()->api_token }}" autocomplete="off">
                      <div class="input-group-btn">
                          <button type="button" class="btn btn-primary btn-flat"><i class="fa fa-search"></i>
                          </button>
                      </div>
                    </div>
                    <div class="search-append" id="search_room_list" style="max-height:200px; overflow: auto; display: none;"></div>
                </div>
               
                <div class="col-sm-12">
                  <BR>
                    <table id="user-in-room-table" class="table table-bordered">
                      <thead>
                        <tr>
                          <th width="50"></th>
                          <th class="vm-ct" width="50">@lang('user.no')</th>
                          <th class="vm-ct">@lang('user.room_number')</th>
                          <th class="vm-ct">@lang('user.approve_status')</th>
                        </tr>
                      </thead>

                      <tbody>
                        @if(isset($roomUser))
                          @foreach($roomUser as $key=>$ru)
                          <tr>
                            <td>
                              @if(!isset($requestRoom))
                              <button type="button" class="btn btn-danger btn-xs btn-user-in-room-del" ><i class="fa fa-close"></i></button>
                              @endif
                            
                            <input type="hidden" class="room-id" value="{{ $ru['room_id'] }}">
                            <input type="hidden" class="room-approve" value="{{ $ru['approve'] }}">
                            </td>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $ru['text_name'] }}</td>
                            <td style="font-weight: bold;">
                              <span class="room-status  @if($ru['approve']) active @endif"> 
                                @if($ru['approve']) 
                                  @lang('user.approved')
                                @else
                                  @lang('user.wait_for_approve')
                                @endif
                              </span>
                             
                              
                              @if(auth()->user()->hasRole('admin'))
                                <button type="button" class="btn btn-default btn-xs btn-user-in-room-approve" title="@lang('user.set_to') @if($ru['approve'])  @lang('user.wait_for_approve') @else @lang('user.approved') @endif
                                " >
                                  <i class="fa fa-{{ ($ru['approve']) ? 'close' : 'check'  }}"></i></button>
                              @endif

                            </td>
                          </tr>
                          @endforeach
                        @endif
                      </tbody>
                      
                    </table>
                </div>
              </div>
                <!-- /.box-body -->

                <div class="box-footer">
                 <!--  <button type="button" class="btn btn-default" id="btn-upload-file">นำส่ง</button> -->
                </div>
              
            </div>
<script src="{{url('bower_components/jquery/dist/jquery.min.js')}}"></script>
<script type="text/javascript">
$(document).on("click","#search_room_list .my-autocomplete-li",function(e) { 
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
            "<td></td><td>"+text+"</td><td style=\"font-weight: bold;\"><span class=\"room-status\"> "+(($("#app_local").val()=='th') ? 'รออนุมัติ' : 'Wait for Approve' )+"</span>"+
            @if(Auth()->user()->hasRole('admin')) 
            "<button type=\"button\" class=\"btn btn-default btn-xs btn-user-in-room-approve\" title=\""+(($("#app_local").val()=='th') ? 'ตั้งค่าเป็น อนุมัติ' : 'Set to Approved' )+"\">"+
            "<i class=\"fa fa-check\"></i></button>"+
            @endif
            "</td></tr>";
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
</script>