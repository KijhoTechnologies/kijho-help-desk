<?php

namespace Kijho\HelpDeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kijho\HelpDeskBundle\Entity as Entity;
use Kijho\HelpDeskBundle\Form\Client\TicketType;
use Kijho\HelpDeskBundle\Form\Client\TicketCommentType;
use Symfony\Component\HttpFoundation\Request;
use Kijho\HelpDeskBundle\Util\Util;

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
        ));
    }

    /**
     * Permite visaulizar los detalles de un ticket
     * @param Request $request
     * @param type $id
     * @return type
     */
    public function viewTicketAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $ticket = $em->getRepository('HelpDeskBundle:Ticket')->find($id);

        if ($ticket && $ticket->getClientId() == $this->getUser()->getId()) {
            
            //creamos el formulario para la creacion de comentarios del cliente
            $ticketComment = new Entity\TicketComment();
            $formComment = $this->createForm(TicketCommentType::class, $ticketComment);
            $formComment->handleRequest($request);
            if ($formComment->isSubmitted() && $formComment->isValid()) {
                //verificamos si el usuario desea cerrar el ticket
                $parameters = $request->request->get('helpdeskbundle_client_ticket_comment_type');
                if (isset($parameters['closeTicket'])) {
                    $ticket->setStatus(Entity\Ticket::STATUS_CLOSED);
                }
                
                //marcamos la ultima actividad sobre el ticket
                $ticket->setLastUpdate(Util::getCurrentDate());
                $em->persist($ticket);
                
                $ticketComment->setClientId($this->getUser()->getId());
                $ticketComment->setType(Entity\TicketComment::COMMENT_BY_CLIENT);
                $ticketComment->setTicket($ticket);
                $em->persist($ticketComment);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('client_success_message', $this->get('translator')->trans('help_desk.tickets.succesfully_send'));
                return $this->redirectToRoute('help_desk_client_tickets_view', array('id' => $ticket->getId()));
            }
            
            //buscamos los comentarios del ticket
            $search = array('ticket' => $ticket->getId());
            $order = array('creationDate' => 'ASC');
            $comments = $em->getRepository('HelpDeskBundle:TicketComment')->findBy($search, $order);
            
            //consultamos clientes y operadores y ponemos leidos los mensajes
            foreach ($comments as $comment) {
                $comment->setOperator($this->container->get('ticket_provider')->getTicketOperator($comment->getOperatorId()));
                $comment->setClient($this->container->get('ticket_provider')->getTicketClient($comment->getClientId()));
                
                if ($comment->getType() == Entity\TicketComment::COMMENT_BY_ADMIN && !$comment->getIsReaded()) {
                    $comment->setIsReaded(true);
                    $comment->setReadedDate(Util::getCurrentDate());
                    $em->persist($comment);
                }
            }
            $em->flush();
            
            return $this->render('HelpDeskBundle:Client:viewTicket.html.twig', array(
                'ticket' => $ticket,
                'comments' => $comments,
                'form_comment' => $formComment->createView(),
            ));
        } else {
            $this->get('session')->getFlashBag()->add('client_error_message', $this->get('translator')->trans('help_desk.tickets.not_found_message'));
            return $this->redirectToRoute('help_desk_client_my_tickets', array('status' => self::STATUS_ALL));
        }
    }

}
