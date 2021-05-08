<?php

namespace App\Entity;

use App\Repository\ActivationTokenRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActivationTokenRepository::class)
 */
class ActivationToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiredAt;

    /**
     * @ORM\Column(type="text")
     */
    private $value;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="token" ,cascade={"persist", "remove"})
     */
    private $user;

    public function __construct()
    {
        $this->value = sha1(sha1(sha1(uniqid(25))));
        $this->createdAt = new \DateTime();
        $this->expiredAt = (new \DateTime())->add(new DateInterval('P1D'));

    }

    public function hasExpired() {
        return new DateTime() > $this->expiredAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeInterface
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(\DateTimeInterface $expiredAt): self
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
