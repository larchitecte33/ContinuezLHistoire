<?php

namespace ContinuezLHistoire\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationConfirmListener implements EventSubscriberInterface {
    
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
                FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess'
        );
    }

    public function onRegistrationSuccess(FormEvent $event)
    { 
        $url = $this->router->generate('continuezlhistoiresite_attributiondroitauteur');

        $event->setResponse(new RedirectResponse($url));
    }
}
