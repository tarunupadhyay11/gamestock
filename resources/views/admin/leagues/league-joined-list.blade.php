@extends('layouts.backend')

@section('title', 'Leagues Joined')

@section('content_header')
    <h1>League Joined</h1>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">League Joined</h3>
                        <!--<button type="button" class="btn btn-primary float-right" data-toggle="modal"
                            data-target="#modal-create">
                            Create League
                        </button>!-->
                    </div>

                    <div class="card-body">
                        <table id="leagueJoinedTable" class="table table-bordered table-striped1">
                            <thead>
                                <tr>
                                    <th>League</th>
                                    <th>User</th>
                                    <th>Joined Date</th>
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

                table =  $('#leagueJoinedTable').DataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "processing": true,
                    "serverSide": true,
                    ajax: {
                        url: "{{ route('leagues-joined') }}",
                    },
                    columns: [{
                            data: 'league',
                            name: 'league'
                        },
                        {
                            data: 'user',
                            name: 'user'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
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
            });
              /* $.validator.setDefaults({
                    submitHandler: function() {
                        alert("Form successful submitted!");
                    }
                }); */
               


              

           

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
                            type: "POST",
                            url: "{{ url('/delete-leagues-joined') }}",
                            data: {id:id} ,
                            success: function (data) {
                                table.draw();
                                Swal.fire({
                                    type: 'warning',
                                    title: 'League joined  deleted successfully',
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
