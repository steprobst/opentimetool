<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/pageHandler.php';

// if we are auth against LDAP we have to set a flag if we really edit an LDAP user
// if not we must be able to modify the password !!
$data['is_LDAP_user'] = false;
if ($config->auth->method == 'LDAP') {
    if (method_exists($userAuth, 'is_LDAP_user')) {
        if ($userAuth->is_LDAP_user($data['login'])) {
            $data['is_LDAP_user'] = true;
        } else {
            $data['is_LDAP_user'] = false;
        }
    }
}

$passwd = false;
$pageHandler->setObject($user);

if (!empty($_REQUEST['newData']) && $config->demoMode) {
    $applMessage->set('Please note! This function is disabled in the demo version.');
} else if (isset($_REQUEST[$pageHandler->getOption('saveButton')])) {
    $ret = true;
    $data = array(
        'password'  => $_REQUEST['newData']['password'],
        'password1' => $_REQUEST['newData']['password1'],
    );
    if (!vp_Validation::isStrengthPassword($data['password'], 8)) {
        $applError->set("Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.");
        $ret = false;
    }
    if ($data['password'] != $data['password1']) {
        $applError->set('The passwords don\'t match!');
        $ret = false;
    }
    if ($ret) {
        $passwd = true;
        // save new password
        $user->updatePassword($userAuth->getData('id'),
                $userAuth->getData('login'), $_REQUEST['newData']['password']);
        $applMessage->set('Data successfully saved.');
    }
}

require_once $config->finalizePage;
