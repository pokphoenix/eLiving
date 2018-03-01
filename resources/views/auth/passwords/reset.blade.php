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
                    <div class="panel-heading">Reset Password</div>

                    <div class="panel-body">
                        <form class="form-horizontal" id="signup-form" method="POST" action="{{ url('api/reset/change/password') }}">
                            {{ csrf_field() }}

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">Password</label>

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
                                <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
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
                                        Reset Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @else
                <div class="text-center" style="margin-top: 50px;">

                    <h3>Token expire</h3>
                    <p>
                        You can reset new password <BR>
                        <a href="{{ url('password/reset') }}" class="btn btn-primary">
                            Go
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
                email: {
                    required: true,
                    email: true
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
                email: "Please enter a valid email address",
                password: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 5 characters long",
                    maxlength: "Your password cannot over 40 characters long"
                },
                password_confirmation: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 5 characters long",
                    maxlength: "Your password cannot over 40 characters long",
                    equalTo: "Please enter the same password as above"
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
                          title: 'Change password complete',
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