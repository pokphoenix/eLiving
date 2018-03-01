<script src="{{url('plugins/sweetalert2/sweetalert2.min.js')}}"></script>
<script src="{{ url('plugins/jquery-validate/jquery.validate.min.js')}}"></script>
<script src="{{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ url('js/utility/address.js') }}"></script> 
<!-- <script type="text/javascript" src="{{ url('js/utility/validate.js') }}"></script>  -->
<script src="{{ url('js/utility/autocomplete.js') }}"></script> 
<script src="{{ url('js/user/room.js') }}"></script> 
<script>
  
var currentStep =0;



function moveNext(){
  currentStep++ ;
  moveStep();
}
function moveBack(){
  currentStep-- ;
  moveStep();
}



function moveStep(){
  console.log('currentStep',currentStep);
  $("section.form-parent").hide();
  $("section.form-parent").each(function(){
     if ($(this).data('id')==currentStep) {
      if($(this).find('textarea').length > 0){
        $(this).show().find('textarea').focus().animate({'scrollTop': $(this).offset().top}, 'fast');
      }else{
        $(this).show().find('input[type!=hidden]:first').focus();
      }
      
      
     }
  }) 

  if(currentStep==0){
    $(".btn-cancel-1,.next-to").show();
    $(".btn-submit,.back-to").hide();
  }else if(currentStep==1){
    $(".next-to,.back-to").show();
    $(".btn-cancel-1,.btn-submit").hide();
  }else if(currentStep==2){
    $(".btn-submit,.back-to").show();
    $(".btn-cancel-1,.next-to").hide();
  }

  $(".row-facebook").show(); 
  if(currentStep!=0){
    $(".row-facebook").hide(); 
  }

}

$("#room-form,#address-form").on("submit",function(){
  return false;
});

$(".btn-submit").on('click touch',function(){
  getSubmitData().then(function(data){

     $('.fa-spinner').show();
           $.ajax({
              type: "POST",
              url: $("#signup-form").attr('action'),
              dataType: 'json',
              data:data,
            cache:false,
            contentType: false,
            processData: false,
            success: function (data) {
                  console.log(data,typeof data.response);
                  $('.fa-spinner').hide();
                  if(data.result=="true"){
                    swal({
                        title:  data.response.prewelcome.text,
                        type: 'success',
                        showCancelButton: false,
                        confirmButtonText: "@lang('main.ok')"
                      }).then((result) => {
                        if (result.value) {
                          window.location.href = "{{ url('login') }}";
                        }
                      })
                  }else{
                    var error = JSON.stringify(data.errors);
                    // console.log(error);
                     swal(
                'Error...',
                error,
                'error'
              )
                  }
               }
           });
           return false; // required to block normal submit since you used ajax
  }).fail();
});

function getSubmitData(){
  var dfd = $.Deferred();

  var data = { room:[] };
    $("#user-in-room-table tbody tr").each(function(){
        var roomId = $(this).find('.room-id').val() ;
        

        var row =  { 
             'room_id':roomId
            ,'id_card':$("#id_card").val()
            ,'room_approve':0
        }
     
        data.room.push(row);
    });


    if(data.room.length <=0 && !$("#no_room").is(':checked')){
      dfd.reject("@lang('room.room_require')");
    }else{

       var form_data = new FormData($("#signup-form")[0]);
     
     $("#address-form,#room-form").find('textarea,select,input').each(function(i, e){
       var name = $(this).attr('name') ;
       var val = $(this).val();
       var prop = $(e).prop('tagName').toLowerCase();
       if(prop=="input"){
        var type = $(this).attr('type') ;
        if(type=="checkbox"){
          val = ($(this).is(':checked'))  ?  1  : 0  ;
        }
       }

       form_data.append(name,val);
     });
     form_data.append('user-room', JSON.stringify(data.room));
      dfd.resolve(form_data);
    }

    
 
  return dfd.promise();
}

$(".next-to").on("click touch",function(){
  // console.log('next-to click',currentStep);
  if(currentStep==0){
    if($("#signup-form").valid()){
      moveNext();
    } 
  }else if(currentStep==1){ 
    if($("#address-form").valid()){
      moveNext();
     
    }
  }
})

$(".back-to").on("click touch",function(){
  moveBack();
})

$(document).on("input","#id_card",function(e) { 
  

  if($(this).val().length==13){
    $("#first_name").attr("disabled",false);
    $("#last_name").attr("disabled",false);
    $("#email").attr("disabled",false);
    $("#tel").attr("disabled",false);
    $(".has-temp").show();
    $("#first_name").val();
    $("#last_name").val();
    $("#email").val();
    $("#tel").val();
    var route = "{{ url('api/search/temp') }}" ;
    var data ={id_card:$(this).val()} ;
      $.ajax({
        url: route,
        type: 'POST',
        dataType: 'json',
        data:data
      })
      .done(function(response) {
        if(response.result=="true"){
            var res = response.response.user ;
            if(res!=null){

              $(".has-temp").hide();

              $("#first_name").attr("disabled",true);
            $("#last_name").attr("disabled",true);
            $("#email").attr("disabled",true);
            $("#tel").attr("disabled",true);
            }
          
        }else{
          
        }
      })
      .fail(function() {
       
      })
  }
  
});


$('#password').tooltip({'trigger':'focus'
  ,'html':true
  , 'title': "@lang('main.password_require')<BR>@lang('main.password_least_character_long')<BR>@lang('main.password_not_over_long')"
  ,'placement':'right'});

$(function() {
  $(".select2-container").css('width',"100%");
  $("#address-form").validate({
      rules: {
        address: {
          required: true,
              maxlength: 255
            },
            zip_code:{
              required: true,
              minlength: 5,
                maxlength: 5
            }
          
         },
        messages: {
            address:(($("#app_local").val()=='en') ? 'ที่อยู่ไม่ถูกต้อง' : 'Wrong address' ),
            district_id:(($("#app_local").val()=='en') ? 'ตำบลไม่ถูกต้อง' : 'Wrong district' ),
            province_id:(($("#app_local").val()=='en') ? 'จังหวัดไม่ถูกต้อง' : 'Wrong province' ),
            amphur_id:(($("#app_local").val()=='en') ? 'อำเภอไม่ถูกต้อง' : 'Wrong amphur' ),
            zip_code:(($("#app_local").val()=='en') ? 'รหัสไปรษณีไม่ถูกต้อง' : 'Wrong zipcode' ),
        }
        ,highlight: function ( element, errorClass, validClass ) {
            $( element ).parents( ".form-group" ).find('em').remove();
            $( element ).parents( ".form-group" ).find('span').remove();
            $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
          }
       ,unhighlight: function ( element, errorClass, validClass ) {
            $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
              $( element ).parents( ".form-group" ).find('label').remove();
          }
    });
});

$(document).on("click touch","#search_room_list .my-autocomplete-li",function(e) { 
  var id = $.trim($(this).find('.search-id').val()) ;
  var text = $.trim($(this).find('.search-text').val()) ;
  
  var canAppend = true ;
   $("#user-in-room-table tbody tr").each(function(index, el) {
   
      if($(this).find('.room-id').val()==id){
        canAppend = false;
      }
   });

   if(!canAppend){
    $("#search_room").val('');
    $("#search_room_list").hide();
     return false;
   }
  var html = "<tr>"+
            "<td><button type=\"button\" "+
            " class=\"btn btn-danger btn-xs btn-user-in-room-del\" >"+
            "<i class=\"fa fa-close\"></i></button>"+
            "<input type=\"hidden\" class=\"room-id\" value=\""+(id)+"\">"+
            "<input type=\"hidden\" class=\"room-approve\" value=\""+(0)+"\">"+
            "</td>"+
            "<td></td><td>"+text+"</td>"+
           
            "</tr>";

  $("#no_room").attr("disabled", true) ;

  $("#user-in-room-table tbody").append(html);
  // $(this).parent().parent().parent().find("#search_room").val($(this).find('h5').text()) ;
  $(this).parent().remove();
  $("#user-in-room-table tbody tr td:nth-child(2)").each(function (i) {
      var j = ++i;
      $(this).text(j);
  });
  $("#search_room").val('');
  $("#search_room_list").hide();
});
</script>