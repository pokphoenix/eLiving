
var RouteUrl = "/purchase/quotation/" ;

$(function() {
	$("#btn-quotation-add-item").on("click",function(){
		var tdCount = $("#table-quatation thead tr:first th").length;
		// console.log('total [td]',tdCount);
		// console.log('[td]',$("#table-quatation tbody tr:first td:gt(2)").length);

		var desc = $("#quotaion_item_desc").val();
		var amt = $("#quotaion_item_amt").val();

		var quatationTable = "<tr><td>"+
		"<button type=\"button\" class=\"btn btn-danger btn-xs btn-quotation-del-item\" > <i class=\"fa fa-close\"></i> </button>"+
		"</td><td></td>"+
		"<td><span>"+desc+"</span></td>"+
		"<td><span>"+amt+"</span></td></tr>" ;

		$("#quotaion_item_desc").val('');
		$("#quotaion_item_amt").val('');

		// if(tdCount>4){
		// 	$('#table-quatation tbody tr:first td:gt(3)').each(function(){
	 //      quatationTable += "<td><span>0</span></td>" ;
		// });
		// $('#table-quatation tbody tr:first td:last').each(function(){
	 //      quatationTable += "<td class=\"text-right\">0 "+ 
	 //      "<button type=\"button\" class=\"btn btn-danger btn-quotation-del-item\" > <i class=\"fa fa-close\"></i> </button>"+
	 //      "</td>" ;
		// });
		// }

		// quatationTable += "</tr>" ;
		

		$("#table-quatation tbody").append(quatationTable);
		$("#table-quatation tbody tr td:nth-child(2)").each(function (i) {
	      var j = ++i;
	      $(this).text(j);
	  });
	});

	$(".btn-save-quotation").on('click',function(){
		getDataQuatationItem().then(function( res ) {
		var route =	RouteUrl+"item?api_token="+api_token ;
           	ajaxPromise('POST',route,res).done(function(data){
            	socket.emit('quotation',data);
          //       createQuatationTable(data);
		        // createQuatationTableItem(data.quotation_items);
		        $("#modal-quotation-item").modal("hide");
				$("#modal-card-content").modal("show");
            })
        }).fail(function(txt) {
		    var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
		});
	  			
	});

	$(".btn-quotation-company-new").on('click',function(){

		getDataQuatationItem().then(function( res ) {
			var route =	RouteUrl+"item?api_token="+api_token ;
           	ajaxPromise('POST',route,res).done(function(data){
            	socket.emit('quotation',data);
                createQuatationTable(data);
		        createQuatationTableItem(data.quotation_items);
                addQuotationItem();
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



	$(document).on("click",".btn-quotation-del-item",function(event) {
	  var rows = $(this).closest("tr") ; 
	  var itemID = $.trim(rows.find('.quotation-item-id').val());
	  // console.log(itemID);
	  if(itemID){
	  	var route = RouteUrl+cardId+'/item/'+itemID+'?api_token='+api_token;
	  	ajaxPromise('DELETE',route,null).done(function(data){
            createQuatationTable(data);
	        createQuatationTableItem(data.quotation_items);
            // addQuotationItem();
        })
	  }
	  rows.remove();
	  $("#table-quatation tbody tr td:nth-child(2)").each(function (i) {
	      var j = ++i;
	      $(this).text(j);
	  });
	});

	$(document).on("click","#table-quatation tbody tr:not(.active-edit)",function(event) {
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


function getDataQuatationItem(){
	var dfd = $.Deferred();
	var cardId = $("#current_card_id").val();
	var insertNew = $("#insert_new_item").val();
	// console.log('card id ',cardId);
	// console.log('insertNew ',insertNew);
	var data = { item:[],quotation_id:cardId,insert:insertNew };
	$("#table-quatation tbody tr").each(function(){
		var name = $(this).find('td:eq(2) span').text() ;
		var amount = $(this).find('td:eq(3) span').text() ;
		var id = $(this).find('.quotation-item-id').val();
		// console.log(typeof id);
		if(typeof id =="undefined"){
			id="0";
		}

		if($(this).find('td:eq(2) input').attr('type')=="text"){
			name = $(this).find('td:eq(2) input').val() ;
		}
		if($(this).find('td:eq(3) input').attr('type')=="text"){
			amount = $(this).find('td:eq(3) input').val() ;
		}

		// console.log(name , typeof name );
		// console.log(amount , typeof amount );

		var row =  { 
					 'id':id 
			 		,'quotation_id':cardId
			 		,'domain_id': $("#domainId").val()
					,'name': name
					,'amount': amount
		}
		// console.log('[getDataQuatationItem]',row); 
		data.item.push(row);
	});

	if(data.item.length>0){
		dfd.resolve(data);
	}else{
		dfd.reject( "กรุณาระบุรายการก่อนค่ะ" );
	}

	return dfd.promise();
}
