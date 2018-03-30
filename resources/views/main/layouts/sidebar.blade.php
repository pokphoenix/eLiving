@php 
$user = Auth()->User();
$hasAdmin = $user->hasRole('admin');
$hasOfficer = $user->hasRole('officer');
$hasHeadUser = $user->hasRole('head.user');
$hasUser = $user->hasRole('user');
$hasGuard = $user->hasRole('security.guard');
@endphp


<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ $user->getProfileImg() }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{ getUserName() }}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- search form -->
     <!--  <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form> -->
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN</li>
          <li class="{{ Request::is('*dashboard*')   ? 'active' : ''  }}" >
          <a href="{{ url( $user->getDomainName().'/dashboard') }}">
            <i class="fa fa-home"></i> 
            <span>
              @lang('sidebar.home')
            </span>
          </a>
         </li>

          @if(isset($domainName))
            @if($hasAdmin)
              <li class="{{ Request::is($domainName.'/rooms*')   ? 'active' : ''  }}" >
                <a href="{{ url($domainName.'/rooms') }}">
                  <i class="fa fa-key"></i> 
                  <span>
                    @lang('sidebar.room_management')
                  </span>
                </a>
              </li>
               <li class="{{ Request::is($domainName.'/pre-welcome*')   ? 'active' : ''  }}" >
                <a href="{{ url($domainName.'/pre-welcome') }}">
                  <i class="fa fa-key"></i> 
                  <span>
                    @lang('sidebar.pre_welcome')
                  </span>
                </a>
              </li>
              <li class="{{ Request::is($domainName.'/request-room*')   ? 'active' : ''  }}" >
                <a href="{{ url($domainName.'/request-room') }}">
                  <i class="fa fa-key"></i> 
                  <span>
                    @lang('sidebar.request_room')
                  </span>
                  <span class="pull-right-container badge-request-room">
                    <span class="label label-primary pull-right"></span>
                  </span>
                </a>
              </li>
               <li class="{{ Request::is($domainName.'/create-admin*')   ? 'active' : ''  }}" >
                <a href="{{ url($domainName.'/create-admin') }}">
                  <i class="fa fa-user-secret"></i> 
                  <span>
                    @lang('sidebar.create_admin')
                  </span>
                </a>
              </li>
              
            @endif
            @if(Permission::hasPermission('create.user'))
          
            <li class="{{ Request::is('*-user*')   ? 'active' : ''  }} treeview">
              <a href="#">
                <img class="icon-side-menu" src="{{ asset('public/img/icon/icon_user_management.png') }}"> <span>
                  @lang('sidebar.user_management')
                  <!-- User Management <BR> <span class="sidebar-row-margin"> จัดการผู้ใช้</span>  -->
                    
                  </span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                  <span class="badge-wait-user">
                      <span class="label label-primary pull-right"></span>
                  </span>
                  
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="{{ Request::is($domainName.'/create-user*')   ? 'active' : ''  }}">
                  <a href="{{ url($domainName.'/create-user') }}">
                     <i class="fa fa-user"></i> 
                     <span> 
                        @lang('sidebar.user')
                     </span>
                  </a>
                </li>
                <li class="{{ Request::is($domainName.'/wait-user*')   ? 'active' : ''  }}">
                  <a href="{{ url($domainName.'/wait-user') }}"> 
                    <img class="icon-side-menu" src="{{ asset('public/img/icon/icon_user_wait_for_approve.png') }}"> 
                    <span>
                      @lang('sidebar.wait_for_approve')
                    </span>
                    <span class="pull-right-container badge-wait-user">
                      <span class="label label-primary pull-right"></span>
                    </span>
                  </a>
                </li>
                <li class="{{ Request::is($domainName.'/remove-user*')   ? 'active' : ''  }}">
                  <a href="{{ url($domainName.'/remove-user') }}">
                     <i class="fa fa-user"></i> 
                     <span> 
                        @lang('sidebar.remove_user')
                     </span>
                  </a>
                </li>
              </ul>
            </li>
           
            @endif
            
          @endif
          

      <!--     <li class="{{ Request::is('domain/join')   ? 'active' : ''  }}" >
                <a href="{{ route('domain.join') }}">
                  <i class="fa fa-circle-o"></i> 
                  <span>@lang('sidebar.domain_join')</span>
                </a>
          </li> -->
       
        


        <!-- <li class="{{ Request::is('domain*')   ? 'active' : ''  }} treeview">
          <a href="#">
            <i class="fa fa-sitemap"></i> <span>Domain Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li class="{{ Request::is('domain/create')   ? 'active' : ''  }}">
              <a href="{{ route('domain.create') }}"><i class="fa fa-circle-o"></i> สร้างโครงการ</a>
            </li>
            <li class="{{ Request::is('domain/join')   ? 'active' : ''  }}">
              <a href="{{ route('domain.join') }}"><i class="fa fa-circle-o"></i> เข้าร่วมโครงการ</a>
            </li>

          </ul>
        </li> -->
    
        
        @if(isset($domainName)&&$user->checkApprove())
         <li>
          <a href="{{ url($domainName.'/phone') }}">
             <i class="fa fa-phone"></i> 
            <span>
              @lang('sidebar.phone_directory')
            </span>
          </a>
        </li>
        <li>
          <a href="{{ url($domainName.'/contact') }}">
             <i class="fa fa-phone"></i> 
            <span>
              @lang('sidebar.contact')
            </span>
          </a>
        </li>
      
        @if($hasOfficer||$hasAdmin)
        <li class="{{ Request::is($domainName.'/master/contact')   ? 'active' : ''  }}" >
          <a href="{{ url($domainName.'/master/contact') }}">
             <i class="fa fa-circle-o"></i> 
            <span>
              @lang('sidebar.contact_type')
            </span>
          </a>
        </li>

        @endif 

        <!--  <li class="{{ Request::is($domainName.'/pea')   ? 'active' : ''  }}" >
          <a href="{{ url($domainName.'/pea') }}">
             <i class="fa fa-circle-o"></i> 
            <span>
              @lang('sidebar.pea')
            </span>
          </a>
        </li> -->

        @if($hasAdmin)
        <li class="{{ Request::is($domainName.'/suggest/system*')   ? 'active' : ''  }}">
          <a href="{{ url($domainName.'/suggest/system') }}" >
            <i class="fa fa-circle-o"></i>
            <span>
               @lang('sidebar.suggest_system')
            </span>
            
          </a>
        </li>
        @endif

        @if($hasOfficer||$hasHeadUser)
        <li>
          <a href="{{ url($domainName.'/task') }}">
            <img class="icon-side-menu" src="{{ asset('public/img/icon/icon_internal_work_1_edit.png') }}"> 
            <span>
              @lang('sidebar.internal_task')
            </span>
          </a>
        </li>
        @endif

        @if($hasOfficer||$hasHeadUser)
  

          <li class="{{ Request::is($domainName.'/officer/task*') || Request::is($domainName.'/officer/work*')   ? 'active' : ''  }} treeview">
              <a href="#">
                <img class="icon-side-menu" src="{{ asset('public/img/icon/icon_user_management.png') }}"> <span>
                  @lang('sidebar.external_task')
                  <!-- User Management <BR> <span class="sidebar-row-margin"> จัดการผู้ใช้</span>  -->
                  </span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                  <span class="badge-task-ex">
                      <span class="label label-primary pull-right"></span>
                  </span>
                  
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="{{ Request::is($domainName.'/officer/task*')   ? 'active' : ''  }}">
                  <a href="{{ url($domainName.'/officer/task') }}">
                    <img class="icon-side-menu" src="{{ asset('public/img/icon/icon_external_work_1_edit.png') }}"> 
                    <span>
                          @lang('sidebar.external_task')
                    </span>
                     <span class="pull-right-container badge-task-ex">
                      <span class="label label-primary pull-right"></span>
                    </span>
                  </a>
                </li>
                 <li class="{{ Request::is($domainName.'/officer/work*')   ? 'active' : ''  }}">
                  <a href="{{ url($domainName.'/officer/work') }}" >
                    <i class="fa fa-circle-o"></i>
                    <span>
                       @lang('sidebar.work')
                    </span>
                  </a>
                </li>
              </ul>
            </li>

        <li class="{{ Request::is($domainName.'/important_day*')   ? 'active' : ''  }}" >
          <a href="{{ url($domainName.'/important_day') }}"><img class="icon-side-menu" src="{{ asset('public/img/icon/icon_purchasing_bidding.png') }}"> 
            <span>
               @lang('sidebar.important_day')
            </span>
            <span class="pull-right-container badge-quotation">
              <span class="label label-primary pull-right"></span>
            </span>
          </a>
        </li>
  

        @endif

        @if($hasUser)
        <li class="{{ Request::is($domainName.'/user/*/task*')   ? 'active' : ''  }} treeview">
          <a href="#">
            <img class="icon-side-menu" src="{{ asset('public/img/icon/icon_external_work.png') }}"> 
            <span>
               @lang('sidebar.juristic_person')
            </span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @foreach ($user->getRoom() as $room)
             <li class="{{ Request::is($domainName.'/user/'.$room->id.'/task*')   ? 'active' : ''  }}">
              <a href="{{ url($domainName.'/user/'.$room->id.'/task') }}"><i class="fa fa-key"></i> {{ $room->name_prefix.$room->name.$room->name_surfix }}</a>
            </li>
            @endforeach
          </ul>
        </li>
        
        <li class="{{ Request::is($domainName.'/user/suggest/system*')   ? 'active' : ''  }}">
          <a href="{{ url($domainName.'/user/suggest/system') }}" >
            <i class="fa fa-circle-o"></i>
            <span>
               @lang('sidebar.suggest_system')
            </span>
            
          </a>
        </li>

        @endif


        @if($user->hasRole('officerss'))
        <li class="{{ Request::is($domainName.'/purchase*')   ? 'active' : ''  }} treeview">
          <a href="#">
            <img class="icon-side-menu" src="{{ asset('public/img/icon/icon_routine_work.png') }}"> <span>Routine Job</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li class="{{ Request::is($domainName.'/routine')   ? 'active' : ''  }}">
              <a href="{{ url($domainName.'/routine') }}"><img class="icon-side-menu" src="{{ asset('public/img/icon/icon_manage.png') }}"> Manage</a>
            </li>
            <li class="{{ Request::is($domainName.'/routine/view')   ? 'active' : ''  }}">
              <a href="{{ url($domainName.'/routine/view') }}"><img class="icon-side-menu" src="{{ asset('public/img/icon/icon_routine_view.png') }}"> View</a>
            </li>
          </ul>
        </li>
         @endif
        
        @if(Permission::hasPermission('quotation.menu'))
        <li class="{{ Request::is($domainName.'/purchase*')   ? 'active' : ''  }}" >
          <a href="{{ url($domainName.'/purchase/quotation') }}"><img class="icon-side-menu" src="{{ asset('public/img/icon/icon_purchasing_bidding.png') }}"> 
            <span>
               @lang('sidebar.quotation')
            </span>
            <span class="pull-right-container badge-quotation">
              <span class="label label-primary pull-right"></span>
            </span>
          </a>
        </li>
        @endif
        @if($hasOfficer||$hasAdmin)
        <li class="{{ ( Request::is($domainName.'/quotation-vote-setting*') || Request::is($domainName.'/quotation-setting*') )    ? 'active' : ''  }} treeview">
          <a href="#">
            <i class="fa fa-cog"></i>
            <span> @lang('sidebar.setting')</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="{{ Request::is($domainName.'/setting/domain*')   ? 'active' : ''  }}" >
              <a href="{{ url($domainName.'/setting/domain') }}"><i class="fa fa-circle-o"></i>
                <span>
                   @lang('sidebar.domain_setting')
                </span>
              </a>
            </li>
            <li class="{{ Request::is($domainName.'/setting/officer*')   ? 'active' : ''  }}" >
              <a href="{{ url($domainName.'/setting/officer') }}"><i class="fa fa-circle-o"></i>
                <span>
                   @lang('sidebar.officer_setting')
                </span>
              </a>
            </li>

    

            <li class="{{ Request::is($domainName.'/quotation-vote-setting*')   ? 'active' : ''  }}" >
              <a href="{{ url($domainName.'/quotation-vote-setting') }}"><i class="fa fa-circle-o"></i>
                <span>
                   @lang('sidebar.quotation_vote_setting')
                </span>
              </a>
            </li>
            <li class="{{ Request::is($domainName.'/quotation-setting*')   ? 'active' : ''  }}" >
                <a href="{{ url($domainName.'/quotation-setting') }}">
                  <i class="fa fa-circle-o"></i> 
                  <span>
                    @lang('sidebar.quotation_setting')
                  </span>
                </a>
              </li>
          </ul>
        </li>
       
       <li class="{{ Request::is('bill*')   ? 'active' : ''  }} treeview">
              <a href="#">
                <i class="fa fa-money"></i><span>
                 @lang('sidebar.bill')
                  <!-- User Management <BR> <span class="sidebar-row-margin"> จัดการผู้ใช้</span>  -->
                    
                  </span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>                  
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="{{ Request::is($domainName.'/bill*')   ? 'active' : ''  }}">
                  <a href="{{ url($domainName.'/bill') }}">
                    <i class="fa fa-upload"></i> 
                     <span> 
                       @lang('sidebar.bill_import')
                     </span>
                  </a>
                </li>
              </ul>
            </li>
        
        @endif

        @if($user->hasRole('officerss')||$user->hasRole('head.userss'))
        <li class="{{ Request::is($domainName.'/resolution*')   ? 'active' : ''  }}" >
          <a href="{{ url($domainName.'/resolution') }}"><img class="icon-side-menu" src="{{ asset('public/img/icon/icon_purchasing_bidding.png') }}"> 
            <span>
               @lang('sidebar.resolution')
            </span></a>
        </li>
         @endif
          <li class="{{ Request::is($domainName.'/post*')   ? 'active' : ''  }}" >
            <a href="{{ url($domainName.'/post') }}"><i class="fa fa-circle-o"></i>
              <span>
                 @lang('sidebar.public_information')
              </span></a>
          </li>
         @if($hasOfficer)
          <li class="{{ Request::is($domainName.'/notice*')   ? 'active' : ''  }}" >
            <a href="{{ url($domainName.'/notice') }}"><i class="fa fa-circle-o"></i>
              <span>
                 @lang('sidebar.notice')
              </span></a>
          </li>

          <li class="{{ ( Request::is($domainName.'/e-sticker*') || Request::is($domainName.'/report-e-sticker*') )    ? 'active' : ''  }} treeview">
          <a href="#">
            <i class="fa fa-cog"></i>
            <span> @lang('sidebar.e-sticker')</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li class="{{ Request::is($domainName.'/e-sticker*')   ? 'active' : ''  }}" >
              <a href="{{ url($domainName.'/e-sticker') }}"><i class="fa fa-circle-o"></i>
                <span>
                   @lang('sidebar.e-sticker-print')
                </span>
              </a>
            </li>
            <li class="{{ Request::is($domainName.'/report-e-sticker*')   ? 'active' : ''  }}" >
                <a href="{{ url($domainName.'/report-e-sticker') }}">
                  <i class="fa fa-circle-o"></i> 
                  <span>
                    @lang('sidebar.e-sticker-report')
                  </span>
                </a>
              </li>
          </ul>
        </li> 

         
          @endif 
          @if($hasOfficer)
         <li class="{{ ( Request::is($domainName.'/parking*') )    ? 'active' : ''  }} treeview">
          <a href="#">
            <i class="fa fa-car"></i>
            <span> @lang('sidebar.parking_buy')</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li class="{{ Request::is($domainName.'/parking/package*')   ? 'active' : ''  }}" >
              <a href="{{ url($domainName.'/parking/package') }}"><i class="fa fa-circle-o"></i>
                <span>
                   @lang('sidebar.parking_package')
                </span>
              </a>
            </li>
            <li class="{{ Request::is($domainName.'/parking/buy*')   ? 'active' : ''  }}" >
                <a href="{{ url($domainName.'/parking/buy') }}">
                  <i class="fa fa-circle-o"></i> 
                  <span>
                    @lang('sidebar.parking_buy')
                  </span>
                </a>
              </li>
              
              <li class="{{ Request::is($domainName.'/parking/report*')   ? 'active' : ''  }}" >
                <a href="{{ url($domainName.'/parking/report') }}">
                  <i class="fa fa-circle-o"></i> 
                  <span>
                    @lang('sidebar.parking_report')
                  </span>
                </a>
              </li>
              <li class="{{ Request::is($domainName.'/parking/debt*')   ? 'active' : ''  }}" >
                <a href="{{ url($domainName.'/parking/debt') }}">
                  <i class="fa fa-circle-o"></i> 
                  <span>
                    @lang('sidebar.parking_debt')
                  </span>
                </a>
              </li>
          </ul>
        </li>
        <li class="{{ ( Request::is($domainName.'/manual/parking/*') )    ? 'active' : ''  }} treeview">
          <a href="#">
            <i class="fa fa-circle-o"></i>
            <span> @lang('sidebar.set_e_coupon_manual')</span>
           
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li class="{{ Request::is($domainName.'/manual/parking/in*')   ? 'active' : ''  }}" >
              <a href="{{ url($domainName.'/manual/parking/in') }}"><i class="fa fa-circle-o"></i>
                <span>
                   @lang('sidebar.set_e_coupon_in_manual')
                </span>
              </a>
            </li>
            <li class="{{ Request::is($domainName.'/manual/parking/out*')   ? 'active' : ''  }}" >
              <a href="{{ url($domainName.'/manual/parking/out') }}"><i class="fa fa-circle-o"></i>
                <span>
                   @lang('sidebar.set_e_coupon_out_manual')
                </span>
              </a>
            </li>
          </ul>
        </li>
          @endif
           @if($hasOfficer)
         <li class="{{ ( Request::is($domainName.'/parcel*') )    ? 'active' : ''  }} treeview">
          <a href="#">
            <i class="fa fa-send"></i>
            <span> @lang('sidebar.parcel')</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
               <span class="badge-parcel-all">
                  <span class="label label-primary pull-right"></span>
              </span>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li class="{{ Request::is($domainName.'/parcel/officer*')   ? 'active' : ''  }}" >
              <a href="{{ url($domainName.'/parcel/officer') }}"><i class="fa fa-circle-o"></i>
                <span>
                   @lang('sidebar.parcel_post')
                </span>
              </a>
            </li>
            <li class="{{ Request::is($domainName.'/parcel/print-list*')   ? 'active' : ''  }}" >
              <a href="{{ url($domainName.'/parcel/print-list') }}"><i class="fa fa-circle-o"></i>
                <span>
                   @lang('sidebar.mailing_list')
                </span>
              </a>
            </li>
            <li class="{{ Request::is($domainName.'/parcel/print-gift*')   ? 'active' : ''  }}" >
              <a href="{{ url($domainName.'/parcel/print-gift') }}"><i class="fa fa-circle-o"></i>
                <span>
                   @lang('sidebar.receive_gift_list')
                </span>
              </a>
            </li>
            <li class="{{ Request::is($domainName.'/parcel/print-mail*')   ? 'active' : ''  }}" >
              <a href="{{ url($domainName.'/parcel/print-mail') }}"><i class="fa fa-circle-o"></i>
                <span>
                   @lang('sidebar.receive_mail_list')
                </span>
              </a>
            </li>
            <li class="{{ Request::is($domainName.'/parcel/backdate*')   ? 'active' : ''  }}" >
              <a href="{{ url($domainName.'/parcel/backdate') }}"><i class="fa fa-circle-o"></i>
                <span>
                   @lang('sidebar.parcel_backdate')
                   <span class="pull-right-container badge-parcel-backdate">
                      <span class="label label-primary pull-right"></span>
                    </span>
                </span>
              </a>
            </li>
            
          </ul>
        </li>
          @endif
          @if($hasUser)
          <li class="{{ Request::is($domainName.'/parking/*/use*')   ? 'active' : ''  }} treeview">
          <a href="#">
            <i class="fa fa-car"></i> 
            <span>
               @lang('sidebar.e_coupon')
            </span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @foreach ($user->getRoom() as $room)
             <li class="{{ Request::is($domainName.'/parking/'.$room->id.'/use*')   ? 'active' : ''  }}">
              <a href="{{ url($domainName.'/parking/'.$room->id.'/use') }}"><i class="fa fa-key"></i>  @lang('sidebar.e_coupon_room') {{ $room->name_prefix.$room->name.$room->name_surfix }}</a>
            </li>
            @endforeach
          </ul>
        </li>
         <li class="{{ Request::is($domainName.'/work/*/user/*')   ? 'active' : ''  }} treeview">
          <a href="#">
            <i class="fa fa-car"></i> 
            <span>
               @lang('sidebar.work')
            </span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @foreach ($user->getRoom() as $room)
             <li class="{{ Request::is($domainName.'/work/'.$room->id.'/user*')   ? 'active' : ''  }}">
              <a href="{{ url($domainName.'/work/'.$room->id.'/user') }}"><i class="fa fa-key"></i>  @lang('sidebar.work_room') {{ $room->name_prefix.$room->name.$room->name_surfix }}</a>
            </li>
            @endforeach
          </ul>
        </li>  
          @endif 
           @if($hasUser)
          <li class="{{ Request::is($domainName.'/parcel/*/user*')   ? 'active' : ''  }} treeview">
          <a href="#">
            <i class="fa fa-send"></i> 
            <span>
               @lang('sidebar.parcel_status')
            </span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @foreach ($user->getRoom() as $room)
             <li class="{{ Request::is($domainName.'/parcel/'.$room->id.'/user*')   ? 'active' : ''  }}">
              <a href="{{ url($domainName.'/parcel/'.$room->id.'/user') }}"><i class="fa fa-key"></i>  @lang('sidebar.parcel_post_room') {{ $room->name_prefix.$room->name.$room->name_surfix }}</a>
            </li>
            @endforeach
          </ul>
        </li>
          @endif 

          


          @if($user->hasRole('system.admin'))
             @include('main.layouts.system_admin_sidebar')
          @endif
          @if($hasGuard)
         
          <li class="{{ Request::is($domainName.'/guard/parking-in*')   ? 'active' : ''  }}" >
            <a href="{{ url($domainName.'/guard/parking-in') }}"> <i class="fa fa-car"></i>
              <span>
                 @lang('sidebar.check_e_coupon_in')
              </span></a>
          </li>
          <li class="{{ Request::is($domainName.'/parking/guard*')   ? 'active' : ''  }}" >
            <a href="{{ url($domainName.'/parking/guard') }}"> <i class="fa fa-car"></i>
              <span>
                 @lang('sidebar.check_e_coupon_out')
              </span></a>
          </li>
          
          @if($hasOfficer||$hasGuard)

           <li class="{{ Request::is($domainName.'/parking/cancel*')   ? 'active' : ''  }}" >
                <a href="{{ url($domainName.'/parking/cancel') }}">
                  <i class="fa fa-circle-o"></i> 
                  <span>
                    @lang('sidebar.parking_cancel')
                  </span>
                </a>
              </li>
          @endif
          @endif
       

            <li class="header">CHAT 
              <span class="pull-right-item">
                <a href="{{ url($domainName.'/channel') }}" >
                  <i class="fa fa-search pull-right"></i>
                </a>
              </span>
            </li>
            @if($user->hasRole('admin.chatsdsf'))
            <li><a href="{{ url($domainName.'/channel/create') }}"><img class="icon-side-menu" src="{{ asset('public/img/icon/icon_new_chat_room.png') }}"> 
              <span>
                 @lang('sidebar.new_chat')
              </span></a></li>
            @endif
            @foreach($user->getChannelJoin() as $channel)
              <li class="{{ Request::is($domainName.'/channel/'.$channel->id)   ? 'active' : ''  }}"><a href="{{ url($domainName.'/channel/'.$channel->id) }}"><i class="fa @if(isset($channel->icon)) {{ 'fa-'.$channel->icon }} @else fa-circle-o @endif"></i> <span>
                {{ $channel->name." (".getChannelTypeName($channel->type).")" }}
                @if($channel->unseen_count > 0)
                <span class="pull-right-container">
                  <span class="label label-primary ">{{$channel->unseen_count}}</span>
                </span>
                @endif
              </span></a></li>
            @endforeach
        
  
            <li class="header">CONTACT 
              <span class="pull-right-item">
                  <a href="{{ url($domainName.'/channel/contact') }}" >
                  <i class="fa fa-search pull-right"></i>
                  </a>
              </span>
            </li>
            @foreach($user->getContact() as $contact)
              <li class="{{ Request::is($domainName.'/channel/'.$contact->channel_id)   ? 'active' : ''  }}"><a href="{{ url($domainName.'/channel/'.$contact->channel_id) }}"><img class="icon-side-menu img-circle" src="{{ $contact->img }}"> <span>{{ $contact->name }}
 @if($contact->unseen_count > 0)
                <span class="pull-right-container">
                  <span class="label label-primary ">{{$contact->unseen_count}}</span>
                </span>
                @endif
              </span>
                </a>
               <span class="pull-right-item">
                <a href="javascript:void(0)" class="sidebar-remove-contact" data-id="{{$contact->channel_id}}" style="cursor: pointer;" title="remove from list">
                <i class="fa fa-times-circle pull-right"></i></a>
                </span>
  
                
               

              </li>
            @endforeach
          <li class="header" style="height:50px;"></li>
       <!--  <li class="header">LABELS</li>
        <li><a href="#"><i class="fa fa-circle-o text-red"></i> <span>Important</span></a></li>
        <li><a href="#"><i class="fa fa-circle-o text-yellow"></i> <span>Warning</span></a></li>
        <li><a href="#"><i class="fa fa-circle-o text-aqua"></i> <span>Information</span></a></li> -->
        @endif
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>