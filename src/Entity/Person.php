<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\PersonRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phonenumber = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $secondaryphone = null;

    #[ORM\Column(nullable: true)]
    private ?int $age = null;

    #[ORM\Column(nullable: true)]
    private ?string $national_id = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $gender = null;

    #[ORM\OneToOne(mappedBy: 'person', cascade: ['persist', 'remove'])]
    private ?User $userperson = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPhonenumber(): ?string
    {
        return $this->phonenumber;
    }

    public function setPhonenumber(?string $phonenumber): static
    {
        $this->phonenumber = $phonenumber;

        return $this;
    }

    public function getSecondaryphone(): ?string
    {
        return $this->secondaryphone;
    }

    public function setSecondaryphone(?string $secondaryphone): static
    {
        $this->secondaryphone = $secondaryphone;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getNationalId(): ?string
    {
        return $this->national_id;
    }

    public function setNationalId(?string $national_id): static
    {
        $this->national_id = $national_id;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getUserperson(): ?User
    {
        return $this->userperson;
    }

    public function setUserperson(User $userperson): static
    {
        // set the owning side of the relation if necessary
        if ($userperson->getPerson() !== $this) {
            $userperson->setPerson($this);
        }

        $this->userperson = $userperson;

        return $this;
    }

}

