<div class="float-end ms-1">
    <button class="btn btn-sm btn-primary btn-new-server">Baru</button>
</div>

<table class="table" id="table-list-wa-server">
    <thead>
        <tr>
            <th style="width: 70px">No.</th>
            <th scope="col">Nama</th>
            <th scope="col">Host</th>
            <th scope="col">Device</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="saveModal" tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title text-center" id="saveModalLabel">Update/Create Server WA</h5>
                <x-wa-server.form-save></x-wa-server.form-save>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        const capitalizeFirstLetter = ([first, ...rest], locale = navigator.language) =>
            first === undefined ? '' : first.toLocaleUpperCase(locale) + rest.join('')

        const dtWaServer = $('#table-list-wa-server').DataTable({
            processing: 'Loading...',
            serverSide: true,
            responsive: true,
            ajax: "{{ route('wa.servers') }}",
            columns: [{
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return '1';
                    }
                },
                {
                    data: 'name',
                    defaultContent: '-'
                },
                {
                    data: 'ip',
                    defaultContent: '-',
                    render: function(data, type, row, meta) {
                        const host = `${row.ip}:${row.port}`
                        return `<a href="http://${host}" target="_blank">${host}</a>`;
                    }
                },
                {
                    data: 'max_devices',
                    defaultContent: '-',
                    render: function(data, type, row, meta) {
                        return `<span class="${row.max_devices > 0 ? 'text-success' : 'text-danger'}">0/${data} Devices</span>`;
                    }
                },
                {
                    data: 'status',
                    defaultContent: '-',
                    render: function(data, type, row, meta) {
                        return `<span style="cursor: pointer" toggle-status="${data}" data-id="${row.id}" class="badge text-bg-${data === 'enable' ? 'success' : 'danger'}">${capitalizeFirstLetter(data)}</span>`;
                    }
                },
                {
                    defaultContent: '-',
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return `<button class="btn btn-sm btn-primary btn-update-server" data-id="${row.id}">Update</button>`;
                    }
                }
            ],
        });

        function clearInput() {
            $('#wa-server_form-save input:not(:hidden)').val('');
        }

        $('#wa-server_form-save').on('submit', function(event) {
            event.preventDefault();
            const form = $(this);
            const formData = form.serialize();
            const url = form.attr('action');
            const method = form.attr('method');
            $.ajax({
                url: url,
                type: method,
                data: formData,
                success: function(data) {
                    $('#saveModal').modal('hide');
                    dtWaServer.ajax.reload(null, false);
                    toast("success", `Success`, data.message);
                    clearInput();
                },
                error: function(error) {
                    console.error(error);
                    const message = error.responseJSON.message;
                    renderErrorValidation(error)
                    toast("error", `Error`, message);
                }
            });

        })

        $('#table-list-wa-server tbody').on('click', 'span.badge[toggle-status]', function() {
            const status = $(this).attr('toggle-status');
            const id = $(this).attr('data-id');
            const url = `{{ route('wa.servers.status') }}`;
            const data = {
                id: id,
                status: status,
                _token: '{{ csrf_token() }}'
            };
            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function(data) {
                    dtWaServer.ajax.reload(null, false);
                    toast("success", `Success ${data.status}`, data.message);
                },
                error: function(error) {
                    toast("error", `Fail update status`, error.responseJSON.message);
                    console.error(error);
                }
            });
        })

        $('.btn-new-server').on('click', function() {
            const action = $('#wa-server_form-save').attr('action');
            $('#wa-server_form-save').attr('action', action.split('?')[0]);
            $('#saveModal').modal('show');
            clearInput();
        })

        $('#table-list-wa-server tbody').on('click', '.btn-update-server', function() {
            const id = $(this).attr('data-id');
            const url = `{{ route('wa.server.show', ['waServer' => ':server']) }}`.replace(':server',
                id);
            $.ajax({
                url: url,
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#saveModal').modal('show');
                    const action = $('#wa-server_form-save').attr('action');
                    $('#wa-server_form-save').attr('action',
                        `${action}?wa_server_id=${id}`);
                    $('#wa-server_form-save input[name="name"]').val(data.name);
                    $('#wa-server_form-save select[name="status"]').val(data.status);
                    $('#wa-server_form-save input[name="ip"]').val(data.ip);
                    $('#wa-server_form-save input[name="port"]').val(data.port);
                    $('#wa-server_form-save input[name="max_devices"]').val(data
                        .max_devices);
                    $('#wa-server_form-save select[name="ssl_check"]').val(data
                        .disable_ssl_check);
                },
                error: function(error) {
                    toast("error", `Fail get information`, error.responseJSON.message);
                    console.error(error);
                }
            });
        })
    });
</script>
