<?php
if (!defined('ABSPATH')) {
    exit;
}

/* Hide admin bar*/
add_action( 'after_setup_theme', 'NGRIFFIN_PLUGIN_HIDEADMINBAR' );
if ( ! function_exists( 'NGRIFFIN_PLUGIN_HIDEADMINBAR' ) )
{
	function NGRIFFIN_PLUGIN_HIDEADMINBAR()
	{
		if ( is_user_logged_in() && !is_admin() && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) )
		{
            show_admin_bar(false);	
		}
	}
}

/* Add translation code */
global $base_lang;

$base_lang = "en"; // Set the default language

if (isset($_COOKIE["lang"])) {
    $base_lang = $_COOKIE["lang"]; // Get language from cookie
}

if (isset($_GET["lang"])) {
    setcookie("lang", strip_tags($_GET["lang"]), strtotime('+30 days'), '/', null, 0);
    $base_lang = strip_tags($_GET["lang"]); // Or set cookie and new language
}

if (!function_exists('NGRIFFIN_PLUGIN_TRANSLATION')) {
    function NGRIFFIN_PLUGIN_TRANSLATION($text)
    {
        global $base_lang;
        if (isset($base_lang) && $base_lang != "en" && file_exists(NGRIFFIN_PLUGIN_PLUGIN_PATH . "/languages/$base_lang.php") && strlen($base_lang) <= 3) {
            include NGRIFFIN_PLUGIN_PLUGIN_PATH . "/languages/$base_lang.php";
            if (isset($lang[$text]) && !empty($lang[$text])) {
                return $lang[$text];
            }
        }
        return $text;
    }
}

/**
 * encypt or decrypt a string
*/
if (!function_exists('NGRIFFIN_PLUGIN_ENCRYPT_DECRYPT_KEY')) {
    function NGRIFFIN_PLUGIN_ENCRYPT_DECRYPT_KEY($action, $string) {
        $output         = false;
        $encrypt_method = "AES-256-CBC";
        $salt           = '141205A4B00E21E1';
        $secret_key     = '07DC6861135AFCD7E4D1A1AE5270B818FEA4DE3DD108B95F';
        $secret_iv      = '508E3359894F9B4D16A9660F09D2B3F8';
        // hash
        $key = hash( 'sha256', $secret_key );

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt( $string, $encrypt_method, $key, 0, $iv );
            $output = base64_encode( $output );
        } else if ( $action == 'decrypt' ) {
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }

        return $output;
    }
}

/* check if json is valid */
if (!function_exists('NGRIFFIN_PLUGIN_VALIDATE_JSON')) {
    function NGRIFFIN_PLUGIN_VALIDATE_JSON($json)
    {
        return is_array(json_decode($json, true)) ? true : false;
    }
}

/* Generate a random string */
if (!function_exists('NGRIFFIN_PLUGIN_RANDOM_STRING_GEN')) {
    function NGRIFFIN_PLUGIN_RANDOM_STRING_GEN($length = 50)
    {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count((array) $characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }
}


if (!function_exists('NGRIFFIN_PLUGIN_REQUIRED_ATTRS')) {
    function NGRIFFIN_PLUGIN_REQUIRED_ATTRS()
    {
        return $default_attribs = array(
            'id' => array(),
            'src' => array(),
            'href' => array(),
            'target' => array(),
            'class' => array(),
            'title' => array(),
            'type' => array(),
            'style' => array(),
            'data' => array(),
            'role' => array(),
            'aria-haspopup' => array(),
            'aria-expanded' => array(),
            'data-toggle' => array(),
            'data-hover' => array(),
            'data-animations' => array(),
            'data-mce-id' => array(),
            'data-mce-style' => array(),
            'data-mce-bogus' => array(),
            'data-href' => array(),
            'data-tabs' => array(),
            'data-small-header' => array(),
            'data-adapt-container-width' => array(),
            'data-height' => array(),
            'data-hide-cover' => array(),
            'data-show-facepile' => array(),
        );
    }
}

if (!function_exists('NGRIFFIN_PLUGIN_REQUIRED_TAGS')) {
    function NGRIFFIN_PLUGIN_REQUIRED_TAGS()
    {
        return $allowed_tags = array(
            'div' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'span' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'p' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'a' => array_merge(NGRIFFIN_PLUGIN_REQUIRED_ATTRS(), array(
                'href' => array(),
                'target' => array('_blank', '_top'),
            )),
            'u' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'br' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'i' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'q' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'b' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'ul' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'ol' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'li' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'br' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'hr' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'strong' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'blockquote' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'del' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'strike' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'em' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'code' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'style' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'script' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
            'img' => NGRIFFIN_PLUGIN_REQUIRED_ATTRS(),
        );
    }
}

/*  Function for making a link */
if (!function_exists('NGRIFFIN_PLUGIN_GENERATE_LINK')) {
    function NGRIFFIN_PLUGIN_GENERATE_LINK($url, $text)
    {
        return wp_kses("<a href='" . esc_url($url) . "' target='_blank'>", NGRIFFIN_PLUGIN_REQUIRED_TAGS()) . $text . wp_kses('</a>', NGRIFFIN_PLUGIN_REQUIRED_TAGS());
    }
}

/******************************************/
/*    Getting User Meta Of a use     */
/******************************************/
if (!function_exists('NGRIFFIN_PLUGIN_GET_USER_META')) {
    function NGRIFFIN_PLUGIN_GET_USER_META($meta_id = '', $meta_key = '')
    {
        $user_meta_value = '';
        if (get_user_meta($meta_id, $meta_key, true) != '') {
            $user_meta_value = get_user_meta($meta_id, $meta_key, true);
        }
        return $user_meta_value;
    }
}

/******************************************/
/*    WP Query  pagination   */
/******************************************/
if (!function_exists('NGRIFFIN_PLUGIN_PAGE_PAGINATION')) {
    function NGRIFFIN_PLUGIN_PAGE_PAGINATION($max_num_pages)
    {
        $big = 999999999; // need an unlikely integer
        $pages = paginate_links(
            array(
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $max_num_pages->max_num_pages,
                'type' => 'array',
                'prev_next' => true,
                'prev_text' => __('<< Prev', 'VFPLUGIN'),
                'next_text' => __('Next >>', 'VFPLUGIN'),
            )
        );

        if (is_array($pages)) {
            $paged = (get_query_var('paged') == 0) ? 1 : get_query_var('paged');

            $pagination = '<ul class="pagination">';
            foreach ($pages as $page) {
                $pagination .= "<li>$page</li>";
            }
            $pagination .= '</ul>';
            return $pagination;
        }
    }
}

/* ========================= */
/*  User loged In   */
/* ========================= */

if (!function_exists('NGRIFFIN_PLUGIN_CHECK_IF_LOGGED_IN')) {
    function NGRIFFIN_PLUGIN_CHECK_IF_LOGGED_IN()
    {
        if (get_current_user_id() == "") {
            echo NGRIFFIN_PLUGIN_JS_REDIRECT(home_url('/'));
            die();
        }
    }
}

if (!function_exists('NGRIFFIN_PLUGIN_JS_REDIRECT')) {
    function NGRIFFIN_PLUGIN_JS_REDIRECT($url = '')
    {
        return '<script>window.location = "' . $url . '";</script>';
    }
}

/* Function for redirecting a user with JS */
if (!function_exists('NGRIFFIN_PLUGIN_JS_JQUERY_REDIRECT')) {
    function NGRIFFIN_PLUGIN_JS_JQUERY_REDIRECT($url)
    {
        echo ("<script>jQuery(document).ready(function($) { window.location = '" . $url . "' });</script>");
    }
}

// remove url from excerpt
if (!function_exists('NGRIFFIN_PLUGIN_REMOVEURL')) {
    function NGRIFFIN_PLUGIN_REMOVEURL($string)
    {
        return preg_replace("/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i", '', $string);
    }
}

// get post description
if (!function_exists('NGRIFFIN_PLUGIN_WORDCOUNT')) {
    function NGRIFFIN_PLUGIN_WORDCOUNT($content = '', $limit = 180)
    {
        $string = '';
        $contents = strip_tags(strip_shortcodes($content));
        $contents = NGRIFFIN_PLUGIN_REMOVEURL($contents);
        $removeSpaces = str_replace(" ", "", $contents);
        $contents = preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $contents);
        if (strlen($removeSpaces) > $limit) {
            return mb_substr(str_replace("&nbsp;", "", $contents), 0, $limit) . '...';
        } else {
            return str_replace("&nbsp;", "", $contents);
        }
    }
}

// Convert data to an array
if (!function_exists('NGRIFFIN_PLUGIN_CONVERT_TO_ARRAY')) {
    function NGRIFFIN_PLUGIN_CONVERT_TO_ARRAY($data = array())
    {
        $count = 0;
        $arr = array();
        foreach ($data as $key => $val) {
            $key = str_replace("'", "", $key);
            $arr[$key] = $val;
        }
        $count = count($arr);
        return array("count" => $count, "arr" => $arr);
    }
}

/* ------------------------------------------------ */
/* function for closing tags */
/* ------------------------------------------------ */
if (!function_exists('NGRIFFIN_PLUGIN_CLOSE_TAGS')) {
    function NGRIFFIN_PLUGIN_CLOSE_TAGS($html)
    {
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1]; #put all closed tags into an array
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count((array) $openedtags);

        if (count((array) $closedtags) == $len_opened) {

            return $html;
        }
        $openedtags = array_reverse($openedtags);
        for ($i = 0; $i < $len_opened; $i++) {

            if (!in_array($openedtags[$i], $closedtags)) {

                $html .= '</' . $openedtags[$i] . '>';
            } else {

                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        return $html;
    }
}

// get feature image
if (!function_exists('NGRIFFIN_PLUGIN_RETURN_FEATURED_IMG_URL')) {
    function NGRIFFIN_PLUGIN_RETURN_FEATURED_IMG_URL($post_id, $image_size)
    {
        return wp_get_attachment_image_src(get_post_thumbnail_id(esc_html($post_id)), $image_size);
    }
}

// get current page url
if (!function_exists('NGRIFFIN_PLUGIN_RETURN_GET_CURRENT_URL_SERVER')) {
    function NGRIFFIN_PLUGIN_RETURN_GET_CURRENT_URL_SERVER()
    {
        return $actual_link = "http://$_SERVER[HTTP_HOST] $_SERVER[REQUEST_URI]";
    }
}


// check post format if exist
if (!function_exists('NGRIFFIN_PLUGIN_post_format_exist')) {
    function NGRIFFIN_PLUGIN_post_format_exist($format = '')
    {
        $formats = array('', 'image', 'audio', 'video', 'quote');
        if (in_array($format, $formats)) {
            return true;
        } else {
            return false;
        }
    }
}

// getting social icon array
if (!function_exists('NGRIFFIN_PLUGIN_RETURN_ICON_CLASS')) {
    function NGRIFFIN_PLUGIN_RETURN_ICON_CLASS($icon_name)
    {
        $icons_array = array(
            'Facebook' => 'vf-icon-loader icon-facebook',
            'Twitter' => 'vf-icon-loader icon-twitter',
            'Linkedin' => 'vf-icon-loader icon-linkedin2',
            'Email' => 'vf-icon-loader icon-envelope-o' 
        );
        return $icons_array[$icon_name];
    }
}

if (!function_exists('NGRIFFIN_PLUGIN_RETURN_COMMENTS_NUM')) {
    function NGRIFFIN_PLUGIN_RETURN_COMMENTS_NUM()
    {
        echo get_comments_number() . " " . esc_html__('comments', 'VFPLUGIN');
    }
}

// Bad word filter
if (!function_exists('NGRIFFIN_PLUGIN_FILTER_WORDS')) {
    function NGRIFFIN_PLUGIN_FILTER_WORDS($words = array(), $string, $replacement)
    {
        $badwordsArray = ["4r5e", "5h1t", "5hit", "a55", "anal", "anus", "ar5e", "arrse", "arse", "ass", "ass-fucker", "asses", "assfucker", "assfukka", "asshole", "assholes", "asswhole", "a_s_s", "b!tch", "b00bs", "b17ch", "b1tch", "ballbag", "balls", "ballsack", "bastard", "beastial", "beastiality", "bellend", "bestial", "bestiality", "bi+ch", "biatch", "bitch", "bitcher", "bitchers", "bitches", "bitchin", "bitching", "bloody", "blow job", "blowjob", "blowjobs", "boiolas", "bollock", "bollok", "boner", "boob", "boobs", "booobs", "boooobs", "booooobs", "booooooobs", "breasts", "buceta", "bugger", "bum", "bunny fucker", "butt", "butthole", "buttmuch", "buttplug", "c0ck", "c0cksucker", "carpet muncher", "cawk", "chink", "cipa", "cl1t", "clit", "clitoris", "clits", "cnut", "cock", "cock-sucker", "cockface", "cockhead", "cockmunch", "cockmuncher", "cocks", "cocksuck", "cocksucked", "cocksucker", "cocksucking", "cocksucks", "cocksuka", "cocksukka", "cok", "cokmuncher", "coksucka", "coon", "cox", "crap", "cum", "cummer", "cumming", "cums", "cumshot", "cunilingus", "cunillingus", "cunnilingus", "cunt", "cuntlick", "cuntlicker", "cuntlicking", "cunts", "cyalis", "cyberfuc", "cyberfuck", "cyberfucked", "cyberfucker", "cyberfuckers", "cyberfucking", "d1ck", "damn", "dick", "dickhead", "dildo", "dildos", "dink", "dinks", "dirsa", "dlck", "dog-fucker", "doggin", "dogging", "donkeyribber", "doosh", "duche", "dyke", "ejaculate", "ejaculated", "ejaculates", "ejaculating", "ejaculatings", "ejaculation", "ejakulate", "f u c k", "f u c k e r", "f4nny", "fag", "fagging", "faggitt", "faggot", "faggs", "fagot", "fagots", "fags", "fanny", "fannyflaps", "fannyfucker", "fanyy", "fatass", "fcuk", "fcuker", "fcuking", "feck", "fecker", "felching", "fellate", "fellatio", "fingerfuck", "fingerfucked", "fingerfucker", "fingerfuckers", "fingerfucking", "fingerfucks", "fistfuck", "fistfucked", "fistfucker", "fistfuckers", "fistfucking", "fistfuckings", "fistfucks", "flange", "fook", "fooker", "fuck", "fucka", "fucked", "fucker", "fuckers", "fuckhead", "fuckheads", "fuckin", "fucking", "fuckings", "fuckingshitmotherfucker", "fuckme", "fucks", "fuckwhit", "fuckwit", "fudge packer", "fudgepacker", "fuk", "fuker", "fukker", "fukkin", "fuks", "fukwhit", "fukwit", "fux", "fux0r", "f_u_c_k", "gangbang", "gangbanged", "gangbangs", "gaylord", "gaysex", "goatse", "God", "god-dam", "god-damned", "goddamn", "goddamned", "hardcoresex", "hell", "heshe", "hoar", "hoare", "hoer", "homo", "hore", "horniest", "horny", "hotsex", "jack-off", "jackoff", "jap", "jerk-off", "jism", "jiz", "jizm", "jizz", "kawk", "knob", "knobead", "knobed", "knobend", "knobhead", "knobjocky", "knobjokey", "kock", "kondum", "kondums", "kum", "kummer", "kumming", "kums", "kunilingus", "l3i+ch", "l3itch", "labia", "lust", "lusting", "m0f0", "m0fo", "m45terbate", "ma5terb8", "ma5terbate", "masochist", "master-bate", "masterb8", "masterbat*", "masterbat3", "masterbate", "masterbation", "masterbations", "masturbate", "mo-fo", "mof0", "mofo", "mothafuck", "mothafucka", "mothafuckas", "mothafuckaz", "mothafucked", "mothafucker", "mothafuckers", "mothafuckin", "mothafucking", "mothafuckings", "mothafucks", "mother fucker", "motherfuck", "motherfucked", "motherfucker", "motherfuckers", "motherfuckin", "motherfucking", "motherfuckings", "motherfuckka", "motherfucks", "muff", "mutha", "muthafecker", "muthafuckker", "muther", "mutherfucker", "n1gga", "n1gger", "nazi", "nigg3r", "nigg4h", "nigga", "niggah", "niggas", "niggaz", "nigger", "niggers", "nob", "nob jokey", "nobhead", "nobjocky", "nobjokey", "numbnuts", "nutsack", "orgasim", "orgasims", "orgasm", "orgasms", "p0rn", "pawn", "pecker", "penis", "penisfucker", "phonesex", "phuck", "phuk", "phuked", "phuking", "phukked", "phukking", "phuks", "phuq", "pigfucker", "pimpis", "piss", "pissed", "pisser", "pissers", "pisses", "pissflaps", "pissin", "pissing", "pissoff", "poop", "porn", "porno", "pornography", "pornos", "prick", "pricks", "pron", "pube", "pusse", "pussi", "pussies", "pussy", "pussys", "rectum", "retard", "rimjaw", "rimming", "s hit", "s.o.b.", "sadist", "schlong", "screwing", "scroat", "scrote", "scrotum", "semen", "sex", "sh!+", "sh!t", "sh1t", "shag", "shagger", "shaggin", "shagging", "shemale", "shi+", "shit", "shitdick", "shite", "shited", "shitey", "shitfuck", "shitfull", "shithead", "shiting", "shitings", "shits", "shitted", "shitter", "shitters", "shitting", "shittings", "shitty", "skank", "slut", "sluts", "smegma", "smut", "snatch", "son-of-a-bitch", "spac", "spunk", "s_h_i_t", "t1tt1e5", "t1tties", "teets", "teez", "testical", "testicle", "tit", "titfuck", "tits", "titt", "tittie5", "tittiefucker", "titties", "tittyfuck", "tittywank", "titwank", "tosser", "turd", "tw4t", "twat", "twathead", "twatty", "twunt", "twunter", "v14gra", "v1gra", "vagina", "viagra", "vulva", "w00se", "wang", "wank", "wanker", "wanky", "whoar", "whore", "willies", "willy", "xrated", "xxx"];

        foreach ($badwordsArray as $word) {
            $string = str_ireplace($word, $replacement, $string);
        }
        return $string;
    }
}

// Time Ago
if (!function_exists('NGRIFFIN_PLUGIN_TIMEAGO_DATE')) {
    function NGRIFFIN_PLUGIN_TIMEAGO_DATE($date)
    {
        $timestamp = strtotime($date);

        $strTime = array(esc_html__('second', 'VFPLUGIN'), esc_html__('minute', 'VFPLUGIN'), esc_html__('hour', 'VFPLUGIN'), esc_html__('day', 'VFPLUGIN'), esc_html__('month', 'VFPLUGIN'), esc_html__('year', 'VFPLUGIN'));
        $length = array("60", "60", "24", "30", "12", "10");

        $currentTime = time();
        if ($currentTime >= $timestamp) {
            $diff = time() - $timestamp;
            for ($i = 0; $diff >= $length[$i] && $i < count((array) $length) - 1; $i++) {
                $diff = $diff / $length[$i];
            }

            $diff = round($diff);
            return $diff . " " . $strTime[$i] . esc_html__('(s) ago', 'VFPLUGIN');
        }
    }
}

/* Hiding admin bar css */
add_action('get_header', 'NGRIFFIN_PLUGIN_REMOVE_LOGIN_HEADER');
function NGRIFFIN_PLUGIN_REMOVE_LOGIN_HEADER()
{
    remove_action('wp_head', '_admin_bar_bump_cb');
}
