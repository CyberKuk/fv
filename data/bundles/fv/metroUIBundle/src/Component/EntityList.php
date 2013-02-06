<?php
namespace Bundle\fv\MetroUIBundle\Component;

use fv\ViewModel\ViewModel;
use Bundle\fv\MetroUIBundle\Module\RootModule;
use Bundle\fv\MetroUIBundle\Component\Reflection;

class EntityList extends ViewModel{
    /**
     * @var \Bundle\fv\MetroUIBundle\Module\RootModule
     */
    private $module;

    public function __construct( RootModule $module ){
        $this->module = $module;
    }

    public function getEntityList(){
        return $this
                ->module
                ->getEntity()
                ->select()
                ->fetchAll();
    }

    public function getRenderedEntityList(){
        $response = [];

        foreach( $this->getEntityList() as $entity ){
            $response[] = new Reflection( $entity );
        }

        return $response;
    }


}
