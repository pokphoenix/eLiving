var RouteUrl = "/purchase/quotation/" ;
$("#btn-add-company").on('click',function(){
	// console.log('[btn-add-company] click');
	var ele = $(this) ;
	ele.find('.fa-spinner').show();

	getDataQuatationCompany().then(function( post ) {
        ajaxSaveQuatationCompany(post).done(function(data){
        	socket.emit('quotation',data);
        	// location.reload();
   //          createQuatationTable(data);
   //      	createQuatationTableItem(data.quotation_items);
        	ele.find('.fa-spinner').hide();
			$("#modal-quotation-company").modal("hide");
			$("#modal-card-content").modal("show");
        }).fail(function(txt) {
        	ele.find('.fa-spinner').hide();
	   		var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )

		});
    }).fail(function(txt) {
    	ele.find('.fa-spinner').hide();
	    var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )

	});


	// var tdCount = $("#table-quatation-summary thead tr:first td").length;
	// var trCount = $("#table-quatation-summary tbody tr").length;
	// var tbCount = $("#table-quatation-summary").length;
	// if(tbCount>0){
	// 	var colCount = $("#table-quatation thead tr:eq(1) th").length ;
	// 	console.log('colCount',colCount);
	// 	if(colCount==0 ){
	// 		$("#table-quatation thead tr:first").append("<th colspan=\"1\" class=\"text-center\" >ราคา</th> ");
	// 		$("#table-quatation thead").append("<tr><th> บริษัท "+$("#company-name").val()+"</th></tr>");
	// 	}else{
	// 		colCount++ ;
	// 		$("#table-quatation thead tr:first th:eq(3)").attr("colspan",colCount);
	// 		$("#table-quatation thead tr:eq(1)").append("<th> บริษัท "+$("#company-name").val()+"</th>");
	// 	}

		
		
	// 	$('#table-quatation tbody tr').each(function(){
	//        $(this).append("<td >0</td>");
	// });
	// }
	// $("#company-name").val('');
	// var text = $('#table-quatation-company').clone().html() ;
 //  	$("#table-quatation-summary").html(text);
	
});

$(document).on("keypress",".item-price-per-unit,.discount,.quotaion-item-amount,.insert-item-amount",function(e) {
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
    	event.preventDefault();
  	}


});

$(document).on("input",".discount",function(e) { 
	setPrice();
});
$(document).on("click",".del-attachment",function(e) { 

	var ele = $(this) ;

	 swal({
      title: (($("#app_local").val()=='th') ? 'คุณแน่ใจไหม?' : 'Are you sure?' ) ,
      text: (($("#app_local").val()=='th') ? 'คุณต้องการลบไฟล์นี้ใช่หรือไม่' : "You want to delete this file!" ) ,
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
      	var boxId = $("#current_card_id").val() ;
		var fileCode = $(this).data('file-code');
		var route = RouteUrl+boxId+"/attach/"+fileCode+"?api_token="+api_token;
      	ajaxPromise('DELETE',route,null).done(function(){
      		 ele.closest('tr').remove();
      		swal({
	              title: (($("#app_local").val()=='th') ? 'ลบสำเร็จ' : 'Delete Success' ),
	              type: 'success',
	              showCancelButton: false,
	              confirmButtonText:  (($("#app_local").val()=='th') ? 'ตกลง' : 'Ok' )
	          }).then((result) => {
	            if (result.value) {
	             
	            }
	          })
      	})

      
      }
    })
});

$("#cal_vat").on("click",function(){
	setPrice();
});



$("#btn-cancel-company").on("click",function(){
	$("#modal-card-content").modal("show");
	$("#modal-quotation-company").modal("hide");
});
  



$(document).on("input",".item-price-per-unit",function(e) {
	var price = this.value ;
	var amount = parseFloat($(this).closest('tr').find('td:eq(2)').text()) ;
	// console.log('[ item-price-per-unit ] keypress',price,amount);
	var priceTotal = (!isNaN(price)) ? (amount*price).toFixed(2) : 0 ;
	$(this).closest('tr').find('.item-price-total').text(priceTotal);
	;
	var priceTotal = 0 ;
	$('#table-quatation-company .item-price-total').each(function(){
		var priceItem = parseFloat($(this).text()) ;
		if(!isNaN(priceItem)) {
			// console.log('priceTotal bf',priceTotal);
			// console.log('price ',priceItem);
			priceTotal += priceItem ;
		}
		// console.log('priceTotal i',priceTotal);
	});
	$('#table-quatation-company .total-price-before-vat').text(priceTotal.toFixed(2));
	setPrice();

});

function setPrice(){
	var priceTotal = ($('#table-quatation-company .total-price-before-vat').text());
	var discount = ($('#table-quatation-company .discount').val());

	// console.log('price',priceTotal);
	// console.log('discount',discount);
	if(isNaN(discount)) { 
		discount = 0 ;
	}

	if(!isNaN(priceTotal)) {
		console.log('priceTotal',priceTotal);
		console.log('discount',discount);
		var priceTotal = parseFloat(priceTotal) - discount ;

		var vat = 0 ;
		if($("#cal_vat").is(':checked')&&priceTotal>0){ 
			vat = ((priceTotal)*0.07).toFixed(2);
		}

		var priceNet = parseFloat(priceTotal)+parseFloat(vat) ;
		
		console.log('priceTotal',priceTotal);
		console.log('vat',vat);
		console.log('priceNet',priceNet);
		$('.price-discount').text(discount);
		$('.price-vat').text(vat);
		$('.total-price-net').text(priceNet.toFixed(2));
	}

}

function getDataQuatationCompany(){
	var dfd = $.Deferred();

	var img = [] ;
	
	$("#table-upload-file tbody tr").each(function(){
		var imgRow = JSON.parse($(this).find('.file_upload').val()) ;
		img.push(imgRow);
	});

	

	// var img_ext = img_name.split('.').pop().toLowerCase();
	// var img_size = img.size ;
	// if($.inArray(img_ext,['gif','png','jpg','jpeg']) == -1 ){
	// 	dfd.reject('Invalid Image File');
	// 	return dfd.promise();
	// }

	// if(img_size > 2048*1000 ){
	// 	dfd.reject('Image File Size is very big');
	// 	return dfd.promise();
	// }

	


	var cardId = $("#current_card_id").val();
	var data = { item:[],company:[],company_id:0,quotation_id:cardId,company_attach:form_data };

	var companyId = $("#company_id").val();
	var supplierName = $("#modal-quotation-company #supplier_name").val();
	var supplierAddress = $("#modal-quotation-company #supplier_address").val();
	var contactName = $("#modal-quotation-company #contact_name").val();
	var contactTel = $("#modal-quotation-company #contact_tel").val();
	var cintactEmail = $("#modal-quotation-company #contact_email").val();
	var domainId = $("#domainId").val();
	// console.log('card id ',cardId);
	// console.log('supplierName ',supplierName);
	
	if (supplierName==""||supplierName==" "){
		dfd.reject( "กรุณาระบุบริษัท" );
		return dfd.promise();
	}

	var priceB4Vat = parseFloat($("#modal-quotation-company .total-price-before-vat").text()).toFixed(2) ;
	var vat = parseFloat($("#modal-quotation-company .price-vat").text()).toFixed(2) ;
	var priceTotal = parseFloat(priceB4Vat) + parseFloat(vat) ;
	var discount = parseFloat(Math.abs($("#modal-quotation-company .price-discount").text())).toFixed(2) ;
	var priceNet = parseFloat($("#modal-quotation-company .total-price-net").text()).toFixed(2) ;
	var remark = $("#modal-quotation-company .company-remark").val();
	var paymentTerm = $.trim($('#modal-quotation-company .payment_term').val()) ;
	var guarantee = $.trim($('#modal-quotation-company .guarantee').val()) ;
	console.log('companyId',companyId);
	if(!isNaN(companyId)){
		data.company_id = companyId;
	}

	data.summary = {
					price_b4_vat : priceB4Vat
					,vat :vat
					,price_total :priceTotal
					,discount :discount
					,price_net :priceNet
					,remark : remark
					,payment_term:paymentTerm
					,guarantee : guarantee
				}

	
	

	
	data.company = { domain_id:domainId
						,name:supplierName
						,address:supplierAddress
						,contact_name:contactName
						,contact_tel:contactTel
						,contact_email:cintactEmail
					 } ;
	


	$("#table-quatation-company tbody tr").each(function(){
		var pricePerUnit = $(this).find('.item-price-per-unit').val() ;
		if(pricePerUnit==""||pricePerUnit==" "){
			pricePerUnit = 0 ;
		}

		var price = $(this).find('.item-price-total').text() ;
		if(price==""||price==" "){
			price = 0 ;
		}
		var quotationItemId = $.trim($(this).find('.quotation-item-id').val()) ;
		
		var row =  { 
			 		 'quotation_id':cardId
			 		,'quotation_item_id': quotationItemId
			 		,'domain_id': $("#domainId").val()
					,'price_per_unit': pricePerUnit
					,'price': price
		}
		// console.log(row); 
		if(price<0){
			dfd.reject( "กรุณาระบุ ราคา / หน่วย ของ "+$(this).find('td:eq(1)').text() );
			return dfd.promise();
		}
		data.item.push(row);
	});

	if(data.item.length>0){
		var form_data = new FormData();
		form_data.append('file_upload',JSON.stringify(img));
		form_data.append('quotation_id',cardId);
		form_data.append('item',JSON.stringify(data.item));
		form_data.append('company_id',data.company_id);
		form_data.append('summary',JSON.stringify(data.summary));
		form_data.append('company',JSON.stringify(data.company));
		dfd.resolve(form_data);
	}else{
		dfd.reject( "กรุณาระบุรายการ" );
	}

	return dfd.promise();
}

function clearQuotationCompany(){
  	$("#modal-quotation-company #append_upload").html('');  
  	$("#modal-quotation-company input").val('');
	$("#modal-quotation-company textarea").val('');
	$("#table-quatation-company .item-price-total").text('0');
	$("#table-quatation-company .total-price-before-vat").text('0');
	$("#table-quatation-company .price-discount").text('0');
	$("#table-quatation-company .price-vat").text('0');
	$("#table-quatation-company .total-price-net").text('0');

}


function ajaxSaveQuatationCompany(data){
	var dfd = $.Deferred();
	var url = $("#apiUrl").val() ;
	$.ajax({
		url: url+"/purchase/quotation/company/create?api_token="+api_token,
		type: 'POST',
		dataType: 'json',
		data:data,
		cache:false,
        contentType: false,
        processData: false,
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

$(document).on("click","#btn-search-company",function(){
	ajaxSearchCompany($(this), $("#apiUrl").val()+'/search/company?api_token='+api_token).then(function( res ) {
        res.ele = $("#supplier_name");
        autoData(res);
    }).fail(function(txt) {
       // console.log(txt);
    }); 
})

//---- autocomplete supplier_name
$(document).on("input","#supplier_name",function(e) { 
	$("#supplier_address").val("");
	$("#contact_name").val("");
	$("#contact_tel").val("");
	$("#contact_email").val("");
	// $("#company_id").val("");


    ajaxSearchCompany($(this), $("#apiUrl").val()+'/search/company?api_token='+api_token).then(function( res ) {
        autoData(res);
    }).fail(function(txt) {
       // console.log(txt);
    }); 
});

$(document).on("click",".my-autocomplete-li",function(e) { 

	var boxId = $("#current_card_id").val() ;
	var companyId = $.trim($(this).find('.company-id').val()) ;
	// console.log("my-autocomplete-li,companyId",companyId);
	$("#company_id").val(companyId);
	var route = RouteUrl+boxId+"/company/"+companyId+"?api_token="+api_token;
	ajaxPromise('GET',route,null).then(function(data){
		setCompanyData(data);
    })
	$(this).parent().prev('input').val($(this).text()) ;
	$(this).parent().remove();
});

function ajaxSearchCompany(ele,url){
  var data =  ele.val();
  var dfd = $.Deferred();
  $.ajax({
    url:  url  ,
    type: 'POST',
    dataType: 'json',
    data: {name:data} ,
  })
  .done(function(res) {
    data = { ele : ele,data : res } ;
    dfd.resolve(data);
  })
  .fail(function() {
    dfd.reject( "error");
  })
  return dfd.promise();
}
function autoData(res){
  elewidth = res.ele.innerWidth();
  // console.log(elewidth);
  $(res.ele).next('ul').remove();
  if(res.data.length>0){
    var autocomplete = "<ul class=\"my-autocomplete-ul\" style=\"width:"+elewidth+"px; \">";
      for(var i=0;i<res.data.length;i++){
        autocomplete+= "<li class=\"my-autocomplete-li \">"+res.data[i].text ;
        autocomplete+= "<input type=\"hidden\" class=\"company-id\" value=\""+res.data[i].id+"\"></li>";
      }  

      autocomplete+='</ul>';

    // res.ele.after().html(autocomplete);

    $(res.ele).after(autocomplete);

  }
}
//----[end] autocomplete supplier_name