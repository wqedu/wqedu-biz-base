<?php

namespace Tests;

use Codeages\Biz\Framework\Dao\ArrayStorage;
use Codeages\Biz\Framework\Dao\Connection;
use Codeages\Biz\Framework\Provider\DoctrineServiceProvider;
use Codeages\Biz\Framework\Provider\SchedulerServiceProvider;
use Codeages\Biz\Framework\Provider\SessionServiceProvider;
use Codeages\Biz\Framework\Provider\TargetlogServiceProvider;
use Codeages\Biz\Framework\Provider\TokenServiceProvider;
use Codeages\Biz\Framework\Provider\SettingServiceProvider;
use Codeages\Biz\Framework\Provider\QueueServiceProvider;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Codeages\Biz\Framework\Context\Biz;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

class IntegrationTestCase extends TestCase
{
    /**
     * @var \Composer\Autoload\ClassLoader
     */
    public static $classLoader = null;

    /**
     * @var Biz
     */
    protected $biz;

    /**
     * @var Connection
     */
    protected $db;

    public function setUp()
    {
        $this->biz = $this->createBiz();
        $this->db = $this->biz['db'];
        $this->db->beginTransaction();
    }

    public function tearDown()
    {
        $this->db->rollBack();

        unset($this->db);
        unset($this->biz);
    }

    protected function assertArrayEquals(array $ary1, array $ary2, array $keyAry = array())
    {
        if (count($keyAry) >= 1) {
            foreach ($keyAry as $key) {
                $this->assertEquals($ary1[$key], $ary2[$key]);
            }
        } else {
            foreach (array_keys($ary1) as $key) {
                if (is_array($ary1[$key])) {
                    $this->assertArrayEquals($ary1[$key], $ary2[$key]);
                } else {
                    $this->assertEquals($ary1[$key], $ary2[$key]);
                }
            }
        }
    }

    protected function createBiz(array $options = array())
    {
        $defaultOptions = array(
            'db.options' => array(
                'dbname' => getenv('DB_NAME') ?: 'wqedu-biz-course-test',
                'user' => getenv('DB_USER') ?: 'root',
                'password' => getenv('DB_PASSWORD') ?: '123456',
                'host' => getenv('DB_HOST') ?: '127.0.0.1',
                'port' => getenv('DB_PORT') ?: 3306,
                'driver' => 'pdo_mysql',
                'charset' => 'utf8',
            ),
            'debug' => true,
        );
        $options = array_merge($defaultOptions, $options);

        $biz = new Biz($options);
        $biz['autoload.aliases']['Example'] = 'Tests\\Example';
        $biz->register(new DoctrineServiceProvider());
        $biz->register(new TargetlogServiceProvider());
        $biz->register(new TokenServiceProvider());
        $biz->register(new SchedulerServiceProvider());
        $biz->register(new SettingServiceProvider());
        $biz->register(new QueueServiceProvider());
        $biz->register(new SessionServiceProvider());

        $cacheEnabled = getenv('CACHE_ENABLED');

        if ('true' === getenv('CACHE_ENABLED')) {
            $biz['dao.cache.enabled'] = true;
            $biz['dao.cache.annotation'] = true;
        }

        if (getenv('CACHE_STRATEGY_DEFAULT')) {
            if ('null' == getenv('CACHE_STRATEGY_DEFAULT')) {
                $biz['dao.cache.strategy.default'] = null;
            } else {
                $biz['dao.cache.strategy.default'] = getenv('CACHE_STRATEGY_DEFAULT');
            }
        }

        if (getenv('CACHE_ARRAY_STORAGE_ENABLED')) {
            $biz['dao.cache.array_storage'] = function () {
                return new ArrayStorage();
            };
        }

        $biz['logger.test_handler'] = function () {
            return new TestHandler();
        };

        $biz['logger'] = function ($biz) {
            $logger = new Logger('phpunit');
            $logger->pushHandler($biz['logger.test_handler']);

            return $logger;
        };

        $biz['lock.flock.directory'] = sys_get_temp_dir();

        $biz->boot();

        return $biz;
    }

    /**
     * @param string $seeder
     * @param bool   $isRun
     *
     * @return ArrayCollection
     */
    protected function seed($seeder, $isRun = true)
    {
        $seeder = new $seeder($this->db);

        return $seeder->run($isRun);
    }

    protected function grabAllFromDatabase($table, $column, array $criteria = array())
    {
    }

    protected function grabFromDatabase($table, $column, array $criteria = array())
    {
    }

    protected function fetchFromDatabase($table, array $criteria = array())
    {
        $builder = $this->biz['db']->createQueryBuilder();
        $builder->select('*')->from($table);

        $index = 0;
        foreach ($criteria as $key => $value) {
            $builder->andWhere("{$key} = ?");
            $builder->setParameter($index, $value);
            ++$index;
        }

        return $builder->execute()->fetch(\PDO::FETCH_ASSOC);
    }

    protected function fetchAllFromDatabase($table, array $criteria = array())
    {
        $builder = $this->biz['db']->createQueryBuilder();
        $builder->select('*')->from($table);

        $index = 0;
        foreach ($criteria as $key => $value) {
            $builder->andWhere("{$key} = ?");
            $builder->setParameter($index, $value);
            ++$index;
        }

        return $builder->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }
}
