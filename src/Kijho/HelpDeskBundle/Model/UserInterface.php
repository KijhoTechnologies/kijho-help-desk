<?php

namespace Kijho\HelpDeskBundle\Model;

/**
 * Interface to User (Clients and Operators)
 * @author Cesar Giraldo - <cnaranjo@kijho.com> 29/04/2016
 */
interface UserInterface {

    /**
     * Returns client or operator identifier
     * @return string
     */
    public function getId();

    /**
     * Returns client or operator name
     * @return string
     */
    public function getName();

    /**
     * Return client or operator email
     * @return string
     */
    public function getEmail();
    
    /**
     * Return boolean if the user is an allowed Ticket Operator
     * @return boolean
     */
    public function getIsTicketOperator();
}
