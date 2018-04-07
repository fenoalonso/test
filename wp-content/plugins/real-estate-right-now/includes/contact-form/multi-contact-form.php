<?php /**

 * @author William Sergio Minozzi

 * @copyright 2017

 */

if (!defined('ABSPATH'))

    exit; // Exit if accessed directly

function RealEstate_contact_form()

{

    wp_enqueue_script('contact-form-js', REALESTATEURL .

        'includes/contact-form/js/multi-contact-form.js', array('jquery'));

}

add_action('wp_loaded', 'RealEstate_contact_form');

function realestate_form_ajaxurl()

{

//    echo '<script type="text/javascript">

//                var ajaxformurl = "' . admin_url('admin-ajax.php') . '";

//              </script>';





?>

    <script type="text/javascript">

        var ajax_object = {};

        ajax_object.ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';

    </script>

<?php

}



add_action('wp_head', 'realestate_form_ajaxurl');



//add_action('wp_ajax_nopriv_realestate_process_form', 'realestate_process_form');



if(is_user_logged_in())

   add_action('wp_ajax_realestate_process_form', 'realestate_process_form');

else

   add_action('wp_ajax_nopriv_realestate_process_form', 'realestate_process_form');









function realestate_process_form()

{

    check_ajax_referer('realestate_cform'); // , 'security', false );

    $Car_name = isset($_POST['realestate_the_title']) ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/",

        "", sanitize_text_field($_POST['realestate_the_title'])) : "";

    define("RECIPIENT_NAME", "WordPress");

    define("EMAIL_SUBJECT", "Visitor Message From RealEstate Plugin About: " . $Car_name);

    $success = false;

    if (isset($_POST['RealEstate_recipientEmail'])) {

        $recipient_email = sanitize_email($_POST['RealEstate_recipientEmail']);

    } else

        $recipient_email = '';

    $senderName = isset($_POST['RealEstate_senderName']) ? preg_replace("/[^\.\-\' a-zA-Z0-9]/",

        "", sanitize_text_field($_POST['RealEstate_senderName'])) : "";

    $senderEmail = isset($_POST['RealEstate_senderEmail']) ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/",

        "", sanitize_text_field($_POST['RealEstate_senderEmail'])) : "";

    if (isset($_POST['title']))

        $message = sanitize_text_field($_POST['title'] . PHP_EOL);

    else

        $message = 'Message: ';

    $message .= isset($_POST['RealEstate_sendermessage']) ? preg_replace("/(From:|To:|BCC:|CC:|Subject:|Content-Type:)/",

        "", sanitize_text_field($_POST['RealEstate_sendermessage'])) : "";

    if ($senderName && $senderEmail && $message && $recipient_email) {

        $recipient = RECIPIENT_NAME . " <" . $recipient_email . ">";

        $mydomain = preg_replace('/www\./i', '', sanitize_text_field($_SERVER['SERVER_NAME']));

        $message = 'eMail: ' . $senderEmail . PHP_EOL . $message;

        $message = 'Name: ' . $senderName . PHP_EOL . $message;

       

        

        $headers = "eMail: WordPress Site < WordPress@" . $mydomain . " >\n";

        $headers .= 'X-Mailer: PHP/' . phpversion();

        $success = wp_mail($recipient_email, EMAIL_SUBJECT, $message, $headers);

    }

    echo $success ? "success" : "error";

    wp_die();

} ?>