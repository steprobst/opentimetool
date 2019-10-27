<?php
/**
 * 
 * $Id: login.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once '../../../config.php';

if (isset($_REQUEST['logout']) && $_REQUEST['logout'] == 1) {
    $userAuth->logout();
}

if ($userAuth->isLoggedIn()) {
    require_once 'HTTP/Header.php';
    HTTP_Header::redirect($config->home);
} else {
    // remove all the temporary session data, mostly user specific,
    // like the filter-settings for the overview page
    unset($session->temp);

    if (isset($_REQUEST['loginFailed'])) {
        $applError->setOnce('Please enter a valid login!');
        // slow down excessive requests
        sleep(5);
    }
}

$showLogin = ($account->isAspVersion() && $session->account->isActive) ||
        !$account->isAspVersion() ? true : false;

require_once $config->finalizePage;
