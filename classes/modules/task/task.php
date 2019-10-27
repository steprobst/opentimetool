<?php
/**
 * 
 * $Id: task.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once $config->classPath . '/modules/time/time.php';
require_once $config->classPath . '/modules/common.php';

/**
 * 
 * 
 *   @package    modules
 *   @version    2002/07/18
 *   @access     public
 *   @author     Wolfram Kriesing <wolfram@kriesing.de>
 */
class modules_task extends modules_common
{

    var $table = TABLE_TASK;

    var $_requestCache = array();

    function __construct()
    {
        parent::__construct();
        $this->preset();
    }

    /**
     * this does a reset and sets the initial state as we think we mostly need it :-)
     */
    function preset()
    {
        $this->reset();
        $this->setOrder('name');
    }

    function getEmptyElement()
    {
        $data['calcTime'] = 1;
        $data['needsProject'] = 1;
        return $data;
    }

    /**
     * 
     * 
     */
    function save($data)
    {
        global $applError;

        $ret = true;

        $data['name']    = trim($data['name']);
        $data['comment'] = trim($data['comment']);
        $data['color']   = trim($data['color']);

        if (empty($data['name'])) {
            $applError->set('Please enter a task name!');
            $ret = false;
        }

        if ($ret) {
            return parent::save($data);
        }
        return $ret;
    }

    /**
     * 
     * 
     */
    function add($data)
    {
        global $applError, $db;

        $ret = true;

        $this->reset();
        $this->setWhere('name=' . $db->quote($data['name']));
        if ($this->getCount()) {
            $applError->set('The task name already exists!');
            $ret = false;
        }

        if ($ret) {
            return parent::add($data);
        }
        return $ret;
    }

    /**
     * 
     * 
     */
    function update($data)
    {
        global $applError, $db;

        $ret = true;

        $curTask = $this->get($data['id']);
        if ($curTask) {
            // does the user want to change the task name?
            if ($data['name'] != $curTask['name']) {
                // check if the name is still available
                $this->reset();
                $this->setWhere('name=' . $db->quote($data['name']) . ' AND id <> ' . $data['id']);
                if ($this->getCount()) {
                    $applError->set('The task name already exists!');
                    $ret = false;
                }
            }
        }

        if ($ret) {
            return parent::update($data);
        }
        return $ret;
    }

    /**
     * check if the task that shall be removed is in use
     * if it is then we dont allow removing
     * 
     */
    function remove($id, $whereCol = '')
    {
        global $applError;

        $id = intval($id);

        $time = new modules_time;
        $time->setWhere('task_id=' . $id);
        $cnt = $time->getCount();
        if ($cnt > 0) {
            $applError->set('Sorry, this task has already been used ' .
                    $cnt . ' times, it cant be removed!');
            return false;
        }
        return parent::remove($id);
    }

    /**
     * this gets the tasks that can be logged without specifiying a project
     */
    function getNoneProjectTasks($cache = true)
    {
        if ($cache && isset($this->_requestCache[__FUNCTION__])) {
            return $this->_requestCache[__FUNCTION__];
        }
        $this->reset();
        $this->setWhere('needsProject=0');
        $res = $this->getAll();
        // we do the reset here, to be clear in every call afterwards, since this method gets called
        // mostly before using the class somewhere else
        $this->reset();
        $this->_requestCache[__FUNCTION__] = $res;
        return $res;
    }

    /**
     * this gets the tasks that have a duration and need a project
     */
    function getProjectTasks($cache = true)
    {
        if ($cache && isset($this->_requestCache[__FUNCTION__])) {
            return $this->_requestCache[__FUNCTION__];
        }
        $this->reset();
        $this->setWhere('needsProject=1,calcTime=1');
        $res = $this->getAll();
        // we do the reset here, to be clear in every call afterwards, since this method gets called
        // mostly before using the class somewhere else
        $this->reset();
        $this->_requestCache[__FUNCTION__] = $res;
        return $res;
    }

    /**
     * SX (AK):
     * tells if given task is a non project task
     */
    function isNoneProjectTask($taskid)
    {
    	$noneProjectTasks = $this->getNoneProjectTasks();
    	$isone = false;
    	foreach ($noneProjectTasks as $task) {
            if ($task['id'] == $taskid) {
                $isone = true;
                break;
            }
    	}
    	return $isone;
    }

    /**
     * SX (AK):
     * tells if given task is a project task
     */
    function isProjectTask($taskid)
    {
    	$ProjectTasks = $this->getProjectTasks();
    	$isone = false;
    	foreach ($ProjectTasks as $task) {
            if ($task['id'] == $taskid) {
                $isone = true;
                break;
            }
    	}
    	return $isone;
    }

    /**
     * this gets the tasks that have a duration and need a project
     */
    function hasDuration($taskid)
    {
        $this->reset();
        $this->setWhere('calcTime=1');
        $res = $this->getAll();
        // we do the reset here, to be clear in every call afterwards, since this method gets called
        // mostly before using the class somewhere else
        $this->reset();

    	$isone = false;
    	foreach ($res as $task) {
            if ($task['id'] == $taskid) {
                $isone = true;
                break;
            }
    	}
        return $isone;
    }

} // end of class

$task = new modules_task;
