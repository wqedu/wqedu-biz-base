<?php

namespace Biz\System\Dao\Impl;

use Biz\System\Dao\LogDao;
use Codeages\Biz\Framework\Dao\GeneralDaoImpl;

class LogDaoImpl extends GeneralDaoImpl implements LogDao
{
    protected $table = 'log';

    public function declares()
    {
        return array(
            'orderbys' => array(
                'createdTime',
                'id',
            ),
            'conditions' => array(
                'clientId = :clientId',
                'module = :module',
                'action = :action',
                'level = :level',
                'createdTime > :startDateTime',
                'createdTime < :endDateTime',
                'createdTime >= :startDateTime_GE',
            ),
        );
    }
}
