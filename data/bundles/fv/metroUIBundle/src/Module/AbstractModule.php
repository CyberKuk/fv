<?php
namespace Bundle\fv\MetroUIBundle\Module;

use \fv\ViewModel\Statable;
use \fv\Config\Configurable;
use \fv\Collection\Collection;

abstract class AbstractModule implements Configurable{
    use Statable;

    public $isActive = false;
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
     * @param \fv\Collection\Collection $config
     * @return mixed
     */
    static function build( Collection $config ){
        $self = new static;
        $self
            ->setGroup( $config->group->get() )
            ->setName( $config->name->get() )
            ->setViews( $config->views )
            ->setSystemName( $config->systemName->get() );

        return $self;
    }

    public function getEditUrl(){
        return \Bundle\fv\RoutingBundle\Routing\Link::to( "backend:modules",
                                                          [ "name" => $this->getSystemName() ] );
    }

    public function getStates(){
        return [
            "Dashboard",
            "LeftBar"
        ];
    }
}
