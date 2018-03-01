@extends('main.layouts.main')

@section('style')
  <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
@endsection
@section('content-wrapper')

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        @lang('chat.title_chat')
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url($home) }}"><i class="fa fa-home"></i>@lang('main.home')</a></li>
        <li class="active">@lang('chat.title_chat')</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
               @include('layouts.error')
            </div>
        </div>
       


        <div class="row">
            <div class="col-sm-9">

                 <div class="box box-primary">
                    <div class="box-header with-border">
                      <h3 class="box-title">
                         @if(Auth()->user()->hasRole('admin.chat'))
        <a href="{{ url($domainName.'/channel/create') }}" class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> @lang('chat.btn_new')</a>
        @endif
                      </h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <ul class="products-list product-list-in-box">
                        @foreach($lists as $channel)
             
                        <li class="item">
                         
                          <div class="product-img">
                            <i class="fa @if(isset($channel['icon'])) {{ 'fa-'.$channel['icon'] }} @else fa-circle-o @endif"></i>
                          </div>
                          <div class="product-info">
                            <a href="{{ url($domainName.'/channel/'.$channel['id']) }}" class="product-title">
                             {{ $channel['name']." (".getChannelTypeName($channel['type']).")" }}
                            </a>
                            <span class="product-description">
                                 {{ $channel['description'] }}
                            </span>
                          </div>
                          
                        </li>

                        @endforeach

                        
                        
                      </ul>
                    </div>
                    <!-- /.box-body -->
                   <!--  <div class="box-footer text-center">
                      <a href="javascript:void(0)" class="uppercase">View All Products</a>
                    </div> -->
                    <!-- /.box-footer -->
                  </div>
            </div> 
          
        </div>
    </section>

    


@endsection

@section('javascript')
<script type="text/javascript">
</script>
@endsection







