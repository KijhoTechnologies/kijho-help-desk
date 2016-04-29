<?php

namespace Kijho\HelpDeskBundle\Entity;

use Kijho\HelpDeskBundle\Model\UserInterface;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User Ticket Entity
 * @author Cesar Giraldo - <cnaranjo@kijho.com> 29/04/2016
 * @ORM\Table(name="kijho_user_ticket")
 * @ORM\Entity
 */
class User implements UserInterface, SecurityUserInterface, \Serializable  {

    /**
     * Constantes para los tipos de usuarios
     */
    const TYPE_CLIENT = 1;
    const TYPE_OPERATOR = 2;
    
    /**
     * @ORM\Id
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * Nombre del usuario
     * @ORM\Column(name="user_name", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    protected $name;
    
    /**
     * Correo del usuario
     * @ORM\Column(name="user_email", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    protected $email;
    
    /**
     * Boolean para saber si el usuario es operador de tickets
     * @ORM\Column(name="user_is_ticket_operator", type="string", nullable=true)
     */
    protected $isTicketOperator;
    
    /**
     * Contrasena del usuario
     * @ORM\Column(name="user_password", type="string", nullable=true)
     */
    protected $password;
    
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getEmail() {
        return $this->email;
    }

    function getPassword() {
        return $this->password;
    }
    
    function getIsTicketOperator() {
        return $this->isTicketOperator;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setIsTicketOperator($isTicketOperator) {
        $this->isTicketOperator = $isTicketOperator;
    }

    public function __toString() {
        return $this->getName();
    }

    public function eraseCredentials() {
        
    }

    public function getRoles() {        
        if ($this->getIsTicketOperator()) {
            return array('ROLE_OPERATOR');
        } 
        return array('ROLE_CLIENT');
    }

    public function getSalt() {
        return '';
    }

    public function getUsername() {
        return $this->getEmail();
    }

    public function serialize() {
        return serialize(array(
            $this->id,
        ));
    }

    public function unserialize($serialized) {
        list (
                $this->id,
                ) = unserialize($serialized);
    }

}
