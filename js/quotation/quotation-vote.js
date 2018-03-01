
var RouteUrl = "/purchase/quotation/" ;

$(document).on("click",".btn-voting-company",function(){ 
	var cardId = $("#current_card_id").val();
	var companyId = $(this).closest('td').find('.company-id').val();
	var route = RouteUrl+cardId+"/voting/"+companyId+"?api_token="+api_token;
	var method = 'GET' ;
	var data = null ;
	if($("#instead_vote").val()==1){
		method = 'POST' ;
		var first_name = $("#instead_first_name").val() ;
		if(first_name==""||first_name==" "){
			alert((($("#app_local").val()=='th') ? 'กรุณากรอกชื่อ ผู้ที่ทำการโหวต' : 'Wrong First Name' )); 
			$("#instead_first_name").focus();
			return false;
		} 
		var last_name = $("#instead_last_name").val() ;
		if(last_name==""||last_name==" "){
			alert((($("#app_local").val()=='th') ? 'กรุณากรอกนามสกุล ผู้ที่ทำการโหวต' : 'Wrong Last Name' ));
			$("#instead_last_name").focus();
			return false;
		}


		data = { first_name : first_name ,last_name:last_name } ;
	}

	ajaxPromise(method,route,data).done(function(data){
		socket.emit('quotation',data);
		location.reload();
		$("#instead_first_name").val('');
		$("#instead_last_name").val('');
		// $("#voting").hide();
	    // $("#modal-voting").modal("hide");
	})
});

$(document).on("click","#instead_novote",function(){ 
	var cardId = $("#current_card_id").val();
	var companyId = 0 ;
	var route = RouteUrl+cardId+"/voting/"+companyId+"?api_token="+api_token;
	var  method = 'POST' ;
	var first_name = $("#instead_first_name").val() ;
	if(first_name==""||first_name==" "){
		alert((($("#app_local").val()=='th') ? 'กรุณากรอกชื่อ ผู้ที่ทำการโหวต' : 'Wrong First Name' )); 
		$("#instead_first_name").focus();
		return false;
	} 
	var last_name = $("#instead_last_name").val() ;
	if(last_name==""||last_name==" "){
		alert((($("#app_local").val()=='th') ? 'กรุณากรอกนามสกุล ผู้ที่ทำการโหวต' : 'Wrong Last Name' ));
		$("#instead_last_name").focus();
		return false;
	}
	var data = { first_name : first_name ,last_name:last_name } ;
	ajaxPromise(method,route,data).done(function(data){
		socket.emit('quotation',data);
		location.reload();
		$("#instead_first_name").val('');
		$("#instead_last_name").val('');
		// $("#voting").hide();
	    // $("#modal-voting").modal("hide");
	})
});

$("#cancel_vote").on("click", function(event) {
	var cardId = $("#current_card_id").val();
	var status = 4 ;
	ajaxUpdateStatus(cardId,status).done(function(data){
	});
});
$("#btn_manual_voted").on("click", function(event) {
	var cardId = $("#current_card_id").val();
	var status = 3 ;
	ajaxUpdateStatus(cardId,status).done(function(data){
	});
});

$("#voting").on("click", function(event) {
	$("#instead_vote").val(0);
	$("#instead_novote,#instead_name").hide();
	var cardId = $("#current_card_id").val();

	var route = RouteUrl+cardId+"/company?api_token="+api_token ;
	ajaxPromise('GET',route,null).done(function(data){
	    createVoteList(data);
	})
});

$("#btn_vote_instead").on("click", function(event) {
	var cardId = $("#current_card_id").val();
	var route = RouteUrl+cardId+"/company-instead?api_token="+api_token ;
	$("#instead_vote").val(1);
	ajaxPromise('GET',route,null).done(function(data){
		$("#instead_novote,#instead_name").show();
	    createVoteList(data);
	})
});

$(document).on("click",".btn-instead-vote-delete",function(){
	var boxId = $("#current_card_id").val() ;
	var ele =$(this);
	var voteId = ele.data('id');
	var route = RouteUrl+boxId+"/voting/"+voteId+"?api_token="+api_token ;
	ajaxPromise('DELETE',route,null).done(function(data){
		socket.emit('quotation',data);
		ele.closest('.user-instead-vote-li').remove();

    })
});


$("#btn_change_voted").on("click", function(event) {

	var url = $("#apiUrl").val() ;
	var cardId = $("#current_card_id").val();
	var route = RouteUrl+cardId+"/change_voted?api_token="+api_token ;

	ajaxPromise('DELETE',route,null).done(function(data){
		socket.emit('quotation',data);
	   	createCard(data);
	});
});


function createVoteList(data){
	var companies = data.quotation_companys ;
	
	var table = "";
	if (companies.length >0){
		for(var i=0;i<companies.length;i++){
			table += "<tr>"+
					"<td><button class=\"btn btn-info btn-flat btn-xs btn-voting-company\" >"+(($("#app_local").val()=='th') ? 'โหวต' : 'Vote' )+"</button>"+
					"<input type=\"hidden\" class=\"company-id\" value=\""+companies[i].id+"\" >"+
					"</td>"+
					"<td>"+(i+1)+"</td>"+
					"<td>"+companies[i].name+"</td>"+
					"<td>"+companies[i].price_net+"</td>"+
					"<td>"+companies[i].vote_count;
			if(companies[i].user.length>0){
				table += " (";
				for(var k=0;k<companies[i].user.length;k++){ 
					// console.log(k,companies[i].user[k].name);
					table +=   (k==0) ? companies[i].user[k].name : ","+companies[i].user[k].name ; 	
				}
				table += ")";
			}		

			table += "</td>"+
					"</tr>";
				// console.log(table);	
		}
		$("#modal-voting #voting-table tbody").html(table);
	}
	// console.log(companies.length);
	
	$("#modal-voting").modal("show");
}	

function ajaxUpdateStatus(boxId,status){
	var dfd = $.Deferred();
	var url = $("#apiUrl").val() ;
	$.ajax({
		url: url+"/purchase/quotation/"+boxId+"/status?api_token="+api_token,
		type: 'PUT',
		dataType: 'json',
		data : {status:status}
	})
	.done(function(res) {
		// console.log(res);
		if(res.result=="true"){
			dfd.resolve(res.response);
			
			socket.emit('quotation',res.response);
			var cardId = $("#current_card_id").val();
			createCard(res.response);
		}else{
			dfd.reject( res.errors );
			var error = JSON.stringify(res.errors);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
		}
	})
	.fail(function() {
		dfd.reject( "error");
       swal(
        'Error...',
        'Cannot connect server.Please try again',
        'error'
      )
	})
	return dfd.promise();
}