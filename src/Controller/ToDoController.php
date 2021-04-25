<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ToDoController extends AbstractController
{
    /* Initialisation de la liste */
    #[Route('/Init', name: 'init')]
    public  function InitAction(SessionInterface $session)
    {
        if (!$session->has("ToDo")) // si la liste n'existe pas
        {   // On ajoute un message d'acceuil :
            $this->addFlash('Welcome','Bienvenue chez nous');
            // On l'intialise un tableau associatif et le dans la session
            $session->set('ToDo',array(
                'achat'=>'acheter clé usb',
                'cours'=>'Finaliser mon cours',
                'correction'=>'corriger mes examens'
            ));
        }

        return $this->forward('App\Controller\ToDoController::AfficheAction',[
            'session'=> $session
        ]);
    }

    /* Affichage de la liste */
    #[Route('/to/do', name: 'AffichAvecMessage')]
    public  function AfficheAction(SessionInterface $session) :Response
    {   // Afficher la liste avec un message s'il y en a :
        return $this->render('to_do/index.html.twig');
    }

    /* Ajout ou mise à jour dans la liste */
    #[Route('/Ajout/{cle}/{value}', name: 'Ajout')]
    public  function addToDoAction(SessionInterface $session,$cle,$value){
        if (!$session->has("ToDo")) // si la liste n'existe pas
        {   // On ajoute un message d'erreur :
            $message = 'La liste n\'est pas encore initialisée' ;
            $this->addFlash('ErreurInitialisation', $message);

        }
        else { // l'élément existe déja dans la liste
            $ToDo = $session->get("ToDo");
            if(array_key_exists($cle,$ToDo)) {
                $ToDo[$cle] = $value;
                $session->set('ToDo',$ToDo);
                $this->addFlash('SuccèsMAJ', 'Element '.$cle.' mis à jour avec succès');
            }
            else
            {
                $ToDo[$cle] = $value;
                $session->set('ToDo',$ToDo);
                $this->addFlash('SuccèsAjout', 'Element '.$cle.' ajouté avec succès');
            }
        }
        return $this->forward('App\Controller\ToDoController::AfficheAction',[
            'session'=> $session
        ]);
    }

    /* Suppression d'un élément de la liste */
    #[Route('/Suppression/{cle}', name: 'Suppression')]
    public  function deleteToDoAction(SessionInterface $session,$cle){
        if (!$session->has("ToDo")) // si la liste n'existe pas
        {   // On ajoute un message d'erreur :
            $this->addFlash('ErreurInitialisation', 'La liste n\'est pas encore initialisée');

        }
        else { // l'élément existe déja dans la liste
            $ToDo = $session->get("ToDo");
            if(array_key_exists($cle,$ToDo)) {
                unset($ToDo[$cle]);
                $session->set('ToDo',$ToDo);
                $this->addFlash('SuccèsSupression', 'Element '.$cle.'supprimé avec succès');
            }
            else
            {
                $this->addFlash('ErreurIntrouvable',"L'element que vous voulez supprimer n'existe pas" );
            }
        }
        return $this->forward('App\Controller\ToDoController::AfficheAction',[
            'session'=> $session
        ]);
    }

    /* Reset de la liste */
    #[Route('/Reset', name: 'Reset')]
    public  function resetToDoAction(SessionInterface $session){
        if (!$session->has("ToDo")) // si la liste n'existe pas
        {   // On ajoute un message d'erreur
            $this->addFlash('ResetAvecInitialisation', 'Initialisation de la liste effectué avec succès');
            return $this->forward('App\Controller\ToDoController::InitAction',[
                'session'=> $session
            ]);

        }
        else {
            $ToDo = array(
                'achat'=>'acheter clé usb',
                'cours'=>'Finaliser mon cours',
                'correction'=>'corriger mes examens'
            );
            $session->set('ToDo',$ToDo);
            $this->addFlash('SuccèsRest', 'Rest de la liste effectué avec succès');

        }
        return $this->forward('App\Controller\ToDoController::AfficheAction',[
            'session'=> $session
        ]);
    }



}
