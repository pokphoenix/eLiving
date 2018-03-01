@extends('layouts.main')

@section('page_heading',$title)

@section('section')

<link rel="stylesheet" href="{{ asset('plugin/jquery-ui/jquery-ui.min.css') }}">
<script src="{{ asset('plugin/jquery-ui/jquery-ui.min.js') }}"></script>

<input type="hidden" id="base_url" value="{{ asset($baseRoute) }}" >
<style>
	.list-group-item-info,.list-group-item { height: 7rem;  list-style-type: none;  }

</style>
<div class="col-sm-6">
	@include('layouts.partials.message')
	<ul id="sortable" class="list-group">

		@if (isset($tables))
			@foreach ($tables as $t)
			<li class="list-group-item " id="{{$t->id}}" style="display:inline-block;padding-top:0;padding-left: 0;">
				<div class=" bg-primary" style="float:left; margin:	0 10px 0 0; padding:0 10px;  line-height: 7rem;" >{{$loop->index+1}}</div>
				<div style="padding-top: 5px">
				  <div class="media" >
	              <div class="media-left  media-middle">
	                <a data-fancybox="gallery" href="{{ (isset($t->thumbnail_th)) ? asset('storage/upload/'.$t->thumbnail_th)  : asset('/img/resource/thumbnail-default.jpg') }}" target="_blank">
	                  <img class="media-object" src="{{ (isset($t->thumbnail_th)) ? asset('storage/upload/'.$t->thumbnail_th)  : asset('/img/resource/thumbnail-default.jpg') }}" width="50" height="50" alt="...">
	                </a>
	              </div>
	              <div class="media-body">
	                <h4 class="media-heading"></h4>
	                <label>TH :</label> {{$t->title_th}}<BR>
	                <label>EN :</label> {{$t->title_en}}
	              </div>
	            </div>
				</div>
				
			</li>
			@endforeach
		@else
			<li class="">ไม่พบข้อมูล</li>
		@endif
	</ul>
	<a href="{{ asset($baseRoute) }}" class="btn btn-danger">Back</a>
	<button type="button" class="btn btn-primary" onclick="SubmitRC()" > save </button>

</div>
<div class="col-sm-6">
	@component('admin.widgets.collapse')
		            @slot('header', 'notice')
		            @slot('class', 'default')
		            @slot('id', '1')
		            @slot('collapseIn', true)
		            @slot('body')
		                คลิกลากเลื่อนขึ้นหรือลงเพื่อสลับตำแหน่ง
		                <br>
		                ที่จะใช้เรียงลำดับการแสดงผล
		            @endslot
		        @endcomponent
</div>


<script>
  $( function() {
    $( "#sortable" ).sortable({
      	placeholder: "list-group-item-info",
        stop: function(event, ui) {
            for (i=1; i<=$(this).children().length ; i++ ){	
            	$(".bg-primary").eq(i-1).text(i);
            }
            // if ( $(this).children().length > 10) {
            //     //ui.sender: will cancel the change.
            //     //Useful in the 'receive' callback.
            //     $(ui.sender).sortable('cancel');
            // }
        }
    });
    $( "#sortable" ).disableSelection();
  } );
  function SubmitRC(){
	var fromdata = [];
	var columcnt = 1 ;
	$('#sortable').find('li').each(function() {
    	data =  { 
    				'position' : columcnt 
    				,'id' : this.id
    			}
    	fromdata.push(data);
    	columcnt++;
	});	
	

	ajax_url = $('#base_url').val()+"/position" ;
	$.ajax({
		url: ajax_url ,
		type: "post",
		data :   {  "_token": "{{ csrf_token() }}", fromdata: fromdata } ,
		async: false,
		
		success: function(html) {													   
	 		console.log(html)
			if(html.result){
				window.location.href = $('#base_url').val();
			}else{
				alert("เกิดข้อผิดพลาดกับระบบ!!!");
			}
		}, 
		 error: function(request, status, errorThrown) {
			 alert("เกิดข้อผิดพลาดกับระบบ!!!");
		}
  	});

}

</script>
@endsection