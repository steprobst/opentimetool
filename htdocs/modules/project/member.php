<?php
/**
 * 
 * $Id: member.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/modules/project/treeDyn.php';
require_once $config->classPath . '/modules/project/member.php';
require_once $config->classPath . '/modules/user/user.php';

$isAdmin = $user->isAdmin();

if (!empty($_GET['projectId'])) {
    $projectId = intval($_GET['projectId']);
} else if (!empty($_POST['projectId'])) {
    $projectId = intval($_POST['projectId']);
} else if (!empty($_REQUEST['projectId'])) {
    $projectId = intval($_REQUEST['projectId']);
} else {
    $projectId = null;
}

// do only allow manager and admins here!
if (!$isAdmin && !empty($projectId) && !$projectMember->isManager($projectId)) {
    require_once 'HTTP/Header.php';
    HTTP_Header::redirect(null);
}

if (isset($_POST['action_save'])) {
    /**
     * If team inheritance is selected, we'll retrieve the
     * members from parent project and add them here.
     */
    if (isset($_REQUEST['InheritTeam'])) {
        $parentId = $projectTreeDyn->getParentId($projectId);

        $user->reset();
        // select only what we really need
        $user->setSelect('id,name,surname');
        $user->addSelect(TABLE_PROJECTTREE2USER . '.id');
        $user->addSelect(TABLE_PROJECTTREE2USER . '.isManager as isManager');
        $user->addSelect(TABLE_PROJECTTREE2USER . '.projectTree_id as projectTree_id');      
        $user->setLeftJoin(TABLE_PROJECTTREE2USER,
                'user_id=' . TABLE_USER . '.id AND projectTree_id=' . $parentId);
        $users = $user->getAll();
        // debug :
        //$applError->set(print_r($users,true) . '<br>');
        $managers = array();
        $members = array();
        foreach ($users as $key => $aUser) {
            if ($aUser['isManager']) {
                $managers[] = $aUser['id'];
                unset($users[$key]);
            } else if ($aUser['projectTree_id'] == $parentId) {
                $members[] = $aUser['id'];
                unset($users[$key]);
            }
        }
        // debug
        //$applError->set(print_r($managers,true) . '<br>');
        //$applError->set(print_r($members,true) . '<br>');

        if ($projectMember->updateSpecial(@$members, @$managers, @$projectId)) {
            unset($projectId);
        }
        // EOF team inheritance
    } else {
        if ($projectMember->updateSpecial(@$_POST['members'], @$_POST['managers'], @$projectId)) {
            unset($projectId);
        }
    }
}

if (isset($projectId)) {
    $curProject = $projectTreeDyn->getPathAsString($projectId);
    /**
     * SX : Get the parent project
     */
    //$parentId = $projectTreeDyn->getParentId($projectId);
    $tmpsplit = explode(' | ', $curProject);
    if (!is_array($tmpsplit) || count($tmpsplit) == 1) {
        $parentProject = $curProject;
    } else {
        $parentProject = $tmpsplit[count($tmpsplit) - 2];
    }

    $user->reset();
    // select only what we really need
    $user->setSelect('id,name,surname');
    $user->addSelect(TABLE_PROJECTTREE2USER . '.id');
    $user->addSelect(TABLE_PROJECTTREE2USER . '.isManager as isManager');
    $user->addSelect(TABLE_PROJECTTREE2USER . '.projectTree_id as projectTree_id');      
    $user->setLeftJoin(TABLE_PROJECTTREE2USER,
            'user_id=' . TABLE_USER . '.id AND projectTree_id=' . $projectId);
    // AK: order that stuff by surname,prename
    $user->setOrder('surname,name');
    $users = $user->getAll();
    $selectSizeUsers = (is_array($users) && sizeof($users) < 10) ? 10 : 20;
    $selectSizeMembers = (is_array($users) && sizeof($users) > 10) ? 10 : 5;
    $selectSizeManagers = 5;

    $managers = array();
    $members = array();
    foreach ($users as $key => $aUser) {
        if ($aUser['isManager']) {
            $managers[] = $aUser;
            unset($users[$key]);
        } else if ($aUser['projectTree_id'] == $projectId) {
            $members[] = $aUser;
            unset($users[$key]);
        }
    }
} else {
    $GLOBALS['bodyClass'] = 'projectMember';
}

$projectTreeJsFile = 'projectTreeTeam' . ($isAdmin?'Admin':'');

require_once $config->finalizePage;
