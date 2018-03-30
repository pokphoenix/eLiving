@extends('main.layouts.main')

@section('style')
  <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
@endsection
@section('content-wrapper')

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{ $channel['name'] }}
        @if($actionStatus['is_join'])
           <a href="{{ url($route.'/'.$channelId) }}"> <button class="btn btn-danger btn-flat" ><i class="fa fa-reply"></i></button> </a>
          <small></small>      
        @endif  
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url($home) }}"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active"><a href="{{ url($route.'/'.$channelId) }}">{{ $channel['name'] }}</a></li>
        
      </ol>

      <div class="box box-solid">
        
        <!-- /.box-header -->
        <div class="box-body">
          <p>{{ $channel['description']}}</p>   
        </div>
        <!-- /.box-body -->
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
               @include('layouts.error')
            </div>
        </div>
        
        <input type="hidden" id="channel_id" value="{{ $channelId }}" >
      


        <div class="row">
            <div class="col-sm-12">
                @if(!$actionStatus['is_can_request'])
               
                 <button type="button" class="btn btn-flat btn-sm bg-yellow " id="btn-join-channel">
                  <i class="fa fa-plus"></i> 
                  @if($channel['type']!=1)
                    @lang('chat.request_join_room')
                  @else
                     @lang('chat.join_room')
                  @endif
                </button>
                <div style="height:10px;"></div>
                @elseif(!$actionStatus['is_join'])
                <button class="btn btn-danger btn-flat"  onclick="leaveChannel()"><i class="fa fa-sign-out"></i> @lang('chat.cancel_request')</button>
                 <div style="height:10px;"></div>
                @endif  
                


               

                <div class="row">
                    <div class="{{ ($actionStatus['is_can_owner']) ? 'col-sm-8' : 'col-sm-12' }}">
                        @if( count($members) >0) 
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box box-success">
                                <div class="box-header with-border">
                                  <h3 class="box-title">@lang('chat.member')</h3>

                                  <div class="box-tools pull-right">

                                    <span class="label label-default">{{ count($members) }} @lang('chat.member')</span>
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    
                                  </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body no-padding">
                                  <ul class="users-list clearfix">
                                   
                                      @foreach($members  as $member)
                                        <li>
                                          <img src="{{ $member['img'] }}" alt="User Image" width="100">
                                          <a class="users-list-name" href="#">{{ $member['first_name']." ".$member['last_name'] }}
                                          {{ ($member['owner']) ? "(owner)" : ""  }}
                                          </a>
                                          <span class="users-list-date">{{ diffByNow($member['created_at']) }}</span>
                                          @if(!$member['owner'])
                                            @if($actionStatus['is_can_owner'])
                                            <input type="hidden" class="request-user-id" value="{{ $member['user_id'] }}" >
                                            <button class="btn-set-owner btn btn-primary btn-xs btn-flat" title="@lang('chat.set_to_owner_desc')" >
                                            @lang('chat.set_to_owner')
                                             </button>
                                            @endif
                                            @if($actionStatus['is_can_kick'])
                                            <input type="hidden" class="request-user-id" value="{{ $member['user_id'] }}" >
                                            <button class="btn-kick-member btn btn-danger btn-xs btn-flat" title="@lang('chat.leave_room_kick')" >@lang('chat.leave_room')</button>
                                            @endif
                                          @endif

                                        </li>
                                      @endforeach

                                    
                                    
                                  </ul>
                                  <!-- /.users-list -->
                                </div>
                                <!-- /.box-body -->
                               
                                <!-- /.box-footer -->
                              </div>
                            </div>  
                        </div>
                        @endif
                        

                    </div>
  
                    @if($actionStatus['is_can_owner'])

                    <div class="col-sm-4">
                      
                     
                      
                      
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box box-danger">
                                <div class="box-header with-border">
                                  <h3 class="box-title">@lang('chat.pending_user')</h3>

                                  <div class="box-tools pull-right">
                                    
                                    
                                    

                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                   
                                    
                                  </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body no-padding">
                                  @if(count($memberRequests) > 0)
                                  <ul class="users-list clearfix">
                                    
                                      @foreach($memberRequests  as $request)
                                        <li>
                                          <img src="{{ $request['img'] }}" alt="User Image">
                                          <a class="users-list-name" href="#">{{ $request['first_name']." ".$request['last_name'] }}</a>
                                          <span class="users-list-date">{{ diffByNow($request['created_at']) }}</span>
                                          
                                          @if($actionStatus['is_can_accept'])
                                          <input type="hidden" class="request-user-id" value="{{ $request['user_id'] }}" >
                                          <button class="btn-accept-request-join btn btn-primary btn-xs btn-flat" >ยืนยัน</button>
                                          @endif
  
                                        </li>
                                      @endforeach
                                  </ul>
                                  @else
                                      <div class="col-sm-12 text-center" style="height:50px; line-height:50px;">
                                        
                                        @lang('chat.no_request')
                                      </div>
                                        
                                  @endif
          
                                    
                                    
                                  
                                  <!-- /.users-list -->
                                </div>
                                <!-- /.box-body -->
                               
                                <!-- /.box-footer -->
                              </div>
                            </div>  
                        </div>
                       
                      

                    </div>
                    @endif
                </div>

            </div>
        </div>


        
    </section>


<!-- Modal -->
<div id="invite_member" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">เชิญผู้ใช้งาน</h4>
      </div>
      <div class="modal-body">
        
        <div class="row">
             <div class="col-sm-12">
                <form id="invite-form" action="" method="POST" enctype="multipart/form-data" >
                {{ csrf_field() }} 
                  <div class="input-group input-group-sm">
                    <select class="form-control select2" id="member_select" name="member_select[]"  multiple="multiple" style="width: 100%;height:50px !important;">
                    <option ></option>
                    </select>
                      <span class="input-group-btn" >
                        <button type="submit" class="btn btn-info btn-flat" >เชิญ</button>
                      </span>
                  </div>
                </form>
                
  

               <!--  <select id="member_select" class="form-control" name="states[]" multiple="multiple">
                <option value="AL">Alabama</option>
                 <option value="AL">Alabama</option>
                 <option value="AL">Alabama</option>
                 <option value="AL">Alabama</option>
                <option value="WY">Wyoming</option>
              </select> -->
              </div>
        </div>
       
        

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ url('js/utility/validate.js') }}"></script> 
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src=" {{ url('js/utility/ajax.js') }} "></script>
<script type="text/javascript">
var room = $("#domainId").val()+"-"+$("#channel_id").val();

socket.emit('subscribe',room);


  $(function() {
    $("#btn-join-channel").on("click",function(){
         $.ajax({
                 type: "POST",
                 url : '{{ url($action) }}',
                 success: function (data) {
                    if(data.result=="true"){
                        var sd = {} ;
                        sd.room = room ;
                        sd.member_channel = data.response.member_channel ;
                        sd.member_request_channel = data.response.member_request_channel ;
                        socket.emit('channel_chat',sd);
                        var channelId = $("#channel_id").val();
                        var route = "{{ url($domainName.'/channel') }}/"+channelId ;
                        if(data.response.accept==1){
                          window.location = route;
                        }else{
                          location.reload();
                        }
                        
                    }else{
                     var error = JSON.stringify(data.errors);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
                    }
                 }
             });
    });

    $(document).on("click",".btn-accept-request-join",function(event) {  
        var parent = $(this).closest('li') ;
        var userId =  $(this).closest('li').find('.request-user-id').val();
        var channelId = $("#channel_id").val();
        var route = "/channel/"+channelId+"/accept?api_token="+api_token ;
        var data = { user_id:userId } ;
        ajaxPromise('POST',route,data).done(function(data){
            var sd = {} ;
            sd.room = room ;
            sd.member_channel = data.member_channel ;
            sd.member_request_channel = data.member_request_channel ;
            socket.emit('channel_chat',sd);
            location.reload();
        }).fail(function(txt) {
          var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
        });
    });

    $(document).on("click",".btn-set-owner",function(event) {  
         swal({
          title: 'Are you sure?',
          text: "คุณต้องการให้ผู้ใช้นี้ เป็นผู้ดูแลห้องใช่หรือไม่!",
          type: 'info',
          showCancelButton: true,
         
          confirmButtonText: 'ยืนยัน',
          cancelButtonText: 'ยกเลิก',
          confirmButtonClass: 'btn btn-primary',
          cancelButtonClass: 'btn btn-default',
          buttonsStyling: false,
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            var parent = $(this).closest('li') ;
            var userId =  $(this).closest('li').find('.request-user-id').val();
            var channelId = $("#channel_id").val();
            var route = "/channel/"+channelId+"/owner?api_token="+api_token ;
            var data = { user_id:userId } ;
            ajaxPromise('POST',route,data).done(function(data){
                // parent.remove();
                location.reload();
            });

          } else if (result.dismiss === 'cancel') {
            
          }
        })

        
    });
    $(document).on("click",".btn-kick-member",function(event) {  
        swal({
          title: 'Are you sure?',
          text: "คุณต้องการให้ผู้ใช้นี้ออกจากห้องใช่หรือไม่!",
          type: 'warning',
          showCancelButton: true,
         
          confirmButtonText: 'ยืนยัน',
          cancelButtonText: 'ยกเลิก',
          confirmButtonClass: 'btn btn-danger',
          cancelButtonClass: 'btn btn-default',
          buttonsStyling: false,
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            var parent = $(this).closest('li') ;
            var userId =  $(this).closest('li').find('.request-user-id').val();
            var channelId = $("#channel_id").val();
            var route = "/channel/"+channelId+"/kick?api_token="+api_token ;
            var data = { user_id:userId,'_method':'DELETE' } ;
            ajaxPromise('POST',route,data).done(function(data){
                 var sd = {} ;
                sd.room = room ;
                sd.member_channel = data.member_channel ;
                sd.member_request_channel = data.member_request_channel ;
                socket.emit('channel_chat',sd);
                location.reload();
            }).fail(function(txt) {
              var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
            });

          } else if (result.dismiss === 'cancel') {
            
          }
        })

        
    });



    $("#member_select").select2({
              // minimumInputLength: 2,
              tags: [],
              ajax: {
                  url: "{{ url('/api/'.$domainId.'/search/user') }}?api_token="+api_token,
                  dataType: 'json',
                  type: "POST",
                  delay: 250,
                  data: function (params) {
                      return {
                          name: params.term
                      };
                  },
                  processResults: function (data) {
                    return {
                      results: data
                    };
                  },
              }
          });

     $("#invite-form").validate({
          rules: {
            member_select: {
              required: true
            },
          },
          messages: {
            username: {
              required: "กรุณากรอกชื่อห้อง",
            }
          },
          submitHandler: function (form) {
                 $.ajax({
                     type: "POST",
                     url : '{{ url($action) }}?api_token='+api_token ,
                     data: $(form).serialize(),
                     success: function (data) {
                        console.log(data,typeof data.response);
                        if(data.response){
                            window.location.href= " {{ url($route) }}"+"/"+data.response.channel_id;
                        }
                     }
                 });
                 return false; // required to block normal submit since you used ajax
             }
        });
  });

function leaveChannel(){
    var channelId = $("#channel_id").val();
      var text = $("#message_text").val();
      var route = "/channel/"+channelId+"/leave?api_token="+api_token ;

      ajaxPromise('POST',route,{'_method':'DELETE'}).done(function(data){
          window.location.href= " {{ url($route) }}"+"/"+channelId;
      })
}

</script>
@endsection







