
@if(isset($domainId))
<input type="hidden" id="domainId" value="{{ $domainId }}">
<input type="hidden" id="baseUrl" value="{{url($domainName)}}">
<input type="hidden" id="apiUrl" value="{{url('api/'.$domainId)}}">

@endif

<input type="hidden" id="mainPath" value="{{url('')}}">
<input type="hidden" id="user_id" value="{{ Auth()->user()->id }}">
<input type="hidden" id="app_local" value="{{ App::getLocale() }}">
