<?php
session_start();

/**
 * @author Denis CLAVIER <clavierd at gmail dot com>
 * Adapted from Oauth2-server-php cookbook
 * @see http://bshaffer.github.io/oauth2-server-php-docs/cookbook/
 */

// include our OAuth2 Server object
require_once __DIR__ . '/server.php';
require_once __DIR__ . '/config_general.php';

$request = OAuth2\Request::createFromGlobals();
$response = new OAuth2\Response();

// If user has clicked on "not me" link, disconnect him by cleaning PHP SESSION variables.
if (isset($_POST['disconnect'])) {
    $_SESSION = array();
}

// Validate the authorize request
if (!$server->validateAuthorizeRequest($request, $response)) {
    $response->send();
    die;
}

// If user is not yet authenticated, he is redirected.
if (!isset($_SESSION['uid'])) {
    // Store the authorize request
    $explode_url = explode("/", strip_tags(trim($_SERVER['REQUEST_URI'])));
    $_SESSION['auth_page'] = end($explode_url);
    header('Location: access_token');
    exit();
}

// Check if user has already authorized oauth to share data with Mattermost. In this case, user should exist in 'user' table.
if ($server->userExists($_SESSION['uid'])) {
    // User had already authorized the client during a previous session.
    $is_authorized = true;
}
// Display an authorization form
else if (empty($_POST)) {
    exit('
    <!DOCTYPE html>
    <html lang="en">
    
    <head>
        <meta charset="UTF-8" />
        <link rel="icon" type="ico" href="./images/prompt_icon.png">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport" />
        <title>' . $html_page_title . ' | Authorization Page</title>
    
        <!-- General CSS Files -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
            integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
            integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous" />
        <!-- Template CSS -->
        <link rel="stylesheet" href="./css/custom.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
    </head>
    
    <body>
    
        <div id="app">
            <section class="section">
                <div class="container mt-1">
                    <div class="row">
                        <div
                            class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                            <div class="login-brand mt-5">
                                <a id="header_url" href=' . $mattermost_host . '><img
                                        src="./images/auth_icon.png" alt="logo" width="300" /></a>
                            </div>
    
                            <div class="col-12 text-center mb-4" style="font-size: x-large; color: black;"><span
                                    style="font-weight: 900;">Mattermost</span> wants to
                                access your Google Account Info</div>
    
                            <div class="card card-primary">
                                <div class="card-body">
                                    <div class="text-center mb-1" style="color: black;">This will allow <span
                                            style="font-weight: 900;">Mattermost</span> to access the following information
                                        for
                                    </div>
                                    <form method="POST">
                                        <div class="text-center mb-1"><span style="font-weight: 900; color: black;">' . $_SESSION['uid'] . '<button type="submit" class="link" name="disconnect"
                                                    value="true" style="font-size: x-small;">&nbsp;(Not
                                                    You?)</buttonbutton></span></div>
                                    </form>
    
    
                                    <hr />
                                    <div class="mb-3">
                                        <ul style="list-style: none; color: black;">
                                            <li><i class="fas fa-check-circle mr-2" style="color: #0074d9;"></i>View your
                                                <span style="font-weight: 900;">email address</span>
                                            </li>
                                            <li><i class="fas fa-check-circle mr-2" style="color: #0074d9;"></i>View your
                                                <span style="font-weight: 900;">basic profile info</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <hr />
                                    <div></div>
                                    <div class="mb-1 text-justify"><span style="font-weight: 900; color: black;">Make sure
                                            you Trust
                                            Mattermost</span>
                                    </div>
                                    <div class="mb-3 text-justify">You may be sharing sensitive data with
                                        the site or app
                                    </div>
                                    <hr />
                                    <form method="POST">
                                        <button type="submit" value="Authorize" name="authorized" id="input_accept"
                                            class="btn btn-success btn-lg btn-block mt-3" tabindex="4">
                                            Grant Access
                                        </button>
                                        <button type="submit" value="Deny" name="authorized" id="input_deny"
                                            class="btn btn-danger btn-lg btn-block mt-3" tabindex="4">
                                            Deny
                                        </button>
                                    </form>
    
                                </div>
                            </div>
                            <div class="simple-footer">' . $footer_text . '</div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- General JS Scripts -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>
        <!-- Page Specific JS File -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
        <script src="./js/custom.js"></script>
    </body>
    
    </html>
  ');
} else {
    // Check if user has authorized to share his data with the client
    $is_authorized = ($_POST['authorized'] === 'Authorize');
}

// Print the authorization code if the user has authorized your client
$server->handleAuthorizeRequest($request, $response, $is_authorized, $_SESSION['uid']);

// Authentication process is terminated, session can be destroyed.
$_SESSION = array();

if ($is_authorized) {
    // This is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
    $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=') + 5, 40);
    header('Location: ' . $response->getHttpHeader('Location'));
    exit();
}

// Send message in case of error
$response->send();
