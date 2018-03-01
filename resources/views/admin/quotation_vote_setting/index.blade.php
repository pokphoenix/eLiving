@extends('main.layouts.main')


@section('style')
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
   <style type="text/css">
    tr:hover {cursor: pointer;}
  </style>
@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-cog"></i>
         @lang('sidebar.quotation_vote_setting')
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('sidebar.quotation_vote_setting')</li>
      </ol>
    </section>



    <!-- Main content -->
    <section class="content">
  
       @include('layouts.error')

    	<div class="box">
           <div class="box-header">
          
              <h3 class="box-title"></h3>
            
            <!--   <button class="btn btn-success btn-sm btn-edit" 
               > <i class="fa fa-edit"></i> @lang('setting.edit')
             </button> -->
             
          
            </div>
            <!-- /.box-header -->
            <div class="box-body" >
              <form id="create-form" action="{{$action}}">
                 <div class="col-sm-12">
                      <div class="form-group">
                        <label for="exampleInputPassword1">@lang('setting.quotation_board_count')</label>
                        <input type="text" class="form-control" id="board_count" name="board_count" placeholder="@lang('setting.quotation_board_count')"  value="{{$data['board_count']}}" >
                      </div>

                      <div class="form-check">
                            <label class="form-check-label">
                            <input type="checkbox" id="is_auto" name="is_auto" class="form-check-input" @if($data['is_auto'])  checked="" @endif >
                               @lang('setting.is_auto') <small> @lang('setting.is_auto_desc')</small>
                            </label>
                      </div>
                     
                      
                    
                      
                  </div>
                 
                  

                  <div class="col-sm-12" style="height: 50px;">
                     <button type="submit" id="save" class="btn btn-primary">@lang('main.btn_save')
                       <i class="fa fa-spinner fa-spin fa-fw none" ></i>
           
                     </button>


                      <button type="button" id="cancel" class="btn btn-danger">@lang('main.btn_cancel')
                      </button>
                    
                  </div>
              </form>
            </div>
            <!-- /.box-body -->
          </div>
    </section>
    <!-- /.content -->

@endsection

@section('javascript')
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>


<script type="text/javascript">


$(function() {

 
  $("#create-form").validate({
      rules: {
        board_count: {
          required: true,
          number:true
        }
      },
     
      highlight: function ( element, errorClass, validClass ) {
      
        $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
       
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
       
      }
      ,submitHandler: function (form) {
        $("#save").find('.fa-spinner').show();
        var form_data = new FormData($("#create-form")[0]);
        form_data.append('_method','PUT');
      

        
       
             $.ajax({
                 type: "POST",
                 url: form.action ,
                 data: form_data ,
                 processData: false,
                 contentType: false,
                 success: function (data) {
                    $("#save").find('.fa-spinner').hide();
                    if(data.result=="true"){
                      swal({
                          title:  @if(isset($edit)) "@lang('main.update_success')" @else  "@lang('main.create_success')" @endif,
                          type: 'success',
                          showCancelButton: false,
                          confirmButtonText: "@lang('main.ok')"
                        }).then((result) => {
                          if (result.value) {
                            location.reload();
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
              $("#save").find('.fa-spinner').hide();
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
