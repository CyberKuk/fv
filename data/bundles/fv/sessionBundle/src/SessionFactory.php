<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 04.02.13
 * Time: 18:14
 */

namespace Bundle\fv\SessionBundle;

use fv\Config\ConfigLoader;
use fv\Collection\Collection;
use Bundle\fv\SessionBundle\Exception\SessionFactoryException;

class SessionFactory
{

    /** @var Collection */
    private static $sessionConfig;

    /** @var Session[] */
    private static $sessions;

    private static $defaultConfig = array(
        'type'  => 'default',
        //'name'  => 'PHPSESSID',
    );

    /**
     * @param string $sessionSystemName
     * @throws Exception\SessionFactoryException
     * @return Collection
     */
    private static function getConfig($sessionSystemName){
        if( empty(self::$sessionConfig) ){
            self::$sessionConfig = ConfigLoader::load( 'sessions' );
        }

        if( ! self::$sessionConfig->$sessionSystemName )
            throw new SessionFactoryException("Session {$sessionSystemName} not defined!");

        self::$sessionConfig->$sessionSystemName->merge(new Collection(self::$defaultConfig));
        return self::$sessionConfig->$sessionSystemName;
    }

    /**
     * @param string $sessionSystemName
     * @return Session
     * @throws Exception\SessionFactoryException
     */
    public static function get( $sessionSystemName='default' ){
        $config = self::getConfig($sessionSystemName);
        $config['systemName'] = $sessionSystemName;

        if (empty(self::$sessionHandlers[$sessionSystemName])) {

            /**
             * @var \SessionHandler $handler
             */
            //$handler = new $className;
            self::$sessionHandlers[$sessionSystemName] =  call_user_func( array( $className, "build" ), $config );
            session_set_save_handler(self::$sessionHandlers[$sessionSystemName]);

            //$config->delete('type');
            //$config->delete('connectionName');
        }

        if (empty(self::$sessions[$sessionSystemName])) {
            self::$sessions[$sessionSystemName] =  Session::build($config);
        }

        return Session::build($config);
    }
}
