<?php

namespace Kijho\HelpDeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OperatorController extends Controller {

    public function ticketsAction() {

        $em = $this->getDoctrine()->getManager();

        $tickets = $em->getRepository('HelpDeskBundle:Ticket')->findAll();

        foreach ($tickets as $ticket) {
            $ticket->setClient($this->container->get('ticket_provider')->getTicketClient($ticket->getClientId()));
            $ticket->setOperator($this->container->get('ticket_provider')->getTicketOperator($ticket->getOperatorId()));
        }

        return $this->render('HelpDeskBundle:Operator:tickets.html.twig', array(
                    'tickets' => $tickets,
        ));
    }

}
