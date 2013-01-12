<?php

namespace fv\Connection\Database\Generator\Column;

class IntColumnDefinition extends DefaultColumnDefinition
{
    function __toString()
    {
        return "INT(11) default NULL";
    }

}
