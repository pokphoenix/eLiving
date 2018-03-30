$(document).on("click",".btn-ban-user",function () {

    var parent = $(this).closest('.box-widget') ;
    
    var created_by = parent.find('.created-by').val();
    var created_name = parent.find('.username a').text();
    var created_by_img = parent.find('.user-block img').attr('src');

    console.log(created_by,created_name,created_by_img);

    swal({
        title: (($("#app_local").val()=='th') ? 'คุณแน่ใจไหม?' : 'Are you sure?' ) ,
        text: (($("#app_local").val()=='th') ? 'คุณต้องการระงับใช้งานผู้ใช้คนนี้ใช่หรือไม่' : "You want to ban this user!" ) ,
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: (($("#app_local").val()=='th') ? 'ยืนยัน' : 'Sure' ),
        cancelButtonText: (($("#app_local").val()=='th') ? 'ยกเลิก' : 'Cancel' ),
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-default',
        buttonsStyling: false,
        reverseButtons: true
    }).then((result) => {
        if (result.value) {
            var post_id = parent.find('.post-id').val();
            var route = "/post/"+post_id+"/ban?api_token="+api_token ;
            ajaxPromise('POST',route,null).done(function (data) {
                var html =  "<div class=\"item\">"+
                    "<div class=\"pull-right\">"+
                        "<button class=\"btn btn-success btn-xs btn-unban-user\" "+
                        " title=\"@lang('post.unban_user')\" "+
                        " data-id=\""+created_by+"\" > <i class=\"fa fa-user-plus\"></i> "+
                        "</button>"+
                    "</div>"+
                    "<img src=\""+created_by_img+"\">"+
                    "<p class=\"message\" >"+
                        "<a href=\"javascript:void(0)\" class=\"name\" "+
                        " style=\"margin-top:5px;\">"+created_name+
                        "</a>"+
                    "</p>"+
                "</div>";
                console.log(html);
                var append = true ;
                $("#member_baned_list .item").each(function () {
                    if ($(this).find('.btn-unban-user').data('id')==created_by) {
                        append = false ;
                    }
                })

                if (append) {
                    $("#member_baned_list").append(html);
                }
               
            })
        } else if (result.dismiss === 'cancel') {
        }
        })
    


    
});
$(document).on("click",".btn-unban-user",function () {

    var user_id = $(this).data('id') ;
    var parent = $(this).closest('.item');
    console.log(parent.length);
    swal({
        title: (($("#app_local").val()=='th') ? 'คุณแน่ใจไหม?' : 'Are you sure?' ) ,
        text: (($("#app_local").val()=='th') ? 'คุณต้องการปลดการระงับใช้งานผู้ใช้คนนี้ใช่หรือไม่' : "You want to un ban this user!" ) ,
        type: 'info',
        showCancelButton: true,
        confirmButtonText: (($("#app_local").val()=='th') ? 'ยืนยัน' : 'Sure' ),
        cancelButtonText: (($("#app_local").val()=='th') ? 'ยกเลิก' : 'Cancel' ),
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-default',
        buttonsStyling: false,
        reverseButtons: true
    }).then((result) => {
        if (result.value) {
            var route = "/post/"+user_id+"/unban?api_token="+api_token ;
            ajaxPromise('POST',route,{'_method':'DELETE'}).done(function (data) {
                parent.remove();
            })
        } else if (result.dismiss === 'cancel') {
        }
        })
    


    
});

$(document).on("click",".btn-like",function () {
    var ele = $(this) ;
    var parent = $(this).closest('.box-widget') ;
    var postId = parent.find('.post-id').val();
    var route = "/post/"+postId+"/like?api_token="+api_token;
    ajaxPromise('POST',route,{'_method':'PUT'}).done(function (data) {
        console.log(data);
        var text = data.post_like+" likes - "+data.post_comment+" comments";
        parent.find(".like-comment").text(text);
        
        var statusLike = (data.like_status) ? 'unlike' : 'like' ;
        ele.find('span').text(statusLike);
        
        if (data.like_status) {
            ele.find('i').removeClass('fa-thumbs-o-up').addClass('fa-thumbs-o-down');
        } else {
            ele.find('i').removeClass('fa-thumbs-o-down').addClass('fa-thumbs-o-up');
        }

    });

});

$(".comment-form").submit(function (event) {
    var ele = $(this) ;
    var parent = $(this).closest('.box-widget') ;
    var postId = parent.find('.post-id').val();
    var route = "/post/"+postId+"/comment?api_token="+api_token;
    var text = $(this).find(".comment-text").val() ;
    if (text!=""||text==" ") {
        var data = { description:text } ;
        ajaxPromise('POST',route,data).done(function (data) {
            console.log(data);
            
            createComment(data.post_id,data.post_comments);
            ele.find(".comment-text").val('');
        });
    }
    
    return false;
});

function createComment(id,data)
{
    var html = '';
    if (data.length>0) {
        for (var i=0; i<data.length; i++) {
            html +="<div class=\"box-comment\">"+
                    "<img class=\"img-circle img-sm\" src=\""+data[i].img+"\" >"+
                    "<div class=\"comment-text\">"+
                        "<span class=\"username\">"+
                        data[i].user_name+
                        "<span class=\"text-muted pull-right\">"+
                        moment.unix(data[i].ts_created_at).format("D/MM/YYYY HH:mm");
            if (data[i].user_id==userId) {
                html += "<button type=\"button\" class=\"btn btn-default btn-comment-delete-real btn-xs\" title=\"Remove\">"+
                            "<i class=\"fa fa-close\"></i>"+
                        "</button>";
            }
            html +=         "</span>"+
                      "</span>"+
                data[i].comment_description+
                "</div>"+
            "</div>";
        }
    }
    $(".post-id").each(function (index, el) {
        if ($(this).val()==id) {
            $(this).closest('.box-widget').find(".box-comments").html(html);
        }
    });
}
