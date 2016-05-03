<?php

namespace Kijho\HelpDeskBundle\Entity;

use Kijho\HelpDeskBundle\Model\TicketInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Kijho\HelpDeskBundle\Util\Util;

/**
 * Ticket Entity
 * @author Cesar Giraldo - <cnaranjo@kijho.com> 29/04/2016
 * @ORM\Table(name="kijho_ticket")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Ticket implements TicketInterface {

    /**
     * Constantes para los estados del ticket
     */
    const STATUS_NEW = 1;
    const STATUS_IN_PROCESS = 2;
    const STATUS_REPLIED = 3;
    const STATUS_CLOSED = 4;

    /**
     * Constantes para las prioridades del ticket
     */
    const PRIORITY_URGENT = 1;
    const PRIORITY_HIGH = 2;
    const PRIORITY_MEDIUM = 3;
    const PRIORITY_LOW = 4;

    /**
     * @ORM\Id
     * @ORM\Column(name="tick_id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * Estado del Ticket
     * @ORM\Column(name="tick_status", type="integer", nullable=true)
     */
    protected $status;

    /**
     * Categoria a la que esta asociado el ticket
     * @ORM\ManyToOne(targetEntity="Kijho\HelpDeskBundle\Entity\TicketCategory")
     * @ORM\JoinColumn(name="tick_category_id", referencedColumnName="tcat_id", nullable=false)
     * @Assert\NotBlank()
     */
    protected $category;

    /**
     * Asunto del Ticket
     * @ORM\Column(name="tick_subject", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    protected $subject;

    /**
     * Mensaje del Ticket
     * @ORM\Column(name="tick_body", type="text", nullable=false)
     * @Assert\NotBlank()
     */
    protected $body;

    /**
     * Fecha en la que se crea el ticket
     * @ORM\Column(name="tick_creation_date", type="datetime", nullable=true)
     */
    protected $creationDate;

    /**
     * Prioridad del Ticket
     * @ORM\Column(name="tick_priority", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    protected $priority;

    /**
     * Identificador del cliente que envia el ticket
     * @ORM\Column(name="tick_client_id", type="string", nullable=false)
     */
    protected $clientId;

    /**
     * Instancia del cliente que realiza el ticket
     */
    protected $client;

    /**
     * Identificador del operador que atiende el ticket del cliente
     * @ORM\Column(name="tick_operator_id", type="string", nullable=true)
     */
    protected $operatorId;

    /**
     * Instancia del operador que atiende el ticket
     */
    protected $operator;

    function getId() {
        return $this->id;
    }

    function getStatus() {
        return $this->status;
    }

    function getCategory() {
        return $this->category;
    }

    function getSubject() {
        return $this->subject;
    }

    function getBody() {
        return $this->body;
    }

    function getCreationDate() {
        return $this->creationDate;
    }

    function getPriority() {
        return $this->priority;
    }

    function getClientId() {
        return $this->clientId;
    }

    function getClient() {
        return $this->client;
    }

    function getOperatorId() {
        return $this->operatorId;
    }

    function getOperator() {
        return $this->operator;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setCategory($category) {
        $this->category = $category;
    }

    function setSubject($subject) {
        $this->subject = $subject;
    }

    function setBody($body) {
        $this->body = $body;
    }

    function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    function setPriority($priority) {
        $this->priority = $priority;
    }

    function setClientId($clientId) {
        $this->clientId = $clientId;
    }

    function setOperatorId($operatorId) {
        $this->operatorId = $operatorId;
    }

    function setClient($client) {
        $this->client = $client;
    }

    function setOperator($operator) {
        $this->operator = $operator;
    }

    public function __toString() {
        return $this->getSubject();
    }

    /**
     * Set Page initial status before persisting
     * @ORM\PrePersist
     */
    public function setDefaults() {
        if (null === $this->getStatus()) {
            $this->setStatus(self::STATUS_NEW);
        }
        if (null === $this->getCreationDate()) {
            $this->setCreationDate(Util::getCurrentDate());
        }
    }

    /**
     * Permite obtener en modo texto el estado del ticket
     * @param integer|null $status estado del ticket
     */
    public function getTextStatus($status = null) {
        if (!$status) {
            $status = $this->getStatus();
        }
        $text = '';
        switch ($status) {
            case self::STATUS_NEW:
                $text = 'help_desk.tickets.status_new';
                break;
            case self::STATUS_IN_PROCESS:
                $text = 'help_desk.tickets.status_in_process';
                break;
            case self::STATUS_REPLIED:
                $text = 'help_desk.tickets.status_replied';
                break;
            case self::STATUS_CLOSED:
                $text = 'help_desk.tickets.status_closed';
                break;
            default:
                break;
        }
        return $text;
    }

    /**
     * Permite obtener en modo texto la prioridad del ticket
     * @param integer|null $priority priodidad del ticket
     */
    public function getTextPriority($priority = null) {
        if (!$priority) {
            $priority = $this->getPriority();
        }
        $text = '';
        switch ($priority) {
            case self::PRIORITY_URGENT:
                $text = 'help_desk.tickets.priority_urgent';
                break;
            case self::PRIORITY_HIGH:
                $text = 'help_desk.tickets.priority_high';
                break;
            case self::PRIORITY_MEDIUM:
                $text = 'help_desk.tickets.priority_medium';
                break;
            case self::PRIORITY_LOW:
                $text = 'help_desk.tickets.priority_low';
                break;
            default:
                break;
        }
        return $text;
    }

    function getLabelClassByPriority($priority = null) {
        if (!$priority) {
            $priority = $this->getPriority();
        }
        $text = '';
        switch ($priority) {
            case self::PRIORITY_URGENT:
                $text = 'label-danger';
                break;
            case self::PRIORITY_HIGH:
                $text = 'label-warning';
                break;
            case self::PRIORITY_MEDIUM:
                $text = 'label-success';
                break;
            case self::PRIORITY_LOW:
                $text = 'label-info';
                break;
            default:
                break;
        }
        return $text;
    }

    public function getLabelClassByStatus($status = null) {
        if (!$status) {
            $status = $this->getStatus();
        }
        $text = '';
        switch ($status) {
            case self::STATUS_NEW:
                $text = 'label-success';
                break;
            case self::STATUS_IN_PROCESS:
                $text = 'label-info';
                break;
            case self::STATUS_REPLIED:
                $text = 'label-warning';
                break;
            case self::STATUS_CLOSED:
                $text = 'label-danger';
                break;
            default:
                break;
        }
        return $text;
    }
}
