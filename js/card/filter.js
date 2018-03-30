var filter =  { no_categoty:0,unsign:0 ,category:[],member:[],type:  (($("#task_internal").length>0) ? 1 : 2 )  } ;


$(document).on("click",".btn-filter,.btn-show-filter",function (e) {
    $("#modal_task_filter #search_filter").val('');
    searchFilter();
});

$(document).on("input","#search_filter",function (e) {
    searchFilter();
});


$(document).on("click",".clear-filter,.btn-close-filter",function (e) {
    filter = { no_categoty:0,unsign:0 ,category:[],member:[],type:  (($("#task_internal").length>0) ? 1 : 2 )  } ;
  // searchTaskWithFiltersearchTaskWithFilter();
    $(".show-content").show();
    $(".group-but-show-filter").hide();
    $(".btn-filter").show();
    $('.media-heading').each(function () {
        $(this).text($(this).text());
    })

});


function setCheckFilterCategory(ele)
{
    var categoryId = ele.find('.task-filter-category-id').val();
    var text = ele.find('.media-heading').text();
    var inArray = $.inArray(categoryId , filter.category) ;
    if (inArray!=-1) {
        var html = "<i class=\"fa fa-check pull-right\"></i>"+text;
        ele.find('.media-heading').html(html);
    } else {
        ele.find('.media-heading').text(text);
    }

   
   

}


function showFilter()
{
    $('.show-content').hide();
  

    $('.show-content').each(function (index, el) {
        // console.log(filter.no_categoty,($(this).find('.category-label').length<=0));
         // console.log($(this).find('.card-member img').length);

         var filterNoCatogory = filter.no_categoty&&($(this).find('.category-label').length<=0) ;
         var filterUnsign = filter.unsign&&($(this).find('.card-member img').length<=0) ;

        if (filterNoCatogory||filterUnsign) {
            $(this).show();
        } else if (!filter.no_categoty&&filterUnsign) {
            $(this).show();
        } else if (!filter.unsign&&filterNoCatogory) {
            $(this).show();
        } else if (!filter.unsign&&!filter.no_categoty&&filter.member.length<=0&&filter.category.length<=0) {
            $(this).show();
        }
    });
 
  //  $('.show-content').each(function(index, el) {
  //       console.log(filter.no_categoty,($(this).find('.category-label').length<=0));
  //      // console.log($(this).find('.card-member img').length);
  //       if(!filter.unsign){
  //         $(this).show();
  //       }
  //       //console.log(filter.unsign,($(this).find('.card-member').length<=0));
  //       console.log('box-title',$(this).find('.box-title').text());

  //       if(filter.unsign&&($(this).find('.card-member img').length<=0)){
  //           $(this).show();
  //       }
       

  // });




    $('.category-label').each(function (index, el) {
        for (var i=0; i < filter.category.length; i++) {
            if ($(this).data('id')==filter.category[i]) {
                $(this).closest('.show-content').show();
            }
        }
    });

  // console.log(filter.member);
    $('.card-member img').each(function (index, el) {
        for (var i=0; i < filter.member.length; i++) {
          // console.log($(this),$(this).data('id'),filter.member[i]);
            if ($(this).data('id')==filter.member[i]) {
              // console.log($(this).closest('.show-content').length );
              // console.log("match" ,$(this).closest('.show-content').find('box-title').text() );
                $(this).closest('.show-content').show();
            }
        }
    });
  // console.log(filter.member.length,filter.category.length,filter.no_categoty,filter.unsign);
    if (filter.member.length==0&&filter.category.length==0&&filter.no_categoty==0&&filter.unsign==0) {
        $(".group-but-show-filter").hide();
        $(".btn-filter").show();
    } else {
        $(".group-but-show-filter").show();
        $(".btn-filter").hide();
    }

}

function setCheckFilterMember(ele)
{
    var memberId = ele.find('.task-filter-member-id').val();
    var text = ele.find('.media-heading').text();
    var inArray = $.inArray(memberId , filter.member) ;
    if (inArray!=-1) {
        var html = "<i class=\"fa fa-check pull-right\"></i>"+text;
        ele.find('.media-heading').html(html);
    } else {
        ele.find('.media-heading').text(text);
    }
}
function setCheckFilterUnsign(ele)
{
    var text =  (($("#app_local").val()=='th') ? 'ไม่ระบุสมาชิก' : 'Unsign' ) ;
    var ele =  $('.task-unsign ') ;
    if (filter.unsign) {
        var html = "<i class=\"fa fa-check pull-right\"></i>"+text;
        ele.find('.media-heading').html(html);
    } else {
        ele.find('.media-heading').html(text);
    }
   
}
function setCheckFilterNocategory(ele)
{
    var text =  (($("#app_local").val()=='th') ? 'ไม่ระบุหมวดหมู่' : 'No Category' ) ;
    var ele =  $('.task-no-category') ;
    if (filter.no_categoty) {
        var html = "<i class=\"fa fa-check pull-right\"></i>"+text;
        ele.find('.media-heading').html(html);
    } else {
        ele.find('.media-heading').html(text);
    }

    

}


$(document).on("click","#filter_category_list .media",function (e) {
   
    var categoryId = $(this).find('.task-filter-category-id').val();
    var text = $(this).find('.media-heading').text();
    var inArray = $.inArray(categoryId , filter.category) ;
    if (inArray==-1) {
        filter.category.push(categoryId);
    } else {
        filter.category.splice(inArray, 1);
    }
    setCheckFilterCategory($(this));
    // searchTaskWithFilter();
    showFilter();
   
});
$(document).on("click",".task-unsign .media",function (e) {
    // console.log('unsign bf',filter.unsign);
    
    filter.unsign = (filter.unsign) ? 0 : 1 ;
    setCheckFilterUnsign();
  
    showFilter();
   
});
$(document).on("click",".task-no-category .media",function (e) {
    filter.no_categoty = (filter.no_categoty) ? 0 : 1 ;
    setCheckFilterNocategory();

    showFilter();
    
});
$(document).on("click","#filter_member_list .media",function (e) {
    
    var memberId = $(this).find('.task-filter-member-id').val();
    var inArray = $.inArray(memberId , filter.member) ;
    if (inArray==-1) {
        filter.member.push(memberId);
    } else {
        filter.member.splice(inArray, 1);
    }
    setCheckFilterMember($(this));

    showFilter();
    
});

function searchTaskWithFilter()
{
    var route = systemRoute+"search/filter?api_token="+api_token;
    ajaxPromise('POST',route,filter).done(function (data) {
        $("#card_new,#card_new_2,#card_accept,#card_reject,#card_in_progress,#card_pending,#card_done").find('.append-card').html('');
        console.log(data.tasks);
        if (data.tasks.length > 0 ) {
            for (var i = 0; i < data.tasks.length; i ++) {
                cardStatusMove(data.tasks[i]);
            }
        }
    });
   
  

}



function searchFilter()
{
    // console.log('filter',filter);
    var route = systemRoute+"filter?api_token="+api_token;
    var data = {name:$("#modal_task_filter #search_filter").val(),type:(($("#task_internal").length>0) ? 1 : 2 ) };
    ajaxPromise('POST',route,data).done(function (data) {
        $("#modal_task_filter #filter_category_list").html('');
        $("#modal_task_filter #filter_member_list").html('');

        setCheckFilterUnsign();
        setCheckFilterNocategory();

        var taskFilterCategory = data.task_filter_category ;
        if (taskFilterCategory.length > 0) {
            var html = "";
            for (var i=0; i<taskFilterCategory.length; i++) {
                html += "<div class=\"media\">"+
                    "<div class=\"media-left media-middle\">"+
                      "<div style=\"background:"+taskFilterCategory[i].color+";\" class=\"img-rounded\"></div>"+
                    "</div>"+
                    "<div class=\"media-body\">"+
                    "<h5 class=\"media-heading\">"+taskFilterCategory[i].name+"</h5>"+
                    "<input type=\"hidden\" class=\"task-filter-category-id\" value=\""+taskFilterCategory[i].id+"\">"+
                    "</div>"+
                  "</div>";
            }
            $("#modal_task_filter #filter_category_list").html(html);


            $("#filter_category_list .media").each(function (index, el) {
                ($(this));
                var categoryId = $(this).find('.task-filter-category-id').val();
                var inArray = $.inArray(categoryId , filter.category) ;
               // console.log(filter.category,categoryId,inArray);
                if (inArray!=-1) {
                    setCheckFilterCategory($(this));
                }
            });
        }

        var taskFilterMember = data.task_filter_member ;
        if (taskFilterMember.length > 0) {
            var html = "";
            for (var i=0; i<taskFilterMember.length; i++) {
                html += "<div class=\"media\">"+
                "<div class=\"media-left media-middle\">"+
                "<img src=\""+taskFilterMember[i].img+"\" class=\"media-object img-circle\" style=\"width:25px\">"+
                "</div>"+
                "<div class=\"media-body\">"+
                "<h5 class=\"media-heading\">"+taskFilterMember[i].text+"</h5>"+
                "<input type=\"hidden\" class=\"task-filter-member-id\" value=\""+taskFilterMember[i].id+"\">"+
                "</div>"+
                "</div>";
            }
            $("#modal_task_filter #filter_member_list").html(html);
            $("#filter_member_list .media").each(function (index, el) {

                var memberId = $(this).find('.task-filter-member-id').val();
                var inArray = $.inArray(memberId , filter.member) ;
                if (inArray!=-1) {
                    setCheckFilterMember($(this));
                }
           
            });
        }

     
    }).fail(function () {
        $("#modal_task_filter #filter_category_list").html('');
        $("#modal_task_filter #filter_member_list").html('');
    })
     $("#modal_task_filter").modal('show');
  
}
