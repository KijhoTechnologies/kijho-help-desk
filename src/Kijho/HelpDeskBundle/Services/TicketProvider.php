<?php

namespace Kijho\HelpDeskBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

/*
 * TicketProvider
 */
class TicketProvider {

    protected $container;
    protected $em;
    protected $clientStorage;
    protected $operatorStorage;

    public function __construct(ContainerInterface $container, EntityManager $em) {
        $this->container = $container;
        $this->em = $em;
        $this->clientStorage = $this->container->getParameter('help_desk.client_provider');
        $this->operatorStorage = $this->container->getParameter('help_desk.operator_provider');
    }

    public function getTicketClient($clientId) {
        if (!empty($clientId)) {
            $client = $this->em->getRepository($this->clientStorage)->find($clientId);
            return $client;
        }
        return null;
    }
    
    public function getTicketOperator($operatorId) {
        if (!empty($operatorId)) {
            $operator = $this->em->getRepository($this->operatorStorage)->find($operatorId);
            return $operator;
        }
        return null;
    }
   
}
