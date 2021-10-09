@extends('layouts.backend')

@section('title', 'Apis')

@section('content_header')
    <h1>Api's</h1>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Api List</h3>
                        <button type="button" class="btn btn-primary float-right" data-toggle="modal"
                            data-target="#modal-create">
                            Create Api's
                        </button>
                    </div>

                    <div class="card-body">
                        <table id="apiTable" class="table table-bordered table-striped1">
                            <thead>
                                <tr>
                                    <th>API</th>
                                    <th>Description</th>
                                    <th>key</th>
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
                            <h4 class="modal-title w-100 text-center">CREATE API CREDENTIALS</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form role="form" id="apicreateForm">
                            <div class="modal-body">

                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name"> Name</label>
                                        <input type="text" name="name" class="form-control" id="name"
                                            placeholder="Enter api name">
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" name="description" class="form-control" id="description"
                                            placeholder="Description">
                                    </div>
                                    <div class="form-group">
                                        <label for="key">Key</label>
                                        <input type="text" name="key" class="form-control" id="key" placeholder="Key">
                                    </div>
                                </div>
                                <!-- /.card-body -->


                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="createApibtn" class="btn btn-primary">Save</button>
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
                            <h4 class="modal-title w-100 text-center">EDIT API CREDENTIALS</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form role="form" id="apieditForm">
                        <input type="hidden" id="eid" name="eid" >
                            <div class="modal-body">

                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name"> Name</label>
                                        <input type="text" name="name" class="form-control" id="ename"
                                            placeholder="Enter api name">
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" name="description" class="form-control" id="edescription"
                                            placeholder="Description">
                                    </div>
                                    <div class="form-group">
                                        <label for="key">Key</label>
                                        <input type="text" name="key" class="form-control" id="ekey" placeholder="Key">
                                    </div>
                                </div>
                                <!-- /.card-body -->


                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="editApibtn" class="btn btn-primary">Save</button>
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

                table =  $('#apiTable').DataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "processing": true,
                    "serverSide": true,
                    ajax: {
                        url: "{{ route('apis.index') }}",
                    },
                    columns: [{
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'key',
                            name: 'key'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false
                        }
                    ]
                });

                $.validator.setDefaults({
                    submitHandler: function() {
                        alert("Form successful submitted!");
                    }
                });
                $('#apicreateForm').validate({
                    rules: {
                        name: {
                            required: true
                            // minlength: 5
                        },
                        key: {
                            required: true
                        }
                    },
                    messages: {
                        name: {
                            required: "Please enter a api name"
                            //minlength: "Api name must be at least 5 characters long"
                        },
                        key: {
                            required: "Please provide a key",
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


                $('#createApibtn').on('click', function() {
                    $("#apicreateForm").valid();
                    var name = $('#name').val();
                    var key = $('#key').val();
                    var description = $('#description').val();
                    if (name != "" && key != "") {
                        //   $("#butsave").attr("disabled", "disabled");
                        $.ajax({
                            url: "{{ route('apis.store') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                name: name,
                                description: description,
                                key: key
                            },
                            cache: false,
                            success: function(dataResult) {
                                console.log(dataResult);
                                if (dataResult.statusCode == 200) {
                                    $('#name').val('');
                                    $('#description').val('');
                                    $('#key').val('');
                                    $('#modal-create').modal('hide');
                                    table.draw();
                                    Swal.fire({
                                        type: 'success',
                                        title: 'Api added successfully',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                } else if (dataResult.statusCode == 201) {
                                    if (dataResult.error.name) {
                                        var nameError = dataResult.error.name;
                                        var name = nameError.toString();
                                        Swal.fire({
                                            type: 'warning',
                                            title: name,
                                            showConfirmButton: false,
                                            timer: 3000
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


                 $('#apieditForm').validate({
                    rules: {
                        ename: {
                            required: true
                        },
                        ekey: {
                            required: true
                        }
                    },
                    messages: {
                        ename: {
                            required: "Please enter a api name"
                        },
                        ekey: {
                            required: "Please provide a key",
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

                 $('#editApibtn').on('click', function() {
                    $("#apieditForm").valid();
                    var name = $('#ename').val();
                    var key = $('#ekey').val();
                    var description = $('#edescription').val();
                     var id = $('#eid').val();
                    if (name != "" && key != "") {
                        //   $("#butsave").attr("disabled", "disabled");
                        $.ajax({
                            url: "{{ route('apis.store') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                name: name,
                                description: description,
                                key: key,
                                id:id
                            },
                            cache: false,
                            success: function(dataResult) {
                                console.log(dataResult);
                                if (dataResult.statusCode == 200) {
                                    $('#ename').val('');
                                    $('#edescription').val('');
                                    $('#ekey').val('');
                                    $('#modal-edit').modal('hide');
                                    table.draw();
                                    Swal.fire({
                                        type: 'success',
                                        title: 'Api updated successfully',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                } else if (dataResult.statusCode == 201) {
                                    if (dataResult.error.name) {
                                        var nameError = dataResult.error.name;
                                        var name = nameError.toString();
                                        Swal.fire({
                                            type: 'warning',
                                            title: name,
                                            showConfirmButton: false,
                                            timer: 3000
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

            function editRow(id,name,description,key){
                $('#eid').val(id);
                $('#ename').val(name);
                $('#edescription').val(description);
                $('#ekey').val(key);
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
                            url: "{{ url('/apis') }}"+'/'+id,
                            success: function (data) {
                                table.draw();
                                Swal.fire({
                                    type: 'warning',
                                    title: 'Api deleted successfully',
                                    showConfirmButton: false,
                                    timer: 3000
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
