@extends('main.layouts.main')

@section('style')

@endsection
@section('content-wrapper')

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-key"></i> @lang('main.room') 
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('domain') }}"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active"> @lang('main.profile')</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
               @include('layouts.error')
            </div>
        </div>

        <form id="create-form" action="{{ $apiUpdate }}" method="" enctype="multipart/form-data" >
            {{ method_field('PUT') }}
            {{ csrf_field() }} 
           
        <div class="row">
            <div class="col-sm-12">
              @include('admin.widgets.room')
            </div>
        </div>
        

        <div class="row">
            <div class="col-sm-12">
                <button type="submit" id="btn_save" class="btn btn-primary" > <i class="fa fa-save"></i>
                    @lang('main.btn_save')</button>
                <button type="button" id="btn_cancel" class="btn btn-danger" > <i class="fa fa-close"></i>
                    @lang('main.btn_cancel')</button>
            </div>
        </div>
        </form>
    </section>

@endsection

@section('javascript')
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ url('js/utility/main.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/utility/validate.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/utility/autocomplete.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/user/room.js') }}"></script> 
<script type="text/javascript">
var idCard = "{{ auth()->user()->id_card }}";
  $(function() {
  
    $("#btn_cancel").on("click",function(){
        window.location.href="{{ url('profile/show')}}";
    }); 

  
    $("#create-form").validate({
      submitHandler: function (form) {

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

        var form_data = new FormData($("#create-form")[0]);
        form_data.append('user-room',JSON.stringify(data.user));

             $.ajax({
                  type: "POST",
                  url: form.action,
                  data: form_data ,
                  processData: false,
                  contentType: false,
                 success: function (data) {
                    // console.log(data,typeof data.response);
                    if(data.result=="true"){
                      swal({
                        type: 'success',
                        title: "@lang('main.update_success')" ,
                        showConfirmButton: false,
                        timer: 1500
                      })

                      setTimeout(function(){ window.location.href = "{{ url('profile/show') }}"; }, 1600);
                      
                    }else{
                      var error = JSON.stringify(data.errors);
                      // console.log(error);
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







