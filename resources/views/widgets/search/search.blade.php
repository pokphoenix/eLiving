<div class="row">
    <div class="col-xs-12" >

      <form id="search-form" method="GET" action="">
        <table >
          
          <tr>
            <td >@lang('parcel.start_search')</td>
            <td style="padding-left: 5px;"></td>
            <td style="padding-left: 50px;">@lang('parcel.end_search')</td>
            <td style="padding-left: 5px;"></td>
            <td class="col-xs-2"></td>
          </tr>
          <tr>
            <td >
              @lang('parcel.day')
              <input type="text" style="width:50px;" id="start_date_day"  placeholder="@lang('parcel.send_date_day')" value="{{ date('d',$startDate) }}" >
              <input type="text" style="width:50px;" id="start_date_month"  placeholder="@lang('parcel.send_date_month')" value="{{ date('m',$startDate) }}" >
              <input type="text" style="width:50px;" id="start_date_year"  placeholder="@lang('parcel.send_date_year')" value="{{ date('Y',$startDate) }}" >
            </td>
            <td style="padding-left: 5px;"> 
              
              @lang('parcel.time') : 
              <input type="text" style="width:50px;" id="start_date_hour"  placeholder="@lang('parcel.send_date_hour')" value="{{ date('H', strtotime('-1 hours',$startDate)  ) }}" >
               <input type="text" style="width:50px;" id="start_date_minute"  placeholder="@lang('parcel.send_date_minute')" value="{{ date('i',strtotime('-1 hours',$startDate) ) }}" >
            </td>
             <td style="padding-left: 50px;" > 
               @lang('parcel.day')
              <input type="text" style="width:50px;" id="end_date_day"  placeholder="@lang('parcel.send_date_day')" value="{{ date('d',$endDate) }}" >
              <input type="text" style="width:50px;" id="end_date_month"  placeholder="@lang('parcel.send_date_month')" value="{{ date('m',$endDate) }}" >
               <input type="text" style="width:50px;" id="end_date_year"  placeholder="@lang('parcel.send_date_year')" value="{{ date('Y',$endDate) }}" >
            </td>
            <td style="padding-left: 5px;"> 
               @lang('parcel.time') : 
               <input type="text" style="width:50px;" id="end_date_hour"  placeholder="@lang('parcel.send_date_hour')" value="{{ date('H',$endDate) }}" >
                <input type="text" style="width:50px;" id="end_date_minute"  placeholder="@lang('parcel.send_date_minute')" value="{{ date('i',$endDate) }}" >
            </td>
            <td style="padding-left: 5px;">
              <button type="button" class="btn btn-primary  btn-search" style="height:26px;padding: 0 10px;"><i class="fa fa-search"></i> @lang('main.search')</button>  
            </td>
          </tr>
        </table>
      </form>

    </div>     
</div>
 <div class="col-sm-12" style="height: 20px;"></div>
