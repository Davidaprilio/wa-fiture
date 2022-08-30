@props([
    'id',
    'text' => 'Logout',
    'ajax' => false,
])
<form action="{{ route('wa.device.logout', ['device' => $id]) }}" method="POST" id="wa-device_form-logout" class="d-inline w-100">
    @csrf
    <button type="submit" {{ $attributes->merge([
        'class' => 'btn btn-danger btn-logout w-100'
    ]) }}>
        {{ $text }}
    </button>
</form>

@if($ajax)
<script>
    $('#wa-device_form-logout').submit(function(e) {
        e.preventDefault();
        sendFormAjax('#wa-device_form-logout').then(data => {
            console.log(data);
        }).catch(err => {
            console.log(err);
        });
    });
</script>
@endif