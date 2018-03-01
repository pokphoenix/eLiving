<div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">@lang('user.role')</h3>
                <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                  </div>
              </div>
              <div class="box-body">
                    <div class="row">
                      <div class="col-sm-12">
                        <?php 
                    foreach ($roles as $role){
                      echo "<br>".
                      "<input type=\"checkbox\"  name=\"role[]\" ".
                      " value=\"".$role['name']."\" ";

                         if(isset($edit)){

                         foreach($data['role'] as $userRole){ 
                          if(isset($edit) && $userRole==$role['name'] ){
                            echo " checked=\"\"";
                          }
                          
                         }
                       }
                      echo "> ".$role['display_name'];
                    }
                    ?>
                      </div>
                    </div>
              </div>
                

                <div class="box-footer">
                 <!--  <button type="button" class="btn btn-default" id="btn-upload-file">นำส่ง</button> -->
                </div>
              
            </div>