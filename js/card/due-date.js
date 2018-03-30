
$('.btn_duedate,#btn_edit_duedate').daterangepicker({
    "singleDatePicker": true,
    "timePicker": true,
    "timePicker24Hour":true,
    showDropdowns: true,
    locale: {
        format: 'MM/DD/YYYY H:mm'
    },
    "opens": "left"
   
}, function (start, end, label) {
    var duedate = start.format('YYYY-MM-DD H:mm');
    var data = {due_dated_at:duedate} ;
    // console.log('[duedate]',data);
    UpdateTask(data).done(function (res) {
        var duedateShow = moment.utc(duedate).format("D/MM/YYYY HH:mm")
        // var m1 = moment.utc();
    //      var m2 = moment.utc(duedate);
        // var humanize = moment.duration(m1.diff(m2)).humanize()

        $("#task_due_date span#btn_edit_duedate").text(duedateShow);
        $("#task_due_date").show();
    })
});

$(document).on("change","#task_duedate_complete",function () {
    due_dated_complete = 0 ;
    due_date_complete_at=null;
    if (this.checked) {
        due_dated_complete = 1 ;
        due_date_complete_at = moment().format("YYYY-MM-DD HH:mm");
    }
    var cardId = $("#current_card_id").val();
    var data = {due_dated_complete:due_dated_complete,due_date_complete_at:due_date_complete_at} ;
    UpdateTask(data).done(function (res) {
        

    });
})
