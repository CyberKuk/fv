<?php
namespace Bundle\fv\MetroUIBundle\Module;

use Bundle\fv\ModelBundle\AbstractModel;
use \fv\Collection\Collection;

class RootModule extends AbstractModule{
    /**
     * @var AbstractModel
     */
    private $entity;

    /**
     * @param \Bundle\fv\ModelBundle\AbstractModel $entity
     * @return RootModule
     */
    public function setEntity( AbstractModel $entity ){
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return \Bundle\fv\ModelBundle\AbstractModel
     */
    public function getEntity(){
        return new $this->entity;
    }

    /**
     * @param \fv\Collection\Collection $config
     * @return RootModule
     * @throws \RuntimeException
     */
    static function build( Collection $config ){
        $self = parent::build( $config );

        if( ! $config->entity instanceof Collection ){
            throw new \RuntimeException( "Cannot instantiate module '{$config->name->get()}'. Field 'class' or 'entity' is not set." );
        }

        $entityClassName = $config->entity->get();

        if( !class_exists( $entityClassName ) ){
            throw new \RuntimeException( "Class {$entityClassName} is not exist!" );
        }
        $entity = new $entityClassName;
        $self->setEntity( $entity );

        return $self;
    }


}
