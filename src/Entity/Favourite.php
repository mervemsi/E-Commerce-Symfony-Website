<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FavouriteRepository")
 */
class Favourite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $productid;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $userid;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductid(): ?int
    {
        return $this->productid;
    }

    public function setProductid(?int $productid): self
    {
        $this->productid = $productid;

        return $this;
    }

    public function getUserid(): ?int
    {
        return $this->userid;
    }

    public function setUserid(?int $userid): self
    {
        $this->userid = $userid;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
