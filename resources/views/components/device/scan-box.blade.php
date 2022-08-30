@props([
    'url' => null,
    'device'
])

<div id="wa-device_scan-box">
    <h3 class="mb-2">
        <span class="badge text-bg-primary">{{ $device->name }}</span>
    </h3>
    <div class="d-flex justify-content-between">
        <span id="wa-device_status">{{ $device->status }}</span> 
        <div>
            <span id="wa-device_id">{{ $device->id }}</span>
            <span class="mx-1">|</span>
            <span id="wa-device_server">{{ $device->wa_server_id }}</span>
        </div>
    </div>
    <img src="https://via.placeholder.com/500?text=QR+Code" id="wa-device_img-scan" class="img-fluid my-1">
    <div class="d-flex justify-content-between">
        <span id="wa-device_device">{{ $device->name }}</span> 
        <span id="wa-device_phone">{{ $device->phone }}</span>
    </div>
</div>

<script>
    $(document).ready(function() {
        const imgScan = $('#wa-device_img-scan')
        
        function getQR() {
            $.ajax({
                url: "{{ $url }}",
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    const message = data.message;
                    let status = data.qrcode == null ? message : 'Scan QRcode';
                    if (status == 'qrcode expired') {
                        $('#qrcode').attr('src', 'https://via.placeholder.com/270?text=QR+Code+Expired');
                    } else {
                        imgScan.attr('src', data.image)
                    }
                    $('#wa-device_status').text(status)
                    $('#wa-device_phone').text(data.phone)
                },
                error: function(err) {
                    console.error(err);
                },
                complete: async function(data) {
                    var time = 0;
                    if (data.statusText == "timeout") {
                        time = 1
                    } else {
                        var json = data.responseJSON
                        if (json.message == "Server offline") {
                            time = 10
                        }
                        // Belum distart
                        else if (json.message == "token tidak tersedia") {
                            // setStatusDevice('NOT AUTHENTICATED');
                            imgScan.attr('src', 'https://via.placeholder.com/270?text=Device+Berhenti');
                            return false;
                        } else if (json.scan) {
                            time = 3
                        } else {
                            // Jika Sudah Scan
                            time = 15
                        }
                    }
                    time = time * 1000;
                    console.log('next request in', time, 'ms');
                    await sleep(time);
                    return getQR()
                }
            })
        }
        
        getQR()
    });
</script>