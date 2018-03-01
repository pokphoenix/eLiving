<section class="form-parent none" data-id="2" >
    <form id="room-form" method="post" class="mu-contact-form">      
        <div class="col-sm-offset-2 col-sm-8">
          	<div class="col-sm-12">
                <div class="input-group">
                   <input type="text" class="form-control" placeholder="@lang('room.search_room')" id="search_room" data-action="{{ url('/api/'.$domainId.'/search/room')}}" autocomplete="off">
                  <div class="input-group-btn">
                      <button type="button" class="btn btn-primary btn-flat"><i class="fa fa-search"></i>
                      </button>
                  </div>
                </div>
                <div class="search-append none" id="search_room_list" style="max-height:200px; overflow: auto;"></div>
            </div>
            <div class="col-sm-12">
              <BR>
                <table id="user-in-room-table" class="table table-bordered">
                  <thead>
                    <tr>
                      <th width="50"></th>
                      <th class="vm-ct" width="50">@lang('user.no')</th>
                      <th class="vm-ct">@lang('user.room_number')</th>
                      
                    </tr>
                  </thead>

                  <tbody>
                    
                  </tbody>
                  
                </table>
            </div>       
            <div class="form-check">
                <label class="form-check-label">
                <input type="checkbox" id="no_room" name="no_room" class="form-check-input" @if(old('agree'))  checked="" @endif >
                   @lang('room.no_room')
                </label>
         	</div>
        </div>         
    </form>
</section>