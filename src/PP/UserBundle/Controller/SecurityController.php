<?php

namespace PP\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PP\UserBundle\Entity\User;
use PP\UserBundle\Form\UserType;

class SecurityController extends Controller
{
  public function loginAction(Request $request)
  {
    if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
      return $this->redirectToRoute('pp_core_homepage');
    }

    // Le service authentication_utils permet de récupérer le nom d'utilisateur
    // et l'erreur dans le cas où le formulaire a déjà été soumis mais était invalide
    // (mauvais mot de passe par exemple)
    $authenticationUtils = $this->get('security.authentication_utils');

    return $this->render('@PPUser/Security/login.html.twig', array(
      'last_username' => $authenticationUtils->getLastUsername(),
      'error'         => $authenticationUtils->getLastAuthenticationError(),
    ));
  }

  public function newAction(Request $request) {
    $user = new User();
    $form = $this->get('form.factory')->create(UserType::class, $user);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $user->setSalt('');
      $user->setRoles(array('ROLE_USER'));
      $em->persist($user);
      $em->flush();
      
      return $this->redirectToRoute('pp_core_homepage');
    }

    return $this->render('@PPUser/Security/newUser.html.twig', array(
      'form' => $form->createView()
    ));
  }
}
