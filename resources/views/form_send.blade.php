<!DOCTYPE html>
<html lang="en">

<head>
    <title>Broadcast</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <script src="https://kit.fontawesome.com/8186c4a2d4.js" crossorigin="anonymous"></script>

    {{-- datatable --}}
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    {{-- <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> --}}

    {{-- bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous">
    </script>
</head>

<body>

    <div class="text-center">
        <h1>Broadcast pesan Whatsapp</h1>
        <p>Mengirim pesan Broadcast dengan laravel dan Whatsapp gateway dari WABLAS</p>
    </div>
    <hr>
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <div class="form-group">
                    <label for="\">No WA</label>
                    <input type="text" name="no_wa"
                        class="form-control" id="" placeholder="No WA">
                </div>
                <a type="button" href="javascript:void(0)" class="btn btn-primary my-3 btn-sm bg-blue-500"
                    id="createCategory"><i class="fa fa-plus"></i>
                    Add data
                </a>
                <div class="table-responsive pt-2">
                    <table id="tb_datatable" class="table">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="col-sm-5">
                <form action="{{ url('form-send') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="\">Pesan</label>
                        <textarea name="pesan" class="form-control"
                            cols="30" rows="5" placeholder="Pesan"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary text-right">Send</button>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // to view/read data
            var table = $('#tb_datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ url('/') }}',
                },
                columns: [{
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        title: 'Name',
                    },
                    {
                        data: 'email',
                        name: 'email',
                        title: 'Email',
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        title: 'WhatApps',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        title: 'Action',
                        searchable: false,
                        orderable: false
                    },
                ],
            });

            // button to add new category
            $('#createCategory').click(function() {
                $('#saveBtn').html("Add");
                $('#data_id').val('');
                $('#form_data').trigger("reset");
                $('#modelHeading').html("Create New Data");
                $('#ajaxModel').modal('show');
            });

            // to affect an action with modal
            $('body').on('click', '#editItem', function() {
                var data_id = $(this).data('id');
                $.get("{{ url('/broadcast') }}" + '/' + data_id + '/edit', function(data) {
                    $('#modelHeading').html("Edit Item");
                    $('#saveBtn').html("Update");
                    $('#ajaxModel').modal('show');
                    $('#data_id').val(data.id);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#phone').val(data.phone);
                })
            });

            // submit button after affect data
            $('#saveBtn').click(function(e) {
                e.preventDefault();
                $(this).html('Sending..');

                $.ajax({
                    data: $('#form_data').serialize(),
                    url: "{{ url('/broadcast') }}",
                    type: "POST",
                    dataType: 'json',
                    enctype: 'multipart/form-data',
                    success: function(data) {

                        $('#form_data').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.draw();

                    },
                    error: function(data) {
                        console.log('Error:', data);
                        $('#saveBtn').html('Save changes');
                    }
                });
            });

            // button to delete item
            $('body').on('click', '#deleteItem', function() {

                var data_id = $(this).data("id");
                var result = confirm("Are you sure want to delete?");

                if (result) {
                    $.ajax({
                        url: "{{ url('/broadcast') }}" + '/' + data_id,
                        type: "delete",
                        data: {
                            id: data_id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            table.draw();
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                } else {
                    return false;
                }
            });
        });

        // for table can selected row
        $(document).ready(function() {
            var table = $('#tb_datatable').DataTable();

            $('#tb_datatable tbody').on('click', 'tr', function() {
                $(this).toggleClass('selected');
            });

            // button for inform amount of table selected (not used)
            $('#button').click(function() {
                alert(table.rows('.selected').data().length + ' row(s) selected');
            });
        });
    </script>

    {{-- modal for action --}}
    <div class="modal fade" id="ajaxModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modelHeading"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form novalidate action="{{ url('admin/category') }}" role="form" method="POST"
                    enctype="multipart/form-data" id="form_data" class="needs-validation">
                    @csrf
                    <input type="hidden" name="data_id" id="data_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="row mb-2 g-3">
                                <div class="mb-3 col-md-6 form-group">
                                    <label for="name" class="form-label">Name</label><span
                                        class="text-danger">*</span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" id="name" placeholder="Category" required
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6 form-group">
                                    <label for="email" class="form-label">Email</label><span
                                        class="text-danger">*</span>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                        name="email" id="email" placeholder="Category" required
                                        value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6 form-group">
                                    <label for="phone" class="form-label">WhatApps</label><span
                                        class="text-danger">*</span>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" id="phone" placeholder="Category" required
                                        value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="bi bi-plus-square btn btn-primary bg-blue-500" id="saveBtn"
                            value="create"><i class="fa fa-plus"></i> </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>
