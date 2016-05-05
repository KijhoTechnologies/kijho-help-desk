<?php

namespace Kijho\HelpDeskBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TicketRepository extends EntityRepository {

    public function findCountTickets($clientId = null, $status = null) {
        $em = $this->getEntityManager();

        $parameters = array();
        $extraQuery = '';

        if ($clientId !== null) {
            $extraQuery .= 'AND t.clientId = :clientId ';
            $parameters['clientId'] = $clientId;
        }

        if ($status == 'active') {
            $parameters['status_new'] = Ticket::STATUS_NEW;
            $parameters['status_process'] = Ticket::STATUS_IN_PROCESS;
            $parameters['status_replied'] = Ticket::STATUS_REPLIED;

            $extraQuery .= ' AND (t.status = :status_new '
                    . 'OR t.status = :status_process '
                    . 'OR t.status = :status_replied) ';
        } elseif ($status == 'closed') {
            $parameters['status_closed'] = Ticket::STATUS_CLOSED;
            $extraQuery .= ' AND t.status = :status_closed ';
        }


        $consult = $em->createQuery("
        SELECT COUNT(t)
        FROM HelpDeskBundle:Ticket t
        WHERE 1=1 " . $extraQuery);
        $consult->setParameters($parameters);

        return $consult->getSingleScalarResult();
    }

}
