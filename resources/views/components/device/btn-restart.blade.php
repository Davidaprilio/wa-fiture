@props([
    'id',
    'text' => 'Start/Restart',
    'ajax' => false,
])
<form action="{{ route('wa.device.restart', ['device' => $id]) }}" class="d-inline w-100" method="POST" id="wa-device_form-start">
    @csrf
    <button type="submit" {{ $attributes->merge([
        'class' => 'btn btn-primary btn-start w-100'
    ]) }}>
        {{ $text }}
    </button>
</form>

@if($ajax)
<script>
    $('#wa-device_form-start').submit(function(e) {
        e.preventDefault();
        sendFormAjax('#wa-device_form-start').then(data => {
            console.log(data);
        }).catch(err => {
            console.log(err);
        });
    });
</script>
@endif