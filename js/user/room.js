$(function(){         
    var $win = $(window); // or $box parent container
    var $box = $("#search_room_list .my-autocomplete-li");

    // var $title = $("#modal-card-content .modal-title");
    
    $win.on("click.Bst", function(event){ 
      if ( $box.has(event.target).length == 0 &&!$box.is(event.target)){
            // console.log("you clicked outside the box");
          $("#search_room").val('');
          $("#search_room_list").hide();
      }
    
      
    });
});


$(document).on("input","#search_room",function(e) { 
    ajaxSearchAutoComplete($(this)).then(function( res ) {
        roomData(res).done(function(data){
            $("#search_room_list").html(data).show();
            $("#search_room_list").show();
        })
    })
});


$(document).on("click touch",".btn-user-in-room-approve",function(event) {
    var rows = $(this).closest("tr") ; 
    rows.find('.room-approve').val(1);
    var html = "<span class=\"room-status active\">"+(($("#app_local").val()=='th') ? ' อนุมัติ' : 'Approved' )+"</span> <button type=\"button\" "+
    " class=\"btn btn-default btn-xs btn-user-in-room-non-approve\" "+
    " title=\""+(($("#app_local").val()=='th') ? 'ตั้งค่าเป็น รออนุมัติ' : 'Set to Wait for Approve' )+"\" >"+
    "<i class=\"fa fa-close\"></i></button>"
    rows.find('td:nth-child(4)').html(html);
    if(rows.find('.room-id').length>0 && rows.find('.room-id').val()!=0){
        var idCard = $("#id_card").val();
        var route = "/create-user/"+idCard+"/room-approve?api_token="+api_token ;
        var data = {approve:1,room_id:rows.find('.room-id').val()} ;
        ajaxPromise('PUT',route,data).done(function(data){
            swal({
              title: 'Set to Approved Success',
              type: 'success',
              showCancelButton: false,
              confirmButtonText: 'Ok'
            })
        })
    }
});
$(document).on("click touch",".btn-user-in-room-non-approve",function(event) {
    var rows = $(this).closest("tr") ; 
    rows.find('.room-approve').val(0);
    var html = "<span class=\"room-status\">"+(($("#app_local").val()=='th') ? 'รออนุมัติ' : 'Wait for Approve' )+" </span> <button type=\"button\" "+
    " class=\"btn btn-default btn-xs btn-user-in-room-approve\" "+
    " title=\""+(($("#app_local").val()=='th') ? 'ตั้งค่าเป็น อนุมัติ' : 'Set to Approved' )+"\" >"+
    "<i class=\"fa fa-check\"></i></button>"
    rows.find('td:nth-child(4)').html(html);
    if(rows.find('.room-id').length>0 && rows.find('.room-id').val()!=0){
        var idCard = $("#id_card").val();
        var route = "/create-user/"+idCard+"/room-approve?api_token="+api_token ;
        var data = {approve:0,room_id:rows.find('.room-id').val()} ;
        ajaxPromise('PUT',route,data).done(function(data){
            swal({
              title: 'Set to Wait for Approve Success',
              type: 'success',
              showCancelButton: false,
              confirmButtonText: 'Ok'
            })

        })
    }
});
$(document).on("click",".btn-user-in-room-del",function(event) {
    var rows = $(this).closest("tr") ; 
    var itemID = $.trim(rows.find('.id-card').val());
    rows.remove();
    $("#user-in-room-table tbody tr td:nth-child(2)").each(function (i) {
        var j = ++i;
        $(this).text(j);
    });

    if( $("#no_room").length > 0  ){
      if( $("#user-in-room-table tbody tr").length <= 0 ){
        $("#no_room").removeAttr("disabled");
      }
    }

  });

function roomData(res){
   var dfd = $.Deferred();
  elewidth = res.ele.innerWidth();
  $(res.ele).next('ul').remove();
  var data = res.data.response.data ;
  if(data.length>0){
    var autocomplete = "<ul class=\"my-autocomplete-ul\" style=\"width:"+elewidth+"px; \">";
      for(var i=0;i<data.length;i++){
        autocomplete+= "<li class=\"my-autocomplete-li \"> " ;
       
        autocomplete+= "<h5>&nbsp;"+data[i].text+"</h5>";

        if(typeof data[i].id !="undefind"){
          autocomplete+= "<input type=\"hidden\" class=\"search-id\" value=\""+$.trim(data[i].id)+"\">";
          autocomplete+= "<input type=\"hidden\" class=\"search-text\" value=\""+$.trim(data[i].text)+"\">";
        }

       
        autocomplete+= "</li>";
      }  

      autocomplete+='</ul>';

    // res.ele.after().html(autocomplete);

    // $(res.ele).after(autocomplete);
     dfd.resolve(autocomplete);
  }else{
     dfd.reject("");
  }
  return dfd.promise();
}