@extends('main.layouts.main')


@section('style')
  <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        @if(isset($waitUser))
        <img class="icon-title" src="{{ asset('public/img/icon/icon_user_wait_for_approve_2.png') }}">
        @else
        <i class="fa fa-user"></i>
        @endif
        {{ $title }}
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active"> {{ $title }}</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      
      <!-- Main row -->
      @if(isset($edit))
         
        <form  id="signup-form" role="form" method="POST" action="{{$route}}" enctype="multipart/form-data"  >
            {{ method_field('PUT') }}
             {{ csrf_field() }}
         
  
      @else
          <form  id="signup-form" role="form" method="POST" action="{{$route}}" enctype="multipart/form-data"  >
          {{ csrf_field() }}
      @endif


      <input type="hidden" id="is_review" name="is_review" value="1">
      
      <div class="row">
        <div class="col-sm-12">
           @include('layouts.error')
        </div>
      </div>

      <div class="row">
      	<div class="col-sm-6">
      		@include('admin.widgets.formation')
      	</div>
        <div class="col-sm-6">
          @if(isset($edit)&&isset($address['id']))
          <input type="hidden" id="address_id" name="address_id" value="{{ $address['id'] }}">
          @endif
           @include('admin.widgets.address')
        </div>
        <div class="col-sm-12"></div>
        <div class="col-sm-6">
             @include('admin.widgets.role')
        </div>
        <div class="col-sm-6">
            @include('admin.widgets.ban')
            
        </div>
        <div class="col-sm-12">
            @include('admin.widgets.room')
        </div>

       
        @if(isset($edit)&&isset($docs))  
        <div class="col-sm-12">
             @include('admin.widgets.attachment-list')
        </div>
        @endif
        <div class="col-sm-12">
            <div class="form-group">
              <label for="exampleInputPassword1">@lang('user.remark')</label>
              <textarea class="form-control" rows=1 id="remark" name="remark" placeholder="@lang('user.remark')" >{{ (isset($edit)&&isset($data['remark'])) ? $data['remark'] : old('remark') }}</textarea>
            </div>
        </div>

        

        <div class="col-sm-12" style="height: 50px;">

         
          @if(!isset($requestRoom))
           <input type="hidden" id="approve" name="approve" >
           <button type="button" id="save" class="btn btn-primary">@lang('main.btn_save')
             <i class="fa fa-spinner fa-spin fa-fw" style="display:none;"></i>
 
           </button>


          @if(!isset($isApprove)||isset($isApprove)&&$isApprove!=1)
            <button type="button" id="save_approve" class="btn btn-success">@lang('main.btn_save_and_approve')
            <i class="fa fa-spinner fa-spin fa-fw" style="display:none;"></i>
 
            </button>

          @endif
          @endif
            <a href="{{ url($domainName.'/'.$routePath) }}" id="cancel" class="btn btn-danger">@lang('main.btn_cancel')</a>
          
        </div>
       
        <div class="col-sm-12">
             @include('admin.widgets.attachment')
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
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ url('js/utility/autocomplete.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/utility/address.js') }}"></script> 
<!-- <script type="text/javascript" src="{{ url('js/utility/validate.js') }}"></script>  -->
<script type="text/javascript" src="{{ url('js/user/room.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/user/attachment.js') }}"></script> 
<script type="text/javascript">


// $("#is_ban").on("click",function(){
//     if($(this).is(':checked')){

//     }
// })



@if(!isset($edit))

$(document).on("input","#id_card",function(e) {
  // if($(this).val().length==13){
    var route = "{{ url('api/search/user-data') }}" ;
    var idCard = $(this).val();
    var data ={id_card:idCard} ;
      $.ajax({
        url: route,
        type: 'POST',
        dataType: 'json',
        data:data
      })
      .done(function(response) {
        // console.log(response);
        if(response.result=="true"){
            var res = response.response.user ;
            if(response.response.user.length > 0 ) {
              swal({
                title: 'Edit Data / แก้ไขข้อมูล ?',
                text: " Duplicate Citizen id / เลขบัตรประชาชนซ้ำในระบบ",
                type: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm Edit / ยืนยันแก้ไข' ,
                cancelButtonText: 'Cancel / ยกเลิก'
              }).then((result) => {
                if (result.value) {
                  window.location = "{{ url($domainName.'/'.$routePath) }}"+"/"+idCard+"/edit" ;
                }
              })
            }
            
        }else{
          
        }
      })
      .fail(function() {
       
      })
  // }
  
});
@endif

  $("#save").on("click",function(){
      $("#approve").val(false);

      $("#signup-form").submit();
  })
  $("#save_approve").on("click",function(){
      $("#approve").val(true);
      $("#signup-form").submit();
  })

$(function() {
    $("#signup-form").validate({
      rules: {
        first_name: {
          required: true,
          maxlength: 255
        },
        last_name: {
          required: true,
          maxlength: 255
        },
        id_card: {
          required: true,
          // minlength: 13,
          maxlength: 13

          @if(!isset($edit))
          ,remote: {
                    url: "{{ url('api/validate/idcard') }}",
                    type: "post",
                    data: {
                        id_card: function() {
                            return $("#id_card").val();
                        }
                    }
                }
          @endif
        },
        // room:"required",
        address: {
          maxlength: 255
        },
        zip_code:{
          maxlength: 255
        },
        // tel:"required",
        // company_name:"required",
        // domain:"required",
        // tel:"required",
        email: {
          required: true,
          email: true,
          maxlength: 255
        },
      },
      messages: {
        first_name: (($("#app_local").val()=='th') ? 'ชื่อไม่ถูกต้อง' : 'Wrong firstname' ),
        last_name: (($("#app_local").val()=='th') ? 'นามสกุลไม่ถูกต้อง' : 'Wrong lastname' ),
        id_card:{
            required: (($("#app_local").val()=='th') ? 'เลขบัตรประชาชนไม่ถูกต้อง' : 'Wrong ID card' ),
            remote: (($("#app_local").val()=='th') ? 'เลขบัตรประชาชนซ้ำในระบบ' : 'ID card already exits' )
        },
        address:(($("#app_local").val()=='th') ? 'ที่อยู่ไม่ถูกต้อง' : 'Wrong address' ),
        district_id:(($("#app_local").val()=='th') ? 'ตำบลไม่ถูกต้อง' : 'Wrong district' ),
        province_id:(($("#app_local").val()=='th') ? 'จังหวัดไม่ถูกต้อง' : 'Wrong province' ),
        amphur_id:(($("#app_local").val()=='th') ? 'อำเภอไม่ถูกต้อง' : 'Wrong amphur' ),
        zip_code:(($("#app_local").val()=='th') ? 'รหัสไปรษณีไม่ถูกต้อง' : 'Wrong zipcode' ),
        email: (($("#app_local").val()=='th') ? 'อีเมลไม่ถูกต้อง' : 'Wrong email address' ),
      },
      highlight: function ( element, errorClass, validClass ) {
      
        $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
       
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
       
      }
      ,submitHandler: function (form) {
        var idCard =  $.trim($("#id_card").val())  ;
        var data = { user:[],'file_type':[] };
        $("#user-in-room-table tbody tr").each(function(){
          var roomId = $(this).find('.room-id').val() ;
          var roomApprove = $(this).find('.room-approve').val() ;
  
        
        if($("#approve").val()=="true") { 
           roomApprove =1 ; 
          $("#save_approve").find('.fa-spinner').show();
        }else{
          $("#save").find('.fa-spinner').show();
        }



          var row =  { 
                 'room_id':roomId
                ,'id_card':idCard
                ,'room_approve':roomApprove
          }
         
          data.user.push(row);
        });

        $("#append_upload tr").each(function(){
           $(this).find('.upload-file-type').val() ;
          data.file_type.push($(this).find('.upload-file-type').val());
        })

     


        var form_data = new FormData($("#signup-form")[0]);
        form_data.append('user-room',JSON.stringify(data.user));
        form_data.append('file-type',JSON.stringify(data.file_type));

        console.log('form_data',form_data);
       
             $.ajax({
                 type: "POST",
                 url: form.action ,
                 data: form_data ,
                 processData: false,
                 contentType: false,
                 success: function (data) {
                    $("#save_approve,#save").find('.fa-spinner').hide();
                    if(data.result=="true"){
                      swal({
                          title:  @if(isset($edit)) "@lang('main.update_success')" @else  "@lang('main.create_success')" @endif,
                          type: 'success',
                          showCancelButton: false,
                          confirmButtonText: "@lang('main.ok')"
                        }).then((result) => {
                          if (result.value) {
                            window.location.href = "{{ url($domainName.'/'.$routePath) }}";
                          }
                        })

                     
                      
                    }else{
                      var error = JSON.stringify(data.errors);
                      swal(
                        'Error...',
                        error,
                        'error'
                      )
                    }
                 }

             }).fail(function() {
              $("#save_approve,#save").find('.fa-spinner').hide();
                      swal(
                        'Error...',
                        "@lang('main.something_when_wrong')",
                        'error'
                      )
            });
             return false; // required to block normal submit since you used ajax
         }

    });

  });
</script>

@endsection		
