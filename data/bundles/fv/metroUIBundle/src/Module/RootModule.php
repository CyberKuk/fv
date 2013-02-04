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
    private $group;
    private $name;
    private $views;
    private $systemName;

    public function setSystemName( $systemName ){
        $this->systemName = $systemName;
        return $this;
    }

    public function getSystemName(){
        return $this->systemName;
    }

    /**
     * @param \fv\Collection\Collection $view
     */
    public function setViews( \fv\Collection\Collection $views ){
        $this->views = $views;
        return $this;
    }

    /**
     * @return \fv\Collection\Collection
     */
    public function getViews(){
        return $this->views;
    }

    /**
     * @param $name
     * @return RootModule
     */
    public function setName( $name ){
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @param string $group
     * @return RootModule
     */
    public function setGroup( $group ){
        $this->group = $group;

        return $this;
    }

    /**
     * @return string
     */
    public function getGroup(){
        return $this->group;
    }

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

        $self
            ->setEntity( $entity )
            ->setGroup( $config->group->get() )
            ->setName( $config->name->get() )
            ->setViews( $config->views )
            ->setSystemName( $config->systemName->get() );

        return $self;
    }

    public function getEditUrl(){
      return \Bundle\fv\RoutingBundle\Routing\Link::to( "backend:test", [$this->getName()] );
    }
}
