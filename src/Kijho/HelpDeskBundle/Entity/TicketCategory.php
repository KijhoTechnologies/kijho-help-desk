<?php

namespace Kijho\HelpDeskBundle\Entity;

use Kijho\HelpDeskBundle\Model\TicketCategoryInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Kijho\HelpDeskBundle\Util\Util;

/**
 * Ticket Category Entity
 * @author Cesar Giraldo - <cnaranjo@kijho.com> 29/04/2016
 * @ORM\Table(name="kijho_ticket_category")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class TicketCategory implements TicketCategoryInterface {

    /**
     * @ORM\Id
     * @ORM\Column(name="tcat_id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * Nombre de la Categoria
     * @ORM\Column(name="tcat_name", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * Descripción de la Categoria
     * @ORM\Column(name="tcat_description", type="text", nullable=true)
     */
    protected $description;

    /**
     * Texto identificador de la categoria para facil acceso a ella
     * @ORM\Column(name="tcat_slug", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    protected $slug;

    /**
     * Fecha en la que se crea la categoria
     * @ORM\Column(name="tcat_creation_date", type="datetime", nullable=true)
     */
    protected $creationDate;

    /**
     * Boolean par habilitar o deshabilitar categorias de tickets
     * @ORM\Column(name="tcat_is_enabled", type="boolean", nullable=true)
     */
    protected $isEnabled;

    /**
     * Correo al cual seran enviados los tickets de la categoria
     * @ORM\Column(name="tcat_email", type="text", nullable=true)
     * @Assert\Email(checkMX=true)
     */
    protected $email;

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getDescription() {
        return $this->description;
    }

    function getSlug() {
        return $this->slug;
    }

    function getCreationDate() {
        return $this->creationDate;
    }

    function getIsEnabled() {
        return $this->isEnabled;
    }

    function getEmail() {
        return $this->email;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function setSlug($slug) {
        $this->slug = $slug;
    }

    function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    function setIsEnabled($isEnabled) {
        $this->isEnabled = $isEnabled;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    public function __toString() {
        return $this->getName();
    }

    /**
     * Set Page initial status before persisting
     * @ORM\PrePersist
     */
    public function setDefaults() {
        if (null === $this->getIsEnabled()) {
            $this->setIsEnabled(true);
        }
        if (null === $this->getCreationDate()) {
            $this->setCreationDate(Util::getCurrentDate());
        }
    }

}
