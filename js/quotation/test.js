
$(document).on("click",".link-attachment-file",function () {
    var boxId = $("#current_card_id").val() ;
    var companyId = $(this).closest('td').find('.file-company-id').val();
    console.log('[link-attachment-file]',companyId);

    ajaxGetQuotationCompanyItemAttachment(boxId,companyId).done(function (data) {

        var file = data.attachment ;
        
        var element = $("#modal-attachment").find('.modal-body') ;

        var appendData = "<ul>";
        for (var i =0; i<file.length; i++) {
            appendData += "<li><a href=\" "+file[i].img+" \" target=\"_blank\" >"+file[i].filename+"</a></li>" ;
        }
        appendData += "</ul>";
        console.log(appendData);
        element.html(appendData);
        $("#modal-attachment").modal('show');
    }).fail(function (txt) {
        swal(
            'Oops...',
            txt,
            'error'
        )
    });

    // if(companyAttachment==""){
    //  swal(
    //        'Oops...',
    //        'ไม่ได้แนบไฟล์สำหรับรายการนี้ค่ะ',
    //        'error'
    //      )
    // }else{
    //  var file = companyAttachment;
        
            

        // imgPath = $("#modal-attachment").find('img').attr("data-img-path");
        // $("#modal-attachment").find('img').attr("src",imgPath+"/"+companyAttachment);
        // $("#modal-attachment").modal('show');
    // }
});

// $(document).on("click","#modal-card-content .modal-title span",function(){
//  var cardId = $("#current_card_id");
//  var title =  $(this).text();
//  console.log(title);
//  var input = "<input type=\"text\" id=\"card_title\" class=\"form-control\" value=\""+title+"\" >";
//  $(this).parent().html(input);
//  $("#modal-card-content .modal-title").addClass('active-edit-title');
// });



$(document).on("click",".show-content",function (event) {
    var boxId = $(this).find(".box-id").val() ;
    console.log("click",boxId);
    var title = boxId+" "+$(this).find(".box-title").text() ;
    var modal = $('#modal-card-content') ;
    modal.find('.modal-title span').text(title);

    window.history.pushState("object or string", title , $("#baseUrl").val()+'/purchase/quotation/'+boxId);

    clearQuotationData();

    

    
    $("#current_card_id").val(boxId);     ;
    $("#company_id").val('');

    ajaxGetCardItem(boxId).done(function (data) {
        createQuatationTable(data);
        createQuatationTableItem(data.quotation_items);
        createQuatationTableHistory(data.quotatoin_historys);
        createQuatationCard(data.quotation);
        createQuatationSetInit(data.status);
        $("#modal-card-content").modal('show');
    }).fail(function (txt) {
        swal(
            'Oops...',
            txt,
            'error'
        )
    });
    // if($("#table-quatation").length){
    //  $("#section-company").show();
    // }
});

$(".btn-quotation-new").on("click",function () {
    $("#modal-card-content").modal("toggle");
    $("#modal-quotation-item").modal("toggle");
});

$(".btn-quotation-company").on('click',function () {
    getDataQuatationItem().done(function (data) {
        console.log(data);
        // createQuatationTable(data);

        // for(var i =0;i<data.item.length;i++){
        //  // data.item[i].id=
        // }


        // createQuatationTableItem(data.item);
        addQuotationItem();
        $("#modal-quotation-company").modal('show');
    }).fail(function (txt) {
        swal(
            'Oops...',
            txt,
            'error'
        )
    });
});

$(document).on("click",".btn-edit-quotation-company",function (event) {
    var boxId = $("#current_card_id").val() ;
    var companyId = $(this).closest('th').find(".company-id").val() ;
    $("#company_id").val(companyId);
    console.log("[ btn-edit-quotation-company ] click",companyId);
    // addQuotationItem();
    ajaxGetQuotationItemData(boxId,companyId).then(function (data) {
        setCompanyData(data);
        // createQuatationTable(data);
        // createQuatationTableItem(data.quotation_items);
        // $("#modal-card-content").modal('show');
    }).fail(function (txt) {
        swal(
            'Oops...',
            txt,
            'error'
        )
    });
});

function setCompanyData(data)
{
    

    var company = data.company;
    var item = data.quotation_items;
    var companyItem = data.quotation_company_items;
    var quotationCompany = data.quotation_companys;


    console.log(company);
    console.log(item);
    console.log(companyItem);
    console.log(quotationCompany);

    $("#supplier_name").val(company.name);
    $("#supplier_address").val(company.address);
    $("#contact_name").val(company.contact_name);
    $("#contact_tel").val(company.contact_tel);
    $("#contact_email").val(company.contact_email);

    if (quotationCompany !== null) {
        $(".total-price-before-vat").text(quotationCompany.price_b4_vat);
        if (quotationCompany.discount>0) {
            $(".price-discount").text(-quotationCompany.discount);
        }
        
        $(".price-vat").text(quotationCompany.vat);
        $(".total-price-net").text(quotationCompany.price_net);
        $(".company-remark").text(quotationCompany.remark);
    }

    
    var table = '';
    for (var i =0; i< item.length; i++) {
        console.log(item[i]);
        table += "<tr>"+
                "<td>"+(i+1)+"<input type=\"hidden\" value=\""+(item[i].id)+"\" class=\"quotation-item-id\"></td>"+
                "<td><span >"+(item[i].name)+"</span></td>"+
                "<td><span >"+(item[i].amount)+"</span></td>";
        console.log(typeof company);
        hasCompanyItem = false;
        for (var k =0; k < companyItem.length; k++) {
            console.log(companyItem[k].quotation_item_id,item[i].id);
            if (companyItem[k].quotation_item_id==item[i].id) {
                table += "<td><input type=\"text\" class=\"item-price-per-unit\" value=\""+companyItem[k].price_per_unit+"\"></td>"+
                "<td><span class=\"item-price-total\">"+companyItem[k].price+"</span></td>" ;
                hasCompanyItem = true;
            }
        }

        if (!hasCompanyItem) {
            table += "<td><input type=\"text\" class=\"item-price-per-unit\" value=\"\"></td>"+
            "<td><span class=\"item-price-total\"></span></td>" ;
        }

                                            
        table += "</tr>";
    }

    console.log(table);

    $("#table-quatation-company tbody").html(table);
    $("#modal-card-content").modal("hide");
    $("#modal-quotation-company").modal("show");
}

function createQuatationTable(data)
{
    $(".btn-quotation-new").text("Create item");
    var company = data.quotation_companys;
    var item = data.quotation_items;
    var companyItem = data.quotation_company_items;
    var quotation = data.quotation ;


    console.log(data.quotation_items);
    console.log(item, typeof item);

    if (item.length <= 0) {
        console.log("not data found");
        $("#data-summary-quotation-table").html('');
        $("#table-quatation tbody").html('');
        return false;
    }

    var table = "<table id=\"table-quatation-summary\" "+
                " class=\"table table-bordered table-striped\">"+
                "<thead>"+
                "<tr>"+
                "<th rowspan=\"2\" class=\"vm-ct\" width=\"50\">ลำดับ</th>"+
                "<th rowspan=\"2\" class=\"vm-ct\">รายละเอียด</th>"+
                "<th rowspan=\"2\" class=\"vm-ct\">จำนวน</th>";

    if (typeof company != "undefined"&&company.length>0) {
        for (var j =0; j < company.length; j++) {
            if (company[j].company_id==data.voted_company_id) {
                $("#voted").html('คุณโหวตบริษัท <BR> '+company[j].name+' ค่ะ');
            }
            console.log(company[j].company_id,quotation.vote_winner);
            if (company[j].company_id==quotation.vote_winner) {
                $("#success_vote").html('สรุปผลโหวต <BR> เลือกข้อเสนอของ <BR> บริษัท '+company[j].name);
            }
            

            table += "<th colspan=\"2\"  >"+
                     
                     "<span class=\"company-name\"> "+
                     
                     company[j].name+"</span>"+
                     "<input type=\"hidden\" class=\"company-id\" value=\""+company[j].company_id+"\">";
                     

            table += "<button class=\"btn btn-default btn-xs btn-edit-quotation-company pull-right\" >"+
                     "<i class=\"fa fa-pencil\"></i></button>"+
                     "</th>" ;
        }
    }
        table += "</tr>";

    if (typeof company != "undefined"&&company.length>0) {
        table += "<tr>" ;
        for (var j =0; j < company.length; j++) {
            table += "<th>Price / Unit</th>"+
                     "<th>Amount / Bath</th>" ;
        }
        table += "</tr>"    ;
    }



        table += "</thead><tbody>"  ;

    // console.log('item',item);
    // console.log('company',company);
    // console.log('companyItem',companyItem);

    // console.log('item.length',item.length);
    // console.log('company.length',company.length);
    // console.log('companyItem.length',companyItem.length);

    for (var i =0; i< item.length; i++) {
        // console.log(item[i]);
        table += "<tr>"+
                "<td>"+(i+1)+"<input type=\"hidden\" value=\""+(item[i].id)+"\"></td>"+
                "<td><span >"+(item[i].name)+"</span></td>"+
                "<td><span >"+(item[i].amount)+"</span></td>";
        // console.log(typeof company);

        if (typeof company != "undefined") {
            for (var j =0; j < company.length; j++) {
                hasCompanyItem = false;
                for (var k =0; k < companyItem.length; k++) {
                    // console.log(companyItem[k].company_id,company[j].company_id,
                        // companyItem[k].quotation_item_id,item[i].id);
                    if (companyItem[k].company_id==company[j].company_id &&
                        companyItem[k].quotation_item_id==item[i].id) {
                        table += "<td>"+companyItem[k].price_per_unit+"</td>"+
                        "<td><span class=\"item-price-total\">"+companyItem[k].price+"</span></td>" ;
                        hasCompanyItem = true;
                    }
                }
                if (!hasCompanyItem) {
                    table += "<td>0</td>"+
                                "<td><span class=\"item-price-total\">0</span></td>"    ;
                }
            }
        }
                                            
        table += "</tr>";
    }

    table += "</tbody><tfoot>";

    table += "<tr>"+
                 "<td colspan=\"3\">ราคารวมก่อน VAT</td>";
    for (var k =0; k < company.length; k++) {
        table += "<td></td><td class=\"total-price-before-vat\">"+company[k].price_b4_vat+"</td>";
    }
    table += "</tr>";

    table += "<tr>"+
             "<td colspan=\"3\">ภาษีมูลค่าเพิ่ม 7%</td>";
    for (var k =0; k < company.length; k++) {
        table += "<td></td><td class=\"price-vat\">"+company[k].vat+"</td>";
    }
    table += "</tr>";

    table += "<tr>"+
             "<td colspan=\"3\">ส่วนลด</td>";
    for (var k =0; k < company.length; k++) {
        table += "<td></td><td class=\"price-discount\">"+((company[k].discount>0) ? "-"+company[k].discount : 0 )+"</td>";
    }
    table += "</tr>";

    table += "<tr>"+
             "<td colspan=\"3\">ราคาสุทธิ</td>";
    for (var k =0; k < company.length; k++) {
        table += "<td></td><td class=\"total-price-net\">"+company[k].price_net+"</td>";
    }
    table += "</tr>";

    table += "<tr>"+
             "<td colspan=\"3\">เงื่อนไขการชำระเงิน</td>";
    for (var k =0; k < company.length; k++) {
        table += "<td colspan=\"2\">เครดิต 30 วัน</td>";
    }
    table += "</tr>";

    table += "<tr>"+
             "<td colspan=\"3\">รับประกัน</td>";
    for (var k =0; k < company.length; k++) {
        table += "<td colspan=\"2\">6 เดือน</td>";
    }
    table += "</tr>";

    table += "<tr>"+
             "<td colspan=\"3\">แนบเอกสาร</td>";
    for (var k =0; k < company.length; k++) {
        table += "<td colspan=\"2\">";

        // attachmentData = [];
        // hasAttachment = false;
        // for (var l =0 ; l < attachment.length;l++){
        //  if (company[k].company_id==attachment[l].company_id){
        //      attachmentData.push(attachment[l]);
        //      hasAttachment = true;
        //  }
        // }
        if (company[k].has_attachment) {
            table += "<a href=\"javascript:void(0)\" class=\"link-attachment-file\" >"+
                    "คลิก</a>"+
                    "<input type=\"hidden\" class=\"file-company-id\" "+
                    " value=\""+ company[k].company_id +"\">" ;
        } else {
            table += "-";
        }
        table +="</td>";
    }
            //todo  //<td colspan="2"> <input type="file"> </td>
    table += "</tr>";

    table += "<tr>"+
             "<td colspan=\"3\">หมายเหตุ</td>";
    for (var k =0; k < company.length; k++) {
        table += "<td colspan=\"2\">"+company[k].remark+"</td>";
    }
    table += "</tr>";

    // for(var i =0;i< item.length ; i++){
    

    // }
    table += "</tfoot><table>";

    $(".btn-quotation-new").text("แก้ไขรายการของ");
    $("#data-summary-quotation-table").html(table);
}


function createQuatationTableItem(data)
{
    console.log(data);
    console.log('[createQuatationTableItem]',$("#table-quatation tbody tr").length);

    if (data.length>0) {
        $("#insert_new_item").val("false");
    }

    if ($("#table-quatation tbody tr").length==0) {
        for (var i =0; i< data.length; i++) {
            // console.log(data[i]);
            table = "<tr>"+
                    "<td><button type=\"button\" "+
                    " class=\"btn btn-danger btn-xs btn-quotation-del-item\" >"+
                    "<i class=\"fa fa-close\"></i></button>"+
                    "<input type=\"hidden\" class=\"quotation-item-id\" value=\""+(data[i].id)+"\">"+
                    "</td>"+
                    "<td>"+(i+1)+"</td>"+
                    "<td><span >"+(data[i].name)+"</span></td>"+
                    "<td><span >"+(data[i].amount)+"</span></td>"+
                    "</tr>";
            $("#table-quatation tbody").append(table)
        }
    } else {
        table = '';
        for (var i =0; i< data.length; i++) {
            table += "<tr>"+
                    "<td><button type=\"button\" "+
                    " class=\"btn btn-danger btn-xs btn-quotation-del-item\" >"+
                    "<i class=\"fa fa-close\"></i></button>"+
                    "<input type=\"hidden\" class=\"quotation-item-id\" value=\" "+(data[i].id)+" \">"+
                    "</td>"+
                    "<td>"+(i+1)+"</td>"+
                    "<td><span >"+(data[i].name)+"</span></td>"+
                    "<td><span >"+(data[i].amount)+"</span></td>"+
                    "</tr>";
        }
        $("#table-quatation tbody").html(table);
    }

    
}

function createQuatationTableHistory(data)
{

    if (data.length>0) {
        var table = '';
        for (var i = 0; i<data.length; i++) {
            console.log(data[i].his_text);
            table += "<h6>"+data[i].his_text+"</h6>";
        }
        console.log(table);
        $("#modal-card-content").find('.history').html(table);
    }

    
}

function createQuatationCard(data)
{
    if (data.status==2) {
        $("#start_vote,#edit-title").hide();
        $(".section-quotation-button-new,.btn-edit-quotation-company").hide();
    } else {
        $("#start_vote,#edit-title").show();
        $(".section-quotation-button-new,.btn-edit-quotation-company").show();
    }
    if (data.vote_winner!=null) {
        $("#start_vote,#edit-title").hide();
        $(".section-quotation-button-new,.btn-edit-quotation-company").hide();
    }

}




function ajaxGetQuotationItemData(boxId,companyId)
{
    var dfd = $.Deferred();
    var url = $("#apiUrl").val() ;
    $.ajax({
        url: url+"/purchase/quotation/"+boxId+"/company/"+companyId,
        type: 'GET',
        dataType: 'json'
    })
    .done(function (res) {
        console.log(res);
        if (res.result=="true") {
            dfd.resolve(res.response);
        } else {
            dfd.reject(res.errors);
        }
    })
    .fail(function () {
        dfd.reject("error");
    })
    return dfd.promise();
}

function ajaxGetCardItem(boxId)
{
    var dfd = $.Deferred();
    var url = $("#apiUrl").val() ;
    $.ajax({
        url: url+"/purchase/quotation/data/"+boxId,
        type: 'GET',
        dataType: 'json'
    })
    .done(function (res) {
        console.log(res);
        if (res.result=="true") {
            dfd.resolve(res.response);
        } else {
            dfd.reject(res.errors);
        }
    })
    .fail(function () {
        dfd.reject("error");
    })
    return dfd.promise();
}

function ajaxGetQuotationCompanyItemAttachment(boxId,companyId)
{
    var dfd = $.Deferred();
    var url = $("#apiUrl").val() ;
    $.ajax({
        url: url+"/purchase/quotation/"+boxId+"/attach/"+companyId,
        type: 'GET',
        dataType: 'json'
    })
    .done(function (res) {
        console.log(res);
        if (res.result=="true") {
            dfd.resolve(res.response);
        } else {
            dfd.reject(res.errors);
        }
    })
    .fail(function () {
        dfd.reject("error");
    })
    return dfd.promise();
}

function addQuotationItem()
{
    hideInputItem();
    $("#table-quatation-company tbody").html('');
    var modalItem = $('#modal-quotation-item') ;
    var modalCompany = $('#modal-quotation-company') ;
    modalItem.find("#table-quatation tbody tr").each(function () {
        content = "<tr>"+$(this).html()+"<td><input type=\"text\" class=\"item-price-per-unit\"  ></td><td><span class=\"item-price-total\">0</span></td></tr>" ;
        console.log(content);
        modalCompany.find("#table-quatation-company tbody ").append(content);
    });

    modalCompany.find("#table-quatation-company tbody tr td:first-child").each(function () {
        getItemId = $(this).find('.quotation-item-id').val();
        console.log($(this).next('td').append("<input type=\"hidden\" value=\""+getItemId+"\" class=\"quotation-item-id\" >"));

        $(this).remove() ;
    });

    $("#modal-quotation-item").modal("hide");
    $("#modal-quotation-company").modal("show");

}


function clearQuotationData()
{
    $("#card_title").val("");
    $("#insert_new_item").val("true");
    $("#modal-card-content").find('.history').html('');
    $('#modal-card-content input,#modal-card-content textarea').val('');
    $('#modal-quotation-item input,#modal-quotation-item textarea').val('');
    $('#modal-quotation-company input,#modal-quotation-company textarea').val('');
    $("#supplier_name").next('ul').remove();
}

function createQuatationSetInit(data)
{
    console.log(data.add_item);
    if (!data.voting) {
        $("#modal-card-content #voting").hide();
    }
    if (!data.voted) {
        $("#modal-card-content #voted").hide();
    }
    if (!data.add_item) {
        $(".section-quotation-button-new").hide();
    }
    if (!data.cancel_vote) {
        $("#modal-card-content #cancel_vote").hide();
    }
    if (!data.winner) {
        $("#modal-card-content #success_vote").hide();
    } else {
        $("#modal-card-content #voted").hide();
    }
}


