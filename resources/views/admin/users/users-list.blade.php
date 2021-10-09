@extends('layouts.backend')

@section('title', 'Users')

@section('content_header')
    <h1>Users</h1>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Users List</h3>
                        <button type="button" class="btn btn-primary float-right" data-toggle="modal"
                            data-target="#modal-create">
                            Create User
                        </button>
                    </div>

                    <div class="card-body">
                        <table id="userTable" class="table table-bordered table-striped1">
                            <thead>
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- /.row -->
                <!-- Main row -->
            </div>
            <!-- /.row (main row) -->
            <div class="modal fade" id="modal-create">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title w-100 text-center">CREATE USER</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form role="form" id="ucreateForm">
                            <div class="modal-body">

                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="first_name">First Name</label>
                                        <input type="text" name="first_name" class="form-control" id="first_name"
                                            placeholder="Enter first name">
                                    </div>
                                    <div class="form-group">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" name="last_name" class="form-control" id="last_name"
                                            placeholder="Enter last name">
                                    </div>
                                     <div class="form-group">
                                        <label for="mobile">Mobile</label>
                                        <input  type="number" name="mobile" class="form-control" id="mobile"
                                            placeholder="Enter mobile no." oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxLength="10" >
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" class="form-control" id="email"
                                            placeholder="Email">
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <div class="input-group mb-3">
                                           <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                                            <div class="input-group-append" style="border: 1px solid #ced4da;">
                                            <span toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password" style="display: flex;position: relative;top: 30%;left: 11px;transform: translateX(-50%);"></span>
                                            </div>
                                        </div>
                                        
                                    
                                    </div>
                                </div>
                                <!-- /.card-body -->


                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="createUbtn" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <!-- /.row (main row) -->
            <div class="modal fade" id="modal-edit">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title w-100 text-center">EDIT USER</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form role="form" id="ueditForm">
                        <input type="hidden" id="eid" name="eid" >
                            <div class="modal-body">

                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="efirst_name">First Name</label>
                                        <input type="text" name="efirst_name" class="form-control" id="efirst_name"
                                            placeholder="Enter first name">
                                    </div>
                                     <div class="form-group">
                                        <label for="elast_name">Last Name</label>
                                        <input type="text" name="elast_name" class="form-control" id="elast_name"
                                            placeholder="Enter last name">
                                    </div>
                                    <div class="form-group">
                                        <label for="emobile">Mobile</label>
                                        <input type="number" name="emobile" class="form-control" id="emobile"
                                            placeholder="Enter mobile no."  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxLength="10">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="eemail" class="form-control" id="eemail"
                                            placeholder="Email">
                                    </div>
                                    <div class="form-group">
                                        <label for="epassword">Password</label>
                                        <div class="input-group mb-3">
                                          <input type="password" name="epassword" class="form-control" id="epassword" placeholder="Password">
                                            <div class="input-group-append" style="border: 1px solid #ced4da;">
                                            <span toggle="#epassword-field" class="fa fa-fw fa-eye field_icon toggle-epassword" style="display: flex;position: relative;top: 30%;left: 11px;transform: translateX(-50%);"></span>
                                            </div>
                                        </div>
                                    
                                    </div>
                                </div>
                                <!-- /.card-body -->


                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="editUbtn" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>


        </div><!-- /.container-fluid -->
    @endsection



    @section('css')
 <style>
   thead{
       background: #343a40;
        color: #fff;
   }
   tbody{
           background-color: #f7b500;
   }
   </style>
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

                table =  $('#userTable').DataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "processing": true,
                    "serverSide": true,
                    ajax: {
                        url: "{{ route('users.index') }}",
                    },
                    columns: [{
                            data: 'first_name',
                            name: 'first_name'
                        },
                        {
                            data: 'last_name',
                            name: 'last_name'
                        },
                        {
                            data: 'mobile',
                            name: 'mobile'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false
                        }
                    ]
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

                $.validator.setDefaults({
                    submitHandler: function() {
                        alert("Form successful submitted!");
                    }
                });
                $('#ucreateForm').validate({
                    rules: {
                        first_name: {
                            required: true
                            // minlength: 5
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
                        password: {
                            required: true,
                             minlength: 8
                        }
                    },
                    messages: {
                        first_name: {
                            required: "Please enter  name"
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
                        password: {
                            required: "Please provide a password",
                            minlength: "Password must be at least 8 characters long"
                        }
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


                $('#createUbtn').on('click', function() {
                    
                    var first_name = $('#first_name').val();
                    var last_name = $('#last_name').val();
                    var mobile = $('#mobile').val();
                    var email = $('#email').val();
                    var password = $('#password').val();
                    if($("#ucreateForm").valid()){
                           $.ajax({
                            url: "{{ route('users.store') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                first_name: first_name,
                                last_name: last_name,
                                mobile:mobile,
                                email: email,
                                password: password
                            },
                            cache: false,
                            success: function(dataResult) {
                                console.log(dataResult);
                                if (dataResult.statusCode == 200) {
                                    $('#first_name').val('');
                                    $('#last_name').val('');
                                    $('#mobile').val('');
                                    $('#email').val('');
                                    $('#password').val('');
                                    $('#modal-create').modal('hide');
                                    table.draw();
                                    Swal.fire({
                                        type: 'success',
                                        title: 'User added successfully',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                } else if (dataResult.statusCode == 201) {
                                    if (dataResult.error.email) {
                                        var nameError = dataResult.error.email;
                                        var name = nameError.toString();
                                        Swal.fire({
                                            type: 'warning',
                                            title: name,
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                    }

                                }

                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                        
                    
                });


                 $('#ueditForm').validate({
                    rules: {
                        efirst_name: {
                            required: true
                            // minlength: 5
                        },
                         emobile: {
                            required: true,
                            number: true,
                            minlength: 10,
                            maxlength: 10
                        },
                        eemail: {
                            required: true,
                            email: true
                        },
                        epassword: {
                             minlength: 8
                        }
                    },
                    messages: {
                        efirst_name: {
                            required: "Please enter  name"
                            //minlength: "Api name must be at least 5 characters long"
                        },
                        emobile: {
                            required: "Please enter  mobile number"
                            //minlength: "Api name must be at least 5 characters long"
                        },
                        eemail: {
                            required: "Please provide email",
                            email: "Please enter a valid email address"
                        },
                        epassword: {
                            minlength: "Password must be at least 8 characters long"
                        }
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

                 $('#editUbtn').on('click', function() {
                    var first_name = $('#efirst_name').val();
                    var last_name = $('#elast_name').val();
                    var mobile = $('#emobile').val();
                    var email = $('#eemail').val();
                    var password = $('#epassword').val();
                    var id = $('#eid').val();
                    if($("#ueditForm").valid()){
                           $.ajax({
                            url: "{{ route('users.store') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                first_name: first_name,
                                last_name: last_name,
                                mobile:mobile,
                                email: email,
                                password: password,
                                id:id
                            },
                            cache: false,
                            success: function(dataResult) {
                                console.log(dataResult);
                                if (dataResult.statusCode == 200) {
                                    $('#efirst_name').val('');
                                    $('#elast_name').val('');
                                     $('#emobile').val('');
                                    $('#eemail').val('');
                                    $('#epassword').val('');
                                    $('#modal-edit').modal('hide');
                                    table.draw();
                                    Swal.fire({
                                        type: 'success',
                                        title: 'User updated successfully',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                } else if (dataResult.statusCode == 201) {
                                    if (dataResult.error.email) {
                                        var nameError = dataResult.error.email;
                                        var name = nameError.toString();
                                        Swal.fire({
                                            type: 'warning',
                                            title: name,
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                    }

                                }

                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                });

            });

            function editRow(id,first,last,mobile,email){
                $('#eid').val(id);
                $('#efirst_name').val(first);
                $('#elast_name').val(last);
                $('#emobile').val(mobile);
                $('#eemail').val(email);
                $('#modal-edit').modal('show');
            }

            function deleteRow(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {

                        $.ajax({
                            type: "DELETE",
                            url: "{{ url('/users') }}"+'/'+id,
                            success: function (data) {
                                table.draw();
                                Swal.fire({
                                    type: 'warning',
                                    title: 'User deleted successfully',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                });

                
            }

        </script>
    @stop
