<?php

namespace Kijho\HelpDeskBundle\Model;

/**
 * Interface to Ticket
 * @author Cesar Giraldo - <cnaranjo@kijho.com> 29/04/2016
 */
interface TicketInterface {

    /**
     * @return string
     */
    public function getId();

    /**
     * @return integer
     */
    public function getStatus();

    /**
     * Returns instance of TicketCategory
     */
    public function getCategory();
    
    /**
     * @return string
     */
    public function getSubject();
    
    /**
     * @return string
     */
    public function getBody();
    
    /**
     * @return \DateTime
     */
    public function getCreationDate();
    
    /**
     * @return integer
     */
    public function getPriority();
    
    /**
     * @return string
     */
    public function getClientId();
    
    /**
     * @return Object instancia del cliente
     */
    public function getClient();
    
    /**
     * @return string
     */
    public function getOperatorId();
    
    /**
     * @return Object instancia del operador
     */
    public function getOperator();
}
