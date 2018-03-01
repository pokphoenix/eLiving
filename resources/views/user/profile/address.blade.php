@extends('main.layouts.main')

@section('style')
  <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
@endsection
@section('content-wrapper')

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-address-book-o"></i> @lang('main.address') 
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('domain') }}"><i class="fa fa-home"></i>  @lang('main.home') </a></li>
        <li><a href="{{ url('profile/show') }}"> @lang('main.profile') </a></li>
        <li class="active"> @lang('main.address') </li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
               @include('layouts.error')
            </div>
        </div>

      
        <div class="row">
            
            <div class="col-sm-12">
              <a href="{{ url('profile/address/create') }}" type="button"   class="btn btn-primary" > <i class="fa fa-plus"></i>
              @lang('main.create_new_address')
              </a>
              <BR><BR>
              @foreach($userAddress as $key => $a)
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">{{ ($key+1)." ) ". $a['address_name'] }}</h3>
                  <div class="box-tools pull-right">
                       
                      <a href="{{ url('/profile/address/'.$a['id'].'/edit') }}" class="btn btn-default btn-xs" >
                            <i class="fa fa-edit"></i>
                      </a>

                      <button type="button" class="btn btn-danger btn-xs btn-del-address" data-id="{{$a['id']}}" >
                            <i class="fa fa-close"></i>
                      </button>
                  </div>

                </div>
                <div class="box-body">
                   {{ $a['address']." ต.".$a['district_name']." ".$a['amphur_name']." ".$a['province_name']." ".$a['zip_code'] }}
                      <input type="hidden" class="address" value="{{$a['address']}}" >
                         
                        
                  
                  </div>
                 

                  <div class="box-footer">
                    <input type="checkbox" class="active-address" data-id="{{$a['id']}}" @if($a['active']) checked="" @endif >ตั้งเป็นที่อยู่หลัก
                  </div>
                
              </div>
              @endforeach

               <button type="button" id="btn_cancel" class="btn btn-danger" > <i class="fa fa-close"></i>
                    @lang('main.back')</button>
            </div>

           
           
        </div>
      
    </section>

@endsection

@section('javascript')
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ url('js/utility/main.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/utility/validate.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/utility/autocomplete.js') }}"></script> 
<script type="text/javascript" src="{{ url('js/utility/address.js') }}"></script> 
<script type="text/javascript">
var idCard = "{{ auth()->user()->id_card }}";
var api_token = "{{ auth()->user()->api_token }}" ;
  $(function() {
    
     $(".active-address").on("click",function(){

        $(".active-address").attr("checked",false);

      var isActive = 0 ;
      if($(this).is(':checked')){
        isActive = 1 ;
      }
       $.ajax({
                  type: "post",
                  url: "{{ url('api/profile/address/active') }}/"+$(this).data('id')+"?api_token="+api_token ,
                  data: {"active": isActive } ,
                 success: function (data) {
                    console.log(data,typeof data.response);
                    if(data.result=="true"){
                      swal({
                          title: "@lang('main.update_success')" ,
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

    })


    $("#btn_cancel").on("click",function(){
        window.location.href="{{ url('profile/show')}}";
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
       rules: {
        address:"required",
        province_id:"required",
        amphur_id:"required",
        district_id:"required",
        zip_code:"required"
      },
      messages: {
        address:"Please enter your  address",
        district_name:"Please enter your  district",
        province_name:"Please enter your  province",
        amphur_name:"Please enter your  amphur",
        zip_code:"Please enter your  zipcode"
      },
      submitHandler: function (form) {

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







