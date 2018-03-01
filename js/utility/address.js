$("#province_id").select2({}).on('change', function (e) {
     $.ajax({
        url: $("#api_search").val()+"/amphur-id",
        type: 'POST',
        dataType: 'json',
        data:{id: $(this).val()}
      })
      .done(function(response) {
        var html = "" ;
        if(response.result=="true"){
            var res = response.response.amphurs ;
            html += "<option value=\"\" >"+(($("#app_local").val()=='th') ? 'อำเภอ / เขต' : 'amphur' )+"</option>";
            for(var i = 0 ; i < res.length ; i++){
              html += "<option value=\""+res[i].id+"\" >"+res[i].name+"</option>" ;
            }
        }

        $("#amphur_id").html(html);
        $("#district_id").html("<option value=\"\" >"+(($("#app_local").val()=='th') ? 'ตำบล / แขวง' : 'district' )+"</option>");
        $("#zip_code").val('');
      })
});
$("#amphur_id").select2().on('change', function (e) {
     $.ajax({
        url: $("#api_search").val()+"/district-id",
        type: 'POST',
        dataType: 'json',
        data:{id: $(this).val()}
      })
      .done(function(response) {
        var html = "" ;
        if(response.result=="true"){
            var res = response.response.districts ;
             html += "<option value=\"\" >"+(($("#app_local").val()=='th') ? 'ตำบล / แขวง' : 'district' )+"</option>";
            for(var i = 0 ; i < res.length ; i++){
              html += "<option value=\""+res[i].id+"\" >"+res[i].name+"</option>" ;
            }
        }
        $("#district_id").html(html);
        $("#zip_code").val('');
      })
});
$("#district_id").select2().on('change', function (e) {
     $.ajax({
        url: $("#api_search").val()+"/zipcode-id",
        type: 'POST',
        dataType: 'json',
        data:{id: $(this).val()}
      })
      .done(function(response) {
        if(response.result=="true"){
            $("#zip_code").val(response.response.zipcode.name);
        }
       
      })
});


$(function() {
  var $win = $(window); // or $box parent container
    var $district = $("#district_name");

    var $amphur = $("#amphur_name");

    var $province = $("#province_name");

    // var $title = $("#modal-card-content .modal-title");
    
    $win.on("click.Bst", function(event){ 
      if ( $district.has(event.target).length == 0 &&!$district.is(event.target)){
            // console.log("you clicked outside district_list");
        $("#district_search").hide();
        $("#district_list").html('');
      }
      if ( $amphur.has(event.target).length == 0 &&!$amphur.is(event.target)){
            // console.log("you clicked outside amphur_list");
        $("#amphur_search").hide();
        $("#amphur_list").html('');
      }
      if ( $province.has(event.target).length == 0 &&!$province.is(event.target)){
            // console.log("you clicked outside province_list");
        $("#province_search").hide();
        $("#province_list").html('');
      }
    });
});


$("#district_search").hide();
$("#district_name").on('focus',function(){
    $("#district_search").toggle();
    $("#search_district").focus();
});

$("#amphur_search").hide();
$("#amphur_name").on('focus',function(){
    $("#amphur_search").toggle();
    $("#search_amphur").focus();
});

$("#province_search").hide();
$("#province_name").on('focus',function(){
    $("#province_search").toggle();
    $("#search_province").focus();
});



$(document).on("input","#search_district",function(e) { 
    ajaxSearchAutoComplete($(this)).then(function( res ) {
        // console.log("search_district",res);
        addressData(res).done(function(data){
            $("#district_list").html(data);
        })
    })
});
$(document).on("input","#search_amphur",function(e) { 
    ajaxSearchAutoComplete($(this)).then(function( res ) {
        // console.log("search_amphur",res);
        addressData(res).done(function(data){
            $("#amphur_list").html(data);
        })
    })
});
$(document).on("input","#search_province",function(e) { 
    ajaxSearchAutoComplete($(this)).then(function( res ) {
        // console.log("search_province",res);
        addressData(res).done(function(data){
            $("#province_list").html(data);
        })
    })
});
$(document).on("click","#district_list .my-autocomplete-li",function(e) { 
  var districtId = $.trim($(this).find('.district-id').val()) ;
  var districtName = $.trim($(this).find('.district-name').val()) ;
  var amphurId = $.trim($(this).find('.amphur-id').val()) ;
  var amphurName = $.trim($(this).find('.amphur-name').val()) ;
  var provinceId = $.trim($(this).find('.province-id').val()) ;
  var provinceName = $.trim($(this).find('.province-name').val()) ;
  var zipcode = $.trim($(this).find('.zipcode').val()) ;
  $("#district_id").val(districtId);
  $("#district_name").val(districtName);
  $("#amphur_id").val(amphurId);
  $("#amphur_name").val(amphurName);
  $("#province_id").val(provinceId);
  $("#province_name").val(provinceName);
  $("#zip_code").val(zipcode);
  $("#district_list").html('');
  $("#district_search").hide();
});

$(document).on("click","#amphur_list .my-autocomplete-li",function(e) { 
  var amphurId = $.trim($(this).find('.amphur-id').val()) ;
  var amphurName = $.trim($(this).find('.amphur-name').val()) ;
  var provinceId = $.trim($(this).find('.province-id').val()) ;
  var provinceName = $.trim($(this).find('.province-name').val()) ;
  $("#amphur_id").val(amphurId);
  $("#amphur_name").val(amphurName);
  $("#province_id").val(provinceId);
  $("#province_name").val(provinceName);
  $("#amphur_list").html('');
  $("#amphur_search").hide();
});

$(document).on("click","#province_list .my-autocomplete-li",function(e) { 
  var provinceId = $.trim($(this).find('.province-id').val()) ;
  var provinceName = $.trim($(this).find('.province-name').val()) ;
  $("#province_id").val(provinceId);
  $("#province_name").val(provinceName);
  $("#province_list").html('');
  $("#province_search").hide();
});

function addressData(res){
   var dfd = $.Deferred();
 
 
  elewidth = res.ele.innerWidth();
  $(res.ele).next('ul').remove();
  if(res.data.length>0){
    var autocomplete = "<ul class=\"my-autocomplete-ul\" style=\"width:"+elewidth+"px; \">";
      for(var i=0;i<res.data.length;i++){
        autocomplete+= "<li class=\"my-autocomplete-li \"> " ;
       
        autocomplete+= "<h5>&nbsp;"+res.data[i].text+"</h5>";

        if(typeof res.data[i].district_id !="undefind"){
          autocomplete+= "<input type=\"hidden\" class=\"district-id\" value=\""+$.trim(res.data[i].district_id)+"\">";
          autocomplete+= "<input type=\"hidden\" class=\"district-name\" value=\""+$.trim(res.data[i].district_name)+"\">";
        }

        if(typeof res.data[i].amphur_id !="undefind"){
          autocomplete+= "<input type=\"hidden\" class=\"amphur-id\" value=\""+$.trim(res.data[i].amphur_id)+"\">";
          autocomplete+= "<input type=\"hidden\" class=\"amphur-name\" value=\""+$.trim(res.data[i].amphur_name)+"\">";
        }

        if(typeof res.data[i].province_id !="undefind"){
          autocomplete+= "<input type=\"hidden\" class=\"province-id\" value=\""+$.trim(res.data[i].province_id)+"\">";
          autocomplete+= "<input type=\"hidden\" class=\"province-name\" value=\""+$.trim(res.data[i].province_name)+"\">";
        }
        if(typeof res.data[i].zipcode !="undefind"){
          autocomplete+= "<input type=\"hidden\" class=\"zipcode\" value=\""+$.trim(res.data[i].zipcode)+"\">";
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



