@extends('main.layouts.main')

@section('style')
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <style type="text/css">
    tr:hover {cursor: pointer;}
  </style>
@endsection
@section('content-wrapper')

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        @lang('chat.contact')
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url($home) }}"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('chat.contact')</li>
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

                 <div class="box box-primary">
                    <div class="box-header with-border">
                      <h3 class="box-title">
                          <a class="btn btn-primary" href="{{ url($domainName.'/channel/blacklist') }}" >@lang('chat.show_blacklist')</a>
                      </h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <table id="example1" class="table table-bordered table-striped">
                          <thead>
                          <tr>
                            <th class="col-sm-1">@lang('main.no')</th>
                            <th>@lang('main.name')</th>
                          </tr>
                          </thead>
                          <tbody>
                            @foreach($lists as $key=>$l)
                            <tr onclick="goDirectChat('{{$l['id']}}')" >
                              <td>{{ $key+1 }}</td>
                              <td> 
                                <img src="{!! $l['img'] !!}" class="img-circle" height=25>
                                 {{ $l['name'] }}
                                </td>
                            </tr>
                            @endforeach

                            
                            
                          </tbody>
                      </table>
                    </div>
                    <!-- /.box-body -->
                   <!--  <div class="box-footer text-center">
                      <a href="javascript:void(0)" class="uppercase">View All Products</a>
                    </div> -->
                    <!-- /.box-footer -->
                  </div>
            </div> 
          
        </div>
    </section>

    


@endsection

@section('javascript')
<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src=" {{ url('js/utility/data_table.js') }}"></script>
<script type="text/javascript">
$(function () {
  $('#example1').DataTable()
})
function goDirectChat(uid){
  var route = "/channel/direct_chat?api_token="+api_token ;
  var data = {
    name:"" ,
    type:0,
    direct_message:1,
    uid : uid 
  } ;
  ajaxPromise('POST',route,data).done(function(data){
        var sd = {} ;
        sd.room = room ;
        sd.chat = data.chat ;
        sd.channel = data.channel;
        sd.init = 1; 
        socket.emit('channel_chat',sd);
        window.location.href="{{ url($domainName.'/channel') }}/"+data.channel.id;
  }).fail(function(txt) {
      var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
  });
}
  
</script>
@endsection







