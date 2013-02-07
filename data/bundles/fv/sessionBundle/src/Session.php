<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 04.02.13
 * Time: 18:26
 */

namespace Bundle\fv\SessionBundle;

class Session implements \fv\Config\Configurable
{
    private static $currentSession;

    /**
     * @var \fv\Collection\Collection
     */
    private $config;

    /**
     * @var \SessionHandler
     */
    private $handler;

    /**
     * @param \fv\Collection\Collection $config
     */
    function __construct($config)
    {
        $this->config = $config;
        //$this->init();
    }


    private function init() {
        if ($this->config->systemName->get() != self::$currentSession) {
            if (self::$currentSession) {
                session_write_close();
            }

            self::$currentSession = $this->config->systemName->get();
            foreach ($this->config as $sessionParamName => $sessionParamValue) {
                /**
                 * @var $sessionParamValue \fv\Collection\Collection
                 */
                ini_set("session.{$sessionParamName}", $sessionParamValue->get());
            }

            /** @noinspection PhpParamsInspection */
            session_set_save_handler($this->getHandler());
            session_start();
        }

    }

    /**
     * @param \fv\Collection\Collection $config
     * @return Session|mixed
     */
    static function build(\fv\Collection\Collection $config)
    {
        $instance = new static($config);
        return $instance;
    }
    /** Set session value */
    function set($key, $value) {
        $this->init();
        global $_SESSION;
        $_SESSION[self::$currentSession.$key] = $value;
    }

    /** Get session value */
    function get($key) {
        $this->init();
        global $_SESSION;
        if (!empty($_SESSION[self::$currentSession.$key]))
            return $_SESSION[self::$currentSession.$key];

        return null;
    }

    /**
     * @throws Exception\SessionHandlerException
     * @return \SessionHandler
     */
    public function getHandler()
    {
        if (!$this->handler) {
            $className = 'Bundle\fv\SessionBundle\Handler\\'.ucfirst((string)$this->config->type->get()).'SessionHandler';;

            if( !class_exists($className) ) {
                throw new \Bundle\fv\SessionBundle\Exception\SessionHandlerException("Class {$className} for session name {$this->config->systemName->get()} not defined!");
            }

            $this->handler = call_user_func( array( $className, "build" ), $this->config );
        }
        return $this->handler;
    }
}
