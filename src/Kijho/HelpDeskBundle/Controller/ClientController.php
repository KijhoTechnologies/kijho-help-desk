<?php

namespace Kijho\HelpDeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientController extends Controller
{
    public function myTicketsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $search = array('clientId'=>$this->getUser()->getId());
        $tickets = $em->getRepository('HelpDeskBundle:Ticket')->findBy($search);

        foreach ($tickets as $ticket) {
            $ticket->setOperator($this->container->get('ticket_provider')->getTicketOperator($ticket->getOperatorId()));
        }
        
        return $this->render('HelpDeskBundle:Client:myTickets.html.twig', array(
                    'tickets' => $tickets,
        ));
    }
}
