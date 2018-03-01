

$("#checklist-form").on("submit",function(e) { 
      newCheckList();
      return false;
});

function newCheckList(){
   var cardId = $("#current_card_id").val() ;

      var title = $("#checklist_title").val() ;
      // console.log(title,cardId);
      if (title=="" || title==" ") return false;

      var route = "/task/"+cardId+"/checklist?api_token="+api_token ;
      var data = {title:title};
      ajaxPromise('POST',route,data).done(function(data){
            socket.emit('task',data);
            createTaskChecklist(data.task_checklists);
            createTaskTableHistory(data.task_historys);
            if(typeof data.lastest_id != "undefind"){
              checklistClick(data.lastest_id);
            }

            $("#modal_task_checklist").modal('toggle');
      });
}



$(document).on("click",".checklist-txt-add-item",function(e) { 
    var ele = $(this).closest('.checklist-box') ;
    var id = ele.find('.checklist-id').val();
    var html =  "<form class=\"btn-checklist-add-item\"> "+
                "<input type=\"text\" placeholder=\"Add item...\" class=\"form-control checklist-item-name\" value=\"\">"+
                "<BR><button type=\"submit\" class=\"btn btn-primary\">Add</button>"+
                "<button class=\"btn btn-checklist-add-item-close\" ><i class=\"fa fa-close\"></i></button>"+
                "</form>";
    ele.find(".checklist-add-item").html(html);
    ele.find(".checklist-item-name").focus().select();
    

});


$(document).on("click",".btn-checklist-add-item-close",function(e) {  
    var html = "<small class=\"checklist-txt-add-item\">"+(($("#app_local").val()=='th') ? 'เพิ่มรายการ' : 'Add Checklist Item' )+"</small>";
    $(this).closest('.checklist-add-item').html(html);
});

$(document).on("click",".checklist-box >.title",function(e) {  
   var parent = $(this).closest('.checklist-box');
   var checklistId =  parent.find('.checklist-id').val();
  
      parent.find(".checkbox-title").val($.trim($(this).text()));
     
      // parent.find(".checkbox-title").select();
      parent.find(".checklist-title-edit").show(0,function(){
          parent.find(".checkbox-title").focus().select();
      });
      parent.find('.title').hide();
});


$(document).on("click",".close_edit_checklist_title",function(){
  var parent = $(this).closest(".checklist-box") ;
  parent.find(".checklist-title-edit").hide();
  parent.find('.title').show();
})

$(document).on("submit",".btn-checklist-add-item",function(e) { 
    var ele = $(this).closest('.checklist-box') ;
    var id = ele.find('.checklist-id').val();
    var cardId = $("#current_card_id").val() ;
    var title = $.trim(ele.find('.checklist-item-name').val());
    var route = "/task/"+cardId+"/checklist/"+id+"/item?api_token="+api_token;
    var data = { title:title }
    ajaxPromise('POST',route,data).done(function(data){
       socket.emit('task',data);
       createTaskChecklist(data.task_checklists);
       if(typeof data.lastest_id != "undefind"){
          checklistClick(data.lastest_id);
       }
    });
    return false;
});
$(document).on("click",".btn-checklist-remove-item",function(e) { 
    var ele = $(this).closest('li') ;
    var id = ele.find('.checklist-item-id').val();
    var cardId = $("#current_card_id").val() ;
    var route = "/task/"+cardId+"/checklist/item/"+id+"?api_token="+api_token;
    ajaxPromise('DELETE',route,null).done(function(data){
       socket.emit('task',data);
       createTaskChecklist(data.task_checklists);
       if(typeof data.lastest_id != "undefind"){
          checklistClick(data.lastest_id);
       }
    });
});

$(document).on("click",".checklist-item-desc",function(e) { 
  var parent = $(this).closest('.checklist-item-element');
  parent.find(".checklist-item-desc-edit").show(0,function(){
          parent.find(".checkbox-item-title").val(parent.find('.checklist-item-desc').text()).focus().select();
      });
  parent.find('.checklist-item-desc').hide();
});

$(document).on("click",".close-edit-checklist-item-desc",function(){
  var parent = $(this).closest(".checklist-item-element") ;
  parent.find(".checklist-item-desc-edit").hide();
  parent.find('.checklist-item-desc').show();
})
$(document).on("submit",".save_edit_checklist_item_desc",function(){
    var ele = $(this).closest('.checklist-item-element') ;
    var id = ele.find('.checklist-item-id').val();
    var cardId = $("#current_card_id").val() ;
    var route = "/task/"+cardId+"/checklist/item/"+id+"?api_token="+api_token;
    var title = ele.find('.checkbox-item-title').val();
    var data = { title :title } ;
    ajaxPromise('PUT',route,data).done(function(data){
       socket.emit('task',data);
       createTaskChecklist(data.task_checklists);
       createTaskTableHistory(data.task_historys);
    });
    return false;
});


$(document).on("change",".checklist-item",function(){
  if (!is_member){
    return false;
  }
  var cardId = $("#current_card_id").val();
  var ele = $(this).closest('li') ;
  var id = ele.find('.checklist-item-id').val();

  var route = "/task/"+cardId+"/checklist/item/"+id+"?api_token="+api_token;
    ajaxPromise('PUT',route,null).done(function(data){
       socket.emit('task',data);
       createTaskChecklist(data.task_checklists);
       if(typeof data.lastest_id != "undefind"){
          checklistClick(data.lastest_id);
       }
    });
})
$(document).on("click",".btn-checklist-delete",function(){
    var ele = $(this).closest('.checklist-box') ;
    var id = ele.find('.checklist-id').val();
    var cardId = $("#current_card_id").val() ;
    var route = "/task/"+cardId+"/checklist/"+id+"?api_token="+api_token;
    ajaxPromise('DELETE',route,null).done(function(data){
       socket.emit('task',data);
       createTaskChecklist(data.task_checklists);
       createTaskTableHistory(data.task_historys);
    });
});
$(document).on("submit",".save_edit_checklist_title",function(){
    var ele = $(this).closest('.checklist-box') ;
    var id = ele.find('.checklist-id').val();
    var cardId = $("#current_card_id").val() ;
    var route = "/task/"+cardId+"/checklist/"+id+"?api_token="+api_token;
    var title = ele.find('.checkbox-title').val();
    var data = { title :title } ;
    ajaxPromise('PUT',route,data).done(function(data){
       socket.emit('task',data);
       createTaskChecklist(data.task_checklists);
       createTaskTableHistory(data.task_historys);
    });
    return false;
});



function checklistClick(lastestId){
   $("#modal-card-content #task_checklist .checklist-box").each(function(index, el) {
      if($(this).find('.checklist-id').val()==lastestId){
          $(this).find('.checklist-txt-add-item').click();
      }
   });
}

function createTaskChecklist(data){
    var cardId = $("#current_card_id").val();
    var checklist_success = 0 ;
    var checklist_total = 0;
    
    if(data.length>0){
        var table ="";
        for (var i = 0 ; i<data.length ;i++){ 
            table += "<div class=\"col-sm-12 checklist-box\">";
            var  total_checklist = data[i].item.length ; 
            
            var check_checklist = 0 ;
            var hasItem = false;
            var percent = 0 ;
            if(data[i].item.length>0){
              hasItem =true;
               for (var jj =0 ; jj<data[i].item.length; jj++){ 
                  if(data[i].item[jj].status){
                    check_checklist++;
                  }
               }
               percent= Math.floor((check_checklist*100)/total_checklist) ;


            }

           

            if (data[i].created_by==user_id){
              table += "<a href=\"javascript:void(0)\" class=\"pull-right btn-checklist-delete\">"+(($("#app_local").val()=='th') ? ' ลบ' : ' Delete' )+"</a>";      
            }       
            table +="<h4 class=\"title cp\">"+
                    "<i class=\"fa fa-check-square-o\"></i>&nbsp;"+data[i].title+"</h4> "+
                     "<div class=\"row checklist-title-edit \" style=\"display:none;\">"+
                        "<form class=\"save_edit_checklist_title\">"+
                        "<div class=\"col-xs-2\">"+
                        "<input type=\"text\"  class=\"form-control checkbox-title\" value=\""+data[i].title.replace(/'/g, "\'")+"\">"+
                        "</div>"+
                        "<div class=\"col-xs-2\">"+
                        "<button type=\"submit\" class=\"btn btn-sm btn-default btn-flat\"><i class=\"fa fa-save\"></i></button>"+
                         "<button  type=\"button\" class=\"btn btn-sm btn-default btn-flat close_edit_checklist_title\"><i class=\"fa fa-close\"></i></button>"+
                        "</div>"+
                        "</form>"+
                        "</div>"+
                    "<input type=\"hidden\" class=\"checklist-id\" value=\""+data[i].id+"\">";
            
            if(hasItem){
              table +="<div class=\"row\">"+
                      "<div class=\"text-right\" style=\"width:5%; display:inline-block;float:left;\"><span>"+percent+"% </span></div>"+
                      "<div class=\"\" style=\"width:95%;margin-top:10px;\">"+
                        "<div class=\"progress progress-xxs\">"+
                          "<div class=\"checklist-progress-bar progress-bar progress-bar-success progress-bar-striped\" "+
                            "role=\"progressbar\" aria-valuenow=\""+percent+"\" aria-valuemin=\"0\" "+
                            "aria-valuemax=\"100\" style=\"width: "+percent+"%\">"+
                              
                          "</div>"+
                        "</div>"+
                      "</div>"+
                    "</div>";
            }
                    
            table +="<div class=\"form-group checklist-box-item\">";
            if (hasItem){
              table += "<ul>";
              for (var j =0 ; j<data[i].item.length; j++){
                table += "<li class=\"checklist-item-element\">"+
                      "<span class=\"pull-right\">";
                       if (data[i].created_by==user_id){
                table += "<button type=\"button\" class=\"btn btn-default btn-checklist-remove-item btn-xs\" title=\"Remove\">"+
                        "<i class=\"fa fa-times\"></i></button>";
                        }
                table +=       "</span>"+
                      "<span style=\"width:25px;float:left;\"><input type=\"checkbox\" ";
                if(data[i].item[j].status){
                  table += " checked ";
                  checklist_success ++;
                }

                if (!is_member){
                    table += " disabled ";
                }

              table += " class=\"checklist-item square-green\"></span>"+
                  "<p class=\"checklist-item-desc\" style=\"width:90%;\">"+data[i].item[j].title+
                  "</p>"+
                   "<div class=\"row checklist-item-desc-edit \" style=\"display:none;\">"+
                        "<form class=\"save_edit_checklist_item_desc\">"+
                        "<div class=\"col-xs-2\">"+
                        "<input type=\"text\"  class=\"form-control checkbox-item-title\" value=\""+data[i].item[j].title.replace(/'/g, "\'")+"\">"+
                        "</div>"+
                        "<div class=\"col-xs-2\">"+
                        "<button type=\"submit\" class=\"btn btn-sm btn-default btn-flat\"><i class=\"fa fa-save\"></i></button>"+
                         "<button  type=\"button\" class=\"btn btn-sm btn-default btn-flat close-edit-checklist-item-desc\"><i class=\"fa fa-close\"></i></button>"+
                        "</div>"+
                        "</form>"+
                        "</div>"+
                  "<input type=\"hidden\"  class=\"checklist-item-id\" value=\""+data[i].item[j].id+"\">"+
                  ""+
                "</li>";
                checklist_total ++;
              }
              table += "</ul>";
              
            }
            table +=  "</div>"+
                      "<div class=\"checklist-add-item\">"+
                        "<small class=\"checklist-txt-add-item\">"+(($("#app_local").val()=='th') ? 'เพิ่มรายการ' : 'Add Checklist Item' )+"</small>"+
                      "</div>"+
                      "</div>" ;
        }
       
        $("#modal-card-content #task_checklist").html(table);


       

        $("#modal-card-content #task_checklist").show();
    }else{
        $("#modal-card-content #task_checklist").html('');
        $("#modal-card-content #task_checklist").hide();
       
    }
}
