let togglePasswordVisibility = function (inputId, toggleBtnId) {
    $(toggleBtnId).on('click', function (event) {
        event.preventDefault();
        var $pass = $(inputId).length > 0 ? $(inputId) : $('[name ="password"]');
        var $btnEyeIcon = $(toggleBtnId + ' i');
        if ($pass.attr("type") === "text") {
            $pass.attr('type', 'password');
            $btnEyeIcon.addClass("fa-eye-slash");
            $btnEyeIcon.removeClass("fa-eye");
        } else if ($pass.attr("type") === "password") {
            $pass.attr('type', 'text');
            $btnEyeIcon.removeClass("fa-eye-slash");
            $btnEyeIcon.addClass("fa-eye");
        }
    })
};

togglePasswordVisibility('password', 'passwordToggle');
togglePasswordVisibility('passwordConfirm', 'passwordConfirmToggle');
