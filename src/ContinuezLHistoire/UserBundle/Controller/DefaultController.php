<?php

namespace ContinuezLHistoire\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ContinuezLHistoireUserBundle:Default:index.html.twig', array('name' => $name));
    }
}
