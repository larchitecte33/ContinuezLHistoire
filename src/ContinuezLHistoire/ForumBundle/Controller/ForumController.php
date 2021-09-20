<?php

namespace ContinuezLHistoire\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ContinuezLHistoire\ForumBundle\Entity\Discussion;
use ContinuezLHistoire\ForumBundle\Entity\Message;
use ContinuezLHistoire\ForumBundle\Form\NouvelleDiscussionType;
use ContinuezLHistoire\ForumBundle\Form\MessageType;
use ContinuezLHistoire\ForumBundle\Form\ModificationDiscussionType;

class ForumController extends Controller 
{
    public function accueilForumAction()
    {
        date_default_timezone_set('Europe/Paris');
        
        $discussion = new Discussion();
        
        $listeDiscussions = $this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('ContinuezLHistoireForumBundle:Discussion')
                                 ->findByActif(true);
        
        $user = $this->getUser();
        
        $form = $this->createForm(new NouvelleDiscussionType(), $discussion);
        
        if ((true === $this->get('security.context')->isGranted('ROLE_AUTEUR')) or (isset($_SESSION['connecte']) && $_SESSION['connecte'] == true)) {
        
        if ($user != null)
        {
            $now = date("y-m-d h:i:s");
            $discussion->setAuteur($user);
            $discussion->setNbDeMessages(0);
            $discussion->setDate(new \DateTime( $now ));
            $discussion->setActif(true);
        
            $request = $this->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($discussion);
                    $em->flush();
                    
                    return $this->redirect($this->generateUrl('continuezlhistoireforum_discussion', array(
                       'id' => $discussion->getId() 
                    )));
                }
            }
        }
        }
        
        return $this->render('ContinuezLHistoireForumBundle:Forum:accueilForum.html.twig', array(
                'listeDiscussions' => $listeDiscussions,
                'form' => $form->createView(),
                'user' => $user
        ));
    }
    
    public function discussionAction($id)
    {
        date_default_timezone_set('Europe/Paris');
        
        $discussion = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('ContinuezLHistoireForumBundle:Discussion')
                           ->find($id);
        
        if ($discussion == null)
            throw $this->createNotFoundException('Discussion[id='.$id.'] inexistante.');
        else if ($discussion->getActif() == false)
            throw $this->createNotFoundException('Discussion[id='.$id.'] inexistante.');
        else
        {
            $message = new Message();
            $listeMessages = $this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('ContinuezLHistoireForumBundle:Message')
                                 ->findBy(array('discussion' => $discussion, 'actif' => true));
            $user = $this->getUser();
            
            $form = $this->createForm(new MessageType(), $message);
            
            $request = $this->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid() && strlen($message->getCorps()) <= 255) {
                    $now = date("y-m-d H:i:s");
                    $message->setDate(new \DateTime($now));
                    $message->setDiscussion($discussion);
                    $message->setAuteur($user);
                    $message->setActif(true);
                    
                    $discussion->setNbDeMessages($discussion->getNbDeMessages() + 1);
                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($message);
                    $em->persist($discussion);
                    $em->flush();
                    
                    return $this->redirect($this->generateUrl('continuezlhistoireforum_accueilforum'));
                }
                else
                    echo '<script type="text/javascript">'
                        . 'alert("Erreur : le message dépasse la taille limite.");'
                        . '</script>';
            }
            
            return $this->render('ContinuezLHistoireForumBundle:Forum:discussion.html.twig', array(
                'discussion' => $discussion,
                'form' => $form->createView(),
                'listeMessages' => $listeMessages,
                'user' => $user
            ));
        }
    }
    
    public function supprimerMessageAction($id)
    {
        $message = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('ContinuezLHistoireForumBundle:Message')
                        ->find($id);
        
        if ($message == array()) {
            throw $this->createNotFoundException('Message[id='.$id.'] inexistant.');
        }
        else if ((false === $this->get('security.context')->isGranted('ROLE_AUTEUR')) || ($message->getAuteur() != $this->getUser())) {
            throw new AccessDeniedException();
        }
        else if ($message->getActif() == false) {
            throw new AccessDeniedException();
        }
        else
        {
            $discussion = $message->getDiscussion();

            $form = $this->createFormBuilder()->getForm();

            $request = $this->getRequest();
            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid()) {
                  $message->setActif(false);
                  $em = $this->getDoctrine()->getManager();
                  $em->persist($message);
                  $em->flush();

                  $discussion->setNbDeMessages($discussion->getNbDeMessages() - 1);
                  $em->persist($discussion);
                  $em->flush();

                  return $this->redirect($this->generateUrl('continuezlhistoireforum_accueilforum'));
                }
            }

           return $this->render('ContinuezLHistoireForumBundle:Forum:supprimerMessage.html.twig', array(
               'message' => $message,
               'form' => $form->createView()
           ));
        }
    }
    
    public function modifierMessageAction($id)
    {
        $message = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('ContinuezLHistoireForumBundle:Message')
                        ->findById($id);
        
        if ($message == array()) {
            throw $this->createNotFoundException('Message[id='.$id.'] inexistant.');
        }
        else if ((false === $this->get('security.context')->isGranted('ROLE_AUTEUR')) || ($message[0]->getAuteur() != $this->getUser())) {
            throw new AccessDeniedException();
        }
        else if ($message[0]->getActif() == false) {
            throw new AccessDeniedException();
        }
        else
        {
            $discussion = $message[0]->getDiscussion();
            
            $listeMessages = $this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('ContinuezLHistoireForumBundle:Message')
                                 ->findBy(array('discussion' => $discussion, 'actif' => true));
            
            $form = $this->createForm(new MessageType(), $message[0]);
            
            $request = $this->getRequest();
            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid() && strlen($message[0]->getCorps()) <= 255) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($message[0]);
                    $em->flush();
                    
                    return $this->redirect($this->generateUrl('continuezlhistoireforum_discussion', array(
                        'id' => $discussion->getId(),
                    )));
                }
                else
                    echo '<script type="text/javascript">'
                        . 'alert("Erreur : le message dépasse la taille limite.");'
                        . '</script>';
            }
            
            return $this->render('ContinuezLHistoireForumBundle:Forum:modifierMessage.html.twig', array(
                'discussion' => $discussion,
                'listeMessages' => $listeMessages,
                'messageActif' => $message[0],
                'form' => $form->createView(),
            ));
        }
    }
    
    public function modifierDiscussionAction($id)
    {
        $discussion = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('ContinuezLHistoireForumBundle:Discussion')
                           ->find($id);
        
        $user = $this->getUser();
        
        if ($discussion == null)
            throw $this->createNotFoundException('Discussion[id='.$id.'] inexistante.');
        else if ($user == null)
            throw new AccessDeniedException;
        else if (($discussion->getAuteur() != $user) || (false === $this->get('security.context')->isGranted('ROLE_AUTEUR')))
            throw new AccessDeniedException;
        else
        {
            $form = $this->createForm(new ModificationDiscussionType(), $discussion);
            
            $request = $this->getRequest();
            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid() && strlen($discussion->getSujet()) <= 50) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($discussion);
                    $em->flush();
                    
                    return $this->redirect($this->generateUrl('continuezlhistoireforum_discussion', array(
                        'id' => $discussion->getId(),
                    )));
                }
                else
                    echo '<script type="text/javascript">'
                        . 'alert("Erreur : le sujet ne doit pas dépasser 50 caractères.");'
                        . '</script>';
            }
            
            return $this->render('ContinuezLHistoireForumBundle:Forum:modifierDiscussion.html.twig', array(
                'form' => $form->createView(),
                'discussion' => $discussion,
            ));
        }
    }
    
    public function supprimerDiscussionAction($id)
    {
        $discussion = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('ContinuezLHistoireForumBundle:Discussion')
                           ->findById($id);
        
        $user = $this->getUser();
        
        if ($user != $discussion[0]->getAuteur())
            throw new AccessDeniedException();
        else
        {
            $form = $this->createFormBuilder()->getForm();
        
            $request = $this->getRequest();
            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    
                    $messages = $this->getDoctrine()
                                     ->getManager()
                                     ->getRepository('ContinuezLHistoireForumBundle:Message')
                                     ->findByDiscussion($discussion[0]);
                    
                    foreach($messages as $message)
                    {
                        $message->setActif(false);
                        $em->persist($message);
                        $em->flush();        
                    }
                    
                    $discussion[0]->setActif(false);
                    $em->persist($discussion[0]);
                    $em->flush();
                    
                    return $this->redirect($this->generateUrl('continuezlhistoireforum_accueilforum'));
                }
            }
            
            return $this->render('ContinuezLHistoireForumBundle:Forum:supprimerDiscussion.html.twig', array(
                'discussion' => $discussion[0],
                'form' => $form->createView(),
            ));
        }
    }
}


