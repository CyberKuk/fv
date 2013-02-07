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
use Bundle\fv\SessionBundle\Entity\Session;

/** @noinspection PhpUndefinedClassInspection */
class DbSessionHandler implements Configurable, \SessionHandlerInterface  {

    /**
     * @var string
     */
    private $connectionName;

    /**
     * @var Session
     */
    private $sessionEntity;

    /**
     * @param Collection $config
     * @throws \Bundle\fv\SessionBundle\Exception\SessionHandlerException
     */
    function __construct($config)
    {
        if (! $config->connectionName instanceof Collection) {
            throw new SessionHandlerException("Session config for DB type must have connectionName param");
        }
        $this->connectionName = $config->connectionName->get();
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
        $destroyTime = date("Y-m-d H:i:s", strtotime("-$maxlifetime seconds"));
        \Bundle\fv\SessionBundle\Entity\Session::query($this->connectionName)
            ->where("`mtime` < :mtime", array('mtime'=>"$destroyTime"))
            ->delete();
        return true;
    }

    public function open($save_path, $session_id){
        return true;
    }

    public function read($session_id)
    {

        return $this->getEntity($session_id)
            ->data
            ->get();
    }

    public function write($session_id, $session_data)
    {
        $this
            ->getEntity($session_id)
            ->data
            ->set($session_data);
        $this
            ->getEntity($session_id)
            ->persist($this->connectionName);
    }

    /**
     * @param string $sessionId
     * @return \Bundle\fv\SessionBundle\Entity\Session
     */
    private function getEntity($sessionId) {
        if (!$this->sessionEntity) {
            $this->sessionEntity = \Bundle\fv\SessionBundle\Entity\Session::fetch($sessionId, $this->connectionName);
            if (!$this->sessionEntity) {
                $this->sessionEntity = new Session();
                $this
                    ->sessionEntity
                    ->sessionId->set($sessionId);
            }
        }
        return $this->sessionEntity;
    }

}
