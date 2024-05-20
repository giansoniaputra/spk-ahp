function displayErrors(errors) {
    // menghapus class 'is-invalid' dan pesan error sebelumnya
    $("input.form-control").removeClass("is-invalid");
    $("select.form-control").removeClass("is-invalid");
    $("div.invalid-feedback").remove();

    // menampilkan pesan error baru
    $.each(errors, function (field, messages) {
        let inputElement = $("input[name=" + field + "]");
        let selectElement = $("select[name=" + field + "]");
        let textAreaElement = $("textarea[name=" + field + "]");
        let feedbackElement = $(
            '<div class="invalid-feedback ml-2"></div>'
        );

        $("#btn-close").on("click", function () {
            inputElement.each(function () {
                $(this).removeClass("is-invalid");
            });
            textAreaElement.each(function () {
                $(this).removeClass("is-invalid");
            });
            selectElement.each(function () {
                $(this).removeClass("is-invalid");
            });
        });

        $.each(messages, function (index, message) {
            feedbackElement.append(
                $('<p class="p-0 m-0 text-center">' + message + "</p>")
            );
        });

        if (inputElement.length > 0) {
            inputElement.addClass("is-invalid");
            inputElement.after(feedbackElement);
        }

        if (selectElement.length > 0) {
            selectElement.addClass("is-invalid");
            selectElement.after(feedbackElement);
        }
        if (textAreaElement.length > 0) {
            textAreaElement.addClass("is-invalid");
            textAreaElement.after(feedbackElement);
        }
        inputElement.each(function () {
            if (inputElement.attr("type") == "text" || inputElement.attr("type") == "number") {
                inputElement.on("click", function () {
                    $(this).removeClass("is-invalid");
                });
                inputElement.on("change", function () {
                    $(this).removeClass("is-invalid");
                });
            } else if (inputElement.attr("type") == "date") {
                inputElement.on("change", function () {
                    $(this).removeClass("is-invalid");
                });
            } else if (inputElement.attr("type") == "password") {
                inputElement.on("click", function () {
                    $(this).removeClass("is-invalid");
                });
            } else if (inputElement.attr("type") == "email") {
                inputElement.on("click", function () {
                    $(this).removeClass("is-invalid");
                });
            }
        });
        textAreaElement.each(function () {
            textAreaElement.on("click", function () {
                $(this).removeClass("is-invalid");
            });
        });
        selectElement.each(function () {
            selectElement.on("change", function () {
                $(this).removeClass("is-invalid");
            });
        });
    });
}