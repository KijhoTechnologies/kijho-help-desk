<?php

namespace Kijho\HelpDeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kijho\HelpDeskBundle\Entity as Entity;
use Kijho\HelpDeskBundle\Form\Operator\TicketCommentType;
use Symfony\Component\HttpFoundation\Request;
use Kijho\HelpDeskBundle\Util\Util;

class OperatorController extends Controller {

    const STATUS_ALL = 'all';
    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';

    public function ticketsAction($status = self::STATUS_ALL) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$this->getUser()->getIsTicketOperator()) {
            throw $this->createNotFoundException('Access Denied. You must be logged in as an operator');
        }

        $em = $this->getDoctrine()->getManager();

        $search = array();

        if ($status == self::STATUS_ACTIVE) {
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

        $order = array('creationDate' => 'DESC');
        $tickets = $em->getRepository('HelpDeskBundle:Ticket')->findBy($search, $order);

        foreach ($tickets as $ticket) {
            $ticket->setClient($this->container->get('ticket_provider')->getTicketClient($ticket->getClientId()));
            $ticket->setOperator($this->container->get('ticket_provider')->getTicketOperator($ticket->getOperatorId()));
        }

        return $this->render('HelpDeskBundle:Operator:tickets.html.twig', array(
                    'tickets' => $tickets,
                    'status' => $status,
                    'current_date' => Util::getCurrentDate(),
        ));
    }

    /**
     * View details for tickets
     * @param Request $request
     * @param string $id identificador del ticket
     * @return type
     */
    public function viewTicketAction(Request $request, $id) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$this->getUser()->getIsTicketOperator()) {
            throw $this->createNotFoundException('Access Denied. You must be logged in as an operator');
        }

        $em = $this->getDoctrine()->getManager();
        $ticket = $em->getRepository('HelpDeskBundle:Ticket')->find($id);

        if ($ticket) {

            $ticket->setClient($this->container->get('ticket_provider')->getTicketClient($ticket->getClientId()));
            $ticket->setOperator($this->container->get('ticket_provider')->getTicketOperator($ticket->getOperatorId()));

            //creamos el formulario para la creacion de comentarios del cliente
            $ticketComment = new Entity\TicketComment();
            $formComment = $this->createForm(TicketCommentType::class, $ticketComment, array('translator' => $this->get('translator')));
            $formComment->handleRequest($request);
            if ($formComment->isSubmitted() && $formComment->isValid()) {

                //marcamos la ultima actividad sobre el ticket
                $ticket->setLastUpdate(Util::getCurrentDate());

                if ($ticket->getStatus() == Entity\Ticket::STATUS_NEW) {
                    $ticket->setStatus(Entity\Ticket::STATUS_REPLIED);
                }

                $ticket->setOperatorId($this->getUser()->getId());

                $em->persist($ticket);

                $ticketComment->setOperatorId($this->getUser()->getId());
                $ticketComment->setClientId($ticket->getClientId());
                $ticketComment->setType(Entity\TicketComment::COMMENT_BY_ADMIN);
                $ticketComment->setTicket($ticket);
                $em->persist($ticketComment);
                $em->flush();

                //notificamos via email el comentario del operador
                $this->get('help_desk_email_manager')->sendNotificationNewComment($ticketComment);

                $this->get('session')->getFlashBag()->add('operator_success_message', $this->get('translator')->trans('help_desk.tickets.succesfully_send'));
                return $this->redirectToRoute('help_desk_operator_tickets_view', array('id' => $ticket->getId()));
            }

            //buscamos los comentarios del ticket
            $search = array('ticket' => $ticket->getId());
            $order = array('creationDate' => 'ASC');
            $comments = $em->getRepository('HelpDeskBundle:TicketComment')->findBy($search, $order);

            //consultamos clientes y operadores y ponemos leidos los mensajes
            foreach ($comments as $comment) {

                if ($formComment->isSubmitted() and empty($comment->getOperatorId())) {
                    $comment->setOperatorId($this->getUser()->getId());
                    $em->persist($comment);
                }

                $comment->setOperator($this->container->get('ticket_provider')->getTicketOperator($comment->getOperatorId()));
                $comment->setClient($this->container->get('ticket_provider')->getTicketClient($comment->getClientId()));

                if ($comment->getType() == Entity\TicketComment::COMMENT_BY_CLIENT && !$comment->getIsReaded()) {
                    $comment->setIsReaded(true);
                    $comment->setReadedDate(Util::getCurrentDate());
                    $em->persist($comment);
                }
            }
            $em->flush();

            return $this->render('HelpDeskBundle:Operator:viewTicket.html.twig', array(
                        'ticket' => $ticket,
                        'comments' => $comments,
                        'form_comment' => $formComment->createView(),
            ));
        } else {
            $this->get('session')->getFlashBag()->add('operator_error_message', $this->get('translator')->trans('help_desk.tickets.not_found_message'));
            return $this->redirectToRoute('help_desk_operator_tickets', array('status' => self::STATUS_ALL));
        }
    }

}
