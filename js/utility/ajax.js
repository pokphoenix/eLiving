function ajaxPromise(method,route,data){
  var dfd = $.Deferred();
  var url = $("#apiUrl").val() ;
  $.ajax({
    url: url+route,
    type: method,
    dataType: 'json',
    data:data
  })
  .done(function(res) {
    if(res.result=="true"){
      dfd.resolve(res.response);
    }else{
      // dfd.reject( res.errors );
      var error = JSON.stringify(res.errors);
       swal(
        'Error...',
        error,
        'error'
      )
      
    }
  })
  .fail(function() {
    dfd.reject( "error");
    swal(
        'Error...',
        'Some thing error',
        'error'
    );
  })
  return dfd.promise();
}

function ajaxFromData(method,route,data){
  var dfd = $.Deferred();
  var url = $("#apiUrl").val() ;
  $.ajax({
    url: url+route,
    type: method,
    dataType: 'json',
    data:data,
    cache:false,
    contentType: false,
    processData: false,
  })
  .done(function(res) {
    if(res.result=="true"){
      dfd.resolve(res.response);
    }else{
      // dfd.reject( res.errors );
      var error = JSON.stringify(res.errors);
       swal(
        'Error...',
        error,
        'error'
      )
      
    }
  })
  .fail(function() {
    dfd.reject( "error");
    swal(
        'Error...',
        'Some thing error',
        'error'
    );
  })
  return dfd.promise();
}