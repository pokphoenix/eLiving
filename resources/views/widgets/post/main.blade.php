@if(count($lists)>0)
      @foreach($lists as $list)
         <div class="row" >
           <div class="col-sm-12">
                <!-- Box Comment -->
                <div class="box box-widget ">
                  <div class="box-header with-border">
                    <input type="hidden" class="post-id" value="{{ $list['id'] }}" >
                    <input type="hidden" class="created-by" value="{{ $list['created_by'] }}" >
                    <div class="user-block">
                      <img class="img-circle" src="{{ $list['user_img'] }}" alt="User Image">
                      <span class="username"><a href="#">{{ $list['user_displayname']}} </a></span>
                      <span class="description"> {{ $list['created_at'] }}</span>
                    </div>
                    <!-- /.user-block -->
                    <div class="box-tools">
                      @if($list['created_by']==Auth()->user()->id)
                      <button type="button" class="btn btn-box-tool btn-edit-post" >
                        <i class="fa fa-edit"></i></button>
                        @endif
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>

                      @if($list['created_by']==Auth()->user()->id || Auth()->user()->hasRole('admin') )
                      <button type="button" class="btn btn-box-tool btn-delete-post"  data-id="{{$list['id']}}" title="@lang('post.delete_post')"><i class="fa fa-times"></i></button>
                      @endif
                      @if(Auth()->user()->hasRole('admin'))
                       <button type="button" class="btn btn-box-tool btn-ban-user" title="@lang('post.ban_user')"><i class="fa fa-user-times"></i></button>
                      @endif
                    </div>
                    <!-- /.box-tools -->
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                    <p class="text-show">
                       {!! $list['description'] !!}
                    </p>
                    <div class="text-edit none">
                      <textarea  class="form-control" style="border:none;" > {!! $list['description'] !!}</textarea>
                      <div class="pull-right">
                        <button class="btn btn-box-tool btn-cancel-edit">
                        <i class="fa fa-times"></i>
                      </button>
                      <button class="btn btn-primary btn-sm btn-flat btn-save-edit">
                        <i class="fa fa-save"></i>
                      </button>
                      </div>
                      <BR>
                      
                    </div>
                    
                    <div class="row">
                      <div class="col-sm-12">
                        @if (count($list['attachments'])>0)
                        <img class="img-responsive" src="{{ $list['attachments'][0]['file_path'] }}" alt="Photo">
                  <div class="">
                    @foreach($list['attachments'] as $key=>$a)
                    @if ($key > 0 && $key < 3)
                      <div class="img-resize-height col-sm-4 nopadding">
                        <a href="{{ $a['file_path'] }}" target="_blank" >
                        <div class="thumbnail">
                          <img class="img-responsive" src="{{ $a['file_path'] }}" alt="Photo">
                        </div>
                        </a>
                      </div>
                    @endif
                    @endforeach
                    @if(count($list['attachments'])>4)
                    <div class="img-resize-height  col-sm-4 nopadding" >
                      <a href="{{ $list['attachments'][3]['file_path'] }}" target="_blank" >
                      <div class="layout-plus" style=" position: absolute;z-index:2;  background:rgba(0,0,0,0.3) ; padding-bottom: 10px; width:100%;color:#FFF; display:table;"> 
                        <p style="position:relative;text-align:center; vertical-align: middle; font-size: 2em;display:table-cell;">
                          + {{  count($list['attachments'])-3 }}
                        </p>
                        
                      </div>
                      <div class="thumbnail" >
                        <img class="img-responsive" src="{{ $list['attachments'][3]['file_path'] }}" alt="Photo">
                      </div>
                      </a>
                    </div>
                    @elseif(count($list['attachments'])==4&&isset($list['attachments'][3]))
                    <div class="img-resize-height  col-sm-4 nopadding">
                      <a href="{{ $list['attachments'][3]['file_path'] }}" target="_blank" >
                      <div class="thumbnail">
                        <img class="img-responsive" src="{{ $list['attachments'][3]['file_path'] }}" alt="Photo">
                      </div>
                      </a>
                    </div>
                    @endif
                  </div>  
                          
                    
                        @endif
                      </div>
                    </div>
                   <!--  <button type="button" class="btn btn-default btn-xs"><i class="fa fa-share"></i> Share</button> -->
                    <button type="button" class="btn btn-default btn-xs btn-like"><i class="fa fa-thumbs-o-up"></i> <span>Like</span></button>
                    <span class="pull-right text-muted like-comment">
                      {{ $list['post_like']." likes - ".$list['post_comment']." comments" }}
                    </span>
                  </div>
                  <!-- /.box-body -->
               <div class="box-footer box-comments">
                @if(count($list['comments']))
                  @foreach($list['comments'] as $key=>$comment)
            <div class="box-comment">
                      <img class="img-circle img-sm" src="{{ $comment['img'] }}" alt="User Image">

                      <div class="comment-text">

                            <span class="username">
                              {{ $comment['user_name'] }}
                              <span class="text-muted pull-right"> 
                              {{ date('d/m/Y H:i',$comment['ts_created_at']) }}
                              @if(Auth::user()->id==$comment['user_id'])
                    <!-- <button type="button" class="btn btn-default btn-comment-delete-real btn-xs" title="Remove"><i class="fa fa-close"></i>
                    </button> -->
                    <div class="btn-group pull-right message-tools">
                                      <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-gear"></i>
                                      </button>
                                      <ul class="dropdown-menu pull-right" role="menu">
                                        <li>
                                          <a href="javascript:void(0)" class="btn-del-msg" >
                                          @lang('chat.delete_message')
                                          </a>
                                        </li>
                                        <li>
                                          <a href="javascript:void(0)" class="btn-pin-msg" >
                                          @lang('chat.pin_message')
                                          </a>
                                        </li>
                                      </ul>
                                  </div>
                  @endif
                            </span>
                            </span>
                      {!! $comment['comment_description'] !!}
                      </div>
                      
                  </div>
                    @endforeach
                @endif
                <!--       
                
                    <div class="box-comment">
                      
                      <img class="img-circle img-sm" src="../dist/img/user4-128x128.jpg" alt="User Image">

                      <div class="comment-text">
                            <span class="username">
                              Luna Stark
                              <span class="text-muted pull-right">8:03 PM Today</span>
                            </span>
                        It is a long established fact that a reader will be distracted
                        by the readable content of a page when looking at its layout.
                      </div>
                     
                    </div> -->
                    
                  </div>
                   @if($canPost=="true")
                  <div class="box-footer">
                     
                    <form class="comment-form" action="#" method="post">
                      <img class="img-responsive img-circle img-sm" src="{{ auth()->user()->getProfileImg() }}" alt="Alt Text">
                      <!-- .img-push is used to add margin to elements next to floating images -->
                      <div class="img-push">
                        <input type="text"  class="form-control input-sm comment-text" placeholder="@lang('post.press_enter_to_post')">
                      </div>
                    </form>
                   
                  </div>
                   @endif
                  <!-- /.box-footer -->
                </div>
                <!-- /.box -->
              </div>

            </div>
      @endforeach
    @endif