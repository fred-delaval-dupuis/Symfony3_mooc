<?php

namespace OC\PlatformBundle\Beta;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener
{
    protected $betaHTML;

    protected $endDate;

    public function __construct(BetaHTMLAdder $betaHTML, $endDate)
    {
        $this->betaHTML = $betaHTML;
        $this->endDate = new \DateTime($endDate);
    }

    public function processBeta(FilterResponseEvent $event)
    {
//        On teste si la requête est bien la requête principale (et non une sous-requête)
        if( ! $event->isMasterRequest() ) {
            return;
        }

        $remainingDays = $this->endDate->diff(new \DateTime())->days;

//        Si la date est dépassée, on ne fait rien
        if($remainingDays <= 0) {
            return;
        }

//        On utilise le service BetaHTML
        $response = $this->betaHTML->addBeta($event->getResponse(), $remainingDays);

//        On met à jour la réponse avec la nouvelle valeur
        $event->setResponse($response);
    }
}