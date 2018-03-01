$(".addcard-hover").on("click",function(){



  var add_card = "<div class=\"addcard-box\"><div class=\"box box-solid\" >"+
        "<div class=\"box-header\">"+
        "<textarea class=\"txt-area-card-title form-control\" rows=\"2\" style=\"border: 0;\">"+
        "</textarea>"+"</div></div>"+
        "<button class=\"btn bg-olive margin btn-add-card\" >"+(($("#app_local").val()=='th') ? ' เพิ่ม' : ' Add ' )+"</button>"+
        "<button class=\"btn btn-close-card\" ><i class=\"fa fa-close\"></i></button></div>";
  var rows = $(this).parent(".box-solid") ; 
  rows.find(".append-card").append(add_card);
  rows.find(".box-footer").removeClass('addcard-hover').html('&nbsp;');
  
  
  var parentHeight = rows.find(".append-card")[0].scrollHeight -  $(".addcard-box")[0].scrollHeight ;
  
  rows.find(".box-body").animate({
    scrollTop: parentHeight
  },'fast');
  $(".txt-area-card-title").focus();
})

$(document).on("click",".btn-close-card",function(event) {
  // console.log("click");
  var rows = $(this).closest(".box-parent") ; 
  rows.find(".append-card").find(".addcard-box").remove();
  rows.find(".box-footer").addClass('addcard-hover').html((($("#app_local").val()=='th') ? 'สร้างงานใหม่' : 'Add a card ' ));

});



$(document).on("click",".btn-add-card",function(event) {
  var rows = $(this).closest(".box-parent") ; 
  var txt = $("textarea.txt-area-card-title").val();

  // var elementStatus = "#card_new";
  // if($("#card_new_2").length>0){ 
  //     elementStatus = "#card_new_2";
  // }


  var start = rows.find("#card_new").length ;
  // console.log('ajaxCreateQuatation',txt,start);
  ajaxCreateQuatation(txt,start).done(function(data){
    socket.emit('task',data);

    var boxId = (typeof data.quotation_id =="undefined") ? data.task_id : data.quotation_id ;

      rows.find(".append-card").find(".addcard-box").remove();
      // var card = "<div class=\"box box-solid card show-content\">"+
      //   "<div class=\"box-header\">"+
      //   "<h3 class=\"box-title\">"+txt+
      //   "</h3>"+
      //   "<input type=\"hidden\" class=\"box-id\" value=\""+boxId+"\" >"+
      //   "</div>"+
      //     "<div class=\"box-body\">"+             
      //       "<div class=\"card-member pull-right\">"+
      //       "</div>"+
      //     "</div>"+
      //   "</div>";

       

      // rows.find(".append-card").append(card);  
      rows.find(".box-footer").addClass('addcard-hover').html((($("#app_local").val()=='th') ? 'สร้างงานใหม่' : 'Add a card ' ));
      $("#first_open_card").val('true');
      OpenCard(txt,boxId);


      

    // location.reload();
    }).fail(function(txt) {
      var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
    });
});

function OpenCard(title,boxId){
  var modal = $('#modal-card-content') ;
  modal.find('.modal-title span').text(title);
  window.history.pushState("object or string", title , $("#baseUrl").val()+'/task/'+boxId);
  clearTaskData();
  $("#current_card_id").val(boxId);     ; 
  var route = "/task/"+boxId+"?api_token="+api_token ;
    var data = "" ;
    ajaxPromise('GET',route,data).done(function(data){
      // console.log('OpenCard -> createTask');
      createTask(data);
    })
}

function createTaskSetInit(data){
  if(!data.start_task) {
    $("#modal-card-content .btn_start_task").hide();
  }else{
    $("#modal-card-content .btn_start_task").show();
  }



  if(!data.todo) {
    $("#modal-card-content .btn_todo").hide();
  }else{
    $("#modal-card-content .btn_todo").show();
  }

  if(!data.accept) {
    $("#modal-card-content .btn_accept").hide();
  }else{
    $("#modal-card-content .btn_accept").show();
  }

  // console.log('first_open_card',$("#first_open_card").val());

  if($("#first_open_card").val()=="true"){
    $("#modal-card-content .task-menu-action .btn_todo").show();
    $("#modal-card-content .task-menu-flow").hide();
  }else{
    $("#modal-card-content .task-menu-action .btn_todo").hide();
    $("#modal-card-content .task-menu-flow").show();
  }

  
  if(!data.cancel_task){
    $("#modal-card-content #btn_cancel").hide();
  }else{
    $("#modal-card-content #btn_cancel").show();
  }
  

  if(!data.in_progress){
    $("#modal-card-content #btn_in_progress").hide();
  }else{
    $("#modal-card-content #btn_in_progress").show();
  }
  if(!data.pending){
    $("#modal-card-content #btn_pending").hide();
  }else{
    $("#modal-card-content #btn_pending").show();
  }
  if(!data.done){
    $("#modal-card-content #btn_done").hide();
  }else{
    $("#modal-card-content #btn_done").show();
  }

  if(!data.re_submit){
    $("#modal-card-content #btn_re_submit").hide();
  }else{
    $("#modal-card-content #btn_re_submit").show();
  }

  if(!data.duedate){
    $("#modal-card-content #task_due_date").hide();
  }else{
    $("#modal-card-content #task_due_date").show();
  }

  if(data.viewer){
    $("#btn-task-viewer").find('i').addClass('fa-eye-slash').removeClass('fa-eye');
  }else{
    $("#btn-task-viewer").find('i').addClass('fa-eye').removeClass('fa-eye-slash');
  }


  $(".task-menu-flow .title-flow").hide();
  if(data.menu_flow){
    $(".task-menu-flow .title-flow").show();
  }

  if((!data.todo||!data.accept)&&!data.cancel_task&&!data.in_progress&&!data.pending&&!data.done&&!data.re_submit){
      $(".task-menu-flow .title-flow").hide();
  }


  $(".task-menu-delete").hide();
  if(data.menu_delete){
    $(".task-menu-delete").show();
  }

  $('#description-body-readonly').hide();

  $('#edit-title').hide();
  if(data.edit_description){
    $('#edit-title').show();
  }

  is_member = data.is_member ;
  

}

$(function() {
  $(".btn_todo").on("click", function(event) {
    var cardId = $("#current_card_id").val();
    var status = 2 ;
    ajaxUpdateStatus(cardId,status).done(function(data){
          createTask(data);
      }).fail(function(txt) {
        var error = JSON.stringify(txt);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
      });
  });
});

