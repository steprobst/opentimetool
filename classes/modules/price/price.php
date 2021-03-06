<?php
/**
 * 
 * $Id: price.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once $config->classPath . '/modules/common.php';

/**
 *   this class handles all the prices
 * 
 *   @package    modules
 *   @version    2002/08/21
 *   @access     public
 *   @author     Wolfram Kriesing <wolfram@kriesing.de>
 */
class modules_price extends modules_common
{

    var $table = TABLE_PRICE;

    //function debug($m){print $m.'<br>';}

    function __construct()
    {
        parent::__construct();

        // create this query to get all prices, no matter if they have a user
        // SELECT * FROM price,task LEFT JOIN user ON user.id=price.user_id WHERE task.id=price.task_id
        // but actually i thought this would work, but obviously a left-join cant work on 2 tables
        // SELECT * FROM price LEFT JOIN user,task ON user.id=price.user_id AND task.id=price.task_id

        $this->setLeftJoin(TABLE_USER, TABLE_USER . '.id=user_id');
        $this->setJoin(TABLE_TASK , TABLE_TASK . '.id=task_id');
    }

    /**
     *   overwrite save to convert the validFrom-timestamp
     * 
     *   @author     Wolfram Kriesing <wk@visionp.de>
     *   @param      array   the data from the form
     *   @return     array   the converted data
     */
    function save($data)
    {
        global $util;

        if ($data['validFrom']) {
            $data['validFrom'] = $util->makeTimestamp($data['validFrom']);
        }
        if ($data['validUntil']) {
            $data['validUntil'] = $util->makeTimestamp($data['validUntil']);
        }
        $ret = parent::save($data);
        return $ret;
    }

    function add($data)
    {
        global $applError;

        if (!$data['validFrom']) {
            $this->setWhere('validFrom=0');
            $this->addWhere('task_id=' . $data['task_id']);
            if ($data['projectTree_id']) {
                $this->addWhere('projectTree_id=' . $data['projectTree_id']);
            }
            if ($data['user_id']) {
                $this->addWhere('user_id=' . $data['user_id']);
            }
            if ($this->getCount() > 0) {
                $this->setWhere();
                $applError->set('A price for this entry does already exist');
                return false;
            }
            $this->setWhere();
        }

        return parent::add($data);
    }

} // end of class

$price = new modules_price;
