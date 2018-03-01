@extends('main.layouts.main')

@section('style')

@endsection
@section('content-wrapper')

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <img class="icon-title" src="{{ asset('public/img/icon/icon_new_chat_room_2.png') }}"> {{ $title }}
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url($home) }}"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">{{ $title }}</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
               @include('layouts.error')
            </div>
        </div>

        <form id="create-form" action="" method="POST" enctype="multipart/form-data" >
            {{ csrf_field() }} 
            @if ( isset($method) && ($method == 'PUT') )
                {{ method_field('PUT') }}
            @endif
        <div class="row">
            <div class="col-sm-6">
                <div class="box box-primary">
                <div class="box-header with-border">
                  
                  
                </div>
                <!-- /.box-header -->
                <!-- form start -->
              
                    
                  <div class="box-body">
                        
                            
            
                    <div class="col-sm-12">
                      
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('chat.name')</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="@lang('main.name')"  value="{{ isset($edit) ? $data['name'] : old('name') }}" >
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('chat.description')</label>
                        <input type="text" class="form-control" id="description" name="description" placeholder="@lang('main.description')"  value="{{ isset($edit) ? $data['description'] : old('description') }}" >
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('chat.type')</label>
                        <select class="form-control" id="type" name="type" >
                           
                            @foreach($channelTypes as $channelType)
                            <option value="{{ $channelType['id'] }}" @if(isset($edit)&&$data['type']==$channelType['id'] ) selected="" @endif >{{ $channelType['name'] }}</option>
                            @endforeach

                        </select>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('chat.icon')</label>
                        <div id="channel_type" style="width:100%;border: 1px solid #ccc;height:35px;padding:5px;" >
                          <div class="pull-right"><i class="fa fa-caret-down"></i></div>
                          <div class="channel_type_value">
                            <i class=" fa fa-{{ (isset($edit)&&$data['icon'] ) ? $data['icon'] :  $channelIcons[0]}}"></i>
                          </div>
                          
                          <input type="hidden" id="icon" name="icon" value="{{ (isset($edit)&&$data['icon'] ) ? $data['icon'] :  $channelIcons[0]}}">
                        </div>
                        <style type="text/css">
                          #channel_type_list li{ list-style-type: none;height:35px;padding:5px;  }
                           #channel_type_list li:hover{ background: #00c0ef; }
                        </style>
                        <ul id="channel_type_list" style="width:100%;border: 1px solid #ccc;padding:0;">
                            @foreach($channelIcons as $channelIcon)
                            <li value="{{$channelIcon}}" @if($channelIcon=="book") class="active" @endif ><i class="fa fa-{{$channelIcon}}"></i></li>
                            @endforeach
                        </ul>
                       
                      </div>
                      
                    </div>
                  </div>
                  <!-- /.box-body -->

                  <div class="box-footer">
                   
                  </div>
                
              </div>
            </div>  
        </div>
        

        <div class="row">
            <div class="col-sm-12">
                <!-- <a href="{{ url($route) }}" class="btn btn-danger"> <i class="fa fa-reply"></i>
                    ยกเลิก</a> -->
                <button type="submit" id="btn_save"  class="btn btn-primary" > <i class="fa fa-save"></i>
                    @lang('main.btn_save')</button>
              
                <a href="{{ $urlBack }}"  class="btn btn-danger" > <i class="fa fa-close"></i>
                    @lang('main.btn_cancel')</a>
         
            </div>
        </div>
        </form>
    </section>

@endsection

@section('javascript')
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ url('js/utility/main.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/utility/validate.js') }}"></script> 
<script type="text/javascript">
   $("#channel_type_list").hide();
  $("#channel_type").on('click',function(){
    $("#channel_type_list").toggle();
  });

  $("#channel_type_list li").on('click',function(){
       var val =  $(this).attr("value");
       $(".channel_type_value").html("<i class=\" fa fa-"+val+"\"></i>");
       $("#channel_type_list").hide();
       $("#icon").val(val);

  });


  $(function() {
    $("#create-form").validate({
      rules: {
        name: {
          required: true,
          minlength: 2,
          @if (!isset($edit)) 
          remote: {
                    url: "{{ url('api/'.$domainId.'/channel/validate/name') }}?api_token="+api_token,
                    type: "post",
                    data: {
                        username: function() {
                            return $("#name").val();
                        }
                    }
                }
          @endif
        },
        description: {
           maxlength: 255,
        }
      },
      messages: {
        name: {
          required: "@lang('chat.validate_name_require')",
          minlength: "@lang('chat.validate_name_minlength')",
          remote: "@lang('chat.validate_name_remote')"
        },
        description: {
          maxlength: "@lang('chat.validate_desc_maxlength')"
        }
      },
    
      submitHandler: function (form) {
             $.ajax({
                 type:"{{ isset($edit) ? 'PUT' : 'POST' }}",
                 url : '{{ url($action) }}',
                 data: $(form).serialize(),
                 success: function (data) {
                    console.log(data,typeof data.response);
                    if(data.result=="true"){

                       
                        window.location.href= " {{ url($route) }}"+ @if (isset($edit)) ''  @else "/"+data.response.channel_id  @endif ;
                    }else{
                       var error = JSON.stringify(data.errors);
                      swal(
                        @lang('main.error'),
                        error,
                        'error'
                      )
                    }
                 }
             });
             return false; // required to block normal submit since you used ajax
         }
    });

    

  });


// $(function() {
//   var $win = $(window); // or $box parent container
//     var $box = $("#channel_type_list,#channel_type_value");
//     $win.on("click.Bst", function(event){ 
//       if ( $box.has(event.target).length == 0 &&!$box.is(event.target)){
//         $("#channel_type_list").html('');
//       }
//     });
// });

</script>
@endsection







