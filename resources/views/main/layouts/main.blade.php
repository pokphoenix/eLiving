<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>eLiving</title>
  <link rel="shortcut icon" type="image/icon" href="{{ url('public/img/favicon.ico') }} "/>


  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ url('bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ url('bower_components/font-awesome/css/font-awesome.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ url('bower_components/Ionicons/css/ionicons.min.css')}}">

  @yield('style')

  <!-- Theme style -->
  <link rel="stylesheet" href="{{ url('dist/css/AdminLTE.min.css')}}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ url('dist/css/skins/_all-skins.min.css')}}">
  <!-- Morris chart -->
  <link rel="stylesheet" href="{{url('bower_components/morris.js/morris.css')}}">
  <!-- jvectormap -->
  <link rel="stylesheet" href="{{url('bower_components/jvectormap/jquery-jvectormap.css')}}">
  <!-- Date Picker -->
  <link rel="stylesheet" href="{{url('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{url('bower_components/bootstrap-daterangepicker/daterangepicker.css')}}">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="{{url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}">

  <link rel="stylesheet" href="{{url('plugins/sweetalert2/sweetalert2.min.css')}}">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">


  
  <link rel="stylesheet" href="{{ url('dist/css/custom.css')}}">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 
    @include('main.layouts.header')
  <!-- Left side column. contains the logo and sidebar -->

    @include('main.layouts.sidebar')

  

  <!-- Content Wrapper. Contains page content -->
    @include('main.layouts.content')
    

   @include('main.layouts.footer')
  
  
  
   @include('main.layouts.control-sidebar')
 
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="{{url('bower_components/jquery/dist/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{url('bower_components/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="{{url('bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- Morris.js charts -->
<script src="{{url('bower_components/raphael/raphael.min.js')}}"></script>
<script src="{{url('bower_components/morris.js/morris.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{url('bower_components/jquery-sparkline/dist/jquery.sparkline.min.js')}}"></script>
<!-- jvectormap -->
<script src="{{url('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
<script src="{{url('plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>


<!-- jQuery Knob Chart -->
<script src="{{url('bower_components/jquery-knob/dist/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{url('bower_components/moment/min/moment.min.js')}}"></script>
<script src="{{url('bower_components/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
<!-- datepicker -->
<script src="{{url('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}"></script>
<!-- Slimscroll -->
<script src="{{url('bower_components/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{url('bower_components/fastclick/lib/fastclick.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{url('dist/js/adminlte.min.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="{{url('dist/js/pages/dashboard.js')}}"></script> -->
<!-- AdminLTE for demo purposes -->
<script src="{{url('dist/js/demo.js')}}"></script>

<!-- Sweet Alert -->
<script src="{{url('plugins/sweetalert2/sweetalert2.min.js')}}"></script>

<script type="text/javascript">
    $(function() {
        $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
            $("#success-alert").slideUp(500);
        });
    });
</script>
<script type="text/javascript" src=" {{ url('js/utility/ajax.js') }} "></script>
@if(isset($domainId))
<script src="{{ url('plugins/socketio/socket.io-1.4.5.js') }}"></script>
<script type="text/javascript">
// var io_connect =  window.location.hostname+':8080' ;

 var io_connect =  '{{ Service::SOCKET_URL }}' ;


var socket = io(io_connect);
var user_id = "{{ Auth()->user()->id }}" ;
var api_token =  "{{ Auth()->user()->api_token }}";
@foreach(Auth()->user()->getChannelJoin() as $channel)
    room =  "{{ $domainId.'-'.$channel->id }}" ;
    socket.emit('subscribe',room);
@endforeach
@foreach(Auth()->user()->getContact(true) as $channel)
    room =  "{{ $domainId.'-'.$channel->channel_id }}" ;
    socket.emit('subscribe',room);
@endforeach
socket.on('channel_chat', function(data){
  if (typeof data==='string'){
      data = JSON.parse(data);
  }
   // console.log('[channel_chat][main] ',data);
  if(data.channel!=null){

    if (typeof data.channel==='string'){
      data.channel = JSON.parse(data.channel);
    }
    if (typeof data.chat==='string'){
      data.chat = JSON.parse(data.chat);
    }


    createChatPopUp(data.channel).then(function(){
      if (typeof data.chat.length!=='undefined'){
         for (var i =0 ; i<data.chat.length;i++){
            addChatMessage(data.chat[i]);
         }
      }else{
        addChatMessage(data.chat);
      }
    });
  }
 
  

  
})

function addChatMessage(data){
  var dfd = $.Deferred();
  //console.log('[addChatMessage]',data);





  if (data.created_by == user_id ){
      msg = "<div class=\"direct-chat-msg right\"> "+
                  "<div class=\"direct-chat-info clearfix\">"+
                    "<span class=\"direct-chat-name pull-right\">"+data.first_name+" "+data.last_name+"</span>"+
                    "<span class=\"direct-chat-timestamp pull-left\">"+moment.unix(data.updated_ts).format("D/MM/YYYY HH:mm")+"</span>"+
                  "</div>"+
                  "<img class=\"direct-chat-img\" src=\"{{ url('public/img/default_profile.jpg')}}\" alt=\"Message User Image\">"+
                  "<div class=\"direct-chat-text\">"+data.text+
                  "</div></div>";
  }else{
      msg = "<div class=\"direct-chat-msg\">"+
                  "<div class=\"direct-chat-info clearfix\">"+
                    "<span class=\"direct-chat-name pull-left\">"+data.first_name+" "+data.last_name+"</span>"+
                    "<span class=\"direct-chat-timestamp pull-right\">"+moment.unix(data.updated_ts).format("D/MM/YYYY HH:mm")+"</span>"+
                  "</div>"+
                  "<img class=\"direct-chat-img\" src=\"{{ url('public/img/default_profile.jpg')}}\" alt=\"Message User Image\">"+
                  "<div class=\"direct-chat-text\">"+data.text+
                  "</div></div>";
  }
  var chatBox ;
  var chatBoxId = parseInt(data.channel_id);
  $(".content-wrapper").find('.chat-box-id').each(function(){
      if(parseInt($(this).val())==chatBoxId){
        chatBox = $(this).closest('.chat-box-info').find('.direct-chat-messages') ;
        if(chatBox.length>0){
            chatBox.append(msg);
        }
      }
   }) ;

  

  dfd.resolve(data);
  return dfd.promise();
}

function createChatPopUp(data){
  var dfd = $.Deferred();
  //console.log('createChatPopUp',data);
  var newBox = true;
  var chatBoxId = parseInt(data.id);
  var channelName = data.name ;
  if(data.direct_message==1){
    if (user_id == data.u1_user_id){
      channelName = data.u2_first_name+" "+data.u2_last_name;
    }else{
      channelName = data.u1_first_name+" "+data.u1_last_name;
    }
  }
 
 
  //--- ถ้ามีหน้าต่าง แชทมากกว่า 2 อันให้ลบอันแรกสุดออกก่อน
  if($(".content-wrapper").find('.chat-box-info').length > 2) {
      $(".content-wrapper").find('.chat-box-id:lt(1)').each(function(){
           $(this).remove();
      });
  }

  var right = $(".content-wrapper").find('.chat-box-info').length*270;
 //  right += $(".sidebar").width();
 // console.log($(".sidebar").width());

   $(".content-wrapper").find('.chat-box-id').each(function(){
       if(parseInt($(this).val())==chatBoxId){
          newBox = false ;
       }
   }) ;

   // if(data.created_by==user_id){
   //     newBox = false ;
   // }


  
  var chatUrl = $("#baseUrl").val()+'/channel/'+chatBoxId;

  var chatBox =  "<div class=\"box box-primary  direct-chat direct-chat-primary chat-box-info\" style=\"right:"+right+"px;z-index:900;\">"+
                        "<div class=\"box-header with-border\">"+
                          "<a href=\" "+chatUrl+" \"><h3 class=\"box-title\">"+channelName+"</h3></a>"+
                          "<input type=\"hidden\" class=\"chat-box-id\" value=\""+chatBoxId+"\">"+
                          "<div class=\"box-tools pull-right\">"+
                              
                             
                              // "<button type=\"button\" class=\"btn btn-box-tool\" "+
                              // " data-toggle=\"tooltip\" title=\"Contacts\" data-widget=\"chat-pane-toggle\">"+
                              //     "<i class=\"fa fa-comments\"></i></button>"+
                              "<button type=\"button\" class=\"btn btn-box-tool btn-box-remove\" >"+
                                  "<i class=\"fa fa-times\"></i></button>"+
                          "</div>"+
                        "</div>"+
                        "<div class=\"box-body\">"+
                            "<div class=\"direct-chat-messages\">"+
                            "</div>"+
                            "<div class=\"direct-chat-contacts\">"+
                            "  <ul class=\"contacts-list\"> "+
                            "  </ul>"+
                            "</div>"+
                        "</div>"+
                        "<div class=\"box-footer\">"+
                          "<form class=\"btn-chat-message\">"+
                        "   <div class=\"input-group\">"+
                              "<input type=\"text\" name=\"message\" placeholder=\"Type Message ...\" class=\"form-control chat-box-message\">"+
                                  "<span class=\"input-group-btn\">"+
                                    "<button type=\"submit\" class=\"btn btn-primary btn-flat \">Send</button>"+
                                  "</span>"+
                            "</div>"+
                            "</form>"+
                        "</div>"+
                   
                    "</div>";
  if(newBox){
      $(".content-wrapper").append(chatBox);
  }
  dfd.resolve(data);
  return dfd.promise();
}

$(document).on("submit",".btn-chat-message",function(){ 
  var ce = $(this).closest('.chat-box-info') ;
  var channelId = ce.find('.chat-box-id').val() ;
  var text = ce.find('.chat-box-message').val();
  var route = "/channel/"+channelId+"/chat?api_token="+api_token ;
  var data = {
    text:text ,
    type:1
  } ;
  console.log(route,data);
  ajaxPromise('POST',route,data).done(function(data){
        // parent.remove()
        var sd = {} ;
        sd.room = room ;
        sd.chat = data.chat ;
        sd.channel = data.channel;
        sd.init = 0; 
        socket.emit('channel_chat',sd);
        ce.find('.chat-box-message').val('');
  }).fail(function(txt) {
     var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
  });
  return false;
});

$(document).on("click",".btn-box-remove",function(){
  $(this).closest('.chat-box-info').remove() ;
})

$(document).on("click",".sidebar-remove-contact",function(){
  $(this).closest('li').remove() ;
  var channelId = $(this).data('id') ;
  var route = "/channel/"+channelId+"/remove-contact?api_token="+api_token ;
 
  ajaxPromise('DELETE',route,"").done(function(data){
    
  }).fail(function(txt) {
      swal(
        'Error...',
        'Some thing wrong',
        'error'
      )
  });


})

$(document).on("click","p.message > .name",function(){
  uid = $(this).closest('.item').find(".msg-item-user-id").val();
  if(uid==user_id){
    return false;
  }

  var route = "/channel/direct_chat?api_token="+api_token ;
  var data = {
    name:"" ,
    type:0,
    direct_message:1,
    uid : uid 
  } ;
  ajaxPromise('POST',route,data).done(function(data){
        // parent.remove()
        var sd = {} ;
        sd.room = room ;
        sd.chat = data.chat ;
        sd.channel = data.channel;
        sd.init = 1; 
        socket.emit('channel_chat',sd);
  }).fail(function(txt) {
      var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
  });
})
</script>
<script type="text/javascript">
  $(".notifications-menu").on("click",function(){
     if(!$(this).hasClass('open')){
        $(this).find("span.label").hide();

        $.ajax({
                  method:'PUT',
                  url: "{{ url('api/notification/seen?api_token=') }}"+api_token,
                  data: null
              })
              .done(function(html) {
                  console.log(html);
              })
              .fail(function() {
                  
              })
     }
    
  })
  function changeLang(ele){
    var lang = ele.data('lang');
    
      $.ajax({
          method:'GET',
          url: "{{ url('api/lang') }}/"+lang,
      })
      .done(function(html) {
          location.reload();

          var setLang = (lang=='th') ? 'en' : 'th' ;
          console.log(setLang);
          ele.data('lang',setLang);
          ele.text(setLang.toUpperCase());
      })
      .fail(function() {
          
      })
  }
</script>
  
@endif

@if(auth()->user()->checkApprove())
  <script src="{{ url('plugins/onesignal/OneSignalSDK.js') }}" async=""></script>
  <script type="text/javascript">
    var OneSignal = window.OneSignal || [];
    // OneSignal.push(["init", {
    //   appId: "2da81194-c514-48e2-8123-ffbe122194a0",
    //   notifyButton: {
    //     enable: true, /* Required to use the Subscription Bell */
    
    //     size: 'small', /* One of 'small', 'medium', or 'large' */
    //     theme: 'default', /* One of 'default' (red-white) or 'inverse" (white-red) */
    //     position: 'bottom-left', /* Either 'bottom-left' or 'bottom-right' */
    //     offset: {
    //         bottom: '0px',
    //         left: '0px', /* Only applied if bottom-left */
    //         right: '0px' /* Only applied if bottom-right */
    //     },
    //     prenotify: true,  Show an icon with 1 unread message for first-time site visitors 
    //     showCredit: false, /* Hide the OneSignal logo */
    //     displayPredicate: function() {
    //         return OneSignal.isPushNotificationsEnabled()
    //             .then(function(isPushEnabled) {
    //                 return !isPushEnabled;
    //             });
    //     }
    //   },
    //   welcomeNotification: {
    //     "title": "My Custom Title",
    //     "message": "Thanks for subscribing!",
    //     // "url": "" /* Leave commented for the notification to not open a window on Chrome and Firefox (on Safari, it opens to your webpage) */
    //   },
    // }]);
    // OneSignal.push(function() {
      // OneSignal.showHttpPrompt();
      // OneSignal.getUserId(function(userId) { 
      //     console.log('one singal id : '+userId);
      // });
      // var isPushSupported = OneSignal.isPushNotificationsSupported();
      // if (isPushSupported) {
      //     // Push notifications are supported
      //     OneSignal.isPushNotificationsEnabled().then(function(isEnabled) {
      //       if (isEnabled){
      //           OneSignal.getUserId(function(userId) {
      //             $.ajax({
      //                 method:'POST',
      //                 url: "{{ url('api/notification?api_token=') }}"+api_token,
      //                 data: { noti_player_id:userId }
      //             })
      //             .done(function(html) {
                      
      //             })
      //             .fail(function() {
                      
      //             })
      //             // (Output) OneSignal User ID: 270a35cd-4dda-4b3f-b04e-41d7463a2316    
      //           });
      //           OneSignal.once('subscriptionChange', function (isSubscribed) {
      //             if (isSubscribed){
      //               //
      //               /* These examples are all valid */
                   
      //             }else{
                    
      //             }
      //             console.log("The user's subscription state is now:", isSubscribed);
      //           });
      //       }else{
      //         console.log("Push notifications are not enabled yet.");      
      //       }
      //     });
      // }
    // });


    // var OneSignal = window.OneSignal || [];




    OneSignal.push(function() {
        OneSignal.init({
          appId: "{{ Service::ONESIGNAL_APP_ID }}" ,
           notifyButton: {
              enable: true,
              position: 'bottom-left', 
              displayPredicate: function() {
                  return OneSignal.isPushNotificationsEnabled()
                      .then(function(isPushEnabled) {
                          /* The user is subscribed, so we want to return "false" to hide the Subscription Bell */
                          return !isPushEnabled;
                      });
              },
          }
        });
         OneSignal.getUserId(function(userId) { 
          console.log('one singal id : '+userId);
         });
        var isPushSupported = OneSignal.isPushNotificationsSupported();
        if (isPushSupported) {
          // Push notifications are supported
          OneSignal.isPushNotificationsEnabled().then(function(isEnabled) {
          if (isEnabled){
              OneSignal.getUserId(function(userId) {
              $.ajax({
                  method:'POST',
                  url: "{{ url('api/notification?api_token=') }}"+api_token,
                  data: { noti_player_id:userId }
              })
              .done(function(html) {
                  
              })
              .fail(function() {
                  
              })
              // (Output) OneSignal User ID: 270a35cd-4dda-4b3f-b04e-41d7463a2316    
            });
              OneSignal.once('subscriptionChange', function (isSubscribed) {
                if (isSubscribed){
                  //
                  /* These examples are all valid */
                 
                }else{
                  
                }
                console.log("The user's subscription state is now:", isSubscribed);
            });


            
            }else{
              console.log("Push notifications are not enabled yet.");      
            }
        });

          
         
      } else {
          // Push notifications are not supported
          console.log('not supported');
      }


                   
    });
        
getMenuCount();
 function getMenuCount(){
  $(".badge-request-room,.badge-wait-user").hide();
   $.ajax({
        method:'GET',
        url: "{{ url('api/menu-count?api_token=') }}"+api_token,
        dataType:'json'
    })
    .done(function(html) {
        if(html.result=="true"){
          var r = html.response ;
            var c_request_room = (r.cnt_request_room>99)? '99+' : r.cnt_request_room ;
            var c_wait_approve = (r.cnt_wait_for_approve>99)? '99+' : r.cnt_wait_for_approve ;
            var c_task_new = (r.cnt_task_new>99)? '99+' : r.cnt_task_new ;
            var c_quotation_voted = (r.cnt_quotation_voted>99)? '99+' : r.cnt_quotation_voted ;
            var c_quotation_has_voting = (r.cnt_quotation_has_voting>99) ? '99+' : r.cnt_quotation_has_voting ;

            var c_all = ((r.cnt_quotation_voted+r.cnt_quotation_has_voting)>99) ? '99+' : r.cnt_quotation_has_voting ;

            if(c_request_room>0){
               $(".badge-request-room").find('.label').text(c_request_room);
               $(".badge-request-room").show();
            } 
            if(c_wait_approve>0){
               $(".badge-wait-user").find('.label').text(c_wait_approve);
               $(".badge-wait-user").show();
            }

            @if(Auth()->user()->hasRole('officer'))
            if(c_task_new>0){
               $(".badge-task-ex").find('.label').text(c_task_new).show();
            }
            @endif

            @if(Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('head.user'))
              if(c_quotation_voted>0){
                $(".badge-quotation").find('.label').text(c_quotation_voted);
                $(".badge-quotation").show();
              }
            @endif

            @if(!Auth()->user()->hasRole('officer')&&Auth()->user()->hasRole('head.user'))
              if(c_quotation_has_voting>0){
                $(".badge-quotation").find('.label').text(c_quotation_has_voting);
                $(".badge-quotation").show();
              }
            @endif

            @if(Auth()->user()->hasRole('officer')&&Auth()->user()->hasRole('head.user'))
              if(c_all>0){
                $(".badge-quotation").find('.label').text(c_all);
                $(".badge-quotation").show();
              }
            @endif

        }
    })
    .fail(function() {
        
    })

 }  
  </script>


  @endif

@yield('javascript')

</body>
</html>