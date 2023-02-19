
let functionReg = function () {

    jQuery(function ($) {
        let form_data = new FormData($('#login_cv_form')[0]);// all data from form.
        form_data.append('action', window.obj.plugin_acronym);// add ajax action.
        form_data.append('security', jQuery('#_wpnonce').val()); //add nonce.
        form_data.append('files', jQuery('#uploaded_file')[0]);//add file! [0]

        $.ajax({
            url: window.obj.ajax_url,
            type: 'POST',
            contentType: false,
            processData: false,
            data: form_data,
            success: function (response) {
                let data = response.data;

                let can_redirect;
                let login_login = jQuery('#login_login');
                let login_mail = jQuery('#login_mail');
                let login_login_error_message = jQuery('#login-login-message');
                let login_mail_error_message = jQuery('#login-mail-message');


                if (typeof data.login_exists !== 'undefined') {
                    login_login_error_message.remove();
                    login_login.after('<p id="login-login-message">Sorry, login is already in use. Try another</p>');
                    login_login.css('border', '4px solid lime');
                    can_redirect = false;
                } else {
                    can_redirect = true;
                    login_login_error_message.remove();
                    login_login.css('border', '');
                }



                if (typeof data.mail_exists !== 'undefined') {
                    login_mail_error_message.remove();
                    login_mail.after('<p id="login-mail-message">Sorry, email is already in use. Try another</p>');
                    login_mail.css('border', '4px solid lime');
                    can_redirect = false;
                } else {
                    can_redirect = true;
                    login_mail_error_message.remove();
                    login_mail.css('border', '');
                }

               // alert(can_redirect)
                if (can_redirect == true) {
                    alert(data.html);
                    jQuery('#modal-body').replaceWith(data.html);
                    jQuery('#myModal1').modal('toggle');

                    setTimeout(" jQuery('#myModal1').modal('toggle'); " +
                        "window.location=window.obj.site_url",
                        5000);
                }
            }
        });
    });
}


let functionAjax = function () {

    let make_form_for_first_time = false;
    if (!jQuery('#login_cv_form').length) {
        make_form_for_first_time = true;//first form's addition
    }

    jQuery.ajax(
        {
            type: 'POST',
            url: window.obj.ajax_url,// url for WP ajax url (get on frontend! set in wp_localize_script like object).
            data: {
                action: obj.plugin_acronym,// must be equal to add_action( 'wp_ajax_filter_plugin', 'ajax_filter_posts_query' ).
                first_time: make_form_for_first_time,
                name: jQuery('#login').val(),
                password: jQuery('#password').val(),
                remember: jQuery('#remember').is(':checked'),
                security: jQuery('#_wpnonce').val(),
            },

            success: function (response) {
                jQuery('nav').after(response.data.html);// insert form after navigation.
            }
        }
    );
};

jQuery(
    function () {// for first time page open - form insertion.
        functionAjax();// make AJAX content update
    }
);


jQuery(function () {
    jQuery(document).on('keyup', '#login_password', function () {
        let login_password = jQuery('#login_password');
        let login_password_error_message = jQuery('#login-password-message');
        login_password_error_message.remove();
        login_password.css('border', '');
        //paswword validation (https://stackoverflow.com/questions/12090077/javascript-regular-expression-password-validation-having-special-characters)
        let regex = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{3,}$/;
        if (regex.test(login_password.val()) === false) {
            login_password.after('<p id="login-password-message">The password must contain only Latin letters, 1 number, and 1 character from the list ( # , % , & , * , ? )</p>');
            login_password.css('border', '4px solid orange');
            login_confirm.css('border', '4px solid orange');
        } else {
            login_password_error_message.remove();
            login_password.css('border', '');
            login_confirm.css('border', '');
        }
    });
});


jQuery(function () {
    jQuery(document).on('click', '#register_button', function () {

        let login_login = jQuery('#login_login');
        let login_name = jQuery('#login_name');
        let login_surname = jQuery('#login_surname');
        let login_mail = jQuery('#login_mail');
        let login_password = jQuery('#login_password');
        let login_confirm = jQuery('#login_confirm');


        let login_login_error_message = jQuery('#login-login-message');
        let login_name_error_message = jQuery('#login-name-message');
        let login_surname_error_message = jQuery('#login-surname-message');
        let login_mail_error_message = jQuery('#login-mail-message');
        let login_password_error_message = jQuery('#login-password-message');
        let login_confirm_error_message = jQuery('#login-confirm-message');
        let login_confirm_error_message_2 = jQuery('#login-confirm-message-2');

        let can_reg = true;

        if (login_login.val() !== '') {
            login_login_error_message.remove();
            login_login.css('border', '');
        } else {
            can_reg = false;
            login_login_error_message.remove();
            login_login.after('<p id="login-login-message">field must be filled</p>');
            login_login.css('border', '4px solid red');
        }

        if (login_name.val() !== '') {
            login_name_error_message.remove();
            login_name.css('border', '');
        } else {
            can_reg = false;
            login_name_error_message.remove();
            login_name.after('<p id="login-name-message">field must be filled</p>');
            login_name.css('border', '4px solid red');
        }

        if (login_surname.val() !== '') {
            login_surname_error_message.remove();
            login_surname.css('border', '');
        } else {
            can_reg = false;
            login_surname_error_message.remove();
            login_surname.after('<p id="login-surname-message">field must be filled</p>');
            login_surname.css('border', '4px solid red');
        }

        if (login_mail.val() !== '') {
            login_mail_error_message.remove();
            login_mail.css('border', '');

            //mail validation (https://stackoverflow.com/questions/2507030/how-can-one-use-jquery-to-validate-email-addresses)
            let regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;

            if (regex.test(login_mail.val()) === false) {
                can_reg = false;
                login_mail.after('<p id="login-mail-message">invalid mail format</p>');
                login_mail.css('border', '4px solid orange');
            }

        } else {
            can_reg = false;
            login_mail_error_message.remove();
            login_mail.after('<p id="login-mail-message">mail field must be filled</p>');
            login_mail.css('border', '4px solid red');
        }


        if (login_password.val() !== '') {
            login_password_error_message.remove();
            login_password.css('border', '');

            //paswword validation (https://stackoverflow.com/questions/12090077/javascript-regular-expression-password-validation-having-special-characters)
            let regex = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{3,}$/;

            if (regex.test(login_password.val()) === false) {
                can_reg = false;
                login_password.after('<p id="login-password-message">invalid password format</p>');
                login_password.css('border', '4px solid orange');
                login_confirm.css('border', '4px solid orange');
            } else {
                login_password_error_message.remove();
                login_password.css('border', '');
                login_confirm.css('border', '');
            }

        } else {
            can_reg = false;
            login_password_error_message.remove();
            login_password.after('<p id="login-password-message">field must be filled</p>');
            login_password.css('border', '4px solid red');
        }

        if (login_confirm.val() !== '') {
            login_confirm_error_message.remove();
            login_confirm.css('border', '');
        } else {
            can_reg = false;
            login_confirm_error_message.remove();
            login_confirm.after('<p id="login-confirm-message">field must be filled</p>');
            login_confirm.css('border', '4px solid red');
        }

        if (login_confirm.val() !== login_password.val()) {
            login_confirm_error_message_2.remove();
            can_reg = false;
            login_confirm.after('<p id="login-confirm-message-2">password and confirmation must be equivalent</p>');
            login_confirm.css('border', '4px solid lime');
            login_password.css('border', '4px solid lime');
        } else {
            login_confirm_error_message_2.remove();
            if (login_confirm.val() !== '') {

                login_confirm.css('border', '');
                login_password.css('border', '');
            }
        }

        if (can_reg != false) {
            functionReg();
        }
    });
});