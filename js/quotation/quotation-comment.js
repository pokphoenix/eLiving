var RouteUrl = "/purchase/quotation/" ;

$("#task-comment-btn").on("click",function(){
	var boxId = $("#current_card_id").val() ;
	var description = $("#task-comment-description").val();
	var route = RouteUrl+boxId+"/comment?api_token="+api_token;
	var data = { description:description };
	ajaxPromise('POST',route,data).done(function(data){
		socket.emit('quotation',data);
       	createQuotationComment(data.quotation_comments);
       	createQuatationTableHistory(data.quotation_historys);
       	$("#task-comment-description").val("");
    });
});


$(document).on("click",".btn-comment-delete",function(event) { 

	swal({
	  title: 'Are you sure?',
	  text: "คุณต้องการลบคอมเมนท์นี้ทิ้งใช่หรือไม่!",
	  type: 'warning',
	  showCancelButton: true,
	  confirmButtonText: 'ลบ',
	  cancelButtonText: 'ยกเลิก',
	  confirmButtonClass: 'btn btn-danger',
      cancelButtonClass: 'btn btn-default',
	  buttonsStyling: false,
	  reverseButtons: true
	}).then((result) => {
	  if (result.value) {
		    var boxId = $("#current_card_id").val() ;
			var parent = $(this).closest('.item') ;
			var commentId = parent.find('.comment-id').val();
			var route =  "/purchase/quotation/"+boxId+"/comment/"+commentId+"?api_token="+api_token ;
			var data = '';
			ajaxPromise('DELETE',route,data).done(function(data){
				socket.emit('quotation',data);
				box.remove();
		    })

	  } else if (result.dismiss === 'cancel') {
	    
	  }
	})


	
});
$(document).on("click",".btn-edit-message-save",function(event) { 
	var boxId = $("#current_card_id").val() ;
	var parent = $(this).closest('.item') ;
	var comment_id = parent.find('.comment-id').val();
	var text = parent.find('.edit-comment').val();
	var route = RouteUrl+boxId+"/comment/"+commentId+"?api_token="+api_token;
	var data = { description:text } ;
	ajaxPromise('POST',route,data).done(function(data){
		socket.emit('task',data);
		parent.find('.edit-message').html(text).removeClass('edit-message').addClass('message');
		parent.find('.btn-edit-message-save').remove();
    });
});

$(document).on("click",".history .message.can-edit",function(event) { 
	var text = $(this).text() ;
	var message = "<textarea class=\"form-control edit-comment\" >"+text+"</textarea>" ;
	// console.log($(this).text()) ;
	// console.log($(this).closest('.item').find('.header') );
	$(this).closest('.item').find('.header').append(" <button class=\"btn btn-xs btn-info btn-flat btn-edit-message-save\" ><i class=\"fa fa-save\"></i></button>");
	$(this).html(message);
	
	
	$(this).removeClass('message').addClass('edit-message');

});



$(document).on("click",".btn-comment-delete-real",function(event) { 

	swal({
	  title: 'Are you sure?',
	  text: "คุณต้องการลบคอมเมนท์นี้ทิ้งใช่หรือไม่!",
	  type: 'warning',
	  showCancelButton: true,
	  confirmButtonText: 'ลบ',
	  cancelButtonText: 'ยกเลิก',
	  confirmButtonClass: 'btn btn-danger',
      cancelButtonClass: 'btn btn-default',
	  buttonsStyling: false,
	  reverseButtons: true
	}).then((result) => {
	  if (result.value) {
		    var boxId = $("#current_card_id").val() ;
			var parent = $(this).closest('.item') ;
			var commentId = parent.find('.comment-id').val();
			var box = $(this).closest('.box') ;

			var route =  "/purchase/quotation/"+boxId+"/comment/"+commentId+"?api_token="+api_token ;
			var data = "" ;
			ajaxPromise('DELETE',route,data).done(function(data){
				socket.emit('quotation',data);
				box.remove();
		    })

	  } else if (result.dismiss === 'cancel') {
	    
	  }
	})


	
});

function createQuotationComment(data){
	// console.log('createQuotationComment',data);
	if (data.length>0){
		html = "" ;

		for (var i = 0 ; i< data.length ;i++){
			html+= "<div class=\"box\">"+
				        "<div class=\"box-body chat\">"+
				            "<div class=\"item\">"+
					            "<img src=\""+data[i].img+"\" alt=\"user image\" >"+
					            "<input type=\"hidden\" class=\"comment-id\" value=\""+data[i].comment_id+"\" >"+
	                            "<div class=\"message\">"+
	                              "<a href=\"javascript:void(0)\" class=\"name\">"+
	                                "<small class=\"text-muted pull-right\">"+
	        "<i class=\"fa fa-clock-o\"></i> "+moment.unix(data[i].ts_created_at).format("D/MM/YYYY HH:mm") ;
	        if(data[i].user_id==user_id){
					html +=  "<button type=\"button\" class=\"btn btn-default btn-comment-delete-real btn-xs\" title=\"Remove\"><i class=\"fa fa-close\"></i></button>";
            }
	        html +=  "</small>"+
	                                data[i].user_name+
	                              "</a>"+
	                              data[i].comment_description+
	                            "</div>"+
	                          "</div>"+
                		"</div>"+
                	"</div>";
		} 
		$(".task-comment .comment-list").html(html);
		 
	}else{
		$(".task-comment .comment-list").html('');
	}
}
