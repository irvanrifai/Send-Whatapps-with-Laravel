<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>

    <div class="text-center">
        <h1>Broadcast pesan Whatsapp</h1>
        <p>Mengirim pesan Broadcast dengan laravel dan Whatsapp gateway dari WABLAS</p>
    </div>
    <hr>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="\">No WA</label>
                    <input type="text" name="no_wa"
                        class="form-control" id="" placeholder="No WA">
                </div>
                <div class="table-responsive pt-2">
                    <table id="tb_datatable" class="table">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col"></th>
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
            <div class="col-sm-4">
                <form action="{{ url('form-send') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="\">Pesan</label>
                        <textarea name="pesan" class="form-control"
                            cols="30" rows="5" placeholder="Pesan"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send</button>
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
                    url: '{{ url('admin/category') }}',
                },
                columns: [{
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'id',
                        name: 'id',
                        title: 'ID Category',
                    },
                    {
                        data: 'name',
                        name: 'name',
                        title: 'Name',
                    },
                    {
                        data: 'create_at',
                        name: 'create_at',
                        title: 'Create',
                    },
                    {
                        data: 'update_at',
                        name: 'update_at',
                        title: 'Update',
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
                $.get("{{ url('admin/category') }}" + '/' + data_id + '/edit', function(data) {
                    $('#modelHeading').html("Edit Item");
                    $('#saveBtn').html("Update");
                    $('#ajaxModel').modal('show');
                    $('#data_id').val(data.id);
                    $('#name').val(data.name);
                })
            });

            // submit button after affect data
            $('#saveBtn').click(function(e) {
                e.preventDefault();
                $(this).html('Sending..');

                $.ajax({
                    data: $('#form_data').serialize(),
                    url: "{{ url('admin/category') }}",
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
                        url: "{{ url('admin/category') }}" + '/' + data_id,
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

</body>

</html>
