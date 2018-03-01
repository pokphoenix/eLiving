$.extend( true, $.fn.dataTable.defaults, {
    "iDisplayLength": 100
    ,"bSortCellsTop": true
     ,"oLanguage": {
      "sSearch": (($("#app_local").val()=='th') ? 'ค้นหา : ' : 'Search : ' )
      ,"oPaginate": {
        "sFirst": (($("#app_local").val()=='th') ? 'หน้าแรก' : 'First page' )
        ,"sLast": (($("#app_local").val()=='th') ? 'หน้าสุดท้าย' : 'Last page' )
        ,"sNext": (($("#app_local").val()=='th') ? 'ถัดไป' : 'Next page' ) 
        ,"sPrevious": (($("#app_local").val()=='th') ? 'ก่อนหน้า' : 'Previous page' )
      }
      ,"sLengthMenu": (($("#app_local").val()=='th') ? "แสดง _MENU_ ข้อมูล" : "Show _MENU_ records" ) 
      ,"sInfo": (($("#app_local").val()=='th') ? "แสดง _START_ ถึง _END_ จาก _TOTAL_ ข้อมูล" : "Showing _START_ to _END_ of _TOTAL_ entries" )  
      ,"sInfoEmpty": (($("#app_local").val()=='th') ? "แสดง _START_ ถึง _END_ จาก _TOTAL_ ข้อมูล" : "Showing _START_ to _END_ of _TOTAL_ entries" )   
      ,"sEmptyTable": (($("#app_local").val()=='th') ? "ไม่พบข้อมูล" : "No data available in table" )  

    }
} );


$('.input-filter').each( function () {
        var title = $(this).text();
        $(this).html("<div class=\"form-group has-feedback has-feedback-left\" ><input type=\"text\" class=\"form-control\" placeholder=\""+title+"\" style=\"width:100%;\"><span class=\"fa fa-search form-control-feedback\"></span></div>");
    } );