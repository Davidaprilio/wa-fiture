@props([
    'action' => route('wa.server.save'),
    'submit' => 'Save',
    'update' => null,
])

@php
if ($update !== null && !($update instanceof \Quods\Whatsapp\Models\WaServer)) {
    throw new \Exception(':update props must be instance of WaServer Model');
}
@endphp

<form action="{{ $action }}" method="POST" id="wa-server_form-save">
    @csrf
    <div class="row">
        <div class="form-group mb-2 col-8">
            <label class="form-control-label" for="name-input">Name Server</label>
            <input type="text" id="name-input" class="form-control @error('name') is-invalid @enderror" name="name"
                value="{{ old('name') }}" required autocomplete="name">
            @error('name')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-4">
            <label class="form-control-label" for="status-select">Status</label>
            <select class="form-select @error('status') is-invalid @enderror" name="status" id="status-select">
                @foreach (['enable', 'disable'] as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
            @error('status')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group mb-2 col-8">
            <label class="form-control-label" for="ip-input">IP Address</label>
            <input type="text" id="ip-input" class="form-control @error('ip') is-invalid @enderror" name="ip"
                value="{{ old('ip') }}" required autocomplete="ip">
            @error('ip')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group mb-2 col-4">
            <label class="form-control-label" for="port-input">PORT</label>
            <input type="number" id="port-input" class="form-control @error('port') is-invalid @enderror"
                name="port" value="{{ old('port') }}" required autocomplete="port" minlength="1000">
            @error('port')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-12">
            <label class="form-control-label" for="api_key-input">API KEY</label>
            <input type="password" id="api_key-input" class="form-control @error('api_key') is-invalid @enderror"
                name="api_key" value="{{ old('api_key') }}" required autocomplete="api_key">
            @error('api_key')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-6">
            <label class="form-control-label" for="max_devices-input">Max Devices</label>
            <input type="tel" id="max_devices-input" class="form-control @error('max_devices') is-invalid @enderror"
                name="max_devices" value="{{ old('max_devices') }}" required autocomplete="max_devices">
            @error('max_devices')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-6">
            <label class="form-control-label" for="ssl_check-select">Disable SSL Check (curl)</label>
            <select class="form-select @error('ssl_check') is-invalid @enderror" name="ssl_check" id="ssl_check-select">
                @foreach (['Always Check' => 0, 'Without Checking' => 1] as $text => $value)
                    <option value="{{ $value }}">{{ $text }}</option>
                @endforeach
            </select>
            @error('ssl_check')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>

    </div>
    @if (gettype($submit) == 'string')
        <div class="text-center mt-5">
            <button type="submit" class="btn btn-primary">{{ $submit }}</button>
        </div>
    @endif
</form>
<script>
    function onSubmitWaServerFormSave() {}
</script>
