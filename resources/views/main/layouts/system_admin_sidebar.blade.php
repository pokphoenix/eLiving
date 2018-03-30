<li class="{{ Request::is('/domain*')   ? 'active' : ''  }}" >
	<a href="{{ url('/domain/list') }}"> <i class="fa fa-tasks"></i>
  <span>
     @lang('sidebar.domain')
  </span></a>
</li>
<li class="{{ Request::is($domainName.'/log-activity*')   ? 'active' : ''  }}" >
<a href="{{ url($domainName.'/log-activity') }}"> <i class="fa fa-tasks"></i>
  <span>
     @lang('sidebar.log_activity')
  </span></a>
</li>
<li class="{{ Request::is($domainName.'/master/*')   ? 'active' : ''  }} treeview">
  <a href="#">
    <i class="fa fa-ellipsis-v"></i> 
    <span>
       Master
    </span>
    <span class="pull-right-container">
      <i class="fa fa-angle-left pull-right"></i>
    </span>
  </a>
  <ul class="treeview-menu">
   	<li class="{{ Request::is($domainName.'/master/title-names*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/title-names') }}">
      	<i class="fa fa-circle-o"></i>  
		@lang('sidebar.title_name')
      </a>
    </li>
    <li class="{{ Request::is($domainName.'/master/debt-types*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/debt-types') }}">
      	<i class="fa fa-circle-o"></i>  
		@lang('sidebar.debt_type')
      </a>
    </li>
    <li class="{{ Request::is($domainName.'/master/esticker-reason*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/esticker-reason') }}">
      	<i class="fa fa-circle-o"></i>  
		@lang('sidebar.esticker_reason')
      </a>
    </li>
    <li class="{{ Request::is($domainName.'/master/parcel-type*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/parcel-type') }}">
      	<i class="fa fa-circle-o"></i>  
		@lang('sidebar.parcel_type')
      </a>
    </li>
    <li class="{{ Request::is($domainName.'/master/pioritize*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/pioritize') }}">
        <i class="fa fa-circle-o"></i>  
    @lang('sidebar.pioritize')
      </a>
    </li>
    <li class="{{ Request::is($domainName.'/master/supply-type*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/supply-type') }}">
        <i class="fa fa-circle-o"></i>  
    @lang('sidebar.supply_type')
      </a>
    </li>
     <li class="{{ Request::is($domainName.'/master/work-area-type*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/work-area-type') }}">
        <i class="fa fa-circle-o"></i>  
    @lang('sidebar.work_area_type')
      </a>
    </li>
    <li class="{{ Request::is($domainName.'/master/work-job-type*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/work-job-type') }}">
        <i class="fa fa-circle-o"></i>  
    @lang('sidebar.work_job_type')
      </a>
    </li>
    <li class="{{ Request::is($domainName.'/master/work-pioritize*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/work-pioritize') }}">
        <i class="fa fa-circle-o"></i>  
    @lang('sidebar.work_pioritize')
      </a>
    </li>
    <li class="{{ Request::is($domainName.'/master/work-system-type*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/work-system-type') }}">
        <i class="fa fa-circle-o"></i>  
    @lang('sidebar.work_system_type')
      </a>
    </li>
    <li class="{{ Request::is($domainName.'/master/word-blacklist*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/word-blacklist') }}">
        <i class="fa fa-circle-o"></i>  
    @lang('sidebar.word_blacklist')
      </a>
    </li>
    <li class="{{ Request::is($domainName.'/master/word-whitelist*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/word-whitelist') }}">
        <i class="fa fa-circle-o"></i>  
    @lang('sidebar.word_whitelist')
      </a>
    </li>
    <li class="{{ Request::is($domainName.'/master/suggest-category*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/suggest-category') }}">
        <i class="fa fa-circle-o"></i>  
    @lang('sidebar.suggest_category')
      </a>
    </li>
    <li class="{{ Request::is($domainName.'/master/channel-type*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/channel-type') }}">
        <i class="fa fa-circle-o"></i>  
    @lang('sidebar.channel_type')
      </a>
    </li>
    <li class="{{ Request::is($domainName.'/master/task-category*')   ? 'active' : ''  }}">
      <a href="{{ url($domainName.'/master/task-category') }}">
        <i class="fa fa-circle-o"></i>  
    @lang('sidebar.task_category')
      </a>
    </li>
  </ul>
</li>