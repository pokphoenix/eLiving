function printContent(el){
  var restorepage = document.body.innerHTML;
  var printcontent = document.getElementById(el).innerHTML;
  document.body.innerHTML = printcontent;
   $('body').css('padding-top',0);
  window.print();
  // document.body.innerHTML = restorepage;
  location.reload();
}

