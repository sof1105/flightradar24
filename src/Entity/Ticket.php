<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 * @ORM\Table
 */
class Ticket
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id = null;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private  $departureTime;

    /**
     * @ORM\Column(type="string")
     */
    private  $source;

    /**
     * @ORM\Column(type="string")
     */
    private  $destination;


    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *  min = 120,
     *  max = 180,
     *  notInRangeMessage = "Seat Number must be between {{ min }} and {{ max }}",
     * )
     * @Assert\NotNull
     */
    private  $seat;




    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getDepartureTime()
    {
        return $this->departureTime;
    }

    /**
     * @param mixed $departureTime
     */
    public function setDepartureTime($departureTime): void
    {
        $this->departureTime = $departureTime;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source): void
    {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param mixed $destination
     */
    public function setDestination($destination): void
    {
        $this->destination = $destination;
    }

    /**
     * @return mixed
     */
    public function getSeat()
    {
        return $this->seat;
    }

    /**
     * @param mixed $seat
     */
    public function setSeat($seat): void
    {
        $this->seat = $seat;
    }




}
