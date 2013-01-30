<?php
namespace Bundle\fv\MetroUIBundle\Module;

use Bundle\fv\ModelBundle\AbstractModel;

use \fv\Config\Configurable;
use \fv\Collection\Collection;

class RootModule extends AbstractModule implements Configurable{
    /**
     * @var AbstractModel
     */
    private $entity;

    /**
     * @param Bundle\fv\ModelBundle\AbstractModel $entity
     * @return RootModule
     */
    public function setEntity( AbstractModel $entity ){
        $this->entity = $entity;
        return $this;
    }

    /**
     * @param \fv\Collection\Collection $config
     * @return mixed
     */
    static function build( Collection $config ){
        $self = new static;
        $entityClassName = $config->entity->get();

        if( !class_exists( $entityClassName ) ){
            throw new \RuntimeException( "Class {$entityClassName} is not exist!" );
        }
        $entity = new $entityClassName;

        $self->setEntity( $entity );
        return $self;
    }
}
