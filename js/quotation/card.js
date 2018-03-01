$(".addcard-hover").on("click",function(){
	var add_card = "<div class=\"addcard-box\"><div class=\"box box-solid\" >"+
				"<div class=\"box-header\">"+
				"<textarea class=\"txt-area-card-title form-control\" rows=\"2\" style=\"border: 0;\">"+
				"</textarea>"+"</div></div>"+
				"<button class=\"btn bg-olive margin btn-add-card\" >"+(($("#app_local").val()=='th') ? ' เพิ่ม' : ' Add ' )+"</button>"+
				"<button class=\"btn btn-close-card\" ><i class=\"fa fa-close\"></i></button></div>";
	var rows = $(this).parent(".box-solid") ; 
	rows.find(".append-card").append(add_card);
	rows.find(".box-footer").hide();
	
	// console.log($(".addcard-box")[0].scrollHeight);
	// console.log(rows.find(".append-card")[0].scrollHeight);
	var parentHeight = rows.find(".append-card")[0].scrollHeight -  $(".addcard-box")[0].scrollHeight ;
	// console.log(parentHeight);

	rows.find(".box-body").animate({
    scrollTop: parentHeight
  },'fast');
	$(".txt-area-card-title").focus();
})

$('#task-edit-description-btn').click(function() {
	$('.task-edit-description').show();
	$('#task-edit-description-btn').hide();
});
$('#task-edit-description-clost-btn').click(function() {
	$('#task-description').val('');
	$('.task-edit-description').hide();
	$('#task-edit-description-btn').show();
});
$('#task-edit-description-add-btn').click(function() {
	var text = $('#task-description').val();
	// console.log(text);
	$('#description-body').text(text);
	$('.task-edit-description').hide();
	$('#task-edit-description-btn').hide();
	$('#description').show(text);
});		

$('#description-body').click(function() {
var ele = $('#description-body') ;
// console.log(ele.outerHeight(),ele.height(),ele.innerHeight());

var height = $('#description-body').outerHeight();
var width = $('#description-body').outerWidth();
	var text = $('#description-body').text();
	$('textarea#description-edit-body-text').val(text);
	$('textarea#description-edit-body-text').innerHeight(height);
	$('textarea#description-edit-body-text').innerWidth(width);
	
	$('#description').hide();
	$('#description-edit').show();
});

$('#description-edit-body-close-btn').click(function() {
	var text = $('#description-edit-body-text').val();
	$('#description-body').text(text);
	$('#description-edit').hide();
	$('#description').show();
});

$('#description-edit-body-add-btn').click(function() {
	var text = $('#description-edit-body-text').val();
	$('#description-body').text(text);
	$('#description-edit').hide();
	$('#description').show();
});

$(document).on("click",".btn-close-card",function(event) {
	// console.log("click");
	var rows = $(this).closest(".box-parent") ; 
	rows.find(".append-card").find(".addcard-box").remove();
	rows.find(".box-footer").show();
});

$(document).on("click",".btn-add-card",function(event) {
	var rows = $(this).closest(".box-parent") ; 
	var txt = $("textarea.txt-area-card-title").val();
	// console.log(txt);
	var card = "<div class=\"box box-solid card show-content\" data-toggle=\"modal\" data-target=\"#modal-card-content\">"+
				"<div class=\"box-header\">"+
				"<h3 class=\"box-title\">"+txt
				"</h3>"+"</div></div>";
	rows.find(".append-card").find(".addcard-box").remove();
	rows.find(".append-card").append(card);
	rows.find(".box-footer").show();

  	openCard(txt,boxId);

	ajaxCreateQuatation(txt);
});



$(document).on("click","#task-edit-description",function(event) {
	var add_description = "<div class=\"addcard-box\"><div class=\"box box-solid\">"+
				"<div class=\"box-header\">"+
				"<textarea class=\"txt-area-card-title\" rows=\"2\" style=\"border: 0;\">"+
				"</textarea>"+"</div></div>"+
				"<button class=\"btn bg-olive margin btn-add-card\" >"+(($("#app_local").val()=='th') ? ' เพิ่ม' : ' Add ' )+"</button>"+
				"<button class=\"btn btn-close-card\" ><i class=\"fa fa-close\"></i></button></div>";
	var rows = $(this).parent(".box-solid") ; 
	rows.find(".append-card").append(add_card);
	rows.find(".box-footer").hide();
});