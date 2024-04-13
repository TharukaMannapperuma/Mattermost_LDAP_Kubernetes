<?php
session_start();
/**
 * @author Denis CLAVIER <clavierd at gmail dot com>
 * A modified verion by dimst23
 */


// include our LDAP object
require_once __DIR__ . '/LDAP/LDAP.php';
require_once __DIR__ . '/LDAP/config_ldap.php';
require_once __DIR__ . '/config_general.php';

$prompt_template = new DOMDocument();
$prompt_template->loadHTMLFile('form_prompt.html', LIBXML_NOERROR);

// Modify header_url to redirect to the right page
$header_url = $prompt_template->getElementById('header_url');
$footer_company = $prompt_template->getElementById('footer_text');
$company_logo = $prompt_template->getElementById('company_logo_url');

// Set the href attribute value
if ($header_url) {
    $header_url->setAttribute('href', $mattermost_host);
}

// Set the text content of the footer
if ($footer_company) {
    $footer_company->textContent = $footer_text;
}

// Set the src attribute value
if ($company_logo) {
    $company_logo->setAttribute('src', $company_logo_url);
}


function messageShow($html_template, $message = 'No Msg')
{
    // $modification_node = $html_template->getElementsByTagName('div')->item(5);
    $modification_node = $html_template->getElementById('err_msg');
    $modification_node->nodeValue = $message;
    // $page_fragment = $html_template->createDocumentFragment();
    // $page_fragment->appendXML($message);

    // $modification_node->appendChild($page_fragment);

    echo $html_template->saveHTML();
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // If user is already authenticated, redirect him to the authorize page
    messageShow($prompt_template, "");
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify all fields have been filled
    if (empty($_POST['user']) || empty($_POST['password'])) {
        if (empty($_POST['user'])) {
            messageShow($prompt_template, 'Username field can\'t be empty.');
        } else {
            messageShow($prompt_template, 'Password field can\'t be empty.');
        }
    } else {
        // Check received data length (to prevent code injection)
        if (strlen($_POST['user']) > 64) {
            messageShow($prompt_template, 'Username has incorrect format ... Please try again');
        } elseif (strlen($_POST['password']) > 64) {
            messageShow($prompt_template, 'Password has incorrect format ... Please try again');
        } else {
            // Remove every html tag and useless space on username (to prevent XSS)
            $user = strtolower(strip_tags(htmlspecialchars(trim($_POST['user']))));
            $password = $_POST['password'];

            // Open a LDAP connection
            $ldap = new LDAP($ldap_host, $ldap_port, $ldap_version, $ldap_start_tls, $ldap_cert_path, $ldap_key_path, $ldap_secure);

            // Check user credential on LDAP
            try {
                $authenticated = $ldap->checkLogin($user, $password, $ldap_search_attribute, $ldap_filter, $ldap_base_dn, $ldap_bind_dn, $ldap_bind_pass);
            } catch (Exception $e) {
                $authenticated = false;
            }

            // If user is authenticated
            if ($authenticated) {
                $_SESSION['uid'] = $user;

                // If user came here with an autorize request, redirect him to the authorize page. Else prompt a simple message.
                if (isset($_SESSION['auth_page'])) {
                    $auth_page = $_SESSION['auth_page'];
                    header('Location: ' . $auth_page);
                    exit();
                } else {
                    messageShow($prompt_template, 'Congratulation you are authenticated ! <br /><br /> However there is nothing to do here ...');
                }
            }
            // check login on LDAP has failed. Login and password were invalid or LDAP is unreachable
            else {
                messageShow($prompt_template, 'Authentication failed...Check your username and password. If the error persists contact your administrator.');
            }
        }
    }
}
