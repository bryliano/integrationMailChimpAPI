$(document).ready(function () {


    const callback = (data, form) => {

        form = form[0];
        console.log(data.status);
        if (data.success) {
            alert(data.success);
        } else {

            if (data.status === 401) { // not valid

                form.email.style = 'border-bottom: 1px solid red';
                form.name.style = 'border-bottom: 1px solid red';

            } else if (data.status === 400) { // isset email in mailchimp



            } else if (data.status === 200) { // success

                let parent = form.parentElement;
                form.querySelector('.answer').insertAdjacentHTML('beforeend', data.message);
                // form.remove(); // remove form

            }
        }
    }

    const send = (form, path) => {

        $.ajax({
            type: "POST",
            url: path,
            dataType: "json",
            data: $(form).serialize(),
            success: data => callback(data, form)
        });
    }


    // Main
    let formSubscribe = document.getElementById('subscribe');

    formSubscribe.addEventListener('submit', function (e) {
        e.preventDefault();

        send($(this), '/wp-admin/admin-ajax.php');
    });

});