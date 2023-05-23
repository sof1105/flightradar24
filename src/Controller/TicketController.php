<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/tickets", name="api_")
 */
class TicketController extends AbstractController
{


    /**
     * @Route("/create", name="tickets_create", methods={"POST"})
     */
    public function add(Request $request, UserPasswordHasherInterface $encoder, EntityManagerInterface $em, ValidatorInterface $validator, TicketRepository $ticketRepository)
    {

        $decoded = json_decode($request->getContent());
        $departureTime  = $decoded->departure_time;
        $source = $decoded->source;
        $destination = $decoded->destination;
        $flightNumber = $decoded->flight_number;

        $ticket = new Ticket();
        $ticket->setSource($source);
        $ticket->setDestination($destination);
        $ticket->setDepartureTime(($departureTime !== null ) ? new \DateTimeImmutable($departureTime) : null);
        $ticket->setFlightNumber($flightNumber);
        $ticket->setSeat($this->getSeatNumber($ticketRepository, $flightNumber, $departureTime));


        $errors = $validator->validate($ticket);
        if ($errors->count() > 0) {
            return new View($errors, Response::HTTP_BAD_REQUEST);
        }
        $em->persist($ticket);
        $em->flush();
        return $this->json(['message' => 'Ticket Created Successfully'],201);
    }


    /**
     * @Route("/cancel/{id}", name="tickets_cancel", methods={"POST"})
     */
    public function cancel($id, Request $request, EntityManagerInterface $em)
    {

        $ticket = $em->getRepository(Ticket::class)->find($id);
        $ticket->setStatus(Ticket::STATUS_CANCELED);

        $em->persist($ticket);
        $em->flush();
        return $this->json(['message' => 'Ticket Canceled Successfully'],201);
    }


    /**
     * @Route("/{id}", name="tickets_index", methods="GET")
     */
    public function index($id, EntityManagerInterface $manager): Response
    {

        $ticket = $manager->getRepository(Ticket::class)->findOneById($id);
        if(!$ticket){
            throw $this->createNotFoundException(sprintf('no ticket with the reference %s',$id));
        }
        return $this->json(
            [
                'id' => $this->getUser()->getId(),
                'departure_time' => $ticket->getDepartureTime(),
                'source' => $ticket->getSource(),
                'destination' => $ticket->getDestination(),
                'flight_number' => $ticket->getFlightNumber(),
                'seat' => $ticket->getSeat(),
            ]
        );
    }

    private function getSeatNumber(TicketRepository $ticketRepository,$flightNumber,$departureTime){

        $seatsNumber = $ticketRepository->getSeatsNumberByFlightAndDate($flightNumber,$departureTime);

        do {
            $rand = rand(1, 32);
        } while(in_array($rand, $seatsNumber));

        return $rand;
    }

}