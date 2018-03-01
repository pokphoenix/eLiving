@extends('main.layouts.main')


@section('style')

<link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

<link rel="stylesheet" href="{{ url('public/css/input.css') }}">

<style>
.videoWrapper {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 */
  padding-top: 25px;
  height: 0;
}
.videoWrapper iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

  </style>
@endsection

@section('content-wrapper')
  

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       
        <i class="fa fa-phone"></i>
       
         {{ $title }}
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">{{ $title }}</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
  
       @include('layouts.error')

        <div class="row">
          <div class="col-xs-12">
            <div class="box">
            <div class="box-header">
              <h3 class="box-title"></h3>
               <button class="btn btn-success btn-sm btn-create" > <i class="fa fa-plus"></i> @lang('contact.insert')</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                
                  <th>@lang('user.no')</th>
                  <th>@lang('contact.name')</th>
                  <th>@lang('contact.contact_name')</th>
                  <th>@lang('contact.contact_tel')</th>
                  <th>@lang('contact.contact_email')</th>
                  <th>@lang('contact.created_at')</th>
                  <th>@lang('contact.type')</th>
                  <th>@lang('contact.status')</th>
                  <th >@lang('main.tool')
                  
                  </th>
                </tr>
                 <tr class="thead-search" >
                  
                  <th></th>
                  
                  <th class="input-filter">@lang('contact.name')</th>
                  <th class="input-filter">@lang('contact.contact_name')</th>
                  <th class="input-filter">@lang('contact.contact_tel')</th>
                  <th class="input-filter">@lang('contact.contact_email')</th>
                  <th class="input-filter">@lang('contact.created_at')</th>
                  <th class="input-filter">@lang('contact.type')</th>
                  <th class="input-filter">@lang('contact.status')</th>
                  <th ></th>
                 
                 
                </tr>
                </thead>

                <tbody>
        
                @foreach ($lists as $key=>$list)
                <tr >
                  
                  <td>{{ $key+1 }}</td>
                  <td>{{ $list['name']}}</td>
                  <td>{{ $list['contact_name']}} </td>
                  <td>{{ $list['contact_tel'] }}</td>
                  <td>{{ $list['contact_email'] }}</td>
                  <td>{{ created_date_format($list['created_at']) }}</td>
                  <td>{{ $list['type_name']}}</td>
                  <td>@if($list['status']) @lang('contact.status_show') @else  @lang('contact.status_not_show') @endif</td>
                  <td>
                    @if($list['created_by']==Auth()->user()->id || Auth()->user()->hasRole('admin'))
                    <button class="btn btn-xs btn-default btn-edit" data-id="{{$list['id']}}"><i class="fa fa-edit"></i></button>
                    @endif
                  </td>
                  

                </tr>
                @endforeach
               
              </table>
            </div>
            <!-- /.box-body -->
      </div>
          </div>
        </div>

       




      
    

    </section>
    <!-- /.content -->
 <div class="modal fade" id="modal-default">
          <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('contact.insert')</h4>
              </div>
              <div class="modal-body">
                 <form  id="contact-form" role="form" method="POST" action="{{$action}}" enctype="multipart/form-data"  >
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label for="name">@lang('contact.name')</label>
                          <input type="text" class="form-control" id="name" name="name" placeholder="@lang('contact.name')"  >
                        </div>
                        <div class="form-group">
                          <label for="name">@lang('contact.address')</label>
                          <textarea type="text" class="form-control" id="address" name="address" placeholder="@lang('contact.address')"  ></textarea> 
                        </div>
                        <div class="form-group">
                            <label for="name">@lang('contact.tin')</label>
                            <input type="text" class="form-control" id="tin" name="tin" placeholder="@lang('contact.tin')"  >
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                            <input type="radio" id="is_branch_1" value="0" name="is_branch" class="form-check-input" >
                               @lang('contact.head_officer')
                              <input type="radio" id="is_branch_2" value="1" name="is_branch" class="form-check-input" >
                               @lang('contact.branch')
                            </label>
                        </div>
                        <div class="row row-branch none">
                          <div class="form-group col-sm-4">
                           
                            <input type="text" class="form-control" id="branch_id" name="branch_id" placeholder="@lang('contact.branch_id')"  >
                          </div>
                          <div class="form-group  col-sm-8">
                            
                              <input type="text" class="form-control" id="branch_no" name="branch_no" placeholder="@lang('contact.branch_no')"  >
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="name">@lang('contact.contact_name')</label>
                          <input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="@lang('contact.contact_name')"  >
                        </div>
                        <div class="form-group">
                          <label for="name">@lang('contact.contact_tel')</label>
                          <input type="text" class="form-control" id="contact_tel" name="contact_tel" placeholder="@lang('contact.contact_tel')"  >
                        </div>
                        <div class="form-group">
                          <label for="name">@lang('contact.contact_email')</label>
                          <input type="text" class="form-control" id="contact_email" name="contact_email" placeholder="@lang('contact.contact_email')"  >
                        </div>
                      </div>
                      <div class="col-sm-6">
                            

                        <div class="form-group">
                          <label for="package_id">@lang('contact.type')</label>
                          <select class="select2 form-control" id="type" name="type" >
                              <option value=""></option>
                              @if (isset($contactType))
                                @foreach($contactType as $p)
                                <option value="{{ $p['id'] }}"  > {{ $p['name'] }} </option>
                                @endforeach
                              @endif
                          </select>
                        </div>

                   
                        <div class="form-group">
                            <label for="name">@lang('contact.credit')</label>
                            <input type="text" class="form-control" id="credit" name="credit" placeholder="@lang('contact.credit')"  >
                        </div>
                        <div class="form-group">
                            <label for="name">@lang('contact.note')</label>
                            <textarea  type="text" class="form-control" id="note" name="note" placeholder="@lang('contact.note')"></textarea>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                            <input type="checkbox" id="status"  name="status" class="form-check-input" >
                               @lang('contact.status_show')
                            </label>
                        </div>
                      </div>
                    </div>
                    
                    
                  

                  
                  
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
                <button type="button" class="btn btn-primary btn-save">@lang('main.btn_save')
                   <i class="fa fa-spinner fa-spin fa-fw none" ></i>
                </button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
@endsection

@section('javascript')
<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src=" {{ url('plugins/jquery-validate/jquery.validate.min.js') }} "></script>
<script src=" {{ url('js/utility/data_table.js') }}"></script>
<script type="text/javascript">
$(function () {

   
 
    // DataTable
     var table = $('#example1').DataTable(
      {
        "bSortCellsTop": true
        ,"order": [[ 0, 'asc' ]]
      }
      );
    
    $.each($('.input-filter', table.table().header()), function () {
        var column = table.column($(this).index());
        $( 'input', this).on( 'keyup change', function () {
            if ( column.search() !== this.value ) {
                column
                    .search( this.value )
                    .draw();
            }
        } );
    } );
    

 
})


</script>

<script src=" {{ url('js/utility/print.js') }}"></script>
<script type="text/javascript">
$(".btn-search").on('click', function(event) {
  var start_date = $("#start_date_year").val()+"-"+$("#start_date_month").val()+"-"+$("#start_date_day").val()+" "+$("#start_date_hour").val()+":"+$("#start_date_minute").val();
  var end_date = $("#end_date_year").val()+"-"+$("#end_date_month").val()+"-"+$("#end_date_day").val()+" "+$("#end_date_hour").val()+":"+$("#end_date_minute").val();


  start_date = moment(start_date).format('x')/1000;
  end_date = moment(end_date).format('x')/1000;

  var url = $("#baseUrl").val()+'/parcel/print-gift?start_date='+start_date+'&end_date='+end_date ;
 
  window.location.href=url ;
});

</script>

<script>

$("input[name=is_branch]").on('change',function(){
  console.log($(this).attr('id'));
   if($(this).attr('id')=="is_branch_1"){
      $(".row-branch").hide();
   }else{
      $(".row-branch").show();
   }
})


$(".btn-edit").on("click",function(){
   $("#modal-default input").val('');
    var contactId = $(this).data('id');
    // var roomId = $(this).data('room-id');
    // var packageId = $(this).data('package-id');
    // var userName = $(this).data('buy-name');
    // var period = $(this).data('period');

    // var periodDate = new Date(period);
 
    // $("#month").val(periodDate.getMonth()+1).trigger('change');
    // $("#year").val(periodDate.getFullYear()).trigger('change');
    // $('#user_buy_name').val(userName);
    // $('#room_id').val(roomId).trigger('change');
    // $('#package_id').val(packageId).trigger('change');
    var route = "/contact/"+contactId+"/edit?api_token="+api_token ;

    ajaxPromise('GET',route,null).done(function(data){
        console.log(data.contact);
        var r = data.contact ;
     
        $('#type').val(r.type).trigger('change');

        var d = new Date(r.send_date);
        $("#send_date_year").val( d.getFullYear() );
        $("#send_date_month").val( d.getMonth()+1 );
        $("#send_date_day").val( d.getDate() );
        $("#send_date_hour").val( d.getHours() );
        $("#send_date_minute").val( d.getMinutes() );

        $('#name').val(r.name);

        $('#contact_name').val(r.contact_name);
        $('#contact_tel').val(r.contact_tel);
        $('#contact_email').val(r.contact_email);

        $('#address').val(r.address);


        $('#tin').val(r.tin);
        $('#credit').val(r.credit);
        $('#note').val(r.note);
        

        if(r.status==1){
          $("#status").attr('checked',true);
        }

        if(r.is_branch==0){
          $('#branch_id').val('');
          $('#branch_no').val('');
          $("#is_branch_1").attr('checked',true);
          $(".row-branch").hide();
        }else{
          $("#is_branch_2").attr('checked',true);
          $('#branch_id').val(r.branch_id);
          $('#branch_no').val(r.branch_no);
          $(".row-branch").show();
        }
          

    

        $("#modal-default modal-title").text((($("#app_local").val()=='th') ? 'แก้ไขรายการ' : 'Edit Contact' ));
        $("#contact-form").attr({'action': $("#apiUrl").val()+"/contact/"+contactId+"?api_token="+api_token });
         var html = '<input type="hidden" id="_method"  name="_method" value="PUT">';
        $("#contact-form").append(html);
       

        $("#modal-default").modal("toggle");
    });


    // $("#modal-default modal-title").text((($("#app_local").val()=='th') ? 'แก้ไขการขาย' : 'Edit parcel Buy' ));
    // $("#contact-form").attr({'action': $("#apiUrl").val()+"/parcel/officer/"+buyId+"?api_token="+api_token });
    // $("#modal-default").modal("toggle");
    // var html = '<input type="hidden" id="_method"  name="_method" value="PUT">';
    // $("#contact-form").append(html);

})
$("#type").on("change",function(){
  console.log($(this).val());
  var type= $(this).val() ;
  $(".parcel-row").hide();
  if(type==3){
    $("#row_gift").show();
  }else if(type==2||type==5){
    $("#row_supplies").show();
  }else{

  }
})

$(".btn-create").on("click",function(){
   $("#modal-default input,#modal-default textarea").val('');

  $('#type').val('1').trigger('change');
  $("#status").attr('checked',false);
  $("#is_branch_2").attr('checked',false);
  $(".row-branch").hide();
  $("#is_branch_1").attr('checked',true);
  $("#contact-form #_method").remove('');
  $("#contact-form").attr('action', "{{$action}}" );
  $("#modal-default").modal("toggle");
})




$(".btn-save").on("click",function(){
  $("#contact-form").submit();
})
$(".btn-delete").on("click",function(){
  var parent = $(this).closest('tr') ;
  var buyId = $(this).data('id');
  swal({
        title: (($("#app_local").val()=='th') ? 'คุณแน่ใจไหม?' : 'Are you sure?' ) ,
        text: (($("#app_local").val()=='th') ? 'คุณต้องการลบข้อมูลนี้ใช่หรือไม่' : "You want to delete this data!" ) ,
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: (($("#app_local").val()=='th') ? 'ลบ' : 'Delete' ),
        cancelButtonText: (($("#app_local").val()=='th') ? 'ยกเลิก' : 'Cancel' ),
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-default',
        buttonsStyling: false,
        reverseButtons: true
  }).then((result) => {
          if (result.value) {
              var route = "/parcel/officer/"+buyId+"?api_token="+api_token ;
              ajaxPromise('DELETE',route).done(function(data){
               parent.remove();
              }).fail(function(txt) {
                var error = JSON.stringify(txt);
                           swal(
                            'Error...',
                            error,
                            'error'
                          )
              });

          } else if (result.dismiss === 'cancel') {
            
          }
        })
})


$(function() {
    $("#contact-form").validate({
      rules: {
        room_id: {
          required: true,
          number: 255
        },
        package_id: {
          required: true,
          number: true
        }, 
        user_buy_name: {
          required: true,
          maxlength:1000
        },
      
      },
      messages: {
        room_id: (($("#app_local").val()=='th') ? 'ห้องไม่ถูกต้อง' : 'Wrong Room' ),
        package_id: (($("#app_local").val()=='th') ? 'แพ็คเกจไม่ถูกต้อง' : 'Wrong Package' ),
       
        
      },
      highlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
      }
      ,submitHandler: function (form) {
        $(".btn-save").find('.fa-spinner').show();
        console.log($("input[name=is_branch]").val());
        var form_data = new FormData($("#contact-form")[0]);

        var is_branch = 1 ;
        if($("#is_branch_1").is(':checked')){
          is_branch = 0 ;
        }
         console.log(is_branch);

        form_data.append('is_branch',is_branch);
               
        

      
   
             $.ajax({
                 type: form.method ,
                 url: form.action ,
                 data: form_data ,
                 processData: false,
                 contentType: false,
                 success: function (data) {
                    if($("#_method").length >0 ){
                      title = "@lang('main.update_success')";
                    }else{
                      title = "@lang('main.create_success')";
                    }
                    $(".btn-save").find('.fa-spinner').hide();
                    if(data.result=="true"){


                      swal({
                          title:title ,
                          type: 'success',
                          showCancelButton: false,
                          confirmButtonText: "@lang('main.ok')"
                        }).then((result) => {
                          if (result.value) {

                           
                            if( $("#type").val()==2 && $("#_method").length<=0 ){
                              $("#supplies_code").val('');
                            }else{
                              location.reload();
                            }

                            
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
              $(".btn-save").find('.fa-spinner').hide();
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
