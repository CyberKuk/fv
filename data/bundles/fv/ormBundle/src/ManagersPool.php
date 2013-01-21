<?php

namespace OrmBundle;

use OrmBundle\Exception\OrmException as Exception;

class ManagersPool {
    
    private static $managers;

    /**
     * Под каждую сущность создаётся только один экземпляр менеджера.
     *
     * @static
     * @param $className Имя класса
     * @return RootManager Менеджер класса (EntityNameManager) либо стандартный (RootManager)
     * @throws Exception
     */
    public static function get( $className ){
        if( empty($className) )
            throw new Exception ("Can't return Entity Manager because class name is empty");
        
        $managerName = $className . 'Manager';
        
        if( !isset(self::$managers[$managerName]) ) {
            $managerClass = class_exists($managerName) ? $managerName : __NAMESPACE__ . "\\RootManager";
            self::$managers[$managerName] = new $managerClass( $className );
        }
        
        return self::$managers[$managerName];
    }
    
}
