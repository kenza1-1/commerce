<?php

namespace App\Entity;
use Serializable;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 * fields={"email"},
 * message="L'email que vous avez indiqué est déja utilisé."
 * )
 */
class User implements UserInterface 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex("/^\w+/")
     */
    public $username;
    
    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     * min = 4,
     * max = 20,
     * minMessage = "Votre mot de passe doit contenir au moins 4 caractères ",
     * maxMessage = "Votre mot de passe doit contenir au plus 20 caractères",
     * allowEmptyString = false
     * )
     */
    private $password;
    /** 
    * @Assert\EqualTo(propertyPath="password",message="les mots de passe ne correspondent pas.Veuiller réessayer")
    */
    public $confirmPassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activation_token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Reset_token;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(){ }

    //  /** @see \Serializable::serialize() */
    //  public function serialize()
    //  {
    //      return serialize(array(
    //          $this->id,
    //          $this->username,
    //          $this->password,
    //          // see section on salt below
    //          // $this->salt,
    //      ));
    //  }
     
    //    /** @see \Serializable::unserialize() */
    // public function unserialize($serialized)
    // {
    //     list (
    //         $this->id,
    //         $this->username,
    //         $this->password,
    //         // see section on salt below
    //         // $this->salt
    //     ) = unserialize($serialized, array('allowed_classes' => false));
    // }

    public function getSalt(){ 
        return null;
    }
   
    // public function getRoles(){
    //     return ['ROLE_USER'];
    // }

    public function getActivationToken(): ?string
    {
        return $this->activation_token;
    }

    public function setActivationToken(?string $activation_token): self
    {
        $this->activation_token = $activation_token;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->Reset_token;
    }

    public function setResetToken(?string $Reset_token): self
    {
        $this->Reset_token = $Reset_token;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
}
