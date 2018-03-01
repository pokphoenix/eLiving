@extends('main.layouts.main')

@section('style')
  <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">

@endsection
@section('content-wrapper')

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-address-book-o"></i> {{ $title }} 
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('domain') }}"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li><a href="{{ url('profile/show') }}">@lang('main.profile')</a></li>
        <li class="active">@lang('main.address')</li>
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
            @if(isset($edit))
               {{ method_field('PUT') }}
            @endif

            {{ csrf_field() }} 
           
        <div class="row">
            
            <div class="col-sm-12">
               @include('admin.widgets.address')
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
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ url('js/utility/main.js') }}"></script> 
<!-- <script type="text/javascript" src="{{ url('js/utility/validate.js') }}"></script>  -->
<script type="text/javascript" src="{{ url('js/utility/autocomplete.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/utility/address.js') }}"></script> 
<script type="text/javascript">
var idCard = "{{ auth()->user()->id_card }}";
var api_token = "{{ auth()->user()->api_token }}" ;
  $(function() {
  
    $("#btn_cancel").on("click",function(){
        window.location.href="{{ url('profile/address')}}";
    });  
    $(".btn-del-address").on("click",function(){
      swal({
      title: 'Are you sure?',
      text: "You want to delete this address!",
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
           $.ajax({
                  type: "DELETE",
                  url: "{{ url('api/profile/address/') }}/"+$(this).data('id')+"?api_token="+api_token ,
                  data: {"_token": "{{ csrf_token() }}"} ,
                 success: function (data) {
                    console.log(data,typeof data.response);
                    if(data.result=="true"){
                      swal({
                          title: "@lang('main.delete_success')" ,
                          type: 'success',
                          showCancelButton: false,
                          confirmButtonText: "@lang('main.ok')"
                      }).then((result) => {
                        if (result.value) {
                          window.location.href = "{{ url('profile/address') }}";
                        }
                      })
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

      }
    })



          
    }); 

  
    $("#create-form").validate({
      ignore: [],       
       rules: {
        address:"required",
        province_id:"required",
        amphur_id:"required",
        district_id:"required",
        zip_code:"required"
      },
      messages: {
        address:"Please enter your  address",
        district_id:"Please select your  district",
        province_id:"Please select your  province",
        amphur_id:"Please select your  amphur",
        zip_code:"Please enter your  zipcode"
      },
       highlight: function ( element, errorClass, validClass ) {
      
        $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
       
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
       
      }
      ,submitHandler: function (form) {

             $.ajax({
                  type: "POST",
                  url: form.action,
                  data:  $(form).serialize() ,
                 success: function (data) {
                    console.log(data,typeof data.response);
                    if(data.result=="true"){
                      swal({
                          title: @if(isset($edit)) "@lang('main.update_success')"  @else "@lang('main.update_success')" @endif ,
                          type: 'success',
                          showCancelButton: false,
                          confirmButtonText: "@lang('main.ok')"
                      }).then((result) => {
                        if (result.value) {
                          window.location.href = "{{ url('profile/address') }}";
                        }
                      })
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







