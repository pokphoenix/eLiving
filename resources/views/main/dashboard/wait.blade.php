@extends('main.layouts.main')


@section('style')
  <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Wait for Approve
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Domain</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      

      <!-- Main row -->
        <form  id="signup-form" role="form" method="POST" action="{{$action}}" enctype="multipart/form-data"  >
            {{ method_field('PUT') }}
             {{ csrf_field() }}
         
  
      @if($data['is_review']==1)
      <div class="row">
          <div class="col-sm-12">
            <div class="callout callout-info">
              <h4>Reviewed</h4>
            </div>
          </div>
      </div>
      @endif
      

     
      
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
          @include('admin.widgets.address')
        </div>
        <div class="col-sm-12">
            @include('admin.widgets.room')
        </div>
        <div class="col-sm-12">
            @include('admin.widgets.attachment')
        </div>
        
        <div class="col-sm-12">
            <div class="form-group">
              <label for="exampleInputPassword1">Remark</label>
              <textarea class="form-control" rows=1 id="remark" readonly="" name="remark" placeholder="Remark" >{{ (isset($edit)&&isset($data['remark'])) ? $data['remark'] : old('remark') }}</textarea>
            </div>
        </div>

        <div class="col-sm-12" style="height: 50px;">

         

           <input type="hidden" id="approve" name="approve" >
           <button type="button" id="save" class="btn btn-primary">Save</button>
          
          
        </div>
          
        @if(isset($edit)&&isset($docs))  
      
        <div class="col-sm-12">
            @include('admin.widgets.attachment-list')
        </div>
        @endif
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
<script type="text/javascript" src="{{ url('js/utility/validate.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/user/room.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/user/attachment.js') }}"></script> 
<script type="text/javascript">


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
        first_name: "required",
        last_name: "required",
        id_card: {
          required: true,
          // minlength: 13,
          maxlength: 13
        },
        // room:"required",
        address:"required",
        // district_name:"required",
        // province_name:"required",
        // amphur_name:"required",
        zip_code: {
          required: true,
          minlength: 5,
          maxlength: 5
        },
        // tel:"required",
        // company_name:"required",
        // domain:"required",
        // tel:"required",
        email: {
          required: true,
          email: true
        },
      },
      messages: {
        first_name: "Please enter your firstname",
        last_name: "Please enter your lastname",
        id_card:{
                  required: 'Please enter your  ID card',
                  remote: 'ID card already exits'
        },
        address:"Please enter your  address",
        // district_name:"Please enter your  district",
        // province_name:"Please enter your  province",
        // amphur_name:"Please enter your  amphur",
        zip_code:"Please enter your  zipcode",
      
        email: "Please enter a valid email address",
     
        
      },
      submitHandler: function (form) {
        var idCard =  $.trim($("#id_card").val())  ;
        var data = { user:[] };
        $("#user-in-room-table tbody tr").each(function(){
          var roomId = $(this).find('.room-id').val() ;
          var roomApprove = $(this).find('.room-approve').val() ;
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
        form_data.append('file-type',data.file_type);

        

        console.log('form_data',form_data);

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
                        title:  @if(isset($edit)) 'แก้ไขข้อมูลผู้ใช้สำเร็จ' @else 'สร้างผู้ใช้สำเร็จ' @endif,
                        showConfirmButton: false,
                        timer: 1500
                      })

                      setTimeout(function(){ location.reload();  }, 1600);
                      
                    }else{
                      var error = JSON.stringify(data.errors);
                      console.log(error);
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
