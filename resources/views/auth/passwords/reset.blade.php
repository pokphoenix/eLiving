@extends('front.app')
@section('content-wrapper')
<link rel="stylesheet" href="{{url('plugins/sweetalert2/sweetalert2.min.css')}}">
<div style="height:100px;"></div>
<main role="main">
    <!-- Start Contact -->

    

    <section id="mu-contact">
        <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                
                @if(!empty($data))
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('main.reset_password')</div>

                    <div class="panel-body">
                        <form class="form-horizontal" id="signup-form" method="POST" action="{{ url('api/reset/change/password') }}">
                            {{ csrf_field() }}

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                <label for="username" class="col-md-4 control-label">@lang('reset.username')</label>

                                <div class="col-md-6">
                                    <input id="username" type="text" class="form-control" name="username" value="{{ $username or old('username') }}" required autofocus>

                                    @if ($errors->has('username'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">@lang('reset.password')</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label for="password-confirm" class="col-md-4 control-label">@lang('reset.confirm_password')</label>
                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                       @lang('reset.btn_reset')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @else
                <div class="text-center" style="margin-top: 50px;">

                    <h3>@lang('main.token_expire')
                    </h3>
                    <p>
                        @lang('main.you_can_reset_new_password') <BR>
                        <a href="{{ url('password/reset') }}" class="btn btn-primary">
                           @lang('main.here')
                        </a>
                    </p>
                </div>
               
                @endif
            </div>
        </div>
    </div>
    </section>
</main>

@endsection
@section('javascript')
<script src="{{url('plugins/sweetalert2/sweetalert2.min.js')}}"></script>

<script type="text/javascript" src="{{ url('plugins/jquery-validate/jquery.validate.min.js')}}"></script>
<script type="text/javascript" src="{{ url('js/utility/validate.js')}}"></script>
<script type="text/javascript">
    $(function() {
        $("#signup-form").validate({
            rules: {
                username: {
                    required: true,
                    
                },
                password: {
                    required: true,
                    minlength: 5,
                    maxlength: 40,
                },
                password_confirmation: {
                    required: true,
                    minlength: 5,
                    maxlength: 40,
                    equalTo: "#password"
                }
            },
            messages: {
                username: (($("#app_local").val()=='th') ? 'กรุณากรอกชื่อผู้ใช้ให้ถูกต้อง' : "Wrong Username" ) ,
                password: {
                    required: (($("#app_local").val()=='th') ? 'กรุณากรอกพาสเวิดให้ถูกต้อง' : "Wrong Password" ),
                    minlength: (($("#app_local").val()=='th') ? 'กรุณากรอกพาสเวิดให้ถูกต้อง' : "Wrong Password" ),
                    maxlength: (($("#app_local").val()=='th') ? 'กรุณากรอกพาสเวิดให้ถูกต้อง' : "Wrong Password" )
                },
                password_confirmation: {
                    required: (($("#app_local").val()=='th') ? 'กรุณากรอกยืนยันพาสเวิดให้ถูกต้อง' : "Wrong confirm password" ),
                    minlength: (($("#app_local").val()=='th') ? 'กรุณากรอกยืนยันพาสเวิดให้ถูกต้อง' : "Wrong confirm password" ),
                    maxlength: (($("#app_local").val()=='th') ? 'กรุณากรอกยืนยันพาสเวิดให้ถูกต้อง' : "Wrong confirm password" ),
                    equalTo: (($("#app_local").val()=='th') ? 'กรุณากรอกยืนยันพาสเวิดให้ถูกต้อง' : "Wrong confirm password" )
                },
            },
            submitHandler: function (form) {

                // console.log(form.action);

             $.ajax({
                 type: "POST",
                 url: form.action,
                 data: $(form).serialize(),
                 success: function (data) {
                    // console.log(data,typeof data.response);
                    if(data.result=="true"){
                        swal({
                          title: (($("#app_local").val()=='th') ? 'เปลี่ยนรหัสผ่านสำเร็จ' : 'Change password complete' ) ,
                          type: 'success',
                          showCancelButton: false,
                          confirmButtonText: "@lang('main.ok')"
                        }).then((result) => {
                          if (result.value) {
                            window.location.href = "{{ url('login') }}";
                          }
                        })

                     
                    }else{
                        var error = JSON.stringify(data.errors);
                        // console.log(error);
                       swal(
                        'Error...',
                        error,
                        'error'
                      )
                    }
                 }
             });
             return false; // required to block normal submit since you used ajax
         }
            

        });

    });
</script>
@endsection