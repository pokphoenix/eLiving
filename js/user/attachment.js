var imgCount = 1 ;

    

$(function () {

          $(".content-wrapper").on("click",".del-doc",function (event) {
              imgCount = $('#append_upload tr').length;
              var rows = $(this).closest("tr") ;
              rows.remove();
              console.log("click",imgCount);
              $("#append_upload tr td:first-child").each(function (i) {
                  var j = ++i;
                  $(this).text(j);
              });
          });

          $('#file_upload').on('change',function () {
              var doc_type = $(".doc_type option:selected").text();
              var doc_value = $(".doc_type option:selected").val();
              var i = $(this).prev('label').clone();
              var file = $('#file_upload')[0].files[0];
              var file_name = file.name;
              var file_ext = file_name.split('.').pop().toLowerCase();
              var file_size = file.size ;
              var reader = new FileReader();
              // var newImg = $(this).val() ;
              var newFile = $(this).clone() ;
              newFile.removeAttr("id");
              newFile.attr("name","file_upload[]");
              newFile.attr("class","file_upload");
              newFile.attr("type","hidden");

              // console.log('imgCount',imgCount);
              // console.log('imgCount',);
              reader.onload = function (e) {
                  var renderfileData ;
                if (file_ext=="png"||file_ext=="jpg") {
                    renderfileData = e.target.result ;
                } else {
                    renderfileData = $("#mainPath").val()+"/public/img/file_format/file.png";
                }
              
                  var img_render = "<tr>"+
                              "<td>"+imgCount+"</td>"+
                              "<td>"+doc_type+"</td>"+
                              "<td><img src=\""+renderfileData+"\" height=50 ></td>"+
                              "<td>"+file.name+"</td>"+
                              "<td>"+convertByte(file.size)+"</td>"+
                              "<td><button type=\"button\" class=\"btn btn-danger del-doc\" > <i class=\"fa fa-close\"></i> </button>"+
                              "<input type=\"hidden\" class=\"upload-file-type\" value=\""+doc_value+"\" > "
                              "</td>"+
                            "</tr>";
               

                  $("#append_upload").append(img_render);
                  var data = {
                        name : file_name ,
                        extension : file_ext ,
                        size : file_size ,
                        data : e.target.result ,
                    }
                    newFile.val(JSON.stringify(data));

                    newFile.insertAfter($('.del-doc').last());

                    imgCount++;
                
              }
             
              // $("form").append(newImg);
              // if(file_ext=="png"||file_ext=="jpg"){
              //   reader.readAsDataURL($('#file_upload')[0].files[0]);
              // }else{
              //   reader.readAsDataURL( $("#mainPath").val()+"/public/img/file_format/etc.png" );
              // }
                reader.readAsDataURL($('#file_upload')[0].files[0]);

                $('#file_upload').val('');
          });

        // $(".btn-upload-file").on("click",function(){

     //        $("#get").each(function(){
                
     //        });


        //  var add_card = "<div class=\"addcard-box\"><div class=\"box box-solid\">"+
        //        "<div class=\"box-header\">"+
        //        "<textarea class=\"txt-area-card-title\" rows=\"2\" style=\"border: 0;\">"+
        //        "</textarea>"+"</div></div>"+
        //        "<button class=\"btn bg-olive margin btn-add-card\" >Add</button>"+
        //        "<button class=\"btn btn-close-card\" ><i class=\"fa fa-close\"></i></button></div>";
        //  var rows = $(this).parent(".box-solid") ;
        //  rows.find(".append-card").append(add_card);
        //  rows.find(".box-footer").hide();
        // })
       


});
      
      

