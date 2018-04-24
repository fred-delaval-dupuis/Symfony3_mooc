<?php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Event\MessagePostEvent;
use OC\PlatformBundle\Event\PlatformEvents;
use OC\PlatformBundle\Form\AdvertEditType;
use OC\PlatformBundle\Form\AdvertType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;


class AdvertController extends Controller
{
    /**
     * Affiche une liste d'annonce
     * Action répondant à la route oc_platform_home
     *
     * @param $page Le numéro de la page à afficher (entier >= 1)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page)
    {
        if($page < 1) {
            throw new NotFoundHttpException("La page $page n'existe pas.");
        }

        $nbPerPage = 3;

        $listAdverts = $this->getDoctrine()->getManager()->getRepository('OCPlatformBundle:Advert')->getAdverts($page, $nbPerPage);

        $nbPages = ceil(count($listAdverts) / $nbPerPage);

        if($page > $nbPages) {
            throw new NotFoundHttpException("La page $page n'existe pas.");
        }

        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
            'listAdverts'   => $listAdverts,
            'page'          => $page,
            'nbPages'       => $nbPages,
        ));
    }

    /**
     * Affiche l'annonce d'id $id
     * Action répondant à la route oc_platform_view
     *
     * @param $id L'id de l'annonce à afficher
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id)
    {
//        On récupère l'entityManager
        $em = $this->getDoctrine()->getManager();

//        On récupère l'entité correspondante à l'id $id
//        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
        $advert = $em->getRepository('OCPlatformBundle:Advert')->getAdvertById($id);

//        $advert est donc une instance de OC\PlatformBundle\Entity\Advert
//        ou null si l'id $id  n'existe pas, d'où ce if :
        if(null === $advert) {
            throw new NotFoundHttpException("L'annonce #$id n'existe pas");
        }

//        On récupère la liste des candidatures de cette annonce
        $listApplications = $em
            ->getRepository('OCPlatformBundle:Application')
//            ->findBy(array('advert' => $advert))
            ->getApplicationsByAdvert($advert)
        ;

//        On récupère la liste des AdvertSkill
        $listAdvertSkills = $em
            ->getRepository('OCPlatformBundle:AdvertSkill')
//            ->findBy(array('advert' => $advert))
            ->getAdvertSkillsByAdvert($advert)
        ;

        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
            'advert'            => $advert,
            'listApplications'  => $listApplications,
            'listAdvertSkills'  => $listAdvertSkills,
        ));
    }

    /**
     * Crée une nouvelle annonce
     * Action répondant à la route oc_platform_add
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction(Request $request)
    {
//        On crée un objet Advert
        $advert = new Advert();

//        Ici, on préremplit avec la date d'aujourd'hui, par exemple
//        Cette date sera donc préaffichée dans le formulaire, cela facilite le travail de l'utilisateur
        $advert->setDate(new \Datetime());

        $form = $this->createForm(AdvertType::class, $advert);

//        Si la requête est en POST, c'est que le visiteur a soumis le formulaire
        if ($request->isMethod('POST') && $form->handleRequest()->isValid()) {

//            On crée l'événement avec ses 2 arguments
            $event = new MessagePostEvent($advert->getContent(), $advert->getUser());

//            On déclenche l'événement
            $this->get('event_dispatcher')->dispatch(PlatformEvents::POST_MESSAGE, $event);

//            On récupère ce qui a été modifié par le ou les listeners, ici le message
            $advert->setContent($event->getMessage());

//            On enregistre notre objet $advert dans la base de données, par exemple
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            $this->addFlash('notice', 'Annonce bien enregistrée.');

//            Puis on redirige vers la page de visualisation de cettte annonce
            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }

//        À ce stade, le formulaire n'est pas valide car :
//        - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
//        - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau

        return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Modifie une annonce
     * Action répondant à la route oc_platform_edit
     *
     * @param $id L'id de l'annonce à modifier
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if(null === $advert) {
            throw new NotFoundHttpException("L'annonce #$id n'existe pas.");
        }

        $form = $this->createForm(AdvertEditType::class, $advert);

//        Même mécanisme que pour l'ajout
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
//            Pas besoin de persister, l'entité a été chargée depuis le repository Doctrine
            $em->flush();

            $this->addFlash('notice', 'Annonce bien modifiée.');

            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }

        return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
            'advert'    => $advert,
            'form'      => $form->createView(),
        ));
    }

    /**
     * Supprime l'annonce d'id $id
     * Action répondant à la route oc_platform_delete
     *
     * @param $id L'id de l'annonce à supprimer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if(null === $advert) {
            throw new NotFoundHttpException("L'annonce #$id n'existe pas.");
        }

        $form = $this->get('form.factory')->create();

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->remove($advert);
            $em->flush();

            $this->addFlash('notice', "L'annonce a bien été supprimée.");

            return $this->redirectToRoute('oc_platform_home');
        }

        return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(
            'advert'    => $advert,
            'form'      => $form->createView(),
        ));
    }

    /**
     * Affiche les $limit dernières annonces dans le menu
     * Est appelée depuis OCPlatformBundle::layout.html.twig
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function menuAction($limit)
    {

        if($limit < 1) {
            throw new \Exception("Le nombre d'annonces à afficher doit être > 0.");
        }

        $em = $this->getDoctrine()->getManager();

        $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->findBy(
            array(),
            array('date' => 'desc'),
            $limit,
            0
        );

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
            'listAdverts' => $listAdverts,
        ));
    }

    /**
     * Supprime les annonces n'ayant aucune candidature et dont la date est dépassée de $days jours.
     * Répond à la route 'oc_platform_purge'
     *
     * @uses OC\PlatformBundle\Purger\AdvertPurger
     *
     * @param int $days Le nombre de jours à partir desquels supprimer les annonces
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function purgeAction($days)
    {
//        Récupération du service
        $advertPurger = $this->get('oc_platform.purger.advert');

//        Exécution de la méthode purge
        $nbPurgedAdverts = $advertPurger->purge($days);

//        Ajout d'un message flash avec le nombre d'annonces purgées
        $dateMsg = "de plus de $days jours";
        if($nbPurgedAdverts <= 1) {
            $msg = 'annonce ' . $dateMsg . ' a été supprimée';
        } else {
            $msg = 'annonces ' . $dateMsg . ' ont été supprimées';
        }
        $this->addFlash('notice', "Purge : $nbPurgedAdverts $msg.");

//        Redirection vers l'accueil
        return $this->redirectToRoute('oc_platform_home');
    }
}