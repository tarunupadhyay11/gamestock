@extends('layouts.backend')

@section('title', 'Leagues')

@section('content_header')
    <h1>Leagues</h1>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">League List</h3>
                        <!--<button type="button" class="btn btn-primary float-right" data-toggle="modal"
                            data-target="#modal-create">
                            Create League
                        </button>!-->
                    </div>

                    <div class="card-body">
                        <table id="leagueTable" class="table table-bordered table-striped1">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Group Name</th>
                                    <th>Founder</th>
                                    <th>League Type</th>
                                    <th>Starting Price</th>
                                    <th>Duration</th>
                                    <th>No. of Members</th>
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
                            <h4 class="modal-title w-100 text-center">CREATE LEAGUE</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('leagues.store') }}" role="form" id="leaguecreateForm" enctype="multipart/form-data" method="POST">
                            <div class="modal-body">
                                 @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name">Group Name</label>
                                        <input type="text" name="name" class="form-control" id="name"
                                            placeholder="Enter group name">
                                    </div>
                                    <div class="form-group">
                                        <label for="league_type">Visibility (League Type)</label>
                                        <select name="league_type" class="form-control" id="league_type"  placeholder="Visibility">
                                         <option value="public">Public</option>
                                          <option value="private">Private</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="portfolio_value">Starting Price</label>
                                        <input type="number" name="portfolio_value" class="form-control" id="portfolio_value" placeholder="Starting Price">
                                    </div>
                                    <div class="form-group">
                                        <label for="duration">League Duration</label>
                                        <input type="datetime-local" name="duration" class="form-control" id="duration" placeholder="League Duration">
                                    </div>
                                     <div class="form-group">
                                        <label for="image">Image</label>
                                        <input type="file" name="image" class="form-control" id="image" placeholder="Image" accept="image/*">
                                    </div>
                                </div>
                                <!-- /.card-body -->


                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" id="createLeaguebtn" class="btn btn-primary">Save</button>
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
                            <h4 class="modal-title w-100 text-center">EDIT LEAGUE</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                       <form  role="form" id="leagueeditForm" enctype="multipart/form-data" method="POST">
                            <div class="modal-body">
                                 @csrf
                                 <input type="hidden" name="eid" id="eid" >
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="ename">Group Name</label>
                                        <input type="text" name="ename" class="form-control" id="ename"
                                            placeholder="Enter group name">
                                    </div>
                                    <div class="form-group">
                                        <label for="eleague_type">Visibility (League Type)</label>
                                        <select name="eleague_type" class="form-control" id="eleague_type"  placeholder="Visibility">
                                         <option value="public">Public</option>
                                          <option value="private">Private</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="epassworddiv">
                                        <label for="epassword">Password</label>
                                        <input type="password" name="epassword" class="form-control" id="epassword" placeholder="Password">
                                    </div>
                                    <div class="form-group">
                                        <label for="eportfolio_value">Starting Price</label>
                                        <input type="number" name="eportfolio_value" class="form-control" id="eportfolio_value" placeholder="Starting Price">
                                    </div>
                                    <div class="form-group">
                                        <label for="eduration">League Duration</label>
                                        <input type="datetime-local" name="eduration" class="form-control" id="eduration" placeholder="League Duration">
                                    </div>
                                     <div class="form-group">
                                        <label for="eimage">Change Image</label>
                                        <input type="file" name="eimage" class="form-control" id="eimage" placeholder="Image" accept="image/*">
                                    </div>
                                </div>
                                <!-- /.card-body -->


                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" id="editLeaguebtn" class="btn btn-primary">Save</button>
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

                $('#eleague_type').on('change', function() {
                    if(this.value=='private'){
                       $("#epassworddiv").show();
                    }
                    else{
                       $("#epassworddiv").hide();
                    }
                });

                table =  $('#leagueTable').DataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "processing": true,
                    "serverSide": true,
                    ajax: {
                        url: "{{ route('leagues.index') }}",
                    },
                    columns: [{
                            data: 'image',
                            name: 'image'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'founder',
                            name: 'founder'
                        },
                        {
                            data: 'league_type',
                            name: 'league_type'
                        },
                        {
                            data: 'portfolio_value',
                            name: 'portfolio_value'
                        },
                        {
                            data: 'duration',
                            name: 'duration'
                        },
                        {
                            data: 'no_of_memebers',
                            name: 'no_of_memebers',
                            render: function ( data, type, row ) {
                                if(data){
                                     return  data;
                                }
                                else{
                                     return 0;
                                }
                                
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false
                        }
                    ]//,createdRow: function ( row, data, index ) {
                        //$('td', row).eq(0).css('background','#F7B500');
                    //}

                });

              /* $.validator.setDefaults({
                    submitHandler: function() {
                        alert("Form successful submitted!");
                    }
                }); */
                $('#leaguecreateForm').validate({
                    rules: {
                        name: {
                            required: true
                            // minlength: 5
                        },
                        league_type: {
                            required: true
                        },
                         portfolio_value: {
                            required: true
                        },
                         duration: {
                            required: true
                        },
                         image: {
                            required: true
                        }
                    },
                    messages: {
                        name: {
                            required: "Please enter a league name"
                            //minlength: "Api name must be at least 5 characters long"
                        },
                        portfolio_value: {
                            required: "Please enter starting price",
                        }
                        ,
                        duration: {
                            required: "Please select league duration",
                        },
                        image: {
                            required: "Please select image",
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


               $("#leaguecreateForm").submit(function (event) {
                     event.preventDefault();
                    if ($("#leaguecreateForm").valid()) {
                        $.ajax({
                            url: "{{ route('leagues.store') }}",
                            type: "POST",
                            data: new FormData($(this)[0]),
                            processData: false,
                            contentType: false,
                            cache: false,
                            success: function(dataResult) {
                                console.log(dataResult);
                                if (dataResult.statusCode == 200) {
                                    $('#name').val('');
                                    $('#portfolio_value').val('');
                                    $('#duration').val('');
                                    $('#image').val('');
                                    $('#modal-create').modal('hide');
                                    table.draw();
                                    Swal.fire({
                                        type: 'success',
                                        title: 'League added successfully',
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

                   $('#leagueeditForm').validate({
                    rules: {
                        ename: {
                            required: true
                            // minlength: 5
                        },
                        eleague_type: {
                            required: true
                        },
                         eportfolio_value: {
                            required: true
                        },
                         eduration: {
                            required: true
                        }
                    },
                    messages: {
                        ename: {
                            required: "Please enter a league name"
                            //minlength: "Api name must be at least 5 characters long"
                        },
                        eportfolio_value: {
                            required: "Please enter starting price",
                        }
                        ,
                        eduration: {
                            required: "Please select league duration",
                        },
                        eimage: {
                            required: "Please select image",
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

                 $("#leagueeditForm").submit(function (event) {
                     event.preventDefault();
                    if ($("#leagueeditForm").valid()) {
                        var id = $('#eid').val();
                        $.ajax({
                            url: "{{ route('leagues-updateLeague') }}",
                            type: "POST",
                            data: new FormData($(this)[0]),
                            processData: false,
                            contentType: false,
                            cache: false,
                            success: function(dataResult) {
                                console.log(dataResult);
                                if (dataResult.statusCode == 200) {
                                    $('#ename').val('');
                                    $('#eportfolio_value').val('');
                                    $('#eduration').val('');
                                    $('#eimage').val('');
                                    $('#modal-edit').modal('hide');
                                    table.draw();
                                    Swal.fire({
                                        type: 'success',
                                        title: 'League updated successfully',
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

            function editRow(id){

                  $.ajax({
                            url: "{{ route('league-by-id') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id:id
                            },
                            cache: false,
                            success: function(dataResult) {
                                console.log(dataResult);
                                $('#eid').val(id);
                                $('#ename').val(dataResult.name);
                                $('#eleague_type').val(dataResult.league_type);
                                 if(dataResult.league_type=='private'){
                                   $("#epassworddiv").show();
                                    $('#epassword').val(dataResult.password);
                                }
                                else{
                                    $('#epassword').val('');
                                  $("#epassworddiv").hide();
                                }

                                $('#eportfolio_value').val(dataResult.portfolio_value);
                                $('#eduration').val(dataResult.duration);
                                $('#modal-edit').modal('show');
                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });

                
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
                            url: "{{ url('/leagues') }}"+'/'+id,
                            success: function (data) {
                                table.draw();
                                Swal.fire({
                                    type: 'warning',
                                    title: 'League deleted successfully',
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
