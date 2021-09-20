<?php

namespace ContinuezLHistoire\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContinuezLHistoireUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
