<?php
namespace Kijho\HelpDeskBundle\Model;

/**
 * Interface to TicketCategory
 * @author Cesar Giraldo - <cnaranjo@kijho.com> 29/04/2016
 */
interface TicketCategoryInterface
{
    /**
     * @return string
     */
    public function getId();
    
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDescription();
    
    /**
     * @return string
     */
    public function getSlug();
    
    /**
     * @return \DateTime
     */
    public function getCreationDate();
    
    /**
     * @return boolean
     */
    public function getIsEnabled();
    
    /**
     * @return string
     */
    public function getEmail();

}
