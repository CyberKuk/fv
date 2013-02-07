<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 05.02.13
 * Time: 13:22
 */

namespace Bundle\fv\SessionBundle\Handler;
use fv\Config\Configurable;

class DefaultSessionHandler extends \SessionHandler  implements Configurable {
    static function build(\fv\Collection\Collection $config) {
        return new static;
    }
}
