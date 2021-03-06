<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\FriendshipRepository;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FriendshipRepository::class)
 */
class Friendship
{
    use TimestampableEntity;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=user::class, cascade={"persist", "remove"})
     */
    private $sender;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=user::class, cascade={"persist", "remove"})
     */

    private $reciver;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $available;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?user
    {
        return $this->sender;
    }

    public function setSender(?user $user): self
    {
        $this->sender = $user;

        return $this;
    }
    public function getReciver(): ?user
    {
        return $this->reciver;
    }

    public function setReciver(?user $user): self
    {
        $this->reciver = $user;

        return $this;
    }

    public function getAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }
}
