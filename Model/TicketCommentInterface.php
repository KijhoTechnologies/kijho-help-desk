<?php

namespace Kijho\HelpDeskBundle\Model;

/**
 * Interface to TicketComment
 * @author Cesar Giraldo - <cnaranjo@kijho.com> 29/04/2016
 */
interface TicketCommentInterface {

    /**
     * @return string
     */
    public function getId();

    /**
     * Returns ticket on comment
     */
    public function getTicket();

    /**
     * @return string
     */
    public function getComment();

    /**
     * @return integer
     */
    public function getType();

    /**
     * @return \DateTime
     */
    public function getCreationDate();

    /**
     * @return boolean
     */
    public function getIsReaded();
    
    /**
     * @return \DateTime
     */
    public function getReadedDate();
    
    /**
     * @return string
     */
    public function getClientId();
    
    /**
     * @return string
     */
    public function getOperatorId();
}
