<?php

namespace Biz;

class AppLoggerConstant implements LoggerConstantInterface
{
    /**
     * [$SYSTEM 系统设置].
     *
     * @var string
     */
    const SYSTEM = 'system';
    /**
     * [$CRONTAB 定时任务].
     *
     * @var string
     */
    const CRONTAB = 'crontab';

    public function getActions()
    {
        return array(
            self::SYSTEM => array(
                'update_settings',
                'update_block',
                'update_app_version',
            ),
            self::CRONTAB => array(
                'job_start',
                'job_end',
            ),
        );
    }

    public function getModules()
    {
        return array(
            self::SYSTEM,
            self::CRONTAB,
        );
    }
}
