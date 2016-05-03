<?php

namespace Kijho\HelpDeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller {

    /**
     * Metodo encargado de validar la autenticacion de usuarios
     * @param Request $request
     * @return type
     */
    public function loginAction(Request $request) {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
                        'HelpDeskBundle:Default:login.html.twig', array(
                    'last_username' => $lastUsername,
                    'error' => $error,
                        )
        );
    }

    public function loginCheckAction() {
        // this controller will not be executed,
        // as the route is handled by the Security system
    }

    public function redirectAction() {
        if ($this->getUser()->getIsTicketOperator()) {
            return $this->redirect($this->generateUrl('help_desk_operator_tickets'));
        } else {
            return $this->redirect($this->generateUrl('help_desk_client_my_tickets', array('status'=>'all')));
        }
    }

}
