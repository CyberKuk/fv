<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 05.02.13
 * Time: 13:22
 */

namespace Bundle\fv\SessionBundle\Handler;

use fv\Config\Configurable;
use fv\Collection\Collection;
use Bundle\fv\SessionBundle\Exception\SessionHandlerException;

/** @noinspection PhpUndefinedClassInspection */
class StorageSessionHandler implements Configurable, \SessionHandlerInterface  {

    const SESSION_PREFIX = 'sess_';

    /**
     * @var \fv\Storage\Storage
     */
    private $storage;

    /**
     * @param Collection $config
     * @throws \Bundle\fv\SessionBundle\Exception\SessionHandlerException
     */
    function __construct($config)
    {
        $factory = new \fv\Storage\StorageFactory();
        if (! $config->connectionName instanceof Collection) {
            throw new SessionHandlerException("Session config for storage type must have connectionName param");
        }
        $this->storage = $factory->get($config->connectionName->get());
    }


    static function build(Collection $config) {
        return new self($config);
    }

    public function close(){
        return true;
    }

    public function destroy($session_id){
        return true;
    }

    public function gc($maxlifetime)
    {
        return true;
    }

    public function open($save_path, $session_id){
        return true;
    }

    public function read($session_id)
    {
        return $this->storage->get(self::SESSION_PREFIX.$session_id);
    }

    public function write($session_id, $session_data)
    {
        $timeout = session_cache_expire()*60;
        return $this->storage->set(self::SESSION_PREFIX.$session_id, $session_data, $timeout);
    }

}
