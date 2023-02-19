<?php
	/**
	 * Plugin Name: Registration Form by TechnoPreacher
	 * Description:reg form with shortcode and AJAX routine
	 * Version: 1.0
	 * Text Domain: tpregform_domain
	 * Domain Path: /lang/
	 * Author: TechnoPreacher
	 * License: GPLv2 or later
	 * Requires at least: 5.0
	 * Requires PHP: 7.4
	 */

	register_activation_hook( __FILE__, 'tpregform_activation_hook_action' );
	register_deactivation_hook( __FILE__, 'tpregform_deactivation_hook_action' );// remove plugin actions.

	add_action( 'wp', 'tpregform_wp_action' );
	add_action( 'plugins_loaded', 'tpregform_plugins_loaded_action' );
	add_action( 'wp_ajax_tpregform', 'tpregform_ajax_action' );// AJAX for registered users.
	add_action( 'wp_ajax_nopriv_tpregform', 'tpregform_ajax_action' );// AJAX for unregistered users.

	add_shortcode( 'tp_reg_form', 'tpregform_shortcode_action' );//shortcode [tp_reg_form]

	function tpregform_shortcode_action( $atts, $content ) {
		return $content;
	}

	function tpregform_wp_action() {
		if ( ! is_admin() ) {
			global $post;
			if ( has_shortcode( $post->post_content,
				'tp_reg_form' ) ) { //enque sqcripts only when find proper shortcode!
				add_action( 'wp_enqueue_scripts',
					function () {
						wp_enqueue_style( 'bootstrap',
							'https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css' ); // bootstrap.
						wp_enqueue_script( 'tpregform-js', plugins_url( 'tp-reg-form-scripts.js', __FILE__ ),
							// js script path.
							array( 'jquery', ), '1.0', true );
						$variables = array(
							'plugin_acronym' => 'tpregform',
							'ajax_url'       => admin_url( 'admin-ajax.php' ),// ajax solver.
							'site_url'       => home_url(),// for redirect
						);
						wp_register_script( 'modaljs', plugins_url( 'tp-reg-form/modals.js' ),
							array( 'jquery' ), '1',
							true );

						wp_register_style( 'modalcss', plugins_url( 'tp-reg-form/modals.css' ), '',
							'', 'all' );

						wp_enqueue_script( 'modaljs' );
						wp_enqueue_style( 'modalcss' );

						wp_localize_script( 'tpregform-js', 'obj',
							$variables );// AJAX-url to frontage into 'obj' object.
					}
				);
			}
		}
	}

	function tpregform_plugins_loaded_action() {
		load_plugin_textdomain( 'tpregform', false, 'tpregform_domain' );
	}

	function tpregform_deactivation_hook_action() {
		unload_textdomain( 'tpregform_domain' );
	}

	function tpregform_activation_hook_action() {
		// TODO some activation actions.
	}

	function tpregform_ajax_action() {

		$make_form = filter_var( $_POST['first_time'] ?? false, FILTER_VALIDATE_BOOLEAN );//make bool from string.

		if ( ! $make_form ) { //chek nonce only if form is present on page.
			check_ajax_referer( 'tpregform_nonce', 'security' );// return 403 if not pass nonce verification.
		}

		if ( $make_form ) {
			$nonce = wp_nonce_field( 'tpregform_nonce', '_wpnonce', true, false );

			$modal = '
			<!-- modal form-->
			<div id="myModal1" class="modal fade" tabindex="-1">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-body" id="modal-body"> </div>
					</div>
				</div>
			</div>';

			$form_html = "
			<form method='post' action='' id = 'login_cv_form'>
			<label for='login_login'>" . __( 'Login:', 'tpregform_domain' ) . "</label><br>
			<input type='text' id='login_login' name='login_login' required><br>
			<label for='login_name'>" . __( 'Name:', 'tpregform_domain' ) . "</label><br>
			<input type='text' id='login_name' name='login_name' required><br>
			<label for='login_surname'>" . __( 'Surname:', 'tpregform_domain' ) . "</label><br>
			<input type='text' id='login_surname' name='login_surname' required><br>
			<label for='login_mail'>" . __( 'E-mail:', 'tpregform_domain' ) . "</label><br>
			<input type='email' id='login_mail' name='login_mail' required><br>
			<label for='login_password'>" . __( 'Password:', 'tpregform_domain' ) . "</label><br>
			<input type='text' id='login_password' name='login_password' required><br>
			<label for='login_confirm'>" . __( 'Confirm:', 'tpregform_domain' ) . "</label><br>
			<input type='text' id='login_confirm' name='login_confirm' required><br>
			<button class='col mr-3' type='button' id='register_button'>" . __( 'Register', 'tpregform_domain' ) . "</button>
		</form>
			";

			$html = " 
 			<div id = \"form-cv-div\" style=\"width:100%;height:100%;border:6px solid aqua;\"> 
    		<p> $nonce.$form_html </p>
    		<p> $modal </p>
 			</div>
 			";
		} else { //proceed data from form.

			$validated         = false;
			$loginValidated    = false;//проверка логина
			$nameValidated     = false;//проверка имени
			$surnameValidated  = false;//проверка фамилии
			$emailValidated    = false;//проверка почты
			$PasswordValidated = false;//проверка пароля
			$ConfirmValidated  = false;//проверка подтверждения

			$html = '';

			// fields.
			$login_from_form    = sanitize_text_field( wp_unslash( $_POST['login_login'] ?? '' ) );
			$name_from_form     = sanitize_text_field( wp_unslash( $_POST['login_name'] ?? '' ) );
			$surname_from_form  = sanitize_text_field( wp_unslash( $_POST['login_surname'] ?? '' ) );
			$mail_from_form     = sanitize_text_field( wp_unslash( $_POST['login_mail'] ?? '' ) );
			$password_from_form = sanitize_text_field( wp_unslash( $_POST['login_password'] ?? '' ) );
			$confirm_from_form  = sanitize_text_field( wp_unslash( $_POST['login_confirm'] ?? '' ) );

			$loginValidated    = (bool) preg_match( "#^[A-Za-zа-яА-Я\-_]+$#", $login_from_form );
			$nameValidated     = (bool) preg_match( "#^[A-Za-zа-яА-Я\-_]+$#", $name_from_form );
			$surnameValidated  = (bool) preg_match( "#^[A-Za-zа-яА-Я\-_]+$#", $surname_from_form );
			$emailValidated    = (bool) filter_var( $mail_from_form, FILTER_VALIDATE_EMAIL );
			$PasswordValidated = true;//(bool) preg_match( "#^[A-Za-zа-яА-Я\-_]+$#", $password_from_form );
			$ConfirmValidated  = true;//(bool) preg_match( "#^[A-Za-zа-яА-Я\-_]+$#", $confirm_from_form );


			$validated = (bool) ( $loginValidated &
			                      $nameValidated &
			                      $surnameValidated &
			                      $emailValidated &
			                      $PasswordValidated &
			                      $ConfirmValidated );

			$data = array(
				'login_from_form'    => $login_from_form,
				'name_from_form'     => $name_from_form,
				'surname_from_form'  => $surname_from_form,
				'mail_from_form'     => $mail_from_form,
				'password_from_form' => $password_from_form,
				'confirm_from_form'  => $confirm_from_form
			);


			if ( $validated ) {


				$user_id = wp_insert_user( array(
					'user_login'   => $login_from_form,
					'user_pass'    => $password_from_form,
					'user_email'   => $mail_from_form,
					'first_name'   => $name_from_form,
					'last_name'    => $surname_from_form,
					'display_name' => $login_from_form,
					'role'         => 'editor'
				) );


				if ( is_wp_error( $user_id ) ) {
					$data = array(
						'html'         => "<div> <b> oops! some errors under user reigstered :( </b></div>",
						'mail_exists'  => email_exists( $mail_from_form ),
						'login_exists' => username_exists( $login_from_form ),
					);
					wp_reset_postdata();
					wp_send_json_error( $data );

					die();
				}

				add_action( 'phpmailer_init', function ( $phpmailer ) {
					$phpmailer->isSMTP();
					$phpmailer->Host       = 'smtp.gmail.com';
					$phpmailer->Port       = '587';
					$phpmailer->SMTPSecure = 'tls';
					$phpmailer->SMTPAuth   = true;
					$phpmailer->Username   = 'nik.nik.sulima@gmail.com';
					$phpmailer->Password   = 'qyozxcqdgkzqhmqd';
					$phpmailer->From       = 'nik.nik.sulima@gmail.com';// $data['mail_from_form'];
					$phpmailer->FromName   = 'admin';
				} );

				$to      = 'nik.nik.sulima@gmail.com';
				$subject = 'system';
				$message = " <div><b>NEW USER REIGISTRATED!!!</b><br> name: " . $data['name_from_form']
				           . "<br> surname: " . $data['surname_from_form']
				           . "<br> mail: " . $data['mail_from_form']
				           . "<br> login: " . $data['login_from_form'];

				$headers = array( 'Content-Type: text/html; charset=UTF-8' );

				global $tpregform_mail_result;

				try {
					$tpregform_mail_result =
						wp_mail( $to, $subject, $message, $headers );
				} catch ( Exception $e ) {
					$tpregform_mail_result = false;
				}
			}
			global $tpregform_mail_result;

			if ( $tpregform_mail_result == true ) {
				$html = "<div> <b> Thank you for registering on our website” </b></div>";
			} else {
				$html = "<div> <b> oops! some errors under user reigstered :( </b></div>";
			}
		}

		$data = array(
			'html' => $html,
		);

		wp_reset_postdata();
		wp_send_json_success( $data );


		die();
	}

