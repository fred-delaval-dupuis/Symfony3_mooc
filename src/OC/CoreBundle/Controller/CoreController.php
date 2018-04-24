<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CoreController extends Controller
{
    /**
     * Affiche la page d'accueil (template 'OCCoreBundle:Core:index.html.twig')
     * Répond à la route oc_core_home '/'
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction() {
        return $this->render('OCCoreBundle:Core:index.html.twig');
    }

    /**
     * Affiche la page de contact
     * Répond à la route oc_core_contact '/contact'
     * Pour le moment, redirige vers l'accueil avec un message flash
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function contactAction(Request $request) {
//        Ajout du message flash

//        legacy code
//        $request->getSession()->getFlashbag()->add('notice', "La page de contact n'est pas encore disponible, merci de revenir plus tard.");

        /** @see https://symfony.com/doc/current/controller.html#flash-messages      $this->addFlash() est l'équivalent de $request->getSession()->getFlashBag()->add() */
        $this->addFlash('notice', "La page de contact n'est pas encore disponible, merci de revenir plus tard.");

//        Redirection vers l'accueil
        return $this->redirectToRoute('oc_core_home');
    }
}
