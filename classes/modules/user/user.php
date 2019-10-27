<?php
/**
 * 
 * $Id: user.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once $config->classPath . '/modules/common.php';
require_once 'vp/Validation/Validation.php';

/**
 * 
 * 
 *   @package    modules
 *   @version    2002/07/19
 *   @access     public
 *   @author     Wolfram Kriesing <wolfram@kriesing.de>
 */
class modules_user extends modules_common
{

    var $table = TABLE_USER;

    var $_sendInfoMail  = false;
    var $_resetPassword = false;

    function __construct()
    {
        parent::__construct();
    }

    function preset()
    {
        $this->reset();
        $this->setOrder('surname');
    }

    /**
     *   overwrite the remove method to check permissions, etc.
     * 
     */
    function remove($id, $whereCol = '')
    {
        global $config, $applError, $time;

        if (!$this->isAdmin()) {
            $applError->set('You are not allowed to remove users!');
            return false;
        }

        $id = intval($id);

        //FIXXME check for references in TABLE_PRICE, and all the other tables too
        // require this here, so we can include the class at the beginning of init.php
        // if we would include it on top it would require a lot of other classes which require
        // instances that are not prepared at that point .. kinda messy

        // we can not require the class time here because it requires many others, see comment above
        $time = new modules_common(TABLE_TIME);
        $time->setWhere('user_id = ' . $id);
        if ($time->getCount()) {
            // AK, system worx : retrieve all logged times for that user and delete them !
            $result = $time->getAll();
            if (is_array($result) && !empty($result)) {
                foreach ($result as $data) {
                    $time->remove($data);
                }
            }
            unset($time);
            // read again number of users
            $time = new modules_common(TABLE_TIME);
            $time->setWhere('user_id = ' . $id);
            if ($time->getCount()) {
                // something has gone wrong ...
                $applError->setOnce('You can not remove this user, because there are still times logged for him/her!');
                return false;
            }
        }

        // AK, system worx : retrieve all projects this user is assigned to and delete the assignement
        // do that only if we aren't this user and he isn't root : TODO
        $team = new modules_common(TABLE_PROJECTTREE2USER);
        $team->setWhere('user_id = ' . $id);
        $result = $team->getAll();
        $p = array();
        if (is_array($result) && !empty($result)) {
            foreach ($result as $data) {
                $team->remove($data);
                $p[] = $data['projectTree_id'];
            }
        }

        return parent::remove($id);
    }

    /**
     * 
     * 
     */
    function save($data)
    {
        global $applError, $applMessage, $config, $userAuth, $util;

        $ret = true;

        $data['name']    = trim($data['name']);
        $data['surname'] = trim($data['surname']);
        $data['email']   = trim($data['email']);
        $data['login']   = trim($data['login']);

        if (empty($data['name']) || empty($data['surname'])) {
            $applError->set('Please enter the complete name!');
            $ret = false;
        }

        if (!vp_Validation::isEmail($data['email'])) {
            $applError->set('Please enter a valid email-address!');
            $ret = false;
        }

        // we always need a login to be given
        if (strlen($data['login']) < 2 || strlen($data['login']) > 20) {
            $applError->set('Please enter a valid login!');
            $ret = false;
        }

        if (!empty($data['hoursPerDay']) && !is_numeric($data['hoursPerDay'])) {
            $applError->set('The hours per day has to be a number!');
            $ret = false;
        }
        // if no hours are set store null, so no 0 appears
        if (empty($data['hoursPerDay'])) {
            $data['hoursPerDay'] = null;
        }

        // reset password
        if (!empty($data['resetPassword']) && $data['login'] != 'root') {
            $data['password'] = $util->generatePassword(8);
            $data['password1'] = $data['password'];
        }

        if (empty($data['password'])) {
            // if no password is given, remove the password from the $data,
            // so the old password wont be overwritten!
            unset($data['password']);
        } else {
            if (!vp_Validation::isStrengthPassword($data['password'], 8)) {
                $applError->set("Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.");
                $ret = false;
            }
        }
        if (@$data['password'] != @$data['password1']) {
            $applError->set('The passwords don\'t match!');
            $ret = false;
        }

        if ($ret) {
            $this->_sendInfoMail  = !empty($data['sendInfoMail']);
            $this->_resetPassword = !empty($data['resetPassword']);

            unset($data['password1']);
            unset($data['resetPassword']);
            unset($data['sendInfoMail']);

            return parent::save($data);
        }
        return $ret;
    }

    /**
     *   the checks that need to be done when updating the user data
     *
     *
     */
    function update($data)
    {
        global $config, $userAuth, $applError, $db;

        $ret = true;

        $curUser = $this->get($data['id']);
        if ($curUser) {
            // does the user want to change his username?
            if ($data['login'] != $curUser['login']) {
                // check if the login is still available, but dont check if it is the user's own :-)
                $this->reset();
                $this->setWhere('login=' . $db->quote($data['login']) . ' AND id <> ' . $data['id']);
                if ($this->getCount()) {
                    $applError->set('This login is not available anymore!');
                    $ret = false;
                }
                // we need the password to digest the password properly
                if (empty($data['password'])) {
                    $applError->set('Please enter a password in order to change the username!');
                    $ret = false;
                }
            }
        }

        if ($ret) {
            if (!empty($data['password'])) {
                $pw = $data['password'];
                $data['password'] = $userAuth->digest($data['login'], $data['password']);
            }
            $ret = parent::update($data);
            if ($ret) {
                // mail (still) needs the original password
                if (!empty($data['password'])) {
                    $data['password'] = $pw;
                }
                $this->sendMail($data);
            }
        }
        return $ret;
    }

    /**
     *   check if enough user-licenses exist
     *
     */
    function add($data)
    {
        global $session, $applError, $config, $db, $userAuth;

        $ret = true;

        //FIXXXME get the data via xml-rpc to have the newset and
        // to be sure the session data are not used here!
        // since they can be manipulated
        $maxNumUsers = $session->account->numUsers;
        $this->reset();
        if ($maxNumUsers <= $this->getCount()) {
            $applError->set("Your license only allows $maxNumUsers users!");
            $ret = false;
        }

        $this->reset();
        $this->setWhere('login = ' . $db->quote($data['login']));
        if ($this->getCount()) {
            $applError->set('This login is not available anymore!');
            $ret = false;
        }

        if (empty($data['password'])) {
            $applError->set('Please enter a password!');
            $ret = false;
        }

        if ($ret) {
            $pw = $data['password'];
            $data['password'] = $userAuth->digest($data['login'], $data['password']);
            $ret = parent::add($data);
            if ($ret) {
                // mail (still) needs the original password
                $data['password'] = $pw;
                $this->sendMail($data);
            }
        }

        return $ret;
    }

    /**
     *   add ldap authenticated user on the fly (SX)
     *   Password is generated and random
     *   This way no harm is done if authentication is changed to db afterwards ...
     */
    function addLdapUserPassthrough($givenname, $surname, $userid, $mail)
    {
        global $session, $applError, $config, $db, $util;

        $ret = true;

        $maxNumUsers = $session->account->numUsers;
        $this->reset();
        if ($maxNumUsers <= $this->getCount()) {
            $applError->set("Your license only allows $maxNumUsers users!");
            $ret = false;
        }

        if (!trim(@$surname) || !trim(@$givenname)) {
            $applError->set('Please enter the complete name!');
            $ret = false;
        }

        if (!isset($mail)) {
            $applError->set('Please enter a valid email-address!');
            $ret = false;
        }

        $this->reset();
        $this->setWhere('login=' . $db->quote($userid));
        if ($this->getAll()) {
            // should never happen as we checked that before in ldap auth
            $applError->set('This login is not available anymore!');
            $ret = false;
        }

        $data['password'] = $util->generatePassword();
        $data['surname']  = $surname;
        $data['name']     = $givenname;
        $data['email']    = $mail;
        $data['login']    = $userid;

        if ($ret) {
            return parent::add($data);
        }
        return $ret;
    }

    /**
     *   is the current user an admin
     * 
     *   @version    13/11/2002
     *   @author     Wolfram Kriesing <wk@visionp.de>
     *   @return boolean if the current user is an admin returns true
     */
    function isAdmin()
    {
        global $session;

        if (empty($session->temp)) {
            return false;
        }

        return isset($session->temp->user->isAdmin) && $session->temp->user->isAdmin;
    }

    /**
     *   switch to admin mode, this can only be done for the currently logged in user!!!
     * 
     */
    function adminModeOn()
    {
        global $session, $applError;

        if (!isset($session->temp)) {
            $session->temp = new stdClass();
        }
        if (!isset($session->temp->user)) {
            $session->temp->user = new stdClass();
        }
        $session->temp->user->isAdmin = $this->canBeAdmin() ? true : false;

        if (!$session->temp->user->isAdmin) {
            $applError->set('You can not switch to admin-mode!');
        }
        return $session->temp->user->isAdmin;
    }

    /**
     *   switch admin mode OFF, this is only be done for the currently logged in user!!!
     * 
     */
    function adminModeOff()
    {
        global $session;

        $session->temp->user->isAdmin = false;
    }

    function canBeAdmin($userId = null)
    {
        global $userAuth;

        if ($userId == null) {
            $canBeAdmin = $userAuth->getData('isAdmin');
        } else {
            $canBeAdmin = $this->get($userId, 'isAdmin') ? true : false;
        }

        return $canBeAdmin;
    }

    /**
     *   this gets all available users, the current person can see
     *   this is actually needed for the overview-filter
     *
     *   @return array   just like getAll, only filtered data
     */
    function getAllAvail()
    {
        global $userAuth;

        $this->preset();
        // the admin can see all the users
        if (!$this->isAdmin()) {
            $myUid = $userAuth->getData('id');

            // get all the projectTree_id's of the projects where i am
            // manager of
            $tree2user = new modules_common(TABLE_PROJECTTREE2USER);
            $tree2user->setWhere("user_id=$myUid AND isManager=1");
            $tree2user->setSelect('projectTree_id');
            $tree2user->setGroup('projectTree_id');
            $projectIds = array();
            $myProjects = $tree2user->getAll();
            if (is_array($myProjects) && !empty($myProjects)) {
                foreach ($myProjects as $aProject) {
                    $projectIds[] = $aProject['projectTree_id'];
                }
            }

            // get the user-data, for those users that are member in any of my projects
            // if i am not a manager in any project, then get only my user data
            $this->autoJoin(TABLE_PROJECTTREE2USER);
            if (!empty($projectIds)) {
                $this->addWhere('projectTree_id IN (' . implode(',', $projectIds) . ')');
            } else {
                $this->addWhere("id=$myUid");
            }
            $this->setSelect(TABLE_USER . '.*');
            $this->setGroup('id');
        }
        return $this->getAll();
    }

    function updatePassword($id, $login, $password)
    {
        global $userAuth;

        if (empty($id) || empty($login) || empty($password)) {
            return false;
        }

        $pw = $userAuth->digest($login, $password);

        $this->reset();
        $this->setWhere('id = ' . intval($id));
        return parent::update(array('password' => $pw));
    }

    function sendMail($data)
    {
        global $applError, $applMessage, $config, $userAuth, $util;

        if (!$this->_sendInfoMail && !$this->_resetPassword) {
            return true;
        }

        $password  = !empty($data['password']) ? $data['password'] : '';
        $adminName = $userAuth->getData('name') . ' ' . $userAuth->getData('surname');

        // send info mail?
        if ($this->_sendInfoMail && !$this->_resetPassword) {
            $subject = 'Your openTimetool registration';
            $message = "    
Your access data are:
    
username:   {$data['login']}
password:   {$password}";
            $setPw = 'change';
            $resetPw = 'registered you';
        }

        // send password reset mail?
        if ($this->_resetPassword) {
            $subject = 'Password reset in openTimetool by admin';
            $message = "    
Please login immediately and set a new one.
    
Your access data are:
    
username:              {$data['login']}
new random password:   {$password}";
            $setPw = 'set';
            $resetPw = 'reset your password';
        }

        $message = "    
Hello {$data['name']} {$data['surname']},
    
$adminName has $resetPw for openTimetool.
$message
    
(Please $setPw your password right away!)
    
You can login here
<{$config->vServerRoot}{$config->home}>
    
best regards
    
$adminName
    
";

        $ret = $util->sendMail($data['email'], $subject, $message);
        if (!$ret) {
            $applError->set("Error sending the mail to '{$data['email']}'!");
        } else {
            $applMessage->set("Info e-mail sent to '{$data['email']}'.");
        }

        return $ret;
    }

} // end of class

$user = new modules_user;
