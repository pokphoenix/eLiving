

$(document).on("click",".btn-filter,.btn-show-filter",function (e) {
  // searchFilter();
    $('.show-content').hide();
    $('.status-vote-label-id').each(function (index, el) {
        if ($(this).val()==0) {
            $(this).closest('.show-content').show();
        }
    });
    $(".group-but-show-filter").show();
    $(".btn-filter").hide();
});

$(document).on("click",".btn-close-filter",function (e) {
    $('.show-content').show();
    $(".group-but-show-filter").hide();
    $(".btn-filter").show();
});






