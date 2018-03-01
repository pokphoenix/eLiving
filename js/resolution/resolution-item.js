
var RouteUrl = "/resolution/" ;

$(function() {
	$("#btn-resolution-add-item").on("submit",function(){
		var tdCount = $("#table-resolution thead tr:first th").length;
		var desc = $("#quotaion_item_desc").val();
		var resolutionTable = "<tr><td>"+
		"<button type=\"button\" class=\"btn btn-danger btn-xs btn-resolution-del-item\" > <i class=\"fa fa-close\"></i> </button>"+
		"</td><td></td>"+
		"<td><span>"+desc+"</span></td></tr>" ;
		$("#quotaion_item_desc").val('');
		$("#table-resolution tbody").append(resolutionTable);
		$("#table-resolution tbody tr td:nth-child(2)").each(function (i) {
	      var j = ++i;
	      $(this).text(j);
	  	});
	  	return false;
	});

	$(".btn-save-resolution").on('click',function(){
		getDataResolutionItem().then(function( res ) {
			var route = RouteUrl+'item?api_token='+api_token ;
			var data = "" ;
			ajaxPromise('POST',route,res).done(function(data){
				socket.emit('resolution',data);
		        $("#modal-resolution-item").modal("hide");
				$("#modal-card-content").modal("show");
		    }).fail(function(txt) {
		    	var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
			});
        }).fail(function(txt) {
		    var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
		});
	  			
	});

	


	$(document).on("click",".btn-resolution-del-item",function(event) {
	  var rows = $(this).closest("tr") ; 
	  var itemID = $.trim(rows.find('.resolution-item-id').val());
	  var cardId = $("#current_card_id").val();
	  console.log(itemID);
	  // return false;
	  if(itemID){
	  	var route = RouteUrl+cardId+'/item/'+itemID+'?api_token='+api_token ;
	  	ajaxPromise('DELETE',route,null).done(function(data){
	  		socket.emit('resolution',data);
         //    createResolutionTable(data);
	        // createTableItem(data.resolution_items);
        }).fail(function(txt) {
	    	var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
		});
	  }
	 


	  rows.remove();
	  $("#table-resolution tbody tr td:nth-child(2)").each(function (i) {
	      var j = ++i;
	      $(this).text(j);
	  });
	});

	$(document).on("click","#table-resolution tbody tr:not(.active-edit)",function(event) {
		// console.log('click tr');
	//---  another input change to text 
		//hideInputItem();

		$(this).find('td:eq(2)').each (function() {
			// console.log($(this).text(),$(this).find('input').length);
			var text = $(this).text();
			$(this).find('span').hide();
			if($(this).find('input').length==0){
				var input = "<input type=\"text\" value=\""+text+"\">";
				// console.log(input);
				$(this).append(input);
					
			}else{
				$(this).find('input').attr("type","text");
			}
				
		});     
		$(this).find('td:eq(3)').each (function() {
			// console.log($(this).text(),$(this).find('input').length);
			var text = $(this).text();
			$(this).find('span').hide();
			if($(this).find('input').length==0){
				var input = "<input type=\"text\" class=\"quotaion-item-amount\" value=\""+text+"\">";
				// console.log(input);
				$(this).append(input);
					
			}else{
				$(this).find('input').attr("type","text");
			}
				
		});     

		$(this).addClass('active-edit').find('td:first-child').focus();

	});
});


function getDataResolutionItem(){
	var dfd = $.Deferred();
	var cardId = $("#current_card_id").val();
	var insertNew = $("#insert_new_item").val();
	var data = { item:[],resolution_id:cardId,insert:insertNew };
	$("#table-resolution tbody tr").each(function(){
		var name = $(this).find('td:eq(2) span').text() ;
		var id = $(this).find('.resolution-item-id').val();
		// console.log(typeof id);
		if(typeof id =="undefined"){
			id="0";
		}
		if($(this).find('td:eq(2) input').attr('type')=="text"){
			name = $(this).find('td:eq(2) input').val() ;
		}
		var row =  { 
					 'id':id 
			 		,'resolution_id':cardId
			 		,'domain_id': $("#domainId").val()
					,'name': name
		}
		data.item.push(row);
	});

	if(data.item.length>0){
		dfd.resolve(data);
	}else{
		dfd.reject( "กรุณาระบุรายการก่อนค่ะ" );
	}
	return dfd.promise();
}