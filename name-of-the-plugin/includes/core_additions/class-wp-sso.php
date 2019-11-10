<?php if (!defined('ABSPATH')) {
    die();
}
// Exit if accessed directly

// Create SSO Shortcodes
$CURRENT_SSO_URL = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

if (isset($_GET['sso_auth_path'])) {
    add_shortcode('sso_login', 'NGRIFFIN_PLUGIN_sso_auth_form');
} else if (isset($_GET['sso_login_path'])) {
    add_shortcode('sso_login', 'NGRIFFIN_PLUGIN_sso_login_form');
} else if (strpos($CURRENT_SSO_URL, '/jobs/') !== false) {
    add_shortcode('sso_login', 'NGRIFFIN_PLUGIN_login_form_jobs');
} else {
    add_shortcode('sso_login', 'NGRIFFIN_PLUGIN_login_form');
}
add_shortcode('sso_signup', 'NGRIFFIN_PLUGIN_signup_form');

function NGRIFFIN_PLUGIN_login_form_jobs()
{
    global $uksjs;

    $registerCode = '
    <script>
    function combineNamesRegister() {
        var firstNameValue = jQuery(\'.combineNamesRegister_First\').val();
        var lastNameValue = jQuery(\'.combineNamesRegister_Last\').val();
        jQuery(\'.combineNamesRegister_Full\').val(firstNameValue + \' \' + lastNameValue);
    }
    </script>
    <form id="sb-signup-form" method="post" >
    <h1>Create Account</h1>
        <div class="ui grid">
            <div class="sixteen wide tablet eight wide computer column">
            <div class="form-group">
                <input onchange="combineNamesRegister()" class="combineNamesRegister_First form-control" placeholder="' . esc_html__('First Name', 'uksjs') . '" type="text" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your first name.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_first_name" >
            </div>
            </div>

            <div class="sixteen wide tablet eight wide computer column">
            <div class="form-group">
                <input onchange="combineNamesRegister()" class="combineNamesRegister_Last form-control" placeholder="' . esc_html__('Last Name', 'uksjs') . '" type="text" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your last name.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_last_name" >
            </div>
            </div>

            <div class="sixteen wide tablet sixteen wide computer column">
            <div class="form-group">
                <input placeholder="' . esc_html__('Your Full Name', 'uksjs') . '" class="combineNamesRegister_Full form-control" type="text" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your name.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_name" >
            </div>
            </div>

            <div class="sixteen wide tablet sixteen wide computer column">
            <div class="form-group">
                <input placeholder="' . esc_html__('Contact Number', 'uksjs') . '" class="form-control" type="number" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your contact number.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_contact" >
            </div>
            </div>

            <div class="sixteen wide tablet ten wide computer column">
            <div class="form-group">
                <input placeholder="' . esc_html__('Your Address', 'uksjs') . '" class="form-control" type="address" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter a valid address.', 'uksjs') . '" data-parsley-trigger="change" name="NGRIFFIN_PLUGIN_reg_address">
            </div>
            </div>

            <div class="sixteen wide tablet six wide computer column">
            <div class="form-group">
                <input placeholder="' . esc_html__('Postcode', 'uksjs') . '" class="form-control" type="postcode" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter a valid postcode.', 'uksjs') . '" data-parsley-trigger="change" name="NGRIFFIN_PLUGIN_reg_postcode">
            </div>
            </div>

            <div class="sixteen wide tablet sixteen wide computer column">
            <div class="form-group">
                <input placeholder="' . esc_html__('Your Email', 'uksjs') . '" class="form-control" type="email" data-parsley-type="email" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your valid email.', 'uksjs') . '" data-parsley-trigger="change" name="NGRIFFIN_PLUGIN_reg_email">
            </div>
            </div>

            <div class="sixteen wide tablet sixteen wide computer column">
            <div class="form-group">
                <input placeholder="' . esc_html__('Your Password', 'uksjs') . '" class="form-control" type="password" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your password.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_password">
            </div>
            </div>

            <input type="hidden" value="' . home_url('/dashboard/') . '" name="NGRIFFIN_PLUGIN_reg_redirect"/>

            <input type="hidden" value="0"/>

            <div class="sixteen wide tablet sixteen wide computer column">
            <div class="buttons-area">
                <div class="form-group">
                <input type="checkbox" name="icheck_box" class="input-icheck-others" data-parsley-required="true" data-parsley-error-message="' . __('Please accept terms and conditions.', 'uksjs') . '" >
                <p> ' . esc_html__('I agree to the full ', 'uksjs') . ' <a href="/terms-and-conditions" target="_blank">Terms of Use</a></p>
                </div>
                <button class="ui inverted primary button btn-mid pull-right " type="submit" id="NGRIFFIN_PLUGIN_register_submit">Register</button>
                <button class="ui inverted primary button btn-mid pull-right  no-display disabled" type="button" id="NGRIFFIN_PLUGIN_register_msg">' . esc_html__('Processing...', 'uksjs') . '</button>
                <button class="ui inverted primary button btn-mid pull-right  no-display disabled" type="button" id="NGRIFFIN_PLUGIN_register_redirect">' . esc_html__('Redirecting...', 'uksjs') . '</button>
            </div>
            </div>
            <input type="hidden" class="get_action" value="register"/>
            <input type="hidden" id="verify_account_msg" value="' . __('A verificaton email has been sent to your email.', 'uksjs') . '" />
            <input type="hidden" id="nonce" value="" />
        </div>
    </form>
    ';

    $code = time();
    $_SESSION['NGRIFFIN_PLUGIN_nonce'] = $code;

    ?>
    <div class="container register-page" id="signUpContainer">
        <div class="form-container sign-up-container">
            <h1>Sign in</h1>
            <?php
$argsLoginInSSO = array(
        'redirect' => home_url('/dashboard/'),
        'form_id' => 'login-with-wp',
        'label_username' => __('Username or email address'),
        'label_password' => __('Password'),
        'label_log_in' => __('Login'),
        'value_remember' => true,
    );
    wp_login_form($argsLoginInSSO);
    ?>
            <div class="button-links-area">
                <a href="/register" style="display:none" class="register-link">Create account</a>
                <a href="/forgot-password" class="forgot-password">Forgot your password?</a>
            </div>
        </div>
        <div class="form-container sign-in-container">
            <?php echo $registerCode; ?>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>Sign in or sign up today to manage your account and track applications.</p>
                    <button class="ui button inverted ghost" id="signUp">Sign Up</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Let's get started!</h1>
                    <p>Fill in the form to get started with a account today.</p>
                    <button class="ui button inverted ghost" id="signIn">Sign In</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('signUpContainer');

        signUpButton.addEventListener('click', function() { container.classList.remove('right-panel-active')});

        signInButton.addEventListener('click', function() { container.classList.add('right-panel-active')});
    </script>
    <?php
}

// login with form
function NGRIFFIN_PLUGIN_login_form()
{
    global $uksjs;
    $social_login = '';
    if (isset($uksjs['fb_api_key']) && $uksjs['fb_api_key'] != "") {
        $social_login .= '<div class="form-group"><a href="javascript:void(0)" class="btn-facebook btn-block btn-social"  onclick="hello(\'facebook\').login(' . "{scope : 'email',}" . ')"><i class="ti-facebook"></i><span>'
            . 'fb' .
            '</span></a></div> ';
    }
    if (isset($uksjs['gmail_api_key']) && $uksjs['gmail_api_key'] != "") {
        $social_login .= '<div class="form-group"><a href="javascript:void(0)" class="btn-google btn-block btn-social"  onclick="hello(\'google\').login(' . "{scope : 'email',}" . ')"><span>'
            . 'gg' .
            '</span></a></div>';
    }

    /* Linkedin key*/
    $linkedin_api_key = '';
    if ((isset($uksjs['linkedin_api_key'])) && $uksjs['linkedin_api_key'] != '' && (isset($uksjs['linkedin_api_secret'])) && $uksjs['linkedin_api_secret'] != '' && (isset($uksjs['redirect_uri'])) && $uksjs['redirect_uri'] != '') {
        $linkedin_api_key = ($uksjs['linkedin_api_key']);
        $linkedin_secret_key = ($uksjs['linkedin_api_secret']);
        $redirect_uri = ($uksjs['redirect_uri']);
        $linkedin_url = 'https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=' . $linkedin_api_key . '&redirect_uri=' . $redirect_uri . '&state=popup&scope=r_emailaddress r_basicprofile';
        $social_login .= '<div class="form-group"><a href="' . esc_url($linkedin_url) . '" class="btn-linkedIn btn-block"><i class="ti-linkedin"></i><span>'
            . 'li' .
            '</span></a></div>';
    }

    $registerCode = '
<script>
  function combineNamesRegister() {
    var firstNameValue = jQuery(\'.combineNamesRegister_First\').val();
    var lastNameValue = jQuery(\'.combineNamesRegister_Last\').val();
    jQuery(\'.combineNamesRegister_Full\').val(firstNameValue + \' \' + lastNameValue);
  }
</script>
<form id="sb-signup-form" method="post" >
<h1>Create Account</h1>
    <div class="ui grid">
        <div class="sixteen wide tablet eight wide computer column">
          <div class="form-group">
              <input onchange="combineNamesRegister()" class="combineNamesRegister_First form-control" placeholder="' . esc_html__('First Name', 'uksjs') . '" type="text" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your first name.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_first_name" >
          </div>
        </div>

        <div class="sixteen wide tablet eight wide computer column">
          <div class="form-group">
              <input onchange="combineNamesRegister()" class="combineNamesRegister_Last form-control" placeholder="' . esc_html__('Last Name', 'uksjs') . '" type="text" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your last name.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_last_name" >
          </div>
        </div>

        <div class="sixteen wide tablet sixteen wide computer column">
          <div class="form-group">
              <input placeholder="' . esc_html__('Your Full Name', 'uksjs') . '" class="combineNamesRegister_Full form-control" type="text" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your name.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_name" >
          </div>
        </div>

        <div class="sixteen wide tablet sixteen wide computer column">
          <div class="form-group">
              <input placeholder="' . esc_html__('Contact Number', 'uksjs') . '" class="form-control" type="number" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your contact number.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_contact" >
          </div>
        </div>

        <div class="sixteen wide tablet ten wide computer column">
          <div class="form-group">
            <input placeholder="' . esc_html__('Your Address', 'uksjs') . '" class="form-control" type="address" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter a valid address.', 'uksjs') . '" data-parsley-trigger="change" name="NGRIFFIN_PLUGIN_reg_address">
          </div>
        </div>

        <div class="sixteen wide tablet six wide computer column">
          <div class="form-group">
            <input placeholder="' . esc_html__('Postcode', 'uksjs') . '" class="form-control" type="postcode" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter a valid postcode.', 'uksjs') . '" data-parsley-trigger="change" name="NGRIFFIN_PLUGIN_reg_postcode">
          </div>
        </div>

        <div class="sixteen wide tablet sixteen wide computer column">
          <div class="form-group">
            <input placeholder="' . esc_html__('Your Email', 'uksjs') . '" class="form-control" type="email" data-parsley-type="email" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your valid email.', 'uksjs') . '" data-parsley-trigger="change" name="NGRIFFIN_PLUGIN_reg_email">
          </div>
        </div>

        <div class="sixteen wide tablet sixteen wide computer column">
          <div class="form-group">
            <input placeholder="' . esc_html__('Your Password', 'uksjs') . '" class="form-control" type="password" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your password.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_password">
          </div>
        </div>

        <input type="hidden" value="' . home_url('/dashboard/') . '" name="NGRIFFIN_PLUGIN_reg_redirect"/>

        <input type="hidden" value="0"/>

        <div class="sixteen wide tablet sixteen wide computer column">
          <div class="buttons-area">
            <div class="form-group">
              <input type="checkbox" name="icheck_box" class="input-icheck-others" data-parsley-required="true" data-parsley-error-message="' . __('Please accept terms and conditions.', 'uksjs') . '" >
              <p> ' . esc_html__('I agree to the full ', 'uksjs') . ' <a href="/terms-and-conditions" target="_blank">Terms of Use</a></p>
            </div>
            <button class="ui inverted primary button btn-mid pull-right " type="submit" id="NGRIFFIN_PLUGIN_register_submit">Register</button>
            <button class="ui inverted primary button btn-mid pull-right  no-display disabled" type="button" id="NGRIFFIN_PLUGIN_register_msg">' . esc_html__('Processing...', 'uksjs') . '</button>
            <button class="ui inverted primary button btn-mid pull-right  no-display disabled" type="button" id="NGRIFFIN_PLUGIN_register_redirect">' . esc_html__('Redirecting...', 'uksjs') . '</button>
          </div>
        </div>
        <input type="hidden" class="get_action" value="register"/>
        <input type="hidden" id="verify_account_msg" value="' . __('A verificaton email has been sent to your email.', 'uksjs') . '" />
        <input type="hidden" id="nonce" value="" />
    </div>
</form>
';

    $code = time();
    $_SESSION['NGRIFFIN_PLUGIN_nonce'] = $code;

    if (isset($_GET['oauth_consumer_key'])) {
        global $wpdb;
        $app_data = $wpdb->get_row($wpdb->prepare("SELECT count(*) as count	FROM {$wpdb->prefix}oauth_clients WHERE consumer_key = %s", $_GET['oauth_consumer_key']), ARRAY_A);

        if ($app_data['count'] == 1) {
            if (isset($_GET['login']) && $_GET['login'] == 'failed') {
                return 'Sorry, it appears that either your username or password is incorrect.';
            }

            if (!is_user_logged_in()) {
                ?>
                <div class="container" id="signUpContainer">
                    <div class="form-container sign-up-container">
                    </div>
                    <div class="form-container sign-in-container">
                        <h1>Sign in</h1>
                        <?php
$argsOauthLogin = array(
                    'redirect' => home_url('/login/?sso_auth_path&oauth_consumer_key=' . $_GET['oauth_consumer_key']),
                    'form_id' => 'login-with-wp',
                    'label_username' => __('Username or email address'),
                    'label_password' => __('Password'),
                    'label_log_in' => __('Login'),
                    'value_remember' => true,
                );
                wp_login_form($argsOauthLogin);
                ?>
                        <a href="/register" style="display:none" class="register-link">Create account</a>
                        <a href="/forgot-password" class="forgot-password">Forgot your password?</a>
                    </div>
                    <div class="overlay-container">
                        <div class="overlay">
                            <div class="overlay-panel overlay-left">
                                <h1>Login to authenticate this connection</h1>
                            </div>
                            <div class="overlay-panel overlay-right">

                            </div>
                        </div>
                    </div>
                </div>
            <?php
} else {
                wp_redirect('/login/?sso_auth_path&oauth_consumer_key=' . $_GET['oauth_consumer_key']);
                exit;
            }
        } else {
            wp_redirect(site_url());
            exit;
        }
    } else {
        if (!is_user_logged_in()) {
            ?>
            <div class="container" id="signUpContainer">
                <div class="form-container sign-up-container">
                    <?php echo $registerCode; ?>
                </div>
                <div class="form-container sign-in-container">
                    <h1>Sign in</h1>
                    <?php
$argsLoginInNormal = array(
                'redirect' => home_url('/dashboard/'),
                'form_id' => 'login-with-wp',
                'label_username' => __('Username or email address'),
                'label_password' => __('Password'),
                'label_log_in' => __('Login'),
                'value_remember' => true,
            );
            wp_login_form($argsLoginInNormal);
            ?>
                    <a href="/register" style="display:none" class="register-link">Create account</a>
                    <a href="/forgot-password" class="forgot-password">Forgot your password?</a>
                </div>
                <div class="overlay-container">
                    <div class="overlay">
                        <div class="overlay-panel overlay-left">
                            <h1>Let's get started!</h1>
                            <p>Fill in the form to get started with a account today.</p>
                            <button class="ui button inverted ghost" id="signIn">Sign In</button>
                        </div>
                        <div class="overlay-panel overlay-right">
                            <h1>Welcome Back!</h1>
                            <p>Sign in or sign up today to manage your account and track applications.</p>
                            <button class="ui button inverted ghost" id="signUp">Sign Up</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                const signUpButton = document.getElementById('signUp');
                const signInButton = document.getElementById('signIn');
                const container = document.getElementById('signUpContainer');

                signUpButton.addEventListener('click', function() { container.classList.add('right-panel-active')});

                signInButton.addEventListener('click', function() { container.classList.remove('right-panel-active')});
            </script>
        <?php
} else {
            wp_redirect('/dashboard');
            exit;
        }
    }
}

// deny or allow sso auth form
function NGRIFFIN_PLUGIN_sso_auth_form()
{
    if (isset($_GET['oauth_consumer_key'])) {

        if (!is_user_logged_in()) {
            ?>
            <div class="container" id="signUpContainer">
                <div class="form-container sign-up-container">

                </div>
                <div class="form-container sign-in-container">
                    <h1>Sign in</h1>
                    <?php
$argsLoginInSSO = array(
                'redirect' => home_url('/login/?sso_auth_path&oauth_consumer_key=' . $_GET['oauth_consumer_key']),
                'form_id' => 'login-with-wp',
                'label_username' => __('Username or email address'),
                'label_password' => __('Password'),
                'label_log_in' => __('Login'),
                'value_remember' => true,
            );
            wp_login_form($argsLoginInSSO);
            ?>
                    <a href="/register" style="display:none" class="register-link">Create account</a>
                    <a href="/forgot-password" class="forgot-password">Forgot your password?</a>
                </div>
                <div class="overlay-container">
                    <div class="overlay">
                        <div class="overlay-panel overlay-left">
                            <h1>Login to authenticate this connection</h1>
                        </div>
                        <div class="overlay-panel overlay-right">

                        </div>
                    </div>
                </div>
            </div>
        <?php
} else {
            global $wpdb;
            $table_name = $wpdb->prefix . 'oauth_clients';
            $table_name1 = $wpdb->prefix . 'oauth_codes';
            $client_id = $_GET['oauth_consumer_key'];
            $application_name = $wpdb->get_results("Select name from $table_name where consumer_key = $client_id ", ARRAY_A);
            // $application_image = $wpdb->get_results( "Select icon from $table_name where consumer_key = $client_id ", ARRAY_A );
            $redirect_uri = $wpdb->get_results("Select redirect_uri, deny_uri from $table_name where consumer_key = $client_id ", ARRAY_A);
            $user = wp_get_current_user();

            if ($user->roles && in_array('employer', $user->roles)) {
                if (isset($_POST['allow_auth'])) {
                    $auth_code = NGRIFFIN_PLUGIN_TokenGenerator::NGRIFFIN_PLUGIN_generateToken();
                    $check_val = $wpdb->insert($table_name1, array(
                        'auth_code' => $auth_code,
                        'oauth_consumer_key' => $client_id,
                        'redirect_uri' => $redirect_uri[0]['redirect_uri'],
                        'user_id' => get_current_user_id(),
                        'expire' => strtotime("+1 minutes"),
                    ));

                    if ($check_val) {
                        wp_redirect($redirect_uri[0]['redirect_uri'] . '&auth_consumer_key=' . $_GET['oauth_consumer_key'] . '&auth_code=' . $auth_code);
                        exit;
                    }
                }

                if (isset($_POST['deny_auth'])) {
                    wp_redirect($redirect_uri[0]['deny_uri']);
                    exit;
                } else {

                    ?>
                    <style>
                        .authorize-wrapper-header h1 {
                            font-size: 2.5rem;
                            margin-bottom: 0;
                        }

                        .authorize-wrapper-header h2 {
                            font-weight: 500;
                            font-size: 1.5rem;
                            color: #222;
                        }

                        .authorize-wrapper-header {
                            border-bottom: 1px solid;
                        }

                        .authorize-wrapper-body strong {
                            font-size: 1.3rem;
                        }

                        .authorize-wrapper-permissions {
                            border: 1px solid #ddd;
                            margin-top: 10px;
                            margin-bottom: 10px;
                            border-radius: 4px;
                        }

                        .authorize-wrapper-permissions-item {
                            padding: 1rem;
                            border-bottom: 1px solid #ddd;
                        }

                        .authorize-wrapper-permissions-item:last-child {
                            border-bottom: 0;
                        }

                        .authorize-wrapper-permissions-item .icon-wrap {
                            width: 60px;
                            display: inline-block;
                            float: left;
                        }

                        .authorize-wrapper-permissions-item .icon-wrap .icon {
                            font-size: 30px;
                            color: #222;
                        }

                        .authorize-wrapper-permissions-item .text-wrap {
                            width: calc(100% - 60px);
                            display: inline-block;
                            height: auto;
                        }

                        .authorize-wrapper-permissions-item .text-wrap .title {
                            font-weight: bold;
                        }

                        .authorize-wrapper-body p {
                            font-size: 14px;
                        }

                        .authorize-wrapper-body p label {
                            width: 50%;
                            display: inline-block;
                            float: left;
                        }

                        .authorize-wrapper-body p label .button {
                            border-radius: 0;
                        }

                        .authorize-wrapper-body p label .button {
                            border-radius: 0;
                        }

                        .authorize-wrapper-body {
                            margin-top: 1rem;
                        }

                        .authorize-wrapper-form {
                            width: 100%;
                            margin-top: 2rem;
                        }

                        .redirect_notification {
                            display: inline-block;
                            width: 100%;
                            margin-top: 1rem;
                        }

                        .redirect_notification .link_redirect {
                            text-decoration: underline;
                        }
                    </style>
                    <div class="authorize-wrapper">
                        <div class="ui grid">
                            <div class="authorize-wrapper-header sixteen wide column">
                                <h1>
                                    Authorise application
                                </h1>
                                <h2>
                                    <?php
if ($application_name[0]['name']) {
                        echo $application_name[0]['name'];
                    } else {
                        echo 'An app';
                    }?> would like permission to access your account
                                </h2>
                            </div>
                            <div class="authorize-wrapper-body sixteen wide column">
                                <strong>
                                    Review permissions
                                </strong>
                                <div class="authorize-wrapper-permissions">
                                    <div class="authorize-wrapper-permissions-item">
                                        <div class="icon-wrap">
                                            <i class="user secret icon"></i>
                                        </div>
                                        <div class="text-wrap">
                                            <span class="title">
                                                Personal user data
                                            </span>
                                            <div class="subtext-wrap">
                                                Email address (read-only), account nickname, user ID
                                            </div>
                                        </div>
                                    </div>
                                    <div class="authorize-wrapper-permissions-item">
                                        <div class="icon-wrap">
                                            <i class="users icon"></i>
                                        </div>
                                        <div class="text-wrap">
                                            <span class="title">
                                                Organisation information
                                            </span>
                                            <div class="subtext-wrap">
                                                Read only access
                                            </div>
                                        </div>
                                    </div>
                                    <div class="authorize-wrapper-permissions-item">
                                        <div class="icon-wrap">
                                            <i class="lock icon"></i>
                                        </div>
                                        <div class="text-wrap">
                                            <span class="title">
                                                Login capabilities
                                            </span>
                                            <div class="subtext-wrap">
                                                Single Sign On will be enabled, allowing the app to effectively log in for you, which does mean that this app could gain access to other account information, job listings and application details.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p>
                                    By authorising this application, you confirm that you are willing to grant these permissions to the application. Should you wish to revert this later, you may do so from your profile page.
                                </p>
                                <div class="authorize-wrapper-form">
                                    <form method="post">
                                        <p>
                                            <label><button class="ui button primary fluid" type="submit" name="allow_auth" value="Allow">
                                                    Authorise Application
                                                </button>
                                            </label>
                                            <label><button class="ui button secondary fluid" type="submit" name="deny_auth" value="Deny">
                                                    Deny
                                                </button></label></p>
                                    </form>
                                    <div class="redirect_notification">
                                        <?php if ($redirect_uri[0]['redirect_uri']) {
                        echo 'You will be redirected to <span class="link_redirect">' . $redirect_uri[0]['redirect_uri'] . '</span> after authorising this connection.';
                    } else {
                        echo 'No redirect URL was set for this application!';
                    }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
}
            } else {
                return '<h1>Sorry, we are only providing SSO functionality to employers at this time.</h1>';
            }
        }
    } else {
        return '<h1>Please visit this page with an oAuth Key to register.</h1>';
    }
}

// Login a user over SSO
function NGRIFFIN_PLUGIN_sso_login_form()
{
    if (isset($_GET['auth_code'])) {
        if (isset($_GET['auth_consumer_key'])) {
            $authCode = $_GET['auth_code'];
            $consumerKey = $_GET['auth_consumer_key'];

            $url = home_url() . '/wp-json/uksjs-sso/v1/oauth/token';
            $data = array(
                'auth_code' => $authCode,
                'auth_consumer_key' => $consumerKey,
            );

            $loginKey = base64_encode("apitestuser:x8B3 N1AO 4PGi cQd2 yd4T 4Sf8");
            $header = array();
            $header[] = 'Content-type: application/x-www-form-urlencoded';
            $header[] = 'Authorization: Basic ' . $loginKey;

            // use key 'http' even if you send the request to https://...
            $options = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
                'http' => array(
                    'header' => $header,
                    'method' => 'POST',
                    'content' => http_build_query($data),
                ),
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            if ($result === false) {
                return 'The API failed';
            }

            $data = json_decode($result);

            if (isset($data[0]->token) && $consumerKey) {
                setcookie('JWT_key', $data[0]->token . ':' . $secretKey, time() + 604800000, "/", "nicholasgriffin.dev", true, true);
                $_COOKIE['JWT_key'] = $data[0]->token . ':' . $secretKey;
                wp_redirect('/ssologin');
                ?>
                <h1>We have completed your request, redirecting you to the sso login page....</h1>
            <?php
            } else {
                return 'No token!';
            }
        } else {
            return 'Please provide an authentication key';
        }
    } else {
        return 'Please provide an authentication code';
    }
}

function NGRIFFIN_PLUGIN_signup_form()
{
    global $uksjs;

    $registerCode = '
    <script>
    function combineNamesRegister() {
        var firstNameValue = jQuery(\'.combineNamesRegister_First\').val();
        var lastNameValue = jQuery(\'.combineNamesRegister_Last\').val();
        jQuery(\'.combineNamesRegister_Full\').val(firstNameValue + \' \' + lastNameValue);
    }
    </script>
    <form id="sb-signup-form" method="post" >
    <h1>Create Account</h1>
        <div class="ui grid">
            <div class="sixteen wide tablet eight wide computer column">
            <div class="form-group">
                <input onchange="combineNamesRegister()" class="combineNamesRegister_First form-control" placeholder="' . esc_html__('First Name', 'uksjs') . '" type="text" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your first name.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_first_name" >
            </div>
            </div>

            <div class="sixteen wide tablet eight wide computer column">
            <div class="form-group">
                <input onchange="combineNamesRegister()" class="combineNamesRegister_Last form-control" placeholder="' . esc_html__('Last Name', 'uksjs') . '" type="text" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your last name.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_last_name" >
            </div>
            </div>

            <div class="sixteen wide tablet sixteen wide computer column">
            <div class="form-group">
                <input placeholder="' . esc_html__('Your Full Name', 'uksjs') . '" class="combineNamesRegister_Full form-control" type="text" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your name.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_name" >
            </div>
            </div>

            <div class="sixteen wide tablet sixteen wide computer column">
            <div class="form-group">
                <input placeholder="' . esc_html__('Contact Number', 'uksjs') . '" class="form-control" type="number" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your contact number.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_contact" >
            </div>
            </div>

            <div class="sixteen wide tablet ten wide computer column">
            <div class="form-group">
                <input placeholder="' . esc_html__('Your Address', 'uksjs') . '" class="form-control" type="address" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter a valid address.', 'uksjs') . '" data-parsley-trigger="change" name="NGRIFFIN_PLUGIN_reg_address">
            </div>
            </div>

            <div class="sixteen wide tablet six wide computer column">
            <div class="form-group">
                <input placeholder="' . esc_html__('Postcode', 'uksjs') . '" class="form-control" type="postcode" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter a valid postcode.', 'uksjs') . '" data-parsley-trigger="change" name="NGRIFFIN_PLUGIN_reg_postcode">
            </div>
            </div>

            <div class="sixteen wide tablet sixteen wide computer column">
            <div class="form-group">
                <input placeholder="' . esc_html__('Your Email', 'uksjs') . '" class="form-control" type="email" data-parsley-type="email" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your valid email.', 'uksjs') . '" data-parsley-trigger="change" name="NGRIFFIN_PLUGIN_reg_email">
            </div>
            </div>

            <div class="sixteen wide tablet sixteen wide computer column">
            <div class="form-group">
                <input placeholder="' . esc_html__('Your Password', 'uksjs') . '" class="form-control" type="password" data-parsley-required="true" data-parsley-error-message="' . esc_html__('Please enter your password.', 'uksjs') . '" name="NGRIFFIN_PLUGIN_reg_password">
            </div>
            </div>

            <input type="hidden" value="' . home_url('/dashboard/') . '" name="NGRIFFIN_PLUGIN_reg_redirect"/>

            <input type="hidden" value="0"/>

            <div class="sixteen wide tablet sixteen wide computer column">
            <div class="buttons-area">
                <div class="form-group">
                <input type="checkbox" name="icheck_box" class="input-icheck-others" data-parsley-required="true" data-parsley-error-message="' . __('Please accept terms and conditions.', 'uksjs') . '" >
                <p> ' . esc_html__('I agree to the full ', 'uksjs') . ' <a href="/terms-and-conditions" target="_blank">Terms of Use</a></p>
                </div>
                <button class="ui inverted primary button btn-mid pull-right " type="submit" id="NGRIFFIN_PLUGIN_register_submit">Register</button>
                <button class="ui inverted primary button btn-mid pull-right  no-display disabled" type="button" id="NGRIFFIN_PLUGIN_register_msg">' . esc_html__('Processing...', 'uksjs') . '</button>
                <button class="ui inverted primary button btn-mid pull-right  no-display disabled" type="button" id="NGRIFFIN_PLUGIN_register_redirect">' . esc_html__('Redirecting...', 'uksjs') . '</button>
            </div>
            </div>
            <input type="hidden" class="get_action" value="register"/>
            <input type="hidden" id="verify_account_msg" value="' . __('A verificaton email has been sent to your email.', 'uksjs') . '" />
            <input type="hidden" id="nonce" value="" />
        </div>
    </form>
    ';

    $code = time();
    $_SESSION['NGRIFFIN_PLUGIN_nonce'] = $code;

    ?>
    <div class="container register-page" id="signUpContainer">
        <div class="form-container sign-up-container">
            <h1>Sign in</h1>
            <?php
$argsLoginInSSO = array(
        'redirect' => home_url('/dashboard/'),
        'form_id' => 'login-with-wp',
        'label_username' => __('Username or email address'),
        'label_password' => __('Password'),
        'label_log_in' => __('Login'),
        'value_remember' => true,
    );
    wp_login_form($argsLoginInSSO);
    ?>
            <div class="button-links-area">
                <a href="/register" style="display:none" class="register-link">Create account</a>
                <a href="/forgot-password" class="forgot-password">Forgot your password?</a>
            </div>
        </div>
        <div class="form-container sign-in-container">
            <?php echo $registerCode; ?>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>Sign in or sign up today to manage your account and track applications.</p>
                    <button class="ui button inverted ghost" id="signUp">Sign Up</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Let's get started!</h1>
                    <p>Fill in the form to get started with a account today.</p>
                    <button class="ui button inverted ghost" id="signIn">Sign In</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('signUpContainer');

        signUpButton.addEventListener('click', function() { container.classList.remove('right-panel-active')});

        signInButton.addEventListener('click', function() { container.classList.add('right-panel-active')});
    </script>
<?php

}
