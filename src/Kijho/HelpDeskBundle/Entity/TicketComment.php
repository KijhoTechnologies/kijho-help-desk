<?php

namespace Kijho\HelpDeskBundle\Entity;

use Kijho\HelpDeskBundle\Model\TicketCommentInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Kijho\HelpDeskBundle\Util\Util;

/**
 * Ticket Comment Entity
 * @author Cesar Giraldo - <cnaranjo@kijho.com> 29/04/2016
 * @ORM\Table(name="kijho_ticket_comment")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class TicketComment implements TicketCommentInterface {

    /**
     * Constantes para los tipos de comentarios del ticket
     */
    const COMMENT_BY_CLIENT = 1;
    const COMMENT_BY_ADMIN = 2;
    
    /**
     * @ORM\Id
     * @ORM\Column(name="tcom_id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * Ticket a la que esta asociado el comentario
     * @ORM\ManyToOne(targetEntity="Kijho\HelpDeskBundle\Entity\Ticket", inversedBy="comments")
     * @ORM\JoinColumn(name="tcom_ticket_id", referencedColumnName="tick_id", nullable=false)
     */
    protected $ticket;
    
    /**
     * Contenido del comentario
     * @ORM\Column(name="tcom_comment", type="text", nullable=false)
     * @Assert\NotBlank()
     */
    protected $comment;
    
    /**
     * Tipo del comentario
     * @ORM\Column(name="tcom_type", type="integer", nullable=false)
     */
    protected $type;
    
    /**
     * Fecha en la que se crea el comentario
     * @ORM\Column(name="tcom_creation_date", type="datetime", nullable=true)
     */
    protected $creationDate;
    
    /**
     * Boolean para saber si el comentario ya fue leido
     * @ORM\Column(name="tcom_is_readed", type="boolean", nullable=true)
     */
    protected $isReaded;
    
    /**
     * Fecha en la que se lee el comentario
     * @ORM\Column(name="tcom_readed_date", type="datetime", nullable=true)
     */
    protected $readedDate;
    
    /**
     * Identificador del cliente que envia el ticket
     * @ORM\Column(name="tcom_client_id", type="string", nullable=false)
     */
    protected $clientId;
    
    /**
     * Instancia del cliente que realiza el ticket
     */
    protected $client;
    
    /**
     * Identificador del operador que atiende el ticket del cliente
     * @ORM\Column(name="tcom_operator_id", type="string", nullable=true)
     */
    protected $operatorId;
    
    /**
     * Instancia del operador que atiende el ticket
     */
    protected $operator;
    
    
    function getId() {
        return $this->id;
    }
    
    function getTicket() {
        return $this->ticket;
    }

    function getComment() {
        return $this->comment;
    }

    function getType() {
        return $this->type;
    }

    function getCreationDate() {
        return $this->creationDate;
    }

    function getIsReaded() {
        return $this->isReaded;
    }

    function getReadedDate() {
        return $this->readedDate;
    }

    function getClientId() {
        return $this->clientId;
    }

    function getOperatorId() {
        return $this->operatorId;
    }

    function setTicket($ticket) {
        $this->ticket = $ticket;
    }

    function setComment($comment) {
        $this->comment = $comment;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    function setIsReaded($isReaded) {
        $this->isReaded = $isReaded;
    }

    function setReadedDate($readedDate) {
        $this->readedDate = $readedDate;
    }

    function setClientId($clientId) {
        $this->clientId = $clientId;
    }

    function setOperatorId($operatorId) {
        $this->operatorId = $operatorId;
    }
    
    function getClient() {
        return $this->client;
    }

    function getOperator() {
        return $this->operator;
    }

    function setClient($client) {
        $this->client = $client;
    }

    function setOperator($operator) {
        $this->operator = $operator;
    }

    public function __toString() {
        return $this->getComment();
    }
    
    /**
     * Set Page initial status before persisting
     * @ORM\PrePersist
     */
    public function setDefaults() {
        if (null === $this->getIsReaded()) {
            $this->setIsReaded(false);
        }
        if (null === $this->getCreationDate()) {
            $this->setCreationDate(Util::getCurrentDate());
        }
    }
}
