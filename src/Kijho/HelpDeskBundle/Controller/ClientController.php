<?php

namespace Kijho\HelpDeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kijho\HelpDeskBundle\Entity as Entity;

class ClientController extends Controller {

    const STATUS_ALL = 'all';
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';

    public function myTicketsAction($status = null) {
        $em = $this->getDoctrine()->getManager();

        $search = array(
            'clientId' => $this->getUser()->getId(),
        );

        
        if ($status) {
            if ($status == self::STATUS_OPEN) {
                $search['status'] = array(
                    Entity\Ticket::STATUS_NEW,
                    Entity\Ticket::STATUS_IN_PROCESS,
                    Entity\Ticket::STATUS_REPLIED
                );
            } elseif ($status == self::STATUS_CLOSED) {
                $search['status'] = array(
                    Entity\Ticket::STATUS_CLOSED
                );
            }
        } else {
            $status = self::STATUS_ALL;
        }

        $tickets = $em->getRepository('HelpDeskBundle:Ticket')->findBy($search);

        foreach ($tickets as $ticket) {
            $ticket->setOperator($this->container->get('ticket_provider')->getTicketOperator($ticket->getOperatorId()));
        }

        return $this->render('HelpDeskBundle:Client:myTickets.html.twig', array(
                    'tickets' => $tickets,
                    'status' => $status,
                    'menu' => 'menu_tickets'
        ));
    }

    public function newSupportTicketAction() {

        return $this->render('HelpDeskBundle:Client:newTicket.html.twig', array(
                    'menu' => 'menu_new_tickets'
        ));
    }

}
