@extends('front.app')
<link rel="stylesheet" href="{{url('plugins/sweetalert2/sweetalert2.min.css')}}">
@section('content-wrapper')
<div style="height:100px;"></div>
<main role="main">
    <!-- Start Contact -->
    <section id="mu-contact">
      <div class="container">
              <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Reset Password</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form id="signup-form" class="form-horizontal" method="POST" action="{{ url('api/resetpass') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Send Password Reset Link
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
            },
            messages: {
                email: "Please enter a valid email address",
            },
            submitHandler: function (form) {
               swal({
                  title: 'Loading!',
                  onOpen: () => {
                    swal.showLoading()
                  }
                }).then((result) => {

                })
                // console.log(form.action);

             $.ajax({
                 type: "POST",
                 url: form.action,
                 data: $(form).serialize(),
                 success: function (data) {
                    // console.log(data,typeof data.response);
                    if(data.result=="true"){
                        swal({
                          title: 'Please check you email',
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