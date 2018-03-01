  function convertByte(val){
          res = '';
          if (val> Math.pow ( 1024, 3 )  ){
            res =  (val/Math.pow ( 1024, 3 )).toFixed(2)+' Gb';
          }else if (val> Math.pow ( 1024, 2 )  ){
            res =  (val/Math.pow ( 1024, 2 )).toFixed(2)+' Mb';
          }else if (val>1024){
            res =  (val/1024).toFixed(2)+' Kb';
          }
          return  res ;
      }
function jsUcfirst(string) 
{
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function ReplaceNumberWithCommas(yourNumber) {
    //Seperates the components of the number
    var n= yourNumber.toString().split(".");
    //Comma-fies the first part
    n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    //Combines the two sections
    return n.join(".");
}

function getMonday(d) {
  d = new Date(d);
  var day = d.getDay();
  return new Date(d.getFullYear(), d.getMonth(), d.getDate() + (day == 0?-6:1)-day);
}

function getSunday(d) {
   d = new Date(d);
  var day = d.getDay();
  return new Date(d.getFullYear(), d.getMonth(), d.getDate() + (day == 0?0:7)-day );
}


