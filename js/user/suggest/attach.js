
var baseSystem = "/suggest/system/" ;


	$(document).on("click",".del-doc",function(event) {
      imgCount = $('#append_upload tr').length;
      var rows = $(this).closest("tr") ; 
      rows.remove();
      // console.log("click",imgCount);
      $("#append_upload tr td:first-child").each(function (i) {
          var j = ++i;
          $(this).text(j);
      });
  });

  $(document).on("click",".btn-attach-delete",function(event) { 

	swal({
	  title: 'Are you sure?',
	  text: "คุณต้องการลบรูปนี้ทิ้งใช่หรือไม่!",
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
			var attach_id = parent.find('.attach-id').val();

			var route = baseSystem+boxId+"/attachment/"+attach_id+"?api_token="+api_token ;
			var data = "" ;
			ajaxPromise('DELETE',route,data).done(function(data){
				socket.emit('suggest',data);
				parent.remove();
		    })

	  } else if (result.dismiss === 'cancel') {
	    
	  }
	})


	
});


  $('#file-upload').on('change',function() {
	  var cardId = $("#current_card_id").val();
	  var file = $('#file-upload')[0].files[0];
	  var file_name = file.name;
	  var file_ext = file_name.split('.').pop().toLowerCase();
	  var file_size = file.size ;
	  var reader = new FileReader();
	  var img = [] ;

	    reader.onload = function(e) {
            var data = {
            	name : file_name ,
            	extension : file_ext ,
            	size : file_size ,
            	data : e.target.result ,
            }
            // console.log(data);
            img.push(data)
            var form_data = new FormData();
			form_data.append('attachment',JSON.stringify(img));
           	var url = $("#apiUrl").val() ;
           
			$.ajax({
				url: url+baseSystem+cardId+"/attachment?api_token="+api_token,
				type: 'POST',
				dataType: 'json',
				data:form_data,
				cache:false,
		        contentType: false,
		        processData: false,
			}).done(function(res) {
				// console.log('attach',res);

				if(res.result=="true"){
					 socket.emit('suggest',res.response);
					 
				}else{
					var error = JSON.stringify(res.errors);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
				}
			})
			.fail(function() {
				var error = JSON.stringify(res.errors);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
			})

           
			

        }   
        reader.readAsDataURL(file);

	  
	   $('#file-upload').val('');
	});


  function createTaskAttachment(data){
	moment.locale('th');

	var userId = $("#user_id").val();
	// console.log('[createTaskAttachment]',data.length);
	if(data.length>0){
		
		var attachment ='';
		for (var i = 0 ; i<data.length ;i++){
			attachment +="<div class=\"row item\"><div class=\"col-sm-12\">";
    		attachment += "<p class=\"message\">"+
    						"<a href=\""+data[i].file_path+"\" download=\""+data[i].filename+"\" target=\"_blank\">"+
    							"<div class=\"fl\"><img src=\""+data[i].file_path+"\" width=\"100\" height=\"70\"></div>"+
    							"<div>"+data[i].filename+"<br>"+
    							"<small class=\"text-muted\">"+ 
    							data[i].first_name+" "+data[i].last_name+
    							"<i class=\"fa fa-clock-o\"></i>"+
    							moment.unix(data[i].created_at).format("D/MM/YYYY HH:mm")+
    							"</small>";
    		if(data[i].created_by==userId){
			attachment +="<br><a href=\"javascript:void(0)\" class=\"btn-attach-delete\">delete</a>" ;
          	}	
    		attachment +=	"</div>"+
    						"</a>"+
    					"</p>"+
    					"<input class=\"attach-id\" type=\"hidden\" value=\""+data[i].id+"\" > </div></div>" ;
		}
		
		$("#task_attach_ment .attachment-list").html(attachment);
		$("#task_attach_ment").show();
	}else{
		$("#task_attach_ment .attachment-list").html('');
		// $("#task_attach_ment").hide();
	}
}