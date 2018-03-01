@extends('main.layouts.main')
@section('style')
 <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
 <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ url('public/css/input.css') }}">
<style type="text/css">
	.inline-col {float: left;margin-right: 10px;}
	.license-item {margin-bottom: 10px;}
</style>

@endsection
@section('content-wrapper')
<!-- data-toggle="modal" data-target="#modal-card-content" -->
	
	<input type="hidden" id="route" value="{{ $route }}" >
	
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      	<i class="fa fa-circle-o"></i>
      	@lang('sidebar.e-sticker')
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> @lang('main.home')</a></li>
        <li class="active">@lang('sidebar.e-sticker')</li>
      </ol>
    </section>
	
    <!-- Main content -->
    <section class="content">
    	 <div class="box">
            <div class="box-header">
              <h3 class="box-title"></h3>
              <button class="btn btn-primary btn-hover btn-create"  >
		            <i class="fa fa-plus" ></i>  @lang('post.create_post')
		        </button>
               
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>@lang('user.no')</th>
                  <th>@lang('esticker.room')</th>
                  <th>@lang('esticker.license_plate')</th>
                  <th>@lang('esticker.year')</th>
                  <th>@lang('esticker.qrcode')</th>
                  <th>@lang('main.tool')</th>
                </tr>
                <tr class="thead-search">
                  <th ></th>
                  <th class="input-filter">@lang('esticker.room')</th>
                  <th class="input-filter">@lang('esticker.license_plate')</th>
                  <th class="input-filter">@lang('esticker.year')</th>
                  <th class="input-filter">@lang('esticker.qrcode')</th>
                 
                  <th></th>
                </tr>
                </thead>
                <tbody>
        
                @foreach ($lists as $key=>$list)
                <tr >
                  <td>{{ $key+1 }}</td>
                  <td> {!! $list['room_name'] !!}</td>
                  <td> @if (!empty($list['license_plate_list'])) 
							@foreach($list['license_plate_list'] as $k)
								{{ $k['license_plate_category']." ".$k['license_plate']." ". $k['province_name']   }} <BR>
							@endforeach
                  		@endif
                  </td>
                  <td>{{ $list['year']+543 }}</td>
                  <td> <img src="{{ $list['qrcode']}}" height="50"></td>
                  <td> 
                   
                    <button class="btn btn-default btn-edit btn-xs" data-id="{{ $list['id'] }}" ><i class="fa fa-edit"></i></button>
                    <button class="btn btn-danger btn-delete btn-xs" data-id="{{ $list['id'] }}"  ><i class="fa fa-trash-o"></i></button>

                    <button class="btn btn-info btn-print btn-xs" data-id="{{ $list['id'] }}"  ><i class="fa fa-print"></i></button>
                   
                  </td>

                </tr>
                @endforeach
               
              </table>
            </div>
            <!-- /.box-body -->
          </div>
    


	
		
		

		
		

      <!-- Main row -->
     
      <!-- /.row (main row) -->

		
		

		

    </section>
    <!-- /.content -->

  <!-- /.content-wrapper -->
    <div class="modal fade" id="modal-default">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('post.public_date_set')</h4>
              </div>
              <div class="modal-body">
                 <form  id="post-form" role="form" method="POST" action="{{$action}}" enctype="multipart/form-data"  >

                 	<div class="form-group row-room">
                    <label for="room_id">@lang('parking.room')</label>
                    <select class="select2 form-control" id="room_id" name="room_id" >
                        <option value=""></option>
                        @if (isset($room))
                          @foreach($room as $r)
                          <option value="{{ $r['id'] }}"  > {{ $r['text' ]}} </option>
                          @endforeach
                        @endif
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="name">@lang('parking.license_plate')</label>
                    <button type="button" class="btn btn-warning btn-xs btn-append-license"><i class="fa fa-plus"></i></button>
                    
                    <div class="row-license-plate">
                    	<div class="license-item">
	                       <input type="text" class="form-control inline-col license_plate_category" style="width:60px;" maxlength="3" placeholder="@lang('parking.license_plate_category')" value="" >
	                        <input type="text" class="form-control inline-col license_plate" style="width:100px;"placeholder="@lang('parking.license_plate')" maxlength="4"  value="" >
	                        <select class="select2 form-control province_id inline-col"   style="width:190px; >
		                        @if (isset($province))
				                    @foreach($province as $key=> $p)
				                    <option value="{{ $p['id'] }}" > {{ $p['text'] }}</option>
				                    @endforeach
				                @endif
		                    </select>
		                    <button type="button" class="btn btn-danger btn-xs btn-remove-license"><i class="fa fa-times"></i>
		                    </button>
	                    </div>

                    
                    </div>
                   
                  </div> 

                  


                  <div class="form-group">
                    <label for="room_id">@lang('esticker.year')</label>
                   	<select class="form-control" id="year" name="year">
	                   	@for($i=(date('Y')-5) ; $i<= (date('Y')+1) ;$i++)
							<option value="{{$i}}" @if(date('Y') == $i) selected="true" @endif>{{ App::isLocale('en') ? $i : $i+543 }}</option>
	                   	@endfor
                   	</select>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
                <button type="button" class="btn btn-primary  btn-save">@lang('main.btn_save')
                   <i class="fa fa-spinner fa-spin fa-fw none" ></i>
                </button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>


         <div class="modal fade" id="modal-print">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('esticker.print')</h4>
              </div>
              <div class="modal-body">
               

                <div class="form-group row-room">
                    <label for="room_id">@lang('esticker.reason')</label>
                    <select class="select2 form-control" id="reason_id" name="reason_id" >
                        <option value=""></option>
                        @if (isset($reason))
                          @foreach($reason as $r)
                          <option value="{{ $r['id'] }}"  > {{ $r['text' ]}} </option>
                          @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label for="name">@lang('parking.license_plate')</label>
                      <input type="text" class="form-control inline-col license_plate_category" style="width:60px;" maxlength="3" placeholder="@lang('parking.license_plate_category')" value="" >
                </div> 
					
               
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
                <button type="button" class="btn btn-primary  btn-save">@lang('main.btn_save')
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



<script src=" {{ url('js/utility/print.js') }}"></script>
<script src=" {{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src=" {{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src=" {{ url('js/utility/data_table.js') }}"></script>	
<script type="text/javascript">
$(function() {
	var table = $('#example1').DataTable(
    {
        "bSortCellsTop": true
        ,"order": [[ 0, 'desc' ]]
    });
    
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

     $("#room_id").select2();
      $("#province_id").select2();
       $(".row-room .select2-container").css('width',"100%");
});


$(document).on("click",".btn-remove-license",function(){
	$(this).closest('.license-item').remove();
})

$(".btn-append-license").on("click",function(){
	var html = "<div class=\"row license-item\">"+
                   	"<div class=\"col-sm-12\">"+
                    	"<input type=\"text\" class=\"form-control inline-col  "+
                    	"license_plate_category\" "+
                    	"style=\"width:60px;\" maxlength=\"3\" "+
                    	" placeholder=\"@lang('parking.license_plate_category')\" value=\"\" >"+
	                    "<input type=\"text\" class=\"form-control inline-col license_plate\" "+
	                    " style=\"width:100px;\" placeholder=\"@lang('parking.license_plate')\" "+
	                    " maxlength=\"4\"  value=\"\" >"+
	                    "<select class=\"select2 form-control inline-col province_id\" "+
	                    " style=\"width:190px;\" >"+
	                       
		                @if (isset($province))
		                    @foreach($province as $key=> $p)
		                    "<option value=\"{{ $p['id'] }}\" > {{ $p['text'] }}</option>"+
		                    @endforeach
		                @endif
		                "</select>"+
		                "<button type=\"button\" class=\"btn btn-danger btn-xs btn-remove-license\">"+
		                "<i class=\"fa fa-times\"></i></button>"+
                    "</div>"+
	                       
	            "</div>";
	$(".row-license-plate").append(html);
	 $(".province_id").select2();
})

$("input[type=checkbox]").each(function(){
	if($(this).val()=="user"){
		$(this).attr("checked",true);
	}
})

$(".btn-print").on("click",function(){
	

	$("#modal-print").modal("toggle");

	// var id = $(this).data('id') ;
	// var route = "/e-sticker/"+id+"/log?api_token="+api_token ;

	// ajaxPromise('POST',route,null);

	// var ele = $(this).closest('tr').find('img') ;
	// ele.width(250);
	// ele.height(250);
	// var newEle = ele.parent().html();
	
	
	// var restorepage = document.body.innerHTML;
	//   var printcontent = newEle;
	
	//   document.body.innerHTML = printcontent;
	//    $('body').css('padding-top',0);
	//   window.print();
	//   // document.body.innerHTML = restorepage;
	//   location.reload();
})

$(".btn-create").on("click",function(){
	



    d = new Date();
    year = d.getFullYear();
    $("#year").val(year).trigger('change');

  $("#modal-default").modal("toggle");

})
$(".btn-role").on("click",function(){
   
  $("#modal-default").modal("toggle");

})



</script>




<script>
	var ApiUrl = $("#apiUrl").val() ;
	var RouteUrl = "/"+$("#route").val();
	var userId = {{ Auth()->user()->id }} ;
$(document).on("click",".btn-delete",function(){ 

	html2canvas(document.querySelector("body")).then(canvas => {
	    document.body.appendChild(canvas)
	});

	var parent = $(this).closest('tr');
	var id = $(this).data('id') ;
	var route = RouteUrl+"/"+id+"?api_token="+api_token ;
	ajaxPromise('DELETE',route,null).done(function(data){ 
		parent.remove();
	})

})



$(document).on("click",".btn-edit",function(){
	// var parent = $(this).closest('.box-widget');
	// parent.find('.text-show').hide();
	// parent.find('.text-edit').show();
	// parent.find('.text-edit textarea').focus().select();
	var id = $(this).data('id') ;
	var route = RouteUrl+"/"+id+"/edit?api_token="+api_token ;
	ajaxPromise('GET',route,null).done(function(data){
		console.log(data);
		var r = data.e_sticker ;


		$("#post-form").append("<input type=\"hidden\" id=\"has_edit\" value=\""+r.id+"\" >");

		$("#room_id").val(r.room_id).trigger('change') ;
		$("#year").val(r.year).trigger('change') ;




		if(r.license_plate_list.length>0){
			var html ="";
			for(var i=0;i<r.license_plate_list.length;i++){
				html += "<div class=\"row license-item\">"+
                   	"<div class=\"col-sm-12\">"+
                    	"<input type=\"text\" class=\"form-control inline-col  "+
                    	"license_plate_category\" "+
                    	"style=\"width:60px;\" maxlength=\"3\" "+
                    	" placeholder=\"@lang('parking.license_plate_category')\" value=\""+r.license_plate_list[i].license_plate_category+"\" >"+
	                    "<input type=\"text\" class=\"form-control inline-col license_plate\" "+
	                    " style=\"width:100px;\" placeholder=\"@lang('parking.license_plate')\" "+
	                    " maxlength=\"4\"  value=\""+r.license_plate_list[i].license_plate+"\" >"+
	                    "<select class=\"select2 form-control inline-col province_id\" "+
	                    " style=\"width:190px;\" >";
	                       
		                @if (isset($province))
		                    @foreach($province as $key=> $p)
		            html +=   "<option value=\"{{ $p['id'] }}\" " ;
		                if(r.license_plate_list[i].province_id=={{ $p['id'] }}){
		            html += " selected " ;
		                }
		            html += " > {{ $p['text'] }}</option>";
		                    @endforeach
		                @endif
		            html += "</select>"+
		                "<button type=\"button\" class=\"btn btn-danger btn-xs btn-remove-license\">"+
		                "<i class=\"fa fa-times\"></i></button>"+
                    "</div>"+
	                       
	            "</div>";
			}

			

			console.log(html);

			$(".row-license-plate").html(html);

		}

		
		
		 $(".province_id").select2();

		 $("#modal-default").modal("toggle");
	});

});


function getData(){
	var dfd = $.Deferred();

	var data= {license:[]} ;

	$(".license-item").each(function(){
		var lpc = $(this).find('.license_plate_category').val();
		var lp = $(this).find('.license_plate').val();
		var pid = $(this).find('.province_id').val();
		var license = { 'license_plate_category':lpc 
						,'license_plate':lp  
						,'province_id':pid
					};
					
		data.license.push(license);
	})
	
	if(data.license.length > 0 ){
		var form_data = new FormData( $("#post-form")[0] );
		form_data.append('license_plate_list', JSON.stringify(data.license)) ;
		dfd.resolve(form_data);
	}else{
		dfd.reject("");
	}

	return dfd.promise();

}

$(".btn-save").on("click",function(){
	var spin = $(this).find('.fa-spinner');
	getData().done(function(form_data){
		spin.show();

		var route = RouteUrl+"?api_token="+api_token ;
		var title= "@lang('main.create_success')";
		if($("#has_edit").length>0){
			title= "@lang('main.update_success')";
			route = RouteUrl+"/"+$("#has_edit").val()+"?api_token="+api_token ;
			form_data.append('_method','PUT');
		}

		ajaxFromData('POST',route,form_data).done(function(){
			spin.hide();
			swal({
                  title: title ,
                  type: 'success',
                  showCancelButton: false,
                  confirmButtonText: "@lang('main.ok')"
            }).then((result) => {
                if (result.value) {
                  location.reload();
                }
            })
		})
		.fail(function() {
			// dfd.reject( "error");
			spin.hide();
		})
	})

		

});
</script>

@endsection		
