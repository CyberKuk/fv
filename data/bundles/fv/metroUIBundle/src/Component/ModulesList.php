<?php
namespace Bundle\fv\MetroUIBundle\Component;

use Bundle\fv\MetroUIBundle\Component\ModulesList as ModulesListScope;
use \fv\ViewModel\ViewModel;

class ModulesList extends ViewModel{
    function __construct(){
        $this
            ->land( "SearchBar", new ModulesListScope\SearchBar() )
            ->land( "ModulesSwitcher", new ModulesListScope\ModulesSwitcher() );
    }


    protected function getLandingPlaces(){
        return [
            "SearchBar",
            "ModulesSwitcher"
        ];
    }
}
