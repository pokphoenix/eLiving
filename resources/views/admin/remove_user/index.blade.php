@extends('main.layouts.main')


@section('style')
 <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ url('public/css/input.css') }}">

   <style type="text/css">
    tr:hover {cursor: pointer;}
  </style>
@endsection

@section('content-wrapper')
	

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <i class="fa fa-user"></i>
        @lang('sidebar.remove_user')
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">{{ $title }}</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
  
       @include('layouts.error')

    	<div class="box">
           <div class="box-header">
            @if(!isset($noCreate))
           
              <h3 class="box-title"></h3>
               <a href=" {{ url($route.'/create') }}" class="btn btn-success btn-sm"> <i class="fa fa-plus"></i>@lang('main.new_user')</a>
            
            @endif
            
            @if(isset($totaluser))
              <div class="pull-right">
                @lang('main.total_registor_user') : {{ $totaluser }}
              </div>
            @endif
            </div>
            <!-- /.box-header -->
            <div class="box-body">
             <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                  <th>@lang('user.id_card')</th>
                  <th>@lang('user.user_name')</th>
                  <th>@lang('user.name')</th>
                  <th>@lang('user.role')</th>
                  <th>@lang('user.create_date')</th>
                  <th>@lang('user.approve_status')</th>
                  <th>@lang('user.active_status')</th>
                  <th>@lang('main.tool')</th>
                  
                </tr>
                <tr class="thead-search">
                  <th></th>
                  <th class="input-filter">@lang('user.id_card')</th>
                  <th class="input-filter">@lang('user.user_name')</th>
                  <th class="input-filter">@lang('user.name')</th>
                  <th class="input-filter">@lang('user.role')</th>
                  <th class="input-filter">@lang('user.create_date')</th>
                  <th class="input-filter">@lang('user.approve_status')</th>
                  <th class="input-filter">@lang('user.active_status')</th>
                  <th></th>
                </tr>
                </thead>
                
                <tbody>
				
				        @foreach ($users as $key=>$user)
                <tr >
                  <td>{{ $key+1 }}</td>
                  <td>{{ $user['id_card']}}</td>
                  <td>{{ $user['username']}}</td>
                  <td>{{ $user['first_name']." ".$user['last_name'] }}</td>
                  <td>@foreach ($user['role'] as $role)   {{ $role }}<BR> @endforeach</td>
                  <td>{{ created_date_format($user['created_at']) }}</td>
                  <td style="font-weight: bold;color: {{ getStatusColor($user['approve']) }}" > {{ getStatusText ($user['approve']) }}</td>
                  <td style="font-weight: bold;color: {{ ($user['registor']==0) ? '#f39c12' : '#255625'  }} " > @if($user['registor']==0) Non Active @else 
                  Active @endif  </td>
                  <td>
                    @if($user['approve']!=1)
                     <button type="button" class="btn btn-danger btn-delete btn-xs" data-id="{{ $user['id_card'] }}"  ><i class="fa fa-trash-o"></i></button>
                   
                    @endif
                  </td>
                </tr>
                @endforeach
                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
    </section>
    <!-- /.content -->

@endsection

@section('javascript')
<!-- DataTables -->
<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src=" {{ url('js/utility/data_table.js') }}"></script> 
<script type="text/javascript">
$(function () {
	

  // Setup - add a text input to each footer cell
     var table = $('#example1').DataTable(
      {
        "bSortCellsTop": true
        
      }
      );
 
    // DataTable
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
    

    $(document).on("click",".btn-delete",function(){

      var post_id = $(this).data('id') ;
      var parent = $(this).closest('tr') ;


      
      
      
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
              
                   var route = "/create-user/"+post_id+"?api_token="+api_token ;
                  ajaxPromise('POST',route,{'_method':'DELETE'}).done(function(data){
                    parent.remove();
                  })

              } else if (result.dismiss === 'cancel') {
                
              }
            })
      


      
    });
    


})

</script>

@endsection		
