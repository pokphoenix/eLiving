$(".btn-task-category-add").on("click", function (event) {
    var cardId = $("#current_card_id").val();
    var categoryId = $(this).find('.category-id').val();
    var route = systemRoute+cardId+"/category/"+categoryId+"?api_token="+api_token ;


    ajaxPromise('POST',route,null).done(function (data) {
        createTaskTableHistory(data.task_historys);
         socket.emit(socketRoute,data);
         // console.log('btn-task-category-add',data);
        if (data.task_category==null) {
            $(".btn-task-category-add").each(function (index, el) {
                if ($(this).find('.category-id').val()==categoryId) {
                    $(this).css(
                        { 'border-left':'none'
                        
                        }
                    )
                    var table ="<i class=\"fa fa-check pull-right\"></i>"
                    $(this).find('i').remove();
                }
            });
            $("#task_category").hide();
            $(".show-content").find('.box-id').each(function (index, el) {
                if ($(this).val()==cardId) {
                    var ele = $(this).closest('.show-content').find('.category-label');
                    ele.remove();
                }
            });
        } else {
            createTaskCategory(data.task_category,cardId);
        }
         $("#modal_task_category").modal('toggle');
    }).fail(function (txt) {
        var error = JSON.stringify(txt);
                       swal(
                           'Error...',
                           error,
                           'error'
                       )
    });
});



function createTaskCategory(data,cardId)
{
    console.log('[createTaskCategory]',data);
    $("#task_category").hide();



    
    // console.log('cardId',cardId,$("#app_local").val());
    if (data!=null) {
        $("#task_category").find('small').css({ background:data.color,color:'#FFF' }).text((($("#app_local").val()=='th') ? data.name_th : data.name_en ));
        $("#task_category").show();

        $(".btn-task-category-add").each(function (index, el) {
            if ($(this).find('.category-id').val()==data.id) {
                $(this).css(
                    { 'border-left':'5px solid rgba(0, 0, 0, 0.3)'
                    }
                )
                
                if ($(this).find('.fa-check').length <=0 ) {
                    var table ="<i class=\"fa fa-check pull-right\"></i>";
                    $(this).find('h5').prepend(table);
                }
            } else {
                $(this).css(
                    { 'border-left':'0'
                    }
                )
                
                if ($(this).find('.fa-check').length >=1 ) {
                    $(this).find('.fa-check').remove();
                }
            }
        });
    }

    
}
