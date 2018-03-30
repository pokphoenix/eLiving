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
                <div class="panel-heading">@lang('main.reset_password')</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form id="signup-form" class="form-horizontal" method="POST" action="{{ url('api/resetpass') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('id_card') ? ' has-error' : '' }}">
                            <label for="id_card" class="col-md-4 control-label">@lang('main.id_card')</label>

                            <div class="col-md-6">
                                <input id="id_card" type="text" maxlength="13" class="form-control" name="id_card" value="{{ old('id_card') }}" required>

                                @if ($errors->has('id_card'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_card') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    @lang('main.send_password_reset_link')
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
                id_card: {
                    required: true,
                    // minlength:13,
                    maxlength:13,
                },
            },
            messages: {
                id_card: (($("#app_local").val()=='th') ? 'กรอกรหัสบัตรประชาชนให้ถูกต้อง' : 'Wrong id card' ),
            },
            submitHandler: function (form) {
               swal({
                  title: (($("#app_local").val()=='th') ? 'กำลังประมวลผล' : 'Loading!' ),
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
                          title: (($("#app_local").val()=='th') ? 'กรุณาตรวจสอบอีเมล์ ' : 'Please check you email ' )+data.response.return_email ,
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