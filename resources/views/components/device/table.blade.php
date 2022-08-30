<div class="float-end ms-1">
    <button class="btn btn-sm btn-primary btn-new-server">Baru</button>
</div>

<table class="table" id="table-list-wa-server">
    <thead>
        <tr>
            <th style="width: 70px">No.</th>
            <th scope="col">Label</th>
            <th scope="col">Phone</th>
            <th scope="col">Status</th>
            <th scope="col">Server</th>
            <th class="text-end">Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<script>
    $(document).ready(function() {
        $('#table-list-wa-server').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('wa.devices') }}',
            columns: [{
                    data: 'id',
                    name: 'id',
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'phone',
                    name: 'phone',
                    defaultContent: '-',
                    render: function(data, type, row) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'server',
                    name: 'server'
                },
                {
                    orderable: false,
                    searchable: false,
                    className: 'text-end',
                    render: function(data, type, row) {
                        const url = {
                            edit: "{{ route('wa.device.view', ['device' => ':id']) }}",
                        }
                        return `<div class="btn-group">
                            <a href="${url.edit.replace(':id', row.id)}" class="btn btn-sm btn-info" data-id="${row.id}">Edit</a>
                            <button class="btn btn-sm btn-primary btn-edit-server" data-id="${row.id}">Edit</button>
                            <button class="btn btn-sm btn-danger btn-delete-server" data-id="${row.id}">Hapus</button>
                        </div>`;
                    }
                }
            ]
        });
    });
</script>