@extends('layouts.backend')

@section('title', 'Profile')

@section('content_header')
    <h1>Profile</h1>
@stop

@section('content')
  <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="{{ auth()->user()->image?url('/uploads/images/user/'.auth()->user()->image):url('/uploads/images/user/default.png')}}"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">{{ ucfirst(auth()->user()->name) }}</h3>

                <p class="text-muted text-center">{{ auth()->user()->email }}</p>
                <p class="text-muted text-center">{{ auth()->user()->mobile }}</p>
               
                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b><i class="fas fa-user mr-1"></i> First Name</b> <a class="float-right">{{ auth()->user()->first_name }}</a>
                  </li>
                  <li class="list-group-item">
                    <b><i class="fas fa-user mr-1"></i> Last Name</b> <a class="float-right">{{ auth()->user()->last_name }}</a>
                  </li>
                  <li class="list-group-item">
                    <b><i class="fas fa-mobile mr-1"></i> Mobile</b> <a class="float-right">{{ auth()->user()->country_code }} {!!  auth()->user()->mobile !!}</a>
                  </li>
                  <li class="list-group-item">
                    <b><i class="fas fa-map-marker-alt mr-1"></i> Address</b> <a class="float-right"> {!!  auth()->user()->address !!}</a>
                  </li>
                  
                </ul>

                <a  class="btn btn-primary btn-block"><b>&nbsp;</b></a>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
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
                  <li class="nav-item w-100"><a class="nav-link active" href="#settings" data-toggle="tab">Update Profile</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="settings">
                    <form class="form-horizontal" id="profileForm" namel="profileForm" action="{{ url('/profile') }}" method="POST" enctype="multipart/form-data">
                     @csrf
                      <div class="form-group row">
                        <label for="first_name" class="col-sm-2 col-form-label">First Name</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" value="{{ auth()->user()->first_name }}">
                        @if ($errors->has('first_name'))
                        <div class="error">
                            {{ $errors->first('first_name') }}
                        </div>
                        @endif
                        </div>
                      </div><div class="form-group row">
                        <label for="last_name" class="col-sm-2 col-form-label">Last Name</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name"  value="{{ auth()->user()->last_name }}"> 
                        @if ($errors->has('last_name'))
                        <div class="error">
                            {{ $errors->first('last_name') }}
                        </div>
                        @endif
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="email" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control" id="email" name="email" placeholder="Email"  value="{{ auth()->user()->email }}">
                         @if ($errors->has('email'))
                        <div class="error">
                            {{ $errors->first('email') }}
                        </div>
                        @endif
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="country_code" class="col-sm-2 col-form-label">Mobile</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control" id="country_code" name="country_code" placeholder="Country Code"  value="{{ auth()->user()->country_code }}">
                         @if ($errors->has('country_code'))
                        <div class="error">
                            {{ $errors->first('country_code') }}
                        </div>
                        @endif
                        </div>
                        <div class="col-sm-8">
                          <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile"  value="{{ auth()->user()->mobile }}">
                        @if ($errors->has('mobile'))
                        <div class="error">
                            {{ $errors->first('mobile') }}
                        </div>
                        @endif
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="address" class="col-sm-2 col-form-label">Address</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" id="address" name="address" placeholder="Address">{!! auth()->user()->address !!}</textarea>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="image" class="col-sm-2 col-form-label">Image</label>
                        <div class="col-sm-10">
                          <input type="file" class="form-control" id="image" name="image" placeholder="Image" accept="image/*">
                        </div>
                      </div>
                     
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-primary">Save</button>
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
                $('#profileForm').validate({
                    rules: {
                        first_name: {
                            required: true
                            // minlength: 5
                        },
                        country_code: {
                           required: true,
                        },
                        mobile: {
                            required: true,
                            number: true,
                            minlength: 10,
                            maxlength: 10
                        },
                        email: {
                            required: true,
                            email: true
                        },
                        
                    },
                    messages: {
                        first_name: {
                            required: "Please enter  first name"
                            //minlength: "Api name must be at least 5 characters long"
                        },
                        country_code: {
                            required: "Please enter  country code"
                            //minlength: "Api name must be at least 5 characters long"
                        },
                        mobile: {
                            required: "Please enter  mobile number"
                            //minlength: "Api name must be at least 5 characters long"
                        },
                        email: {
                            required: "Please provide email",
                            email: "Please enter a valid email address"
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
