function autoData(res,tool){
   var dfd = $.Deferred();
  var thumbnail = false ;
  if(tool.thumbnail){
    thumbnail = tool.thumbnail ;
  }
  elewidth = res.ele.innerWidth();
  $(res.ele).next('ul').remove();
  if(res.data.length>0){
    var autocomplete = "<ul class=\"my-autocomplete-ul\" style=\"width:"+elewidth+"px; \">";
      for(var i=0;i<res.data.length;i++){
        autocomplete+= "<li class=\"my-autocomplete-li \"> " ;
        if(thumbnail){
          autocomplete+= "<div class=\"pull-left\" >"+
                              "<img src=\" "+res.data[i].img+" \" class=\"img-circle\" height=\"25\" alt=\"User Image\">"+
                          "</div>";                   
        }
        autocomplete+= "<h5>&nbsp;"+res.data[i].text+"</h5>";
        autocomplete+= "<input type=\"hidden\" class=\"search-id\" value=\""+res.data[i].id+"\"></li>";
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

function ajaxSearchAutoComplete(ele){
  var name =  ele.val();

  var data = {name:name};
  if($("#domain_id").length>0){
    data.domain_id = $("#domain_id").val();
  }

  var dfd = $.Deferred();
  var url = ele.data('action');
  $.ajax({
    url: url  ,
    type: 'POST',
    dataType: 'json',
    data: data ,
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
