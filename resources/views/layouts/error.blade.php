

@if(isset ($errors) && count($errors) > 0)
    <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                 @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
               
            </div>
@endif



@if(Session::get('success', false))

    <style>
       
    </style>
    <?php $data = Session::get('success'); ?>
    @if (is_array($data))
        @foreach ($data as $msg)
            <div id="success-alert" class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-ban"></i> Alert!</h4>
             @if (is_array($msg))
                    @foreach ($msg as $m)
                    {{ $m }}
                    @endforeach
                @else
                {{ $msg }}
                @endif
          </div>
        @endforeach
    @else
        <div id="success-alert" class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
                {{ $data }}
              </div>
    @endif
@endif

@if(Session::get('error', false))
    <?php $data = Session::get('error'); ?>
    @if (is_array($data))
        
        @foreach ($data as $msg)
             <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                @if (is_array($msg))
                    @foreach ($msg as $m)
                    {{ $m }}
                    @endforeach
                @else
                {{ $msg }}
                @endif
               
            </div>
        @endforeach
    @else
         <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-ban"></i> Alert!</h4>
            {{ $data }}
            
        </div>
    @endif
@endif

