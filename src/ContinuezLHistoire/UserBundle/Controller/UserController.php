<?php

namespace ContinuezLHistoire\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ContinuezLHistoire\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends Controller {
    public function validationCompteAction()
    {
        $user = $this->getDoctrine()
                     ->getManager()
                     ->getRepository('ContinuezLHistoireUserBundle:User')
                     ->findAll();
        
        $form = $this->createFormBuilder($user)
                     ->add('username', 'text', array('label' => 'Nom d\'utilisateur'))
                     ->add('codeActivation', 'text', array('label' => 'Code activation :'))
                     ->getForm();
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $formValeurs = $this->getRequest()->request->get('form');
                
                $user = $this->getDoctrine()
                             ->getManager()
                             ->getRepository('ContinuezLHistoireUserBundle:User')
                             ->findByUsername($formValeurs['username']);
                
                if ($user === array())
                {
                    throw $this->createNotFoundException('Utilisateur[user='.$formValeurs['username'].' inexistant.');
                }
                else if (($user[0]->getCodeActivation() == $formValeurs['codeActivation']) && ($user[0]->isEnabled() == false))
                {
                    $user[0]->setEnabled(true);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user[0]);
                    $em->flush();
                    
                    return $this->redirect($this->generateUrl('continuezlhistoireuser_recapitulatifcompte', array(
                        'id' => $user[0]->getId()
                    )));
                }
                else if ($user[0]->getCodeActivation() == $formValeurs['codeActivation'])
                {
                    throw new \Exception('Le compte de l\'utilisateur ' . $user[0]->getUsername() . ' a déjà été validé.');
                }
                else {
                    return $this->redirect($this->generateUrl('continuezlhistoireuser_infosnonvalides'));
                }
            }
        }
            
        return $this->render('ContinuezLHistoireUserBundle:User:validationcompte.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function recapitulatifCompteAction($id)
    {
        $user = $this->getDoctrine()
                     ->getManager()
                     ->getRepository('ContinuezLHistoireUserBundle:User')
                     ->findById($id);
        
        return $this->render('ContinuezLHistoireUserBundle:User:recapitulatifcompte.html.twig', array(
            'user' => $user
        ));
    }
    
    public function infosNonValidesAction()
    {
        return $this->render('ContinuezLHistoireUserBundle:User:infosnonvalides.html.twig');
    }
    
    public function inscriptionAction()
    {
        $user = new User();
        
        $form = $this->createFormBuilder($user)
                     ->add('username', 'text', array('label' => 'Nom d\'utilisateur'))
                     ->add('password', 'repeated', array(
                           'type' => 'password',
                           'invalid_message' => 'Passwords have to be equal.',
                           'first_name'      => 'Password',
                           'second_name'     => 'Re-enter_Password'))
                     ->add('email', 'email', array('label' => 'Adresse mail'))
                     ->getForm();
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            
            if ($form->isValid()) {
              //$user->setUsername($form['username']);
              //$user->setUsername('cathy');
              //$user->setPassword($form['password']);
              //$user->setEmail($form['email']);
              //$user->setEmailCanonical('titigogo@live.fr');
              $user->setActif('O');
              
              
              $em = $this->getDoctrine()->getManager();
              $em->persist($user);
              $em->flush();
            }
        }
        
        return $this->render('ContinuezLHistoireUserBundle:User:inscription.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function desinscriptionAction($id)
    {
        $userADesinscrire = $this->getDoctrine()
                     ->getManager()
                     ->getRepository('ContinuezLHistoireUserBundle:User')
                     ->findById($id);
        
        $user = $this->getUser();
        
        //echo $user->getUsername() . ' | ' . $userADesinscrire[0]->getUsername();
        //var_dump($userADesinscrire);
        
        if($user == NULL)
        {
            throw new AccessDeniedException();
        }
        else if($user->getUsername() != $userADesinscrire[0]->getUsername())
        {
            throw new AccessDeniedException();
        }
        else
        {
            $form = $this->createFormBuilder()->getForm();
            
            $request = $this->getRequest();
            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid()) {
                    // On désactive le compte de l'utilisateur
                    $em = $this->getDoctrine()->getManager();
                    
                    $userADesinscrire[0]->setEnabled(false);
                    $em->persist($user);
                    $em->flush();

                    // On définit un message flash
                    $this->get('session')->getFlashBag()->add('info', 'Votre compte a bien été supprimé');

                    // Puis on redirige vers l'accueil
                    return $this->redirect($this->generateUrl('continuezlhistoiresite_logout'));
                }
            }
            
            return $this->render('ContinuezLHistoireUserBundle:User:desinscription.html.twig', array(
                'user' => $userADesinscrire[0],
                'form' => $form->createView(),
            ));
        }
    }
}

