<script>
    function csrf_token() {
        return "{{ csrf_token() }}";
    }

    const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));

    async function sendFormAjax(selector) {
        const form = $(selector);
        return new Promise((resolve, reject) => {
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                success: function(data) {
                    resolve(data);
                },
                error: function(err) {
                    reject(err);
                }
            });
        });
    }

    // Validation error laravel to input bootstrap 
    function renderErrorValidation(err) {
        const errors = err?.responseJSON?.errors ?? null
        if (errors) {
            for (name in errors) {
                const input = $(`input[name=${name}]`)
                const formGroup = input.parent()
                input.addClass('is-invalid')
                if (formGroup.children('small.invalid-feedback').get(0) == undefined) {
                    formGroup.append(`<small class='invalid-feedback'>${errors[name][0]}</small>`)
                } else {
                    formGroup.children('small.invalid-feedback').text(errors[name][0])
                }
            }
        }
    }
</script>