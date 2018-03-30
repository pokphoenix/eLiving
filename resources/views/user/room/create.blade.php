@extends('main.layouts.main')


@section('style')
  <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-key"></i>@lang('main.room_management')
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('domain') }}"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('main.room_management')</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      
      <!-- Main row -->
      @if(isset($edit))
         
        <form  id="signup-form" role="form" method="POST" action="{{$apiUrl}}" enctype="multipart/form-data"  >
            {{ method_field('PUT') }}
             {{ csrf_field() }}
         
  
      @else
          <form  id="signup-form" role="form" method="POST" action="{{$apiUrl}}" enctype="multipart/form-data"  >
          {{ csrf_field() }}
      @endif

          <input type="hidden" id="room_id" value="{{ isset($edit) ? $roomId : ''  }} ">
     
      
      <div class="row">
        <div class="col-sm-12">
           @include('layouts.error')
        </div>
      </div>

      <div class="row">
      	<div class="col-sm-6">
      		<div class="box box-primary">
            
          
            	
              <div class="box-body">
					
					    
        
                <div class="col-sm-12">
                  @if(!isset($edit))
                  <div class="checkbox">
                      <label>
                        <input type="checkbox" id="is_run" name="is_run">
                        @lang('main.room_create_range')
                        
                      </label>
                    </div>
                  @endif
                   <div class="form-group single-room ">
                      <label for=""> @lang('main.room_display')</label>
                      <input type="text"  class="form-control" id="display_name" placeholder="(Ex. 100/1 a)"  value="{{ isset($edit) ? $data['name_prefix'].$data['name'].$data['name_surfix'] : old('name_prefix') }} " readonly="" >
                    </div>
                    <div class="form-group ">
                      <label for=""> @lang('main.room_prefix')</label>
                      <input type="text"  class="form-control" id="name_prefix" name="name_prefix" placeholder="(Ex. 100/ )"  value="{{ isset($edit) ? $data['name_prefix'] : old('name_prefix') }}" >
                    </div>
                    <div class="form-group single-room" >
                      <label for=""> @lang('main.room_number')</label>
                      <input type="text"  class="form-control" id="name" name="name" placeholder="(Ex. 1 )"  value="{{ isset($edit) ? $data['name'] : old('name') }}" >
                    </div>
                  <div id="run_room" class="row" style="display: none;">
                    
                    <div class="form-group col-sm-6" >
                      <label for=""> @lang('main.room_number_start')<small id="name_start"></small></label>
                      <input type="text"  class="form-control" id="number_start" name="number_start" placeholder="(Ex. 1 )"  value="" >
                    </div>
                     <div class="form-group col-sm-6">
                      <label for=""> @lang('main.room_number_end')<small id="name_end"></small></label>
                      <input type="text"  class="form-control" id="number_end" name="number_end" placeholder="(Ex. 100 )"  value="" >
                    </div>
                   
                  </div>

                    <div class="form-group ">
                      <label for=""> @lang('main.room_surfix')</label>
                      <input type="text"  class="form-control" id="name_surfix" name="name_surfix" placeholder="(Ex. a )"  value="{{ isset($edit) ? $data['name_surfix'] : old('name_surfix') }}" >
                    </div>
                   
                  
                  
                </div>
              </div>
             
            
          </div>
      	</div>
        
        @if(isset($edit))
        <div class="col-sm-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">@lang('main.residence')</h3>
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
                       <input type="text" class="form-control" id="search_user" data-action="{{ url('/api/'.$domainId.'/search/user?api_token=').auth()->user()->api_token }}" autocomplete="off">
                      <div class="input-group-btn">
                          <button type="button" class="btn btn-primary btn-flat"><i class="fa fa-search"></i>
                          </button>
                      </div>
                    </div>
                    <div class="search-append"></div>
                </div>
               
                <div class="col-sm-12">
                  <BR>
                    <table id="user-in-room-table" class="table table-bordered">
                      <thead>
                        <tr>
                          <th width="50"></th>
                          <th class="vm-ct" width="100">@lang('user.no')</th>
                          <th class="vm-ct">@lang('user.name')</th>
                          <th class="vm-ct">@lang('user.id_card')</th>
                        </tr>
                      </thead>

                      <tbody>
                        @if(isset($roomUser))
                          @foreach($roomUser as $key=>$ru)
                          <tr>
                            <td><button type="button" class="btn btn-danger btn-xs btn-user-in-room-del" >
                            <i class="fa fa-close"></i></button>
                            <input type="hidden" class="id-card" value="{{ $ru['id_card'] }}">
                            </td>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $ru['text_name'] }}</td>
                            <td>{{ $ru['id_card'] }}</td>
                          </tr>
                          @endforeach
                        @endif
                      </tbody>
                      
                    </table>
                </div>
              </div>
          </div>
        </div>
        @endif

        <div class="col-sm-12" style="height: 50px;">

         

           
           <button type="button" id="save" class="btn btn-primary">@lang('main.btn_save')</button>
           <button type="button" id="cancel" class="btn btn-danger" " >@lang('main.btn_cancel')</button>
           
        </div>
      
      </div>
      </form>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->

@endsection

@section('javascript')
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ url('js/utility/main.js') }}"></script> 
<!-- <script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script> -->
<script type="text/javascript" src="{{ url('js/utility/autocomplete.js') }}"></script> 
<!-- <script type="text/javascript" src="{{ url('js/utility/address.js') }}"></script>  -->
<script type="text/javascript" src="{{ url('js/utility/validate.js') }}"></script> 
<script type="text/javascript">
  $("#is_run").on("click",function(){
    if($(this).is(':checked')){
      $("#run_room").show();
      $(".single-room").hide();
    }else{
      $("#run_room").hide();
      $(".single-room").show();
    }
  })
  $("#cancel").on("click",function(){
      window.location = "{{ url($domainName.'/rooms') }}"
  })



  $("#name_prefix,#name,#name_surfix").on("input",function(){
    var prefix = $("#name_prefix").val();
    var txtStart = "("+prefix+""+$("#number_start").val()+")";
    $("#name_start").text(txtStart);
    var txtStart = "("+prefix+""+$("#number_end").val()+")";
    $("#name_end").text(txtStart);
    $("#display_name").val($("#name_prefix").val()+$("#name").val()+$("#name_surfix").val());
  });
  

  $("#number_start").on("input",function(){
    var prefix = $("#name_prefix").val();
    var txt = "("+prefix+""+$(this).val()+")";
    console.log(txt);
    $("#name_start").text(txt);
  });
  $("#number_end").on("input",function(){
    var prefix = $("#name_prefix").val();
    var txt = "("+prefix+""+$(this).val()+")";
    console.log(txt);
    $("#name_end").text(txt);
  });

  // $("#gen_room").on("click",function(){
  //     var prefix = $("#name_prefix").val();
  //     var start =  $("#number_start").val();
  //     var end $("#number_end").val();
  //     for (var i = start ; i<end ;i++){
  //         var txt = prefix+""+i ;
  //     }
  // })
  

  $("#save").on("click",function(){
      $("#signup-form").submit();
  })




 $(document).on("input","#search_user",function(e) { 
    ajaxSearchAutoComplete($(this)).then(function( res ) {
        // console.log("search_district",res);
        console.log("search_user",res);
        var tool = { thumbnail:false } ;
        userData(res).done(function(data){
          console.log(data);
            $(".search-append").html(data);
        })
    })
});
$(document).on("click",".my-autocomplete-li",function(e) { 
  var id = $.trim($(this).find('.search-id').val()) ;
  var text = $.trim($(this).find('.search-text').val()) ;
  var idCard = $.trim($(this).find('.search-id-card').val()) ;
  console.log('search-id',id,text,idCard);

  var html = "<tr>"+
            "<td><button type=\"button\" "+
            " class=\"btn btn-danger btn-xs btn-user-in-room-del\" >"+
            "<i class=\"fa fa-close\"></i></button>"+
            "<input type=\"hidden\" class=\"id-card\" value=\""+(idCard)+"\">"+
            "</td>"+
            "<td></td><td>"+text+"</td><td>"+idCard+"</td></tr>";
  // var route = "/task/"+cardId+"/member/"+id+"?api_token="+api_token
  // ajaxPromise('POST',route,null).done(function(data){
  //       socket.emit('task',data);
  //       createTaskMember(data.task_members,data.task.id);
  //       createTaskTableHistory(data.task_historys);
  //       createTaskCard(data.task);
  //       createTaskSetInit(data.status);
  //       $("#modal_task_member").modal('toggle');
  // });
  $("#user-in-room-table tbody").append(html);
  // $(this).parent().parent().parent().find("#search_user").val($(this).find('h5').text()) ;
  $(this).parent().remove();
  $("#user-in-room-table tbody tr td:nth-child(2)").each(function (i) {
      var j = ++i;
      $(this).text(j);
  });
  $("#search_user").val('');
});

$(document).on("click",".btn-user-in-room-del",function(event) {
    var rows = $(this).closest("tr") ; 
    var userIdCard = $.trim(rows.find('.id-card').val());
    var roomId =  $.trim($("#room_id").val())  ;
    console.log(userIdCard);
    if(userIdCard){
        var route = "/rooms/"+roomId+"/"+userIdCard+"?api_token="+api_token;
        ajaxPromise('POST',route,{'_method':'DELETE'}).done(function(){
            rows.remove();
             $("#user-in-room-table tbody tr td:nth-child(2)").each(function (i) {
                var j = ++i;
                $(this).text(j);
            });
        })
    }

   
  });

function userData(res){
   var dfd = $.Deferred();
 
 
  elewidth = res.ele.innerWidth();
  $(res.ele).next('ul').remove();
  if(res.data.length>0){
    var autocomplete = "<ul class=\"my-autocomplete-ul\" style=\"width:"+elewidth+"px; \">";
      for(var i=0;i<res.data.length;i++){
        autocomplete+= "<li class=\"my-autocomplete-li \"> " ;
       
        autocomplete+= "<h5>&nbsp;"+res.data[i].text+"</h5>";

        if(typeof res.data[i].id !="undefind"){
          autocomplete+= "<input type=\"hidden\" class=\"search-id\" value=\""+$.trim(res.data[i].id)+"\">";
          autocomplete+= "<input type=\"hidden\" class=\"search-id-card\" value=\""+$.trim(res.data[i].id_card)+"\">";
          autocomplete+= "<input type=\"hidden\" class=\"search-text\" value=\""+$.trim(res.data[i].text)+"\">";
        }

       
        autocomplete+= "</li>";
      }  

      autocomplete+='</ul>';

    // res.ele.after().html(autocomplete);

    // $(res.ele).after(autocomplete);
     dfd.resolve(autocomplete);
  }else{
     dfd.reject("");
  }
  return dfd.promise();
}

$(function() {
    $("#signup-form").validate({
      
      submitHandler: function (form) {

        var roomId =  $.trim($("#room_id").val())  ;
        var data = { user:[] };
        $("#user-in-room-table tbody tr").each(function(){
          var idCard = $(this).find('.id-card').val() ;
         

          var row =  { 
                 'room_id':roomId
                ,'id_card':idCard
          }
          // console.log('[getUserData]',row); 
          data.user.push(row);
        });



        var form_data = new FormData($("#signup-form")[0]);
        form_data.append('user-room',JSON.stringify(data.user));
      

             $.ajax({
                type: "POST",
                url: form.action ,
                data: form_data ,
                processData: false,
                contentType: false,
                success: function (data) {
                    console.log(data,typeof data.response);
                    if(data.result=="true"){
                      swal({
                        type: 'success',
                        title:  @if(isset($edit)) "@lang('room.update_success')"  @else "@lang('room.create_success')" @endif,
                        showConfirmButton: false,
                        timer: 1500
                      })

                      setTimeout(function(){ window.location.href = "{{ url($domainName.'/'.$baseRoute) }}"; }, 1600);
                      
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
             return false; // required to block normal submit since you used ajax
         }

    });

  });
</script>

@endsection		
