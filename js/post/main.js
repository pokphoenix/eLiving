function readURL(input) {
	// var dfd = $.Deferred();
    var newFile = $(input).clone();
    newFile.removeAttr("id");
    newFile.removeAttr("name");
    newFile.removeAttr("style");
	  // newFile.attr("name","upload_file[]");
	newFile.attr("class","file_upload");
	newFile.attr("type","hidden");

  if (input.files && input.files[0]) {
    var reader = new FileReader();
    var file = input.files[0];
    var file_name = file.name;
    var file_ext = file_name.split('.').pop().toLowerCase();
    var file_size = file.size ;
    reader.onload = function(e) {
    	 var data = {name : file_name ,
              extension : file_ext ,
              size : file_size ,
              data : e.target.result ,
            };
        newFile.val(JSON.stringify(data));
    	var html = "<div class=\"col-sm-3 parent-img\" >"+
			"<small class=\"pull-right badge btn-close-image\">"+
			"<i class=\"fa fa-close\"></i>"+
			"</small>"+
            
            "<img src=\""+e.target.result+"\" class=\"img-responsive\"  >"+
        "</div>";

       

        $('#preview-img').append(html).show();
        newFile.insertAfter($('.btn-close-image').last());
        // console.log($('.parent-img > .img-responsive').innerWidth());
        // $(".parent-img .img-responsive").height($('.parent-img .img-responsive').innerWidth());
    }

    reader.readAsDataURL(input.files[0]);

  }

  console.log($('.btn-close-image').last());

  

  // dfd.resolve();
  // return dfd.promise();
}


$("#file_upload").change(function() {
  readURL(this);
  $(this).val('');
});

$(document).on("click",".btn-delete-post",function(){

	var post_id = $(this).data('id') ;
 
  if( $("table#notice_table").length > 0  ){
      var parent = $(this).closest('tr') ;
  }else{
      var parent = $(this).closest('.box-widget') ;
  }


	
	
	
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
         	
    			     var route = "/post/"+post_id+"?api_token="+api_token ;
        			ajaxPromise('POST',route,{'_method':'DELETE'}).done(function(data){
        				parent.remove();
        			})

          } else if (result.dismiss === 'cancel') {
            
          }
        })
	


	
});