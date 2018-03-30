<div class="modal fade" id="modal-debt">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="debt-title"></h3>
          <h5 class="debt-text"></h5>
        </div>
        <div class="modal-body">
           <select id="debt_type" name="debt_type" class="form-control">
               <option value="0">@lang('parking.please_select_debt_type')</option>
            @if(isset($debtType))
              @foreach($debtType as $d)
                 <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
              @endforeach
            @endif
          </select> 
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('main.close')</button>
          <button type="button" class="btn btn-primary btn-save"> @lang('parking.accept_pay_debt')
             <i class="fa fa-spinner fa-spin fa-fw" style="display:none;"></i>
          </button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
