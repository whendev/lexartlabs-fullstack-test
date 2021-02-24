// ajax form
$("form:not('.ajax_off')").submit(function (e) {
    e.preventDefault();
    var form = $(this);
    var flashClass = "ajax-response";
    var flash = $("." + flashClass);
    var dados = form.serialize();

    $.ajax({
        url: form.attr("action"),
        data: dados,
        type: "POST",
        dataType: "json",
        beforeSend: function () {
        },
        success: function (response) {
            //redirect
            if (response.redirect) {
                window.location.href = response.redirect;
            }

            //message
            if (response.message) {
                if (flash.length) {
                    flash.html(response.message).fadeIn(100);
                } else {
                    form.prepend("<div class='" + flashClass + "'>" + response.message + "</div>")
                        .find("." + flashClass);
                }
            } else {
                flash.fadeOut(100);
            }
        },
        complete: function () {
            if (form.data("reset") === true) {
                form.trigger("reset");
            }
        }
    });
})
