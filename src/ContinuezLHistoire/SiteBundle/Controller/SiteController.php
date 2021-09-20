<?php

namespace ContinuezLHistoire\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ContinuezLHistoire\SiteBundle\Entity\Histoire;
use ContinuezLHistoire\SiteBundle\Form\HistoireType;
use ContinuezLHistoire\SiteBundle\Form\ChoixUtilisateurType;
use ContinuezLHistoire\SiteBundle\Form\DroitsType;
use ContinuezLHistoire\SiteBundle\Form\ChoixCommentaireType;
use ContinuezLHistoire\SiteBundle\Form\ImageType;
use ContinuezLHistoire\SiteBundle\Entity\SousHistoire;
use ContinuezLHistoire\SiteBundle\Entity\AvisUtilisateur;
use ContinuezLHistoire\SiteBundle\Entity\Note;
use ContinuezLHistoire\SiteBundle\Entity\Client;
use ContinuezLHistoire\SiteBundle\Entity\Image;
use JMS\SecurityExtraBundle\Annotation\Secure;
use ContinuezLHistoire\UserBundle\Entity\User;
use ContinuezLHistoire\SiteBundle\Entity\HistoireRepository;
use ContinuezLHistoire\SiteBundle\Entity\MessageContact;
use ContinuezLHistoire\SiteBundle\Form\MessageContactType;
 
class SiteController extends Controller
{
    // Fonction qui renvoie les roles d'un utilisateur
    public function getRoles() {
        $roles = array();
         
        foreach ($this->container->getParameter('security.role_hierarchy.roles') as $name => $rolesHierarchy) {
            $roles[$name] = $name;
         
            foreach ($rolesHierarchy as $role) {
                if (!isset($roles[$role])) {
                    $roles[$role] = $role;
                }
            }
        }
         
        return $roles;
    }
    
    // Fonction qui permet d'afficher la page d'acceuil
    public function indexAction()
    {      
        date_default_timezone_set('Europe/Paris');
        
        $user = $this->getUser();
        
        // Ajout droit d'admin
        /*$user->addRole('ROLE_ADMIN');
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();*/
        
        $histoires = $this->getDoctrine()
                          ->getManager()
                          ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                          ->findBy(array('estClos' => false,
                                         'actif' => true));
        
        $notes = $this->getDoctrine()
                      ->getManager()
                      ->getRepository('ContinuezLHistoireSiteBundle:Note')
                      ->findAll();
        
        $clientsQuiViennentDeSInscrire = $this->getDoctrine()
                                              ->getManager()
                                              ->getRepository('ContinuezLHistoireSiteBundle:Client')
                                              ->findBy(array('vientDeSInscrire' => true));
        
        $vientDeSInscrire = false;
        
        $this->rechercherHistoiresTerminees($histoires);
  
        foreach ($clientsQuiViennentDeSInscrire as $client)
        {
            if ($client->getAddresseClient() == $_SERVER["REMOTE_ADDR"])
            {
                $client->setVientDeSInscrire(false);
                $em = $this->getDoctrine()->getManager();
                $em->persist($client);
                $em->flush();
                $vientDeSInscrire = true;
            }
        }
        
        return $this->render('ContinuezLHistoireSiteBundle:Site:index.html.twig', array(
            'histoires' => $histoires,
            'notes' => $notes,
            'vientDeSInscrire' => $vientDeSInscrire,
            'typemodification' => 1,
            'user' => $user,
        ));
    }
    
    // Fonction qui redirige l'utilisateur vers l'accueil
    public function redirigerVersAccueilAction()
    {
        return $this->redirect('app.php/accueil');
    }
  
    // Fonction qui permet d'afficher une histoire
    public function voirAction($id, $modificationencours, $typemodification)
    {
        $isModeEdition = false;
        
        $histoire = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                         ->findById($id);
					 
	if ($histoire === array()) {
            throw $this->createNotFoundException('Histoire[id='.$id.'] inexistante.');
        }
        else if ($histoire[0] === null)
            throw $this->createNotFoundException('Histoire[id='.$id.'] inexistante.');
        else if ($histoire[0]->getActif() == false)
            throw $this->createNotFoundException ('Histoire non disponible.');
        else
        {
            $listeSousHistoires = $this->getDoctrine()
                                       ->getManager()
                                       ->getRepository('ContinuezLHistoireSiteBundle:SousHistoire')
                                       ->findBy(array('histoire' => $id,
                                                      'actif' => true));
            
            $avisUtilisateur = new AvisUtilisateur();
            $note = new Note();
            
            $form = $this->createForm(new ChoixCommentaireType(), $avisUtilisateur);
            
            $notesExistantes = $this->getDoctrine()
                                    ->getManager()
                                    ->getRepository('ContinuezLHistoireSiteBundle:Note')
                                    ->findByUser($this->getUser());
            
            /*$listeCommentaire = $this->getDoctrine()
                                    ->getManager()
                                    ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                                    ->findOneByCommentaireUtilisateur($id, $this->getUser()->getId());*/
            
            $avis = '';
            foreach ($notesExistantes as $note) {
              if($note->getHistoire()->getId() == $id)
              {
                  $avis = $note->getAvisUtilisateur()->getLibelleAvis();
              }
            }
            
            $request = $this->get('request');
            
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                 
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    
                    foreach ($notesExistantes as $notesExistante) {
                        if ($notesExistante->getHistoire()->getId() == $histoire[0]->getId())
                        {
                            $em->remove($notesExistante);
                        }
                    }
                    
                    $note->setAvisUtilisateur($avisUtilisateur->getLibelleAvis());
                    $note->setHistoire($histoire[0]);
                    $note->setUser($this->getUser());
                    
                    
                    $em->persist($note);
                    $em->flush();
                    
                    return $this->redirect($this->generateUrl('continuezlhistoiresite_voir', array('id' => $id, 'typemodification' => $typemodification)));
                }
            }
            
            return $this->render('ContinuezLHistoireSiteBundle:Site:voir.html.twig', array(
                'histoire' => $histoire,
                'listeSousHistoires' => $listeSousHistoires,
                'isModeEdition' => $isModeEdition,
                'modificationencours' => $modificationencours,
                'form' => $form->createView(),
                'typemodification' => $typemodification,
                'avis' => $avis,
            ));
        }
    }
  
    // Fonction qui permet d'ajouter une histoire
    public function ajouterAction()
    {
        if ((false === $this->get('security.context')->isGranted('ROLE_AUTEUR')) && !(isset($_SESSION['connecte']) && $_SESSION['connecte'] == true)) {
            throw new AccessDeniedException();
        }
        else{
            $auteur = $this->getUser();
            $imageValide = false;
            $message = '';
            
            $histoire = new Histoire;
            $histoire->setEdite(false);
            $histoire->setDebutEdition(new \DateTime());
            $histoire->setEditeur($this->getUser());
            $histoire->setDernierAuteur($this->getUser());
            $histoire->setPremierAuteur($this->getUser());
            
            $form = $this->createForm(new HistoireType(), $histoire);

            $request = $this->get('request');
            
            if ($request->getMethod() == 'POST') {
                
                $form->bind($request);
                
                if ($form->isValid()) {
                    
                    $finfo = finfo_open(FILEINFO_MIME_TYPE); // Retourne le type mime à la extension mimetype
                    $histoire->setActif(true);
                    
                    if($histoire->getImage() != NULL)
                    {
                        if((finfo_file($finfo, $histoire->getImage()->getFile()) == 'image/png')
                          || (finfo_file($finfo, $histoire->getImage()->getFile()) == 'image/jpg')
                          || (finfo_file($finfo, $histoire->getImage()->getFile()) == 'image/jpeg')      
                          || (finfo_file($finfo, $histoire->getImage()->getFile()) == 'image/bmp')
                          || (finfo_file($finfo, $histoire->getImage()->getFile()) == 'image/gif'))
                        {
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($histoire);
                            $em->flush();
                            $auteur->setNbPoints($auteur->getNbPoints() + 1);
                            $em->persist($auteur);
                            $em->flush($auteur);
                            $imageValide = true;
                        }
                    }
                    else
                    {
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($histoire);
                        $em->flush();
                        $auteur->setNbPoints($auteur->getNbPoints() + 1);
                        $em->persist($auteur);
                        $em->flush($auteur);
                        $imageValide = true;
                    }
                    
                    finfo_close($finfo);

                    if($imageValide)
                        return $this->redirect($this->generateUrl('continuezlhistoiresite_accueil'));
                    else
                        $message = 'Image non valide';
                }
            }

            return $this->render('ContinuezLHistoireSiteBundle:Site:ajouter.html.twig', array(
                'form' => $form->createView(),
                'imageValide' => $imageValide,
                'message' => $message,
            ));
        }
    }
    
    // Fonction qui permet à un administrateur de choisir un utilisateur pour gérer ses droits
    public function choixutilisateurAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('Accès limité au super admin.');
        }
        else {
            $user = new User();

            $form = $this->createForm(new ChoixUtilisateurType(), $user);

            $request = $this->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {
                    $utilisateur = $this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('ContinuezLHistoireUserBundle:User')
                                 ->findByUsername(''.$user->getUsername());

                    return $this->redirect($this->generateUrl('continuezlhistoiresite_droits', array('id' => $utilisateur[0]->getId())));
                }
            }

            return $this->render('ContinuezLHistoireSiteBundle:Site:choixutilisateur.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }
    
    // Fonction qui permet de gérer les droits d'un utilisateur
    public function droitsAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('Accès limité au super admin.');
        }
        else {
            $utilisateur = $this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('ContinuezLHistoireUserBundle:User')
                                 ->findById($id);

            $user = new User();

            foreach ($utilisateur[0]->getRoles() as $role) {
                $user->addRole($role);
            }

            $form = $this->createForm(new DroitsType(), $user);

            $request = $this->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {


                    // On supprime d'abord tous les droits que possède l'utilisateur
                    foreach ($this->getRoles() as $role)
                    {
                        if ($utilisateur[0]->hasRole($role))
                        {
                            $utilisateur[0]->removeRole($role);
                        }
                    }

                    // On ajoute ensuite tous les droits cohés à l'utilisateur
                    foreach ($user->getRoles() as $role)
                    {
                        $utilisateur[0]->addRole($role);
                    }

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($utilisateur[0]);
                    $em->flush();

                    return $this->redirect($this->generateUrl('continuezlhistoiresite_choixutilisateur'));
                }
            }

            return $this->render('ContinuezLHistoireSiteBundle:Site:droits.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }
    
    // Fonction qui permet d'accorder les droits d'auteur à un utilisateur qui vient de s'inscrire
    // et qui lui envoie aussi son code d'activation par mail
    public function attributionDroitAuteurAction()
    {
        $user = $this->getUser();
        
        if ($user->getDroitAuteurAttribue() == 'O')
          throw new AccessDeniedException();
        else
        {
            $client = new Client();

            $codeActivation = mt_rand(1000000, 10000000);

            $user->setCodeActivation($codeActivation);

            $em = $this->getDoctrine()->getManager();
            if(!$user->hasRole('ROLE_AUTEUR'))
            {
                $user->addRole('ROLE_AUTEUR');
                $user->setEnabled(false);

                $em->persist($user);
                $em->flush();
            }

            $client->setAddresseClient($_SERVER["REMOTE_ADDR"]);
            $client->setVientDeSInscrire(true);
            $em->persist($client);
            $user->setDroitAuteurAttribue('O');
            $em->persist($user);
            $em->flush();

            /*$message = \Swift_Message::newInstance()
                ->setSubject('Activation compte Continuez L\'Histoire')
                ->setFrom('send@example.com')
                ->setTo($user->getEmail())
                ->setBody('Bonjour ' . $user->getUsername() . '. Pour activer votre compte, veuillez cliquer sur le lien "Valider mon compte" sur la page d\'accueil du site puis entrer votre login ainsi que le code d\'activation suivant : ' . $codeActivation);

            $this->get('swiftmailer.mailer.default')->send($message);*/
            
            \ini_set('SMTP', 'smtp.continuezlhistoire.fr');
                    
            $message = 'Bonjour ' . $user->getUsername() . '. Pour activer votre compte, veuillez cliquer sur le lien "Valider mon compte" sur la page d\'accueil du site puis entrer votre login ainsi que le code d\'activation suivant : ' . $codeActivation;
                    
            $message = \wordwrap($message, 70, "\r\n");
                    
            mail($user->getEmail(), 'Message de ' . 'admin@continuezlhistoire.fr', $message);

            return $this->redirect($this->generateUrl('continuezlhistoiresite_logout'));
        }
    }
    
    public function modifierSousHistoireAction($id, $typemodification)
    {
        $sousHistoire = $this->getDoctrine()
                             ->getManager()
                             ->getRepository('ContinuezLHistoireSiteBundle:SousHistoire')
                             ->findById($id);
        
        $auteurAnonyme = $this->getDoctrine()
                              ->getManager()
                              ->getRepository('ContinuezLHistoireUserBundle:User')
                              ->findOneBy(array('username' => 'anonymous'));
        
        if ($sousHistoire == array()) {
            throw $this->createNotFoundException('Sous-histoire[id='.$id.'] inexistante.');
        }
        else if ((false === $this->get('security.context')->isGranted('ROLE_AUTEUR')) || !(($sousHistoire[0]->getAuteur() == $this->getUser()) || ($sousHistoire[0]->getAuteur() == $auteurAnonyme))) {
            throw new AccessDeniedException();
        }
        else if ($sousHistoire[0]->getActif() == false) {
            throw new AccessDeniedException();
        }
        else
        {
            $user = $this->getUser();  

            $histoire = $this->getDoctrine()
                             ->getManager()
                             ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                             ->findById($sousHistoire[0]->getHistoire());

            $listeSousHistoires = $this->getDoctrine()
                                       ->getManager()
                                       ->getRepository('ContinuezLHistoireSiteBundle:SousHistoire')
                                       ->findByHistoire($histoire);

            $form = $this->createFormBuilder($sousHistoire[0])
                         ->add('contenu',     'textarea', array('attr' => array('cols' => '20', 'rows' => '10', 'class' => 'tinymce')))
                         ->getForm();
            
            $request = $this->get('request');
	
            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid()) {
                    if ($sousHistoire[0]->getAuteur() == $auteurAnonyme)
                    {
                        $sousHistoire[0]->setAuteur($this->getUser());
                        $user->setNbPoints($user->getNbPoints() + 1);
                    }
                    
                    $sousHistoire[0]->setContenu(substr_replace($sousHistoire[0]->getContenu(), ' class="enLigne"', 2, 0));
                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($sousHistoire[0]);
                    $em->persist($user);
                    $em->flush();
                    
                    return $this->redirect($this->generateUrl('continuezlhistoiresite_voir', array('id' => $sousHistoire[0]->getHistoire()->getId(),
                                                                                                    'typemodification' => $typemodification)));
                }
            }
        }
        
        return $this->render('ContinuezLHistoireSiteBundle:Site:modifiersoushistoire.html.twig', array(
            'form' => $form->createView(),
            'listeSousHistoires' => $listeSousHistoires,
            'sousHistoire' => $sousHistoire[0],
            'histoire' => $histoire[0],
            'typemodification' => $typemodification
        ));
    }
    
    public function modifierAction($id, $typemodification)
    {
        $histoire = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                         ->find($id);
        
        //$this->libererEdition($id);
        
        $auteur = $this->getUser();
        
        if (false === $this->get('security.context')->isGranted('ROLE_AUTEUR')) {
            throw new AccessDeniedException();
        }
        else if ($histoire == null) {
            throw $this->createNotFoundException('Histoire[id='.$id.'] inexistante.');
        }
        else if ($histoire->getActif() == false)
            throw $this->createNotFoundException ('Histoire non disponible.');
        else if ($histoire != null)
        {
            $this->libererEdition($histoire->getId());
            
            if ($histoire->getEdite() && ($histoire->getEditeur() != $auteur))
            {
                return $this->redirect($this->generateUrl('continuezlhistoiresite_voir', array('id' => $id, 'modificationencours' => 1, 'typemodification' => $typemodification)));
            }
            else
            {
                $listeSousHistoires = $this->getDoctrine()
                                           ->getManager()
                                           ->getRepository('ContinuezLHistoireSiteBundle:SousHistoire')
                                           ->findBy(array('histoire' => $histoire,
                                                    'actif' => true));

                $sousHistoire = new SousHistoire();

                $form = $this->createFormBuilder($sousHistoire)
                             ->add('contenu', 'textarea', array('attr' => array('cols' => '20', 'rows' => '10', 'class' => 'tinymce')))
                             ->getForm();

                
                if(isset($auteur) && !($histoire->getEstClos()) && !$histoire->getEdite()){
                    $now = date("Y-m-d H:i:s");
                    $histoire->setEdite(true);
                    $histoire->setDebutEdition(new \DateTime( $now ));
                    $histoire->setEditeur($auteur);
                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($histoire);
                    $em->flush();
                    // On écrase la valeur précédente si elle existe
                    $_SESSION['page_edite'] = $id;
                }

                $request = $this->get('request');

                if ($request->getMethod() == 'POST') {
                    
                    $form->bind($request);

                    if ($form->isValid() && strlen($sousHistoire->getContenu()) <= 255) {
                       // throw new AccessDeniedException();
                        if ($auteur != null)
                        {
                            if ($auteur->hasRole('ROLE_AUTEUR') || $auteur->hasRole('ROLE_ADMIN') || $auteur->hasRole('ROLE_SUPER_ADMIN')) 
                            {
                                $histoire->setDernierAuteur($auteur);
                                $sousHistoire->setContenu(substr_replace($sousHistoire->getContenu(), ' class="enLigne"', 2, 0));
                                $sousHistoire->setHistoire($histoire);
                                $sousHistoire->setDate(new \DateTime("now"));
                                $sousHistoire->setPlace(count($listeSousHistoires));
                                $sousHistoire->setAuteur($auteur);
                                $sousHistoire->setActif(true);
                                $sousHistoire->integrerSautsDeLigne();
                                //$sousHistoire->setContenu($form->getData())
                                $em = $this->getDoctrine()->getManager();
                                $em->persist($histoire);
                                $em->persist($sousHistoire);
                                $em->flush();
                                
                                $auteur->setNbPoints($auteur->getNbPoints() + 1);
                                $em->persist($auteur);
                                $em->flush();
                            }
                        }


                        $histoire->setEdite(false);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($histoire);
                        $em->flush();

                        return $this->redirect($this->generateUrl('continuezlhistoiresite_voir', array('id' => $id, 'typemodification' => $typemodification)));
                    }
                    else    
                        echo 'La sous histoire dépasse la taille limite <br/>';
                }

                return $this->render('ContinuezLHistoireSiteBundle:Site:modifierhistoire.html.twig', array(
                    'form' => $form->createView(),
                    'listeSousHistoires' => $listeSousHistoires,
                    'histoire' => $histoire,
                    'typemodification' => $typemodification,
                ));
            }
        }
    }
    
    public function libererEdition($histoireId)
    {
        $histoire = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                         ->findById($histoireId);
        
        if ($histoire[0] <> null)
        {
            $dateFinEdition = $histoire[0]->getDebutEdition()->add(new \DateInterval('PT2M'));
            $maintenant = \DateTime::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s"));
            //$intervalle = $maintenant->getTimestamp() - $dateFinEdition->getTimestamp();

            if($histoire[0]->getEdite() && ($maintenant > $dateFinEdition))
            {
                $histoire[0]->setEdite(false);
                $em = $this->getDoctrine()->getManager();
                $em->persist($histoire[0]);
                $em->flush();
            }
        }
    }
    
    public function deconnexionAction()
    {
        $histoires = $this->getDoctrine()
                          ->getEntityManager()
                          ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                          ->findByEditeur($this->getUser());
        
        if(!empty($histoires))
            foreach ($histoires as $histoire)
            {
                if($histoire->getEdite())
                {
                    $histoire->setEdite(false);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($histoire);
                    $em->flush();
                }
            }
        
        return $this->redirect($this->generateUrl('fos_user_security_logout'));
    }
    
    public function stopperEditionAction($id, $typemodification)
    {
        $user = $this->getUser();
        
        $histoire = $this->getDoctrine()
                             ->getManager()
                             ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                             ->find($id);
        
        if ($user != $histoire->getEditeur())
          throw new AccessDeniedException();
        else {
            $histoire->setEdite(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($histoire);
            $em->flush();

            return $this->redirect($this->generateUrl('continuezlhistoiresite_voir', array('id' => $id, 'typemodification' => $typemodification)));
        }
    }
    
    public function supprimerSousHistoireAction($id, $typemodification)
    {
        $sousHistoire = $this->getDoctrine()
                             ->getManager()
                             ->getRepository('ContinuezLHistoireSiteBundle:SousHistoire')
                             ->findById($id);
        
        if ($sousHistoire == array()) {
            throw $this->createNotFoundException('Sous-histoire[id='.$id.'] inexistante.');
        }
        else if ($sousHistoire[0]->getAuteur() != $this->getUser()) {
            throw new AccessDeniedException();
        }
        else
        {
            $histoire = $sousHistoire[0]->getHistoire();
            
            $derniereSousHistoire = $this->getDoctrine()
                                         ->getManager()
                                         ->getRepository('ContinuezLHistoireSiteBundle:SousHistoire')
                                         ->findBy(array('histoire' => $histoire),
                                                  array('id' => 'desc'),
                                                  1,
                                                  0);

            $auteurAnonyme = $this->getDoctrine()
                                  ->getManager()
                                  ->getRepository('ContinuezLHistoireUserBundle:User')
                                  ->findOneBy(array('username' => 'anonymous'));

            $user = $this->getUser();

            // On crée un formulaire vide, qui ne contiendra que le champ CSRF
            // Cela permet de protéger la suppression d'article contre cette faille
            $form = $this->createFormBuilder()->getForm();

            $request = $this->getRequest();
            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid()) {
                    // On supprime la sous-histoire
                    $em = $this->getDoctrine()->getManager();
                    if ($sousHistoire[0] == $derniereSousHistoire[0])
                    {
                        $sousHistoire[0]->setActif(false);
                        $em->persist($sousHistoire[0]);
                        $em->flush();
                    }
                    else
                    {
                        $sousHistoire[0]->setContenu("[...]");
                        $sousHistoire[0]->setAuteur($auteurAnonyme);
                        $em->persist($sousHistoire[0]);
                        $em->flush();
                    }

                    $user->setNbPoints($user->getNbPoints() - 1);
                    $em->persist($user);
                    $em->flush();

                    // On définit un message flash
                    $this->get('session')->getFlashBag()->add('info', 'Sous-histoire bien supprimé');

                    // Puis on redirige vers l'accueil
                    return $this->redirect($this->generateUrl('continuezlhistoiresite_accueil'));
                }
            }

            // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
            return $this->render('ContinuezLHistoireSiteBundle:Site:supprimersoushistoire.html.twig', array(
              'form' => $form->createView(),
              'sousHistoire' => $sousHistoire[0],
              'histoire' => $histoire,
              'typemodification' => $typemodification
            ));
        }
    }
    
    public function cloturerAction($id, $typemodification)
    {
        $histoire = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                         ->findById($id);
        
        if (false === $this->get('security.context')->isGranted('ROLE_AUTEUR')) {
            throw new AccessDeniedException();
        }
        else if ($histoire == null) {
            throw $this->createNotFoundException('Histoire[id='.$id.'] inexistante.');
        }
        else if ($histoire != null)
        {
            if ($histoire[0]->getEstClos())
            {
                return $this->render('ContinuezLHistoireSiteBundle:Site:cloturerimpossible.html.twig', array(
                    'histoire' => $histoire[0],
                    'typemodification' => $typemodification,
                ));
            }
            else if ($histoire[0]->getEstEnInstanceDeCloture())
            {
                return $this->render('ContinuezLHistoireSiteBundle:Site:cloturerdemandeimpossible.html.twig', array(
                    'histoire' => $histoire[0],
                    'typemodification' => $typemodification,
                ));
            }
            if (!($histoire[0]->getEstEnInstanceDeCloture() || $histoire[0]->getEstClos()))
            {
                $now = date("y-m-d h:i:s");
                $histoire[0]->setDateCloture(new \DateTime($now));

                $dateFin = date('y-m-d h:i:s', strtotime($now. ' + 3 days'));
                $histoire[0]->setDateClotureEffective(new \DateTime($dateFin));

                $histoire[0]->setEstEnInstanceDeCloture(true);

                $em = $this->getDoctrine()->getManager();
                $em->persist($histoire[0]);
                $em->flush();
            
                return $this->render('ContinuezLHistoireSiteBundle:Site:cloturer.html.twig', array(
                    'histoire' => $histoire[0],
                    'typemodification' => $typemodification
                ));
            }
        }
    }
    
    public function decloturerAction($id, $typemodification)
    {
        $histoire = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                         ->findById($id);
        
        if (false === $this->get('security.context')->isGranted('ROLE_AUTEUR')) {
            throw new AccessDeniedException();
        }
        else if ($histoire == null) {
            throw $this->createNotFoundException('Histoire[id='.$id.'] inexistante.');
        }
        else if ($histoire != null)
        {
            if ($histoire[0]->getEstClos())
            {
                return $this->render('ContinuezLHistoireSiteBundle:Site:decloturerimpossible.html.twig', array(
                    'histoire' => $histoire[0],
                    'typemodification' => $typemodification,
                ));
            }
            else if ($histoire[0]->getEstEnInstanceDeCloture() == false)
            {
                return $this->render('ContinuezLHistoireSiteBundle:Site:decloturerdemandeimpossible.html.twig', array(
                    'histoire' => $histoire[0],
                    'typemodification' => $typemodification,
                ));
            }   
            else
            {
                $histoire[0]->setEstEnInstanceDeCloture(false);
            
                $em = $this->getDoctrine()->getManager();
                $em->persist($histoire[0]);
                $em->flush();
                
                return $this->render('ContinuezLHistoireSiteBundle:Site:decloturer.html.twig', array(
                    'histoire' => $histoire[0],
                    'typemodification' => $typemodification
                ));
            }
        }
    }
    
    public function rechercherHistoiresTerminees($histoires)
    {
        $dateActuelle = new \DateTime();
        foreach ($histoires as $histoire) {
            if ($histoire->getEstEnInstanceDeCloture())
            {
                $dateCloture = $histoire->getDateClotureEffective();
                $dateActuelle = $dateActuelle->format('Ymdhis');
                $dateCloture = $dateCloture->format('Ymdhis');
                
                if ($dateActuelle >= $dateCloture)
                {
                    if (!$histoire->getEstClos())
                    {
                        $histoire->setEstClos(true);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($histoire);
                        $em->flush();
                    }
                }
            }
        }
    }
    
    public function consulterListeAuteursAction($id, $typemodification)
    {
        $histoire = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                         ->find($id);
        
        if ($histoire == array())
            throw $this->createNotFoundException('Histoire[id='.$id.'] inexistante.');
        else
        {
            $sousHistoires = $this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('ContinuezLHistoireSiteBundle:SousHistoire')
                                 ->findByHistoire($histoire);

            $listeAuteurs = array();

            $listeAuteurs[] = $histoire->getPremierAuteur();

            foreach ($sousHistoires as $sousHistoire) {
                if (!in_array($sousHistoire->getAuteur(), $listeAuteurs))
                {
                    $listeAuteurs[] = $sousHistoire->getAuteur();
                }
            }

            return $this->render('ContinuezLHistoireSiteBundle:Site:consulterlisteauteurs.html.twig', array(
                'listeAuteurs' => $listeAuteurs,
                'histoire' => $histoire,
                'typemodification' => $typemodification    
            ));
        }
    }
    
    public function gererProfilAction($id)
    {
        $user = $this->getDoctrine()
                     ->getManager()
                     ->getRepository('ContinuezLHistoireUserBundle:User')
                     ->find($id);
        
        $image = new Image();
        $message = '';
        
        if ($user != $this->getUser())
        {
            throw new AccessDeniedException();
        }
        else
        {
            $form = $this->createForm(new ImageType(), $image);
            
            $request = $this->getRequest();
            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid()) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE); // Retourne le type mime à la extension mimetype
                    $imageValide = false;

                    if((finfo_file($finfo, $image->getFile()) == 'image/png')
                      || (finfo_file($finfo, $image->getFile()) == 'image/jpg')
                      || (finfo_file($finfo, $image->getFile()) == 'image/jpeg')      
                      || (finfo_file($finfo, $image->getFile()) == 'image/bmp')
                      || (finfo_file($finfo, $image->getFile()) == 'image/gif'))
                    {
                        $imageValide = true;
                        $em = $this->getDoctrine()->getManager();
                        $user->setImageProfil($image);
                        $em->persist($user);
                        $em->flush();    
                    }
                    
                    finfo_close($finfo);
                
                    if(!$imageValide)
                        $message = 'Format d\'image icorrect';
                }
            }
            
            return $this->render('ContinuezLHistoireSiteBundle:Site:gererprofil.html.twig', array(
                'user' => $user,
                'form' => $form->createView(),
                'message' => $message,
            ));
        }   
    }
    
    public function testBootstrapAction()
    {
        return $this->render('ContinuezLHistoireSiteBundle:Site:testbootstrap.html.twig');
    }
    
    public function consulterHistoiresTermineesAction()
    {
        $user = $this->getUser();
        
        $listeHistoires = $this->getDoctrine()
                                   ->getManager()
                                   ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                                   ->findBy(array('estClos' => true));
        
        $notes = $this->getDoctrine()
                      ->getManager()
                      ->getRepository('ContinuezLHistoireSiteBundle:Note')
                      ->findAll();
        
        return $this->render('ContinuezLHistoireSiteBundle:Site:consulterhistoiresterminees.html.twig', array(
            'histoires' => $listeHistoires,
            'notes' => $notes,
            'typemodification' => 2,
            'user' => $user
        ));
    }
    
    public function infosAuteurAction()
    {
        return $this->render('ContinuezLHistoireSiteBundle:Site:infosauteur.html.twig');
    }
    
    public function statistiquesSiteAction()
    {
        $nombreMembres = $this->getDoctrine()
                              ->getManager()
                              ->getRepository('ContinuezLHistoireUserBundle:User')
                              ->findBy(array('actif' => 'O'));
        
        $nombreMessages = $this->getDoctrine()
                               ->getManager()
                               ->getRepository('ContinuezLHistoireForumBundle:Message')
                               ->findBy(array('actif' => true));
        
        return $this->render('ContinuezLHistoireSiteBundle:Site:statistiquessite.html.twig', array(
            'nombreMembres' => count($nombreMembres),
            'nombreMessages' => count($nombreMessages),
        ));
    }
    
    public function ajouterParagrapheAction($id, $typemodification)
    {
       $histoire = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                        ->find($id);
       
       $sousHistoires = $this->getDoctrine()
                             ->getManager()
                             ->getRepository('ContinuezLHistoireSiteBundle:SousHistoire')
                             ->findByHistoire($histoire);
       
       end($sousHistoires);
       $key = key($sousHistoires);
       $sousHistoires[$key]->setContenu($sousHistoires[$key]->getContenu() . "<br/>");
       
       $em = $this->getDoctrine()->getManager();
       $em->persist($sousHistoires[$key]);
       $em->flush();
       
       return $this->redirect($this->generateUrl('continuezlhistoiresite_voir', array('id'=>$id, 'typeModification'=>$typemodification)));
    }
    
    public function consulterProfilAction($username)
    {
        $user = $this->getDoctrine()
                     ->getManager()
                     ->getRepository('ContinuezLHistoireUserBundle:User')
                     ->findBy(array('username' => $username));
        
        if ($user == array())
            throw $this->createNotFoundException('Utilisateur[username='.$username.'] inexistant.');
        else
        {
            $sousHistoires = $this->getDoctrine()
                                    ->getManager()
                                    ->getRepository('ContinuezLHistoireSiteBundle:SousHistoire')
                                    ->findBy(array('auteur' => $user[0],
                                                   'actif' => true));

            $nbSousHistoires = count($sousHistoires);

            $messages = $this->getDoctrine()
                             ->getManager()
                             ->getRepository('ContinuezLHistoireForumBundle:Message')
                             ->findBy(array('auteur' => $user[0],
                                            'actif' => true));

            $nbMessages = count($messages);

            return $this->render('ContinuezLHistoireSiteBundle:Site:profil.html.twig', array(
                'user' => $user[0],
                'nbSousHistoires' => $nbSousHistoires,
                'nbMessages' => $nbMessages));
        }
    }
    
    public function modifierDescriptionAction($username)
    {
        $user = $this->getDoctrine()
                     ->getManager()
                     ->getRepository('ContinuezLHistoireUserBundle:User')
                     ->findBy(array('username' => $username));
        
        if ($user == array())
            throw $this->createNotFoundException('Utilisateur[username='.$username.'] inexistant.');
        else if ($user[0] != $this->getUser())
            throw new AccessDeniedException();
        else
        {
            $form = $this->createFormBuilder($user[0])
                 ->add('quiSuisJe', 'textarea', array('label' => false, 'attr' => array('cols' => '20', 'rows' => '10', 'class' => 'tinymce')))
                 ->getForm();

            $request = $this->getRequest();
            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid() && strlen($user[0]->getQuiSuisJe()) <= 255) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user[0]);
                    $em->flush();

                    return $this->redirect($this->generateUrl('continuezlhistoiresite_gererprofil', array('id' => $user[0]->getId())));
                }
                else    
                  echo 'La description dépasse la taille limite <br/>';
            }

            return $this->render('ContinuezLHistoireSiteBundle:Site:description.html.twig', array(
              'user' => $user[0],
              'form' => $form->createView()
            ));
        }
    }
    
    public function TestAction()
    {
        $form = $this->createFormBuilder()
             ->add('quiSuisJe', 'textarea', array('attr' => array('cols' => '20', 'rows' => '10', 'class' => 'tinymce')))
             ->getForm();
        
        return $this->render('ContinuezLHistoireSiteBundle:Site:test.html.twig', array(
          'form' => $form->createView()
        ));
    }
    
    public function TestTinyMCEAction()
    {
        $form = $this->createFormBuilder()
                             ->add('contenu', 'textarea', array('attr' => array('cols' => '20', 'rows' => '10', 'maxlength' => '255', 'class' => 'tinymce')))
                             ->getForm();
        
        return $this->render('ContinuezLHistoireSiteBundle:Site:testtinymce.html.twig', array(
             'form' => $form->createView()));
    }
    
    public function AjouterImageAction($id, $typemodification)
    {
        $message = '';
        $histoire = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                         ->find($id);
        
        if ($histoire == array())
            throw $this->createNotFoundException('Histoire['.$id.'] inexistante');
        else if ($histoire->getEstClos())
            throw new AccessDeniedException();
        else
        {    
            $image = new Image();

            $form = $this->createForm(new ImageType(), $image);

            $request = $this->getRequest();
            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid()) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE); // Retourne le type mime à la extension mimetype
                    $imageValide = false;

                    if((finfo_file($finfo, $image->getFile()) == 'image/png')
                      || (finfo_file($finfo, $image->getFile()) == 'image/jpg')
                      || (finfo_file($finfo, $image->getFile()) == 'image/jpeg')      
                      || (finfo_file($finfo, $image->getFile()) == 'image/bmp')
                      || (finfo_file($finfo, $image->getFile()) == 'image/gif'))
                    {
                        $em = $this->getDoctrine()->getManager();
                        $histoire->setImage($image);
                        $em->persist($histoire);
                        $em->flush();
                        $imageValide = true;
                    }

                    finfo_close($finfo);

                    if($imageValide)
                      return $this->redirect($this->generateUrl('continuezlhistoiresite_voir', array('id'=>$id, 'typemodification'=>$typemodification)));
                    else
                    {
                      $message = 'Format d\'image icorrect';
                    }
                }
            }

            return $this->render('ContinuezLHistoireSiteBundle:Site:ajouterimage.html.twig', array(
                'histoire' => $histoire,
                'form' => $form->createView(),
                'typemodification' => $typemodification,
                'message' => $message,
            ));
        }
    }
    
    public function AfficherFormulaireContactAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_AUTEUR')) {
            throw new AccessDeniedException();
        }
        else
        {
            $messageContact = new MessageContact();

            $form = $this->createForm(new MessageContactType(), $messageContact);

            $request = $this->getRequest();
            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid()) {
                    $messageContact->setDate(new \DateTime());
                    $messageContact->setAuteur($this->getUser());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($messageContact);
                    $em->flush();
                    
                    /*$message = \Swift_Message::newInstance()
                        ->setSubject('Message de ' . $this->getUser())
                        ->setFrom(array($this->getUser()->getEmail() => $this->getUser()->getUserName()))
                        ->setTo('gdubourd@gmail.com')
                        ->setBody($messageContact->getCorps() . ' Envoyé par ' . $this->getUser()->getEmail());

                    $this->get('swiftmailer.mailer.default')->send($message);*/
                    
                    \ini_set('SMTP', 'smtp.continuezlhistoire.fr');
                    
                    $message = $messageContact->getCorps() . ' Envoyé par ' . $this->getUser()->getEmail();
                    
                    $message = \wordwrap($message, 70, "\r\n");
                    
                    mail('admin@continuezlhistoire.fr', 'Message de ' . $this->getUser(), $message);
                    
                    //$mailer->send($message);

                    return $this->redirect($this->generateUrl('continuezlhistoiresite_accueil'));
                }
            }

            return $this->render('ContinuezLHistoireSiteBundle:Site:formulairecontact.html.twig', array(
                'form' => $form->createView()
            ));
        }
    }
    
    public function ConsulterMessagesAction($id)
    {
        return $this->render('ContinuezLHistoireSiteBundle:Site:messagerie.html.twig');
    }
    
    public function ConsulterCGUAction()
    {
        return $this->render('ContinuezLHistoireSiteBundle:Site:cgu.html.twig');
    }
    
    public function SupprimerAction($id)
    {
        $histoire = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('ContinuezLHistoireSiteBundle:Histoire')
                         ->findOneById($id);
        
        $user = $this->getUser();
        
        if ($user != $histoire->getPremierAuteur())
            throw new AccessDeniedException();
        else
        {    
            $form = $this->createFormBuilder()->getForm();
        
            $request = $this->getRequest();
            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid()) {
                    $listeSousHistoires = $this->getDoctrine()
                                               ->getManager()
                                               ->getRepository('ContinuezLHistoireSiteBundle:SousHistoire')
                                               ->findByHistoire($histoire);
                    
                    $em = $this->getDoctrine()->getManager();
                    
                    foreach($listeSousHistoires as $sousHistoire)
                    {
                        $sousHistoire->setActif(false);
                        $em->persist($sousHistoire);
                        $em->flush();        
                    }
                    $histoire->setActif(false);
                    $em->persist($histoire);
                    $em->flush(); 

                    $user->setNbPoints($user->getNbPoints() - 1);
                    $em->persist($user);
                    $em->flush();

                    return $this->redirect($this->generateUrl('continuezlhistoiresite_accueil'));
                }
            }

            return $this->render('ContinuezLHistoireSiteBundle:Site:supprimer.html.twig', array(
                'form' => $form->createView(),
                'histoire' => $histoire,
            ));
        }
    }
    
    public function ConsulterModeEmploiAction()
    {
        return $this->render('ContinuezLHistoireSiteBundle:Site:modeemploi.html.twig');
    }
    
    public function ConsulterPlanSiteAction()
    {
        return $this->render('ContinuezLHistoireSiteBundle:Site:plansite.html.twig');
    }
}