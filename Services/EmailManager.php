<?php

namespace Kijho\HelpDeskBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Kijho\HelpDeskBundle\Entity as Entity;
use Doctrine\ORM\EntityManager;

/*
 * EmailManager
 * Esta clase implementa metodos generalizados para la construccion y 
 * envio de correos electronicos en la aplicacion, los cuales pueden ser utilizados
 * como un servicio
 */

class EmailManager {

    protected $mailer;
    protected $request;
    protected $container;
    protected $translator;
    protected $em;

    const SENDER_GENERAL_EMAILS = 'testmasterunlock@gmail.com';

    /**
     * Constructor del servicio encargado de enviar todos los correos de la aplicacion
     * @author Cesar Giraldo <cesargiraldo1108@gmail.com> 20/01/2016
     * @param RequestStack $requestStack
     * @param ContainerInterface $container
     * @param EntityManager $entityManager
     */
    public function __construct(RequestStack $requestStack, ContainerInterface $container, EntityManager $entityManager) {
        $this->request = $requestStack->getCurrentRequest();
        $this->container = $container;
        $this->translator = $this->container->get('translator');
        $this->mailer = $this->container->get('mailer');
        $this->em = $entityManager;
    }

    /**
     * Permite enviar al administrador la notificacion de un nuevo ticket
     * @author Cesar Giraldo <cesargiraldo1108@gmail.com> May 04, 2016
     * @param Entity\Ticket $ticket
     */
    public function sendNotificationNewTicket(Entity\Ticket $ticket) {

        $client = $this->container->get('ticket_provider')->getTicketClient($ticket->getClientId());
        $emails = $ticket->getCategory()->getEmail();
        $arrayEmails = explode(",", $emails);
        $validEmails = array();

        //validar que lo que esta en el campo de email sea valido
        foreach ($arrayEmails as $mails) {
            if (filter_var($mails, FILTER_VALIDATE_EMAIL)) {
                array_push($validEmails, $mails);
            }
        }

        if ($client && !empty($client->getEmail()) && !empty($ticket->getCategory()->getEmail())) {
            $ticket->setClient($client);

            $message = \Swift_Message::newInstance()
                    ->setSubject($this->translator->trans('help_desk.ticket_notification.new_ticket_client') . ' - ' . $this->translator->trans('help_desk.global.ticket') . ' # ' . $ticket->getId())
                    ->setFrom($client->getEmail())
                    ->setBcc($validEmails)
                    ->setBody(
                    $this->container->get('templating')->render(
                            'HelpDeskBundle:Email:newTicket.html.twig', array('ticket' => $ticket)
                    ), 'text/html'
                    )
            ;
            $this->mailer->send($message);
        }
    }

    /**
     * Permite enviar al cliente u administrador la notificacion de un comentario sobre el ticket
     * @author Cesar Giraldo <cesargiraldo1108@gmail.com> May 05, 2016
     * @param Entity\TicketComment $ticketComment
     */
    public function sendNotificationNewComment(Entity\TicketComment $ticketComment) {

        $client = $this->container->get('ticket_provider')->getTicketClient($ticketComment->getTicket()->getClientId());
        $category = $ticketComment->getTicket()->getCategory();

        $emails = $category->getEmail();
        $arrayEmails = explode(",", $emails);
        $validEmails = array();

        //validar que lo que esta en el campo de email sea valido
        foreach ($arrayEmails as $mails) {
            if (filter_var($mails, FILTER_VALIDATE_EMAIL)) {
                array_push($validEmails, $mails);
            }
        }


        if ($category && $client && !empty($client->getEmail())) {

            $ticketComment->setClient($client);


            if ($ticketComment->getType() == Entity\TicketComment::COMMENT_BY_ADMIN) {

//                \Symfony\Component\VarDumper\VarDumper::dump($client->getEmail());die();
                $operator = $this->container->get('ticket_provider')->getTicketOperator($ticketComment->getOperatorId());

                if ($operator) {
                    $ticketComment->setOperator($operator);

                    $message = \Swift_Message::newInstance()
                            ->setSubject($this->translator->trans('help_desk.ticket_notification.new_ticket_comment') . ' - ' . $this->translator->trans('help_desk.global.ticket') . ' # ' . $ticketComment->getTicket()->getId())
                            ->setFrom($validEmails)
                            ->setTo($client->getEmail())
                            ->setBody(
                            $this->container->get('templating')->render(
                                    'HelpDeskBundle:Email:newTicketComment.html.twig', array('ticket_comment' => $ticketComment)
                            ), 'text/html'
                            )
                    ;
                    $this->mailer->send($message);
                }
            } elseif ($ticketComment->getType() == Entity\TicketComment::COMMENT_BY_CLIENT) {
                $operator = $this->container->get('ticket_provider')->getTicketOperator($ticketComment->getTicket()->getOperatorId());

                $ticketComment->setOperator($operator);



                $message = \Swift_Message::newInstance()
                        ->setSubject($this->translator->trans('help_desk.ticket_notification.new_ticket_comment') . ' - ' . $this->translator->trans('help_desk.global.ticket') . ' # ' . $ticketComment->getTicket()->getId())
                        ->setFrom($client->getEmail())
//                        ->setTo($category->getEmail())
                        ->setBcc($validEmails)
                        ->setBody(
                        $this->container->get('templating')->render(
                                'HelpDeskBundle:Email:newTicketComment.html.twig', array('ticket_comment' => $ticketComment)
                        ), 'text/html'
                        )
                ;
                $this->mailer->send($message);
            }
        }
    }

    /**
     * Permite enviar al administrador la notificacion de que un ticket fue cerrado
     * @author Cesar Giraldo <cesargiraldo1108@gmail.com> May 12, 2016
     * @param Entity\Ticket $ticket
     */
    public function sendNotificationClosedTicket(Entity\Ticket $ticket) {

        $client = $this->container->get('ticket_provider')->getTicketClient($ticket->getClientId());
        $emails = $ticket->getCategory()->getEmail();
        $arrayEmails = explode(",", $emails);
        $validEmails = array();

        //validar que lo que esta en el campo de email sea valido
        foreach ($arrayEmails as $mails) {
            if (filter_var($mails, FILTER_VALIDATE_EMAIL)) {
                array_push($validEmails, $mails);
            }
        }

        if ($client && !empty($client->getEmail()) && !empty($ticket->getCategory()->getEmail())) {
            $ticket->setClient($client);

            $operator = $this->container->get('ticket_provider')->getTicketOperator($ticket->getOperatorId());
            if ($operator) {
                $ticket->setOperator($operator);
            }

            $message = \Swift_Message::newInstance()
                    ->setSubject($this->translator->trans('help_desk.ticket_notification.closed_ticked') . ' - ' . $this->translator->trans('help_desk.global.ticket') . ' # ' . $ticket->getId())
                    ->setFrom($client->getEmail())
//                    ->setTo($ticket->getCategory()->getEmail())
                    ->setBcc($validEmails)
                    ->setBody(
                    $this->container->get('templating')->render(
                            'HelpDeskBundle:Email:closedTicket.html.twig', array('ticket' => $ticket)
                    ), 'text/html'
                    )
            ;
            $this->mailer->send($message);
        }
    }

}
