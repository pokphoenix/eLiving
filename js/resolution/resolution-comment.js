var RouteUrl = "/resolution/" ;

$("#task-comment-btn").on("click",function(){
	var boxId = $("#current_card_id").val() ;
	var route = RouteUrl+boxId+"/comment?api_token="+api_token;
	var data = { description:$("#task-comment-description").val() };
	ajaxPromise('POST',route,data).done(function(data){
		socket.emit('resolution',data);
		createComment(data.resolution_comments);
       	createHistory(data.resolution_historys);
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
			var route =  RouteUrl+boxId+"/comment/"+commentId+"?api_token="+api_token ;
			var data = '';
			ajaxPromise('DELETE',route,data).done(function(data){
				socket.emit('resolution',data);
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
	var description = $("#task-comment-description").val();
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


function ajaxEditComment(boxId,commentId,text){
	var dfd = $.Deferred();
	var url = $("#apiUrl").val() ;
	var description = $("#task-comment-description").val();
	$.ajax({
		url: url+RouteUrl+boxId+"/comment/"+commentId+"?api_token="+api_token,
		type: 'POST',
		dataType: 'json',
		data : { description:text }
	})
	.done(function(res) {
		// console.log(res);
		if(res.result=="true"){
			dfd.resolve(res.response);
		}else{
			dfd.reject( res.errors );
		}
	})
	.fail(function() {
		dfd.reject( "error");
	})
	return dfd.promise();
}

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

			var route =  RouteUrl+boxId+"/comment/"+commentId+"?api_token="+api_token ;
			var data = "" ;
			ajaxPromise('DELETE',route,data).done(function(data){
				socket.emit('resolution',data);
				box.remove();
		    })

	  } else if (result.dismiss === 'cancel') {
	    
	  }
	})


	
});

function createComment(data){
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
