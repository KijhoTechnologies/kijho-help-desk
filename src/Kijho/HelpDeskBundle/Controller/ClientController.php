<?php

namespace Kijho\HelpDeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kijho\HelpDeskBundle\Entity as Entity;
use Kijho\HelpDeskBundle\Form\Client\TicketType;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends Controller {

    /**
     * Constantes para variables de busqueda para los estados de los tickets
     */
    const STATUS_ALL = 'all';
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';

    /**
     * Esta funcion permite listar los tickets de un cliente
     * @author Cesar Giraldo <cnaranjo@kijho.com> 02/05/2016
     * @param string $status estado de los tickets a listar
     * @return type
     */
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

        $order = array('creationDate' => 'DESC');
        $tickets = $em->getRepository('HelpDeskBundle:Ticket')->findBy($search, $order);

        foreach ($tickets as $ticket) {
            $ticket->setOperator($this->container->get('ticket_provider')->getTicketOperator($ticket->getOperatorId()));
        }

        return $this->render('HelpDeskBundle:Client:myTickets.html.twig', array(
                    'tickets' => $tickets,
                    'status' => $status,
                    'menu' => 'menu_tickets'
        ));
    }

    /**
     * Permite el despliegue, validacion y almacenamiento de tickets
     * creados por el cliente
     * @author Cesar Giraldo <cnaranjo@kijho.com> 02/05/2016
     * @param Request $request datos de la solicitud
     * @return type
     */
    public function newSupportTicketAction(Request $request) {

        $ticket = new Entity\Ticket();
        $form = $this->createForm(TicketType::class, $ticket);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $ticket->setClient($this->getUser());
                $ticket->setClientId($this->getUser()->getId());

                $em = $this->getDoctrine()->getManager();
                $em->persist($ticket);
                $em->flush();

                $this->get('session')->getFlashBag()->add('client_success_message', $this->get('translator')->trans('help_desk.tickets.succesfully_send'));
                return $this->redirectToRoute('help_desk_client_my_tickets', array('status' => self::STATUS_ALL));
            } catch (\Exception $exc) {
                $this->get('session')->getFlashBag()->add('client_error_message', $this->get('translator')->trans('help_desk.tickets.error_while_send'));
            }
        }

        return $this->render('HelpDeskBundle:Client:newTicket.html.twig', array(
                    'form' => $form->createView(),
                    'menu' => 'menu_new_tickets'
        ));
    }

}
