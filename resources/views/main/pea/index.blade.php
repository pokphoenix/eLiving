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
               <form  id="contact-form" role="form" method="POST"  enctype="multipart/form-data"  >
                    <div class="form-group">
                          <label for="name">@lang('contact.ca')</label>
                          <input type="text" class="form-control" id="ca" name="ca" placeholder="@lang('contact.name')"  >
                        </div>
                        
                        <div class="form-group">
                            <label for="name">@lang('contact.peano')</label>
                            <input type="text" class="form-control" id="peano" name="peano" placeholder="@lang('contact.tin')"  >
                        </div>
                         <button type="button" class="btn btn-primary btn-save">@lang('main.btn_save')
                   <i class="fa fa-spinner fa-spin fa-fw none" ></i>
                </button>
                      
                  

                  
                  
                </form>
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

   

 
})


</script>

<script src=" {{ url('js/utility/print.js') }}"></script>
<script type="text/javascript">
$(".btn-save").on('click', function(event) {

var headers = {
"Content-Type": "application/json; charset=utf-8",
"Accept" : "application/json"
};

$.ajax({

               type:"POST",
url: "https://www.pea.co.th/WebApplications/BillHistory/GetBillHistory.aspx/GetListOfBillHistory",
data: { 'CA' : '020006033261' , 'PEANO' : '5701320006' },
crossDomain: true,
headers: {
  'Access-Control-Allow-Origin': '*'
},
dataType: 'json'

               
            ,success: function (data) {
               console.log(data);
            },
            error: function (result) {
                alert("Error");
            }

            });


        
});
function setHeader(xhr) {

  xhr.setRequestHeader('Authorization', '');
}
</script>



@endsection   
