@extends('main.layouts.main')

@section('style')
  <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ url('plugins/fancybox/jquery.fancybox.css') }}" type="text/css" media="screen" />
  <style type="text/css">
  .message-tools{
    visibility: hidden;
  }
  #chat-box-message .item:hover .message-tools,#pin-box-message .item:hover .message-tools {
    visibility: visible;
  }
 .owner-message{
margin-top:10px; border-radius: 20px;background: #3c8dbc;color:#FFF; padding:5px 10px;
 }
  </style>
@endsection
@section('content-wrapper')


 <?php 
    $userId = Auth()->user()->id ;
?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        @if( $channels['direct_message']==1)
            @if (auth()->user()->id!=$channels['u1_user_id'] )
              {{ $channels['u1_first_name']." ".$channels['u1_last_name'] }}
            @else
              {{ $channels['u2_first_name']." ".$channels['u2_last_name'] }}
            @endif
        @else
        <i class="fa @if(isset($channels['icon'])) {{'fa-'.$channels['icon']}} @else fa-circle-o  @endif "> </i>
        {{ $channels['name'] }}
        @endif


        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url($home) }}"><i class="fa fa-home"></i>@lang('main.home')</a></li>
        <li class="active">@lang('chat.title_chat')</li>
      </ol>
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
            <div class="col-sm-9 main-chat">
                 <div class="box box-primary">
                  <div class="box-header">
                    <i class="fa fa-thumb-tack"></i>
                    <h3 class="box-title">@lang('chat.title_pin_message')</h3>
                  </div>
                  <div class="box-body chat" id="pin-box-message">
                     @if( count($messages) > 0)

                        @foreach ($messages as $message)
                          @if($message['pin'])
                          <div class="item">
                             @if($message['hide']==1)
                             <div>
                                  <button class="btn btn-default btn-xs  pull-right btn-show-msg" >
                                  @lang('chat.show_message')
                                  </button>
                                  @lang('chat.hide_message')
                              </div>
                             @else
                                  @if($actionStatus['is_owner'])
                                  <div class="btn-group pull-right message-tools">
                                      <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-gear"></i>
                                      </button>
                                      <ul class="dropdown-menu pull-right" role="menu">
                                        <li>
                                          <a href="javascript:void(0)" class="btn-del-msg" >
                                          @lang('chat.delete_message')
                                          </a>
                                        </li>
                                        <li>
                                          <a href="javascript:void(0)" class="btn-unpin-msg" >
                                          @lang('chat.unpin_message')
                                          </a>
                                        </li>
                                        <li>
                                          <a href="javascript:void(0)" class="btn-hide-msg" >
                                          @lang('chat.hide_message')
                                          </a>
                                        </li>
                                        <li>
                                          <a href="javascript:void(0)" class="btn-blacklist-inform-user" >
                                          @lang('chat.blacklist_inform')
                                          </a>
                                        </li>
                                      </ul>
                                  </div>
                                  @endif
                                 
                                  @if($userId!=$message['created_by'])
                                    <img src="{{ $message['img'] }}" class=" {{ ($message['is_online']) ? 'online' : 'offline' }}">
                                    @else
                                    <div style="height: 40px;"></div>
                                  @endif
                                  
                                   <input type="hidden" class="msg-item-user-id" value="{{$message['created_by']}}" >
                                  <input type="hidden" class="msg-id" value="{{$message['id']}}" >
                                  @if($message['type']==1)
                                  <p class="message">
                                    <a href="javascript:void(0)" class="name">
                                       @if($userId!=$message['created_by'])
                                        <small class="text-muted pull-right">
                                        
                                        <i class="fa fa-clock-o"></i> {{ date('d/m/Y H:i',$message['updated_ts'] )  }}
                                      </small>
                                          {{ $message['first_name']." ".$message['last_name'] }}
                                        @endif
                                    <!--   <button class="btn btn-xs btn-danger pull-right btn-del-msg" title="@lang('chat.delete_message')"><i class="fa fa-trash"></i></button> -->
                                    </a>
                                    @if($userId==$message['created_by'])
                                    
                                      <div class="pull-right ">
                                        @if($message['has_seen'])
                                           @lang('chat.readed')  
                                           @if($channels['direct_message']==1)  
                                           {{ $message['has_seen_date'] }}
                                           @else
                                           {{ $message['has_seen_count'] }}
                                           @endif
                                        @endif
                                        <span class="owner-message">
                                           {{ $message['text'] }}
                                        </span>
                                       
                                      </div>
                                    @else
                                      {{ $message['text'] }}
                                    @endif
                                  </p>
                                  @else

                                  <div class="attachment">
                                    @if($userId==$message['created_by'])
                                    <div class="pull-right">
                                        <p class="filename">
                                           @if($message['has_seen'])
                                           @lang('chat.readed')  
                                           @if($channels['direct_message']==1)  
                                           {{ $message['has_seen_date'] }}
                                           @else
                                           {{ $message['has_seen_count'] }}
                                           @endif
                                        @endif
                                          @if( strpos($message['attachment_extension'],'image') > -1 )
                                          <a class="fancybox" href="{{ $message['attachment_path'] }}">
                                          <img src="{{ $message['attachment_path'] }}" height=50 ></a>
                                          @else
                                          {{ $message['attachment_name'] }}
                                          @endif
                                        </p>
                                        <a href="{{ $message['attachment_path'] }}" class="pull-right" target="_blank" download="{{ $message['attachment_name'] }}">Download</a>
                                    </div>
                                    @else
                                     <p class="filename">
                                      <a class="fancybox" href="{{ $message['attachment_path'] }}">
                                        <img src="{{ $message['attachment_path'] }}" height=50 >
                                      </a>
                                    </p>
                                    <a href="{{ $message['attachment_path'] }}" target="_blank" download="{{ $message['attachment_name'] }}">Download</a>
                                    @endif

                                   
                                  </div>
                                  <!-- /.attachment -->
                                  @endif


                             @endif

                            
                          </div>
                          @endif
                          <!-- /.item -->
                        @endforeach
                     @endif

                    
                  
                  </div>
                  <!-- /.chat -->
                  <div class="box-footer" style="z-index:1!important;">
                  </div>
                </div>

                <div class="box box-primary">
                  <div class="box-header">
                    <i class="fa fa-comments-o"></i>

                    <h3 class="box-title">@lang('chat.message')</h3>
                    @if( $channels['direct_message']==0)
                    <!-- tools box -->
                    <div class="pull-right box-tools">
                      <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                          <i class="fa fa-gear"></i></button>
                        <ul class="dropdown-menu pull-right" role="menu">
                          <li><a href="javascript:void(0)" data-toggle="modal" data-target="#invite_member" >
                          @lang('chat.invite_user_to_chat_room')
                          </a></li>
                          <li><a href="{{ url($route.'/'.$channelId.'/member') }}">
                          @lang('chat.member_in_chat_room')
                          </a></li>
                          <li><a href="javascript:void(0)" id="on_off_notification" >
                            @if($actionStatus['push_notification']==1)  @lang('chat.turn_off') @else  @lang('chat.turn_on')  @endif @lang('chat.notification')</a></li>

                          @if($actionStatus['is_owner'])

                          <li class="divider"></li> 
                          <li><a href="{{ url($route.'/'.$channelId.'/edit') }}">@lang('chat.edit_chat_room')</a></li> 
                          <li><a href="javascript:void(0)" id="delete_channel" >@lang('chat.del_chat_room')</a></li> 
                          @endif
                          <li class="divider"></li> 
                          <li><a href="javascript:void(0)" onclick="leaveChannel()" >@lang('chat.leave_chat_room')</a></li> 
                        </ul>
                      </div>
                    </div>
                    <!-- /. tools -->
                    @endif
                    
                  </div>


                
                  <div class="box-body chat" id="chat-box-message">
                     @if( count($messages) > 0)
                        @foreach ($messages as $message)
                         
                          <!-- chat item -->
                          <div class="item"  >
                             <input type="hidden" class="msg-id" value="{{$message['id']}}" >
                            @if($message['hide']==1)
                              <div>
                                  <button class="btn btn-default btn-xs  pull-right btn-show-msg" >
                                  @lang('chat.show_message')
                                  </button>
                                  @lang('chat.hide_message')
                              </div>
                            @else
                                @if($actionStatus['is_owner'] || auth()->user()->hasRole('admin') )
                                <div class="btn-group pull-right message-tools">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                      <i class="fa fa-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                      <li>
                                        <a href="javascript:void(0)" class="btn-del-msg" >
                                        @lang('chat.delete_message')
                                        </a>
                                      </li>
                                      <li>
                                        <a href="javascript:void(0)" class="btn-pin-msg" >
                                        @lang('chat.pin_message')
                                        </a>
                                      </li>
                                       <li>
                                        <a href="javascript:void(0)" class="btn-hide-msg" >
                                        @lang('chat.hide_message')
                                        </a>
                                      </li>
                                      <li>
                                          <a href="javascript:void(0)" class="btn-blacklist-inform-user" >
                                          @lang('chat.blacklist_inform')
                                          </a>
                                        </li>
                                    </ul>
                                </div>
                                @endif
                               
                                @if($userId!=$message['created_by'])
                                  <img src="{{ $message['img'] }}" class=" {{ ($message['is_online']) ? 'online' : 'offline' }}">
                                  @else
                                  <div style="height: 40px;"></div>
                                @endif
                                
                                 <input type="hidden" class="msg-item-user-id" value="{{$message['created_by']}}" >
                               
                                @if($message['type']==1)
                                <p class="message">
                                  <a href="javascript:void(0)" class="name">
                                      @if($userId!=$message['created_by'])
                                      <small class="text-muted pull-right">
                                      
                                      <i class="fa fa-clock-o"></i> {{ date('d/m/Y H:i',$message['updated_ts'] )  }}
                                    </small>
                                        {{ $message['first_name']." ".$message['last_name'] }}
                                      @endif
                                  </a>
                                  @if($userId==$message['created_by'])
                                  
                                    <div class="pull-right ">
                                       @if($message['has_seen'])
                                         @lang('chat.readed')  
                                         @if($channels['direct_message']==1)  
                                         {{ $message['has_seen_date'] }}
                                         @else
                                         {{ $message['has_seen_count'] }}
                                         @endif
                                      @endif
                                      <span class="owner-message">
                                         {{ $message['text'] }}
                                      </span>
                                     
                                    </div>
                                  @else
                                    {{ $message['text'] }}
                                  @endif
                                  
                                   
                                </p>
                                @else
          
                                <div class="attachment">
                                  @if($userId==$message['created_by'])

                                  <div class="pull-right">

                                      <p class="filename">
                                         @if($message['has_seen'])
                                         @lang('chat.readed')  
                                         @if($channels['direct_message']==1)  
                                         {{ $message['has_seen_date'] }}
                                         @else
                                         {{ $message['has_seen_count'] }}
                                         @endif
                                      @endif
                                        @if( strpos($message['attachment_extension'],'image') > -1 )

                                        <a class="fancybox" href="{{ $message['attachment_path'] }}">
                                        <img src="{{ $message['attachment_path'] }}" height=50 ></a>
                                        @else
                                        {{ $message['attachment_name'] }}
                                        @endif
                                      </p>
                                      <a href="{{ $message['attachment_path'] }}" class="pull-right" target="_blank" download="{{ $message['attachment_name'] }}">Download</a>
                                  </div>
                                  @else
                                   <p class="filename">
                                    <a class="fancybox" href="{{ $message['attachment_path'] }}">
                                      <img src="{{ $message['attachment_path'] }}" height=50 >
                                    </a>
                                  </p>
                                  <a href="{{ $message['attachment_path'] }}" target="_blank" download="{{ $message['attachment_name'] }}">Download</a>
                                  @endif

                                 
                                </div>
                                <!-- /.attachment -->
                                @endif
                            @endif
          

                            
                          </div>
                         
                        @endforeach
                     @endif

                    
                    <!-- chat item -->
                   <!--  <div class="item">
                      <img src="{{ url('dist/img/user3-128x128.jpg')}}" alt="user image" class="offline">

                      <p class="message">
                        <a href="#" class="name">
                          <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 5:15</small>
                          Alexander Pierce
                        </a>
                        I would like to meet you to discuss the latest news about
                        the arrival of the new theme. They say it is going to be one the
                        best themes on the market
                      </p>
                    </div> -->
                    <!-- /.item -->
                    <!-- chat item -->
                    <!-- <div class="item">
                      <img src="{{ url('dist/img/user2-160x160.jpg')}}" alt="user image" class="offline">

                      <p class="message">
                        <a href="#" class="name">
                          <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 5:30</small>
                          Susan Doe
                        </a>
                        I would like to meet you to discuss the latest news about
                        the arrival of the new theme. They say it is going to be one the
                        best themes on the market
                      </p>
                    </div> -->
                    <!-- /.item -->
                  </div>
                  <!-- /.chat -->
                  <div class="box-footer" style="z-index:1!important;">
                    <form id="chat_message">  
                    
                    <div class="input-group">
                      <div class="input-group-btn">
                       
                        <label for="file-upload" class="btn btn-attachment btn-default">
                                    <i class="fa fa-paperclip"></i>
                        </label>
                                <input id="file-upload" name='doc_file[]' type="file" style="display:none;">
                      </div>
                      <input class="form-control" id="message_text" placeholder="@lang('chat.type_message')" style="z-index:1!important;">

                      <div class="input-group-btn">
                        <button type="submit" class="btn btn-success "  ><i class="fa fa-plus"></i></button>
                      </div>
                    </div>
                    </form>
                  </div>
                </div>
            </div> 
            <div class="col-sm-3">
                <div class="box box-primary" >
                  <div class="box-header">
                    <i class="fa fa-users"></i>
                    <h3 class="box-title">@lang('chat.member')</h3>
                  </div>
                  <div class="box-body chat member" id="member_channel_list" >
                     @if( count($members) > 0)
                        @foreach ($members as $member)
                          
                          <div class="item">
                            <img src="{{ $member['img'] }}"  class="{{ ($member['is_online']) ? 'online' : 'offline' }}">
                             <input type="hidden" class="msg-item-user-id" value="{{$member['user_id']}}" >
                             <div class="btn-group pull-right">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                  <i class="fa fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                  <li>
                                    <a href="javascript:void(0)" class="btn-blacklist-user" >
                                    @lang('chat.blacklist')
                                    </a>
                                  </li>
                              
                                </ul>
                            </div>
                            <p class="message" >
                              <a href="javascript:void(0)" class="name" style="margin-top:5px;">
                                
                                {{ $member['first_name']." ".$member['last_name'] }}
                              </a>
                            </p> 
                          </div>
                         
                          <!-- /.item -->
                        @endforeach
                     @endif

                    

                  </div>
                  <!-- /.chat -->
                  
                </div>
                @if(( (isset($channels['type']) && $channels['type']!=1 && $channels['type']!=0 ) && $actionStatus['is_owner'] && $channels['direct_message']==0 )  )
                <div class="box box-primary" >
                  <div class="box-header">
                    <i class="fa fa-user-plus"></i>
                    <h3 class="box-title">@lang('chat.request')</h3>
                  </div>
                  <div class="box-body chat member" id="request_member_channel_list" >
                     @if( count($requests) > 0)
                        @foreach ($requests as $request)
                          <!-- chat item -->
                          <div class="item">
                            <img src="{{ $request['img'] }}" class="{{ ($request['is_online']) ? 'online' : 'offline' }}">
                             <input type="hidden" class="msg-item-user-id" value="{{$request['user_id']}}" >
                            <p class="message" >
                              <a href="javascript:void(0)" class="name" style="margin-top:5px;">
                                {{ $request['first_name']." ".$request['last_name'] }}
                              </a>
                               <button class="btn-accept-request-join btn btn-primary btn-xs btn-flat">ยืนยัน</button>
                            </p>
                            
                          </div>
                          <!-- /.item -->
                        @endforeach
                     @endif

                    

                  </div>
                  <!-- /.chat -->
                  
                </div>
                @endif
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
        <h4 class="modal-title">@lang('chat.invite_user_to_chat_room')</h4>
      </div>
      <div class="modal-body">
        
        <div class="row">
             <div class="col-sm-12">
                <form id="invite-form" action="" method="POST" enctype="multipart/form-data" >
                {{ csrf_field() }} 
                  <div class="form-group">
                    <select class="form-control select2" id="member_select" name="member_select[]"  multiple="multiple" style="width: 100%;height:20px !important;">
                    <option ></option>
                    </select>
                      
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
        <button type="button" onclick="$('#invite-form').submit()" class="btn btn-info btn-flat" >
        @lang('chat.invite') </button>
        <button type="button" class="btn btn-default" data-dismiss="modal">
        @lang('main.close')</button>
      </div>
    </div>

  </div>
</div>


<div id="blacklist_inform_member" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">@lang('chat.blacklist_inform')</h4>
      </div>
      <div class="modal-body">
        
        <div class="row">
             <div class="col-sm-12">
               
                <input type="hidden" id="blacklist_user_id" name="blacklist_user_id">
                <input type="hidden" id="blacklist_message_id" name="blacklist_message_id">
                <div class="form-group">
                  <label for="">@lang('chat.blacklist_inform_description')</label>
                  <input type="text" class="form-control" id="blacklist_inform_description">
                    
                </div>
               
                
  

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
        <button type="button" class="btn btn-primary btn-flat btn-blacklist-inform-user-save" >
        @lang('main.btn_save') </button>
        <button type="button" class="btn btn-default" data-dismiss="modal">
        @lang('main.close')</button>
      </div>
    </div>

  </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ url('js/utility/validate.js') }}"></script> 

<script type="text/javascript" src="{{ url('js/channel/member.js') }}"></script> 
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>

<script type="text/javascript" src="{{ url('plugins/fancybox/jquery.fancybox.js') }} "></script>


<script type="text/javascript">


var room = $("#domainId").val()+"-"+$("#channel_id").val();

socket.emit('subscribe',room);



socket.on('channel_chat', function(r){
  console.log('[channel_chat][show] ',r);
  if (typeof r==='string'){
    r = JSON.parse(r);
  }
  var channelId = $("#channel_id").val();

  $(".content-wrapper").find('.chat-box-message-id').each(function(){
      // console.log('.chat-box-message-id',$(this).val(),channelId);
      if(parseInt($(this).val())==channelId){
          $(this).closest('.chat-box-message-info').remove() ;
      }
  }) ;

  
  // console.log(channelId,r.channel.id,r.chat);
  if(r.channel!=null){
     if(channelId!=r.channel.id || r.init ==1 ){
      return false;
    }
  }
 
  if(r.chat!=null){

    if (typeof r.chat==='string'){
      r.chat = JSON.parse(r.chat);
    }

     setChat(r.chat);
  }

  if(r.message_del_id!=null){
      $("#chat-box-message .msg-id").each(function(){
          if($(this).val()==r.message_del_id){
            $(this).closest('.item').remove();
          }
      });
  }

  if(r.message_pin_id!=null){
      findPinChat(r.message_pin_id).done(function(canMove){
        console.log('canMove : ',canMove);
        if(canMove){
          $("#chat-box-message .msg-id").each(function(){
              if($(this).val()==r.message_pin_id){
                var clone = $(this).closest('.item').clone();
                clone.find('.btn-pin-msg').addClass('btn-unpin-msg').removeClass('btn-pin-msg').text((($("#app_local").val()=='th') ? 'ไม่ปักหมุดข้อความนี้' : 'Unpin message' ));

               $("#pin-box-message").append(clone);
                // $(this).closest('.item').remove();
              }
          });
        }
      }) 
      
  }
  if(r.message_unpin_id!=null){
      $("#pin-box-message .msg-id").each(function(){
          if($(this).val()==r.message_unpin_id){
            // var clone = $(this).closest('.item').clone();
            // clone.find('.btn-unpin-msg').addClass('btn-pin-msg').removeClass('btn-unpin-msg').text((($("#app_local").val()=='th') ? 'ปักหมุดข้อความนี้' : 'Pin message' ));
            // $("#chat-box-message").append(clone);
            $(this).closest('.item').remove();
          }
      });
  }
  
  $("#message_text").val('');

  if(r.member_channel!=null){
    if (typeof r.member_channel==='string'){
      r.member_channel = JSON.parse(r.member_channel);
    }
    setMember(r.member_channel);
  }
  if(r.member_request_channel!=null){
    if (typeof r.member_request_channel==='string'){
      r.member_request_channel = JSON.parse(r.member_request_channel);
    }
    setRequestUser(r.member_request_channel);
  }

  // setTimeout(function(){
   
  // }, 100);
  $(".direct-chat").hide();
  
})

function findPinChat(messageId){
  var dfd = $.Deferred();
   var canMove = true;
      $("#pin-box-message .msg-id").each(function(){
          if($(this).val()==messageId){
            canMove = false;
          }
      });
  dfd.resolve(canMove);
  return dfd.promise();
}

function setMember(data){ 
  // console.log(data);
  var html = "" 
   if(data.length>0){
     for(var i=0;i<data.length;i++){
        html += "<div class=\"item\">"+
              "<img src=\""+data[i].img+"\" alt=\"user image\" class=\""+((data[i].is_online) ? 'online':'offline' )+"\">" +
              "<input type=\"hidden\" class=\"msg-item-user-id\" value=\""+data[i].user_id+"\" >"+
              "<p class=\"message\">"+
              "<a href=\"javascript:void(0)\" class=\"name\" style=\"margin-top:5px;\">"+
              data[i].first_name+" "+data[i].last_name+
              "</a></p></div>";
     }
   }
   // console.log(html);
  $("#member_channel_list").html(html);
}
function setRequestUser(data){ 
  // console.log(data,data.length);
  var html = "" 
   if(data.length>0){
     for(var i=0;i<data.length;i++){
        html += "<div class=\"item\">"+
              "<img src=\""+data[i].img+"\" alt=\"user image\" class=\""+((data[i].is_online) ? 'online':'offline' )+"\">" +
              "<input type=\"hidden\" class=\"msg-item-user-id\" value=\""+data[i].user_id+"\" >"+
              "<p class=\"message\">"+
              "<a href=\"javascript:void(0)\" class=\"name\" style=\"margin-top:5px;\">"+
              data[i].first_name+" "+data[i].last_name+
              "</a>"+
               "<button class=\"btn-accept-request-join btn btn-primary btn-xs btn-flat\">ยืนยัน</button>"+
              "</p></div>";
     }
   }
   // console.log(html);
  $("#request_member_channel_list").html(html);
}
function itemMember(data){
   
   return html ;
}

</script>

<script type="text/javascript">



 $(document).on("click",".btn-accept-request-join",function(event) {  
        var parent = $(this).closest('.item') ;
        var userId =  parent.find('.msg-item-user-id').val();
        var channelId = $("#channel_id").val();
        var route = "/channel/"+channelId+"/accept?api_token="+api_token ;
        var data = { user_id:userId } ;
        ajaxPromise('POST',route,data).done(function(data){
          var sd = {} ;
                sd.room = room ;
                sd.member_channel = data.member_channel ;
                sd.member_request_channel = data.member_request_channel ;
                socket.emit('channel_chat',sd);
        }).fail(function(txt) {
          var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
        });
    });
 $(document).on("click",".btn-del-msg",function(event) {  
        var parent = $(this).closest('.item') ;
        var messageId =  parent.find('.msg-id').val();
        var route = "/channel/message/"+messageId+"?api_token="+api_token ;
        ajaxPromise('POST',route,{'_method':'DELETE'}).done(function(data){
            parent.remove();
            var sd = {} ;
            sd.room = room ;
            sd.message_del_id = messageId ;
            socket.emit('channel_chat',sd);
        }).fail(function(txt) {
          var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
        });
    }); 
 $(document).on("click",".btn-pin-msg",function(event) {  
        var parent = $(this).closest('.item') ;
        var messageId =  parent.find('.msg-id').val();
        var route = "/channel/message/"+messageId+"/pin?api_token="+api_token ;
        ajaxPromise('POST',route,{'_method':'PUT'}).done(function(data){
            // parent.remove();
            var sd = {} ;
            sd.room = room ;
            sd.message_pin_id = messageId ;
            socket.emit('channel_chat',sd);
        }).fail(function(txt) {
          var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
        });
    });
 $(document).on("click",".btn-unpin-msg",function(event) {  
        var parent = $(this).closest('.item') ;
        var messageId =  parent.find('.msg-id').val();
        var route = "/channel/message/"+messageId+"/unpin?api_token="+api_token ;
        ajaxPromise('POST',route,{'_method':'PUT' }).done(function(data){
            // parent.remove();
            var sd = {} ;
            sd.room = room ;
            sd.message_unpin_id = messageId ;
            socket.emit('channel_chat',sd);
        }).fail(function(txt) {
          var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
        });
    });
 $(document).on("click",".btn-hide-msg,.btn-show-msg",function(event) {  
        var parent = $(this).closest('.item') ;
        var messageId =  parent.find('.msg-id').val();
        var route = "/channel/message/"+messageId+"/hide?api_token="+api_token ;
        ajaxPromise('POST',route,{'_method':'PUT'}).done(function(data){
            // parent.remove();
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
  $(function() {
    
     $("#delete_channel").on('click',function(){
      swal({
      title: 'Are you sure?',
      text: "You want to delete this Chat room!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      cancelButtonText: 'Cancel',
      confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-default',
      buttonsStyling: false,
      reverseButtons: true
    }).then((result) => {
      if (result.value) {
          var channelId = $("#channel_id").val();
          var route = "/channel/"+channelId+"?api_token="+api_token ;
          ajaxPromise('POST',route,{'_method':'DELETE'}).done(function(data){
               swal({
                          title: "@lang('main.delete_success')" ,
                          type: 'success',
                          showCancelButton: false,
                          confirmButtonText: "@lang('main.ok')"
                      }).then((result) => {
                        if (result.value) {
                          window.location.href = "{{ url($domainId.'/dashboard') }}";
                        }
                      })
          });
      }
    })


     

       
    });

var beforeMessage = '';

var initial = 500;
var count = initial;
var counter;

function timer() {
    if (count <= 0) {
        clearInterval(counter);
        return;
    }
    count--;
}

$(document).on('focus click', '.main-chat',  function(e){
    var channelId = $("#channel_id").val();
    console.log('focus',channelId);
      var route = "/channel/"+channelId+"/pushoff?api_token="+api_token ;
      ajaxPromise('POST',route,null);
});


$(document).on('click touch', '.btn-blacklist-user',  function(e){
    var parent = $(this).closest('.item');
    var userId = parent.find('.msg-item-user-id').val();
    var route = "/channel/blacklist?api_token="+api_token ;
    ajaxPromise('POST',route,{'user_id':userId}).done(function(data){
      location.reload();
    })
});

$(document).on('click touch', '.btn-blacklist-inform-user',  function(e){
    var parent = $(this).closest('.item') ;
    var messageId = parent.find('.msg-id').val();
    var userId = parent.find('.msg-item-user-id').val();
    $("#blacklist_user_id").val(userId);
    $("#blacklist_message_id").val(messageId);
    $("#blacklist_inform_description").val('');
    $("#blacklist_inform_member").modal('toggle');
});

$(document).on('click touch', '.btn-blacklist-inform-user-save',  function(e){
    var memberId = $("#blacklist_user_id").val();
    var msgId = $("#blacklist_message_id").val();
    var desc = $("#blacklist_inform_description").val();
    var data = { 
      'user_id' : memberId
      ,'text' : desc
      ,'message_id' : msgId
    };
                 
    var parent = $(this).closest('.item');
    var userId = parent.find('.msg-item-user-id').val();
    var route = "/channel/blacklist/inform?api_token="+api_token ;
    ajaxPromise('POST',route,data).done(function(res){
      swal({
            title: res.response.text ,
            type: 'success',
            showCancelButton: false,
            confirmButtonText: "@lang('main.ok')"
      })
       $("#blacklist_inform_member").modal('toggle');
    })
});


    $("#chat_message").on('submit',function(e){
      
      clearInterval(counter);
      counter = setInterval(timer, 10);
      var channelId = $("#channel_id").val();
      var text = $("#message_text").val();

      if(beforeMessage==text&& count >0 ){
        return false;
      }


      beforeMessage = text ;

      var route = "/channel/"+channelId+"/chat?api_token="+api_token ;
      var data = {
        text:text ,
        type:1
      } ;

       ajaxPromise('POST',route,data).done(function(data){
            clearInterval(counter);
            count = initial;
    
            beforeMessage = '';
            var sd = {} ;
            sd.room = room ;
            sd.chat = data.chat ;
            sd.channel = data.channel ;
            sd.init = 0; 
            socket.emit('channel_chat',sd);
        });
       return false;
       
    }); 

    $("#on_off_notification").on('click',function(){
      var channelId = $("#channel_id").val();
      var route = "/channel/"+channelId+"/push?api_token="+api_token ;
      ajaxPromise('POST',route,null).done(function(data){
          // console.log(data)
          var txt = (data.push_notification_status==0) ? 'Turn on notification' : 'Turn off notification'
          $("#on_off_notification").text(txt);
          swal({
              title: data.push_notification_text ,
              type: 'success',
              showCancelButton: false,
              confirmButtonText: "@lang('main.ok')"
          })
      });

       
    });

    // focusLastElement();


     
  
 
   

    $("#member_select").select2({
              // minimumInputLength: 2,
              tags: [],
              ajax: {
                  url: "{{ url('/api/'.$domainId.'/search/member/channel/'.$channelId.'?api_token=')}}"+api_token,
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
                     url : '{{ url($action) }}',
                     data: $(form).serialize(),
                     success: function (data) {
                        console.log(data,typeof data.response);
                        if(data.response){
                            window.location.href= " {{ url($route) }}"+"/"+ ($("#channel_id").val());
                        }else{
                          swal(
                            'Oops...',
                            data.errors,
                            'error'
                          )
                        }
                     }
                 });
                 return false; // required to block normal submit since you used ajax
             }
        });
  });
function setChat(data){
  console.log(data);
  if(data.created_by==$('#user_id').val()){

  }else{

  }



  var html = "<div class=\"item\"> "+
   @if($actionStatus['is_owner'])
              "<div class=\"btn-group pull-right message-tools\">"+
                  "<button type=\"button\" class=\"btn btn-default btn-xs dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"false\">"+
                    "<i class=\"fa fa-gear\"></i>"+
                  "</button>"+
                  "<ul class=\"dropdown-menu pull-right\" role=\"menu\">"+
                    "<li>"+
                      "<a href=\"javascript:void(0)\" class=\"btn-del-msg\" >"+
                      "@lang('chat.delete_message')"+
                      "</a>"+
                    "</li>"+
                    "<li>"+
                      "<a href=\"javascript:void(0)\" class=\"btn-pin-msg\" >"+
                      "@lang('chat.pin_message')"+
                      "</a>"+
                    "</li>"+
                    "<li>"+
                      "<a href=\"javascript:void(0)\" class=\"btn-blacklist-inform-user\" >"+
                      "@lang('chat.blacklist_inform')"+
                      "</a>"+
                    "</li>"+
                  "</ul>"+
              "</div>"+   
  @endif
               "<input type=\"hidden\" class=\"msg-item-user-id\" value=\""+data.created_by+"\" >"+
               "<input type=\"hidden\" class=\"msg-id\" value=\""+data.id+"\">";
  if(data.created_by!=$('#user_id').val()){
      html += "<img src=\""+data.img+"\" class=\" "+( (data.is_online) ? 'online' : 'offline' )+" \">";
  }else{
      html += "<div style=\"height: 40px;\"></div>";
  }

  if(data.type==2){
      html +=  "<div class=\"attachment\">" ;
      if(data.created_by==$('#user_id').val()){
      html +=  "<div class=\"pull-right\">"+
                  "<p class=\"filename\">";
          if( data.attachment_extension.search("image") > -1){
      html += "<a class=\"fancybox\" href=\""+data.attachment_path+"\">"+
              "<img src=\""+data.attachment_path+"\" height=50 ></a>";
          }else{
      html +=  data.attachment_name ;    
          }
      html += "</p>"+
              "<a href=\""+data.attachment_path+"\" class=\"pull-right\" target=\"_blank\" download=\""+data.attachment_name+"\">Download</a>"+
               "</div>";
      }else{
      html += "<p class=\"filename\">"+
              "<a class=\"fancybox\" href=\""+data.attachment_path+"\">" +
               "<img src=\""+data.attachment_path+"\" height=50 >"+
               "</a></p>"+
               "<a href=\""+data.attachment_path+"\" target=\"_blank\" download=\""+data.attachment_name+"\">Download</a>";
      }
       html += "</div>";
  }else{
      html += "<p class=\"message\">"+
               "<a href=\"javascript:void(0)\" class=\"name\">";
  

      if(data.created_by!=$('#user_id').val()){
          html += "<small class=\"text-muted pull-right\">"+
                   "<i class=\"fa fa-clock-o\"></i> "+ 
                   moment.unix(data.updated_ts).format("DD/MM/YYYY HH:mm") +"</small> "+
                   data.first_name+" "+data.last_name ;
      } 
          html += "</a>";
     if(data.created_by==$('#user_id').val()){
          html += "<div class=\"pull-right\">";
          if(data.has_seen){
              @if($channels['direct_message']==1)  
                 html += "@lang('chat.readed')  "+data.has_seen_date ;
              @else
                  html += "@lang('chat.readed')  "+data.has_seen_count ;
              @endif
          }
          html += "<span class=\"owner-message\">"+
                   data.text+
                   "</span>"+
                   "</div>";                   
                                   
                                 
    }else{
          html +=  data.text ;
    }

              

      html +=  "</p>";
  }


  html +="</div>";
   $("#chat-box-message").append(html);
  var parentHeight = $("#chat-box-message")[0].scrollHeight -  $(".item")[0].scrollHeight ;
  
  $("#chat-box-message").animate({
    scrollTop: parentHeight
  },'fast');

  jQuery("#chat-box-message .fancybox").fancybox({openEffect : 'none',
        closeEffect : 'none'});

}

function leaveChannel(){
    var channelId = $("#channel_id").val();
      var text = $("#message_text").val();
      var route = "/channel/"+channelId+"/leave?api_token="+api_token ;

      ajaxPromise('POST',route,{'_method':'DELETE'}).done(function(data){
            window.location = "{{ url($domainName.'/dashboard') }}" ;
      })
}

function focusLastElement(){
  // console.log('box height',$("#chat-box-message")[0].scrollHeight);
  // console.log('item height',$(".item")[0].scrollHeight);
  // var parentHeight = $("#chat-box-message")[0].scrollHeight -  $(".item")[0].scrollHeight ;
  // $("#chat-box-message").animate({
  //   scrollTop: parentHeight
  // },'fast');

  if($('#chat-box-message .item').length >0 ){
    $("#chat-box-message").animate({scrollTop: $('#chat-box-message .item:last').offset().top - 30},'fast');




  }

  
}
 // var windowH = $(window).height();
 //    var wrapperH = $('.member').height();
 //    if(windowH > wrapperH) {                            
 //        $('.member').css({'height':($(window).height())+'px'});
 //    }                                                                               
 //    $(window).resize(function(){
 //        var windowH = $(window).height();
 //        var wrapperH = $('.member').height();
 //        var differenceH = windowH - wrapperH;
 //        var newH = wrapperH + differenceH;
 //        var truecontentH = $('#truecontent').height();
 //        if(windowH > truecontentH) {
 //            $('.member').css('height', (newH)+'px');
 //        }

 //    }) 





var contentHeight = $(window).height() - ($(".main-footer").outerHeight()+$('#chat-box-message').offset().top)


if(contentHeight<250) contentHeight=250;
// console.log($(window).height(), $(".main-footer").outerHeight() ,$('#chat-box-message').offset().top,contentHeight);

var chatboxFooter = $('.main-chat .box-footer').outerHeight() ;

// $('.member').css('height', (contentHeight-$(".box-header").outerHeight())/2 );
var memberHeight = ($("#request_member_channel_list").length > 0 ) ? (contentHeight-$(".box-header").outerHeight()-23)/2 : contentHeight ;

$('.member').slimScroll({
    height: memberHeight
});

$('#chat-box-message').slimScroll({
    height: contentHeight-chatboxFooter,
    alwaysVisible: true,
    railVisible: false,
});


$('#chat-box-message').slimScroll({
    scroll : $('#chat-box-message .item:last').offset().top - 30,
});


$(".main-chat").find('.slimScrollBar').css("top", $('#chat-box-message .item:last').offset().top - 30);
$('#chat-box-message').trigger('mouseover').scrollTop($('#chat-box-message .item:last').offset().top - 30);


</script>


<script>
  $('#file-upload').on('change',function() {
    var channelId = $("#channel_id").val();
    var file = $('#file-upload')[0].files[0];
    var file_name = file.name;
    var file_ext = file_name.split('.').pop().toLowerCase();
    var file_size = file.size ;
    var reader = new FileReader();
    var img = [] ;

      reader.onload = function(e) {
            var data = {
              name : file_name ,
              extension : file_ext ,
              size : file_size ,
              data : e.target.result ,
            }
            // console.log(data);
            img.push(data)
            var form_data = new FormData();
            form_data.append('attachment',JSON.stringify(img));
            form_data.append('type',2);
            var url = $("#apiUrl").val() ;
           
      $.ajax({
        url: url+"/channel/"+channelId+"/attach?api_token="+api_token,
        type: 'POST',
        dataType: 'json',
        data:form_data,
        cache:false,
        contentType: false,
        processData: false,
      }).done(function(res) {
        console.log('attach',res);

        if(res.result=="true"){
          var data = res.response;
           var sd = {} ;
            sd.room = room ;
            sd.chat = data.chat ;
            sd.channel = data.channel ;
            sd.init = 0; 
            socket.emit('channel_chat',sd);

          
           // createTaskAttachment(res.response.task_attachs);
           // createTaskTableHistory(res.response.task_historys);
        }else{
          var error = JSON.stringify(res.errors);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
        }
      })
      .fail(function() {
        var error = JSON.stringify(res.errors);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
      })

           
      

        }   
        reader.readAsDataURL(file);

    
     $('#file-upload').val('');
  });
</script>
<script>
  $(function() {
      $(".fancybox").fancybox({
       openEffect : 'none',
        closeEffect : 'none'
      });
    });
 
</script>
@endsection







