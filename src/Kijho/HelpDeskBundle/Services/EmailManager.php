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

    const SENDER_GENERAL_EMAILS = 'myagilescrum@gmail.com';

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
     * Permite enviar a un usuario el correo de invitacion a un proyecto
     * @author Cesar Giraldo <cesargiraldo1108@gmail.com> May 04, 2016
     * @param Entity\Ticket $ticket
     */
    public function sendNotificationNewTicket(Entity\Ticket $ticket) {

        $client = $this->container->get('ticket_provider')->getTicketClient($ticket->getClientId());

        if ($client && !empty($client->getEmail())) {
            $ticket->setClient($client);

            $message = \Swift_Message::newInstance()
                    ->setSubject($this->translator->trans('help_desk.ticket_notification.new_ticket_client'))
                    ->setFrom($client->getEmail())
                    ->setTo($ticket->getCategory()->getEmail())
                    ->setBody(
                    $this->container->get('templating')->render(
                            'HelpDeskBundle:Email:newTicket.html.twig', array('ticket' => $ticket)
                    ), 'text/html'
                    )
            ;
            $this->mailer->send($message);
        }
    }

}
