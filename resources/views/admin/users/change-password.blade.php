@extends('layouts.backend')

@section('title', 'Profile')

@section('content_header')
    <h1>Change Password</h1>
@stop

@section('content')
  <div class="container-fluid">
        <div class="row">
         
          <div class="col-md-12">
            <div class="card">
              <div class="card-header p-2">
                <!-- Success message -->
                @if(Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong>  {{Session::get('success')}}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                @endif
                <ul class="nav nav-pills ">
                  <li class="nav-item w-100"><a class="nav-link active" href="#settings" data-toggle="tab">Change Password</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="settings">
                    <form class="form-horizontal" id="changepasswordForm" namel="changepasswordForm" action="{{ url('/change-password') }}" method="POST" enctype="multipart/form-data">
                     @csrf
                      <div class="form-group row">
                        <label for="current_password" class="col-sm-2 col-form-label">Current Password</label>
                        <div class="col-sm-10">
                          <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Current Password" value="">
                        @if ($errors->has('current_password'))
                        <div class="error">
                            {{ $errors->first('current_password') }}
                        </div>
                        @endif
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="new_password" class="col-sm-2 col-form-label">New Password</label>
                        <div class="col-sm-10">
                          <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password"  value=""> 
                        @if ($errors->has('new_password'))
                        <div class="error">
                            {{ $errors->first('new_password') }}
                        </div>
                        @endif
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="confirm_password" class="col-sm-2 col-form-label">Confirm Password</label>
                        <div class="col-sm-10">
                          <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password"  value=""> 
                        @if ($errors->has('confirm_password'))
                        <div class="error">
                            {{ $errors->first('confirm_password') }}
                        </div>
                        @endif
                        </div>
                      </div>
                     
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    @endsection



    @section('css')

    @stop

    @section('js')

        <script>
        var table;
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });


                $(document).on('click', '.toggle-password', function() {
                    $(this).toggleClass("fa-eye fa-eye-slash");
                    var input = $("#password");
                    input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password')
                });

                 $(document).on('click', '.toggle-epassword', function() {
                    $(this).toggleClass("fa-eye fa-eye-slash");
                    var input = $("#epassword");
                    input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password')
                });

                //$.validator.setDefaults({
                   // submitHandler: function() {
                        //alert("Form successful submitted!");
                   // }
                //});
                $('#changepasswordForm').validate({
                    rules: {
                        current_password: {
                            required: true,
                           // minlength: 8
                        },
                        new_password: {
                           required: true,
                           minlength: 8
                        },
                        confirm_password: {
                            required: true,
                            minlength: 8,
                            equalTo: "#new_password"
                        },
                        
                    },
                    messages: {
                        current_password: {
                            required: "Please enter current password"
                           // minlength: "Password must be at least 8 characters long"
                        },
                        new_password: {
                            required: "Please enter  new password",
                            minlength: "New password must be at least 8 characters long"
                        },
                        confirm_password: {
                            required: "Please enter  confirm password",
                            minlength: "confirm password must be at least 8 characters long"
                        },
                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    }
                });

            });


        </script>
    @stop
