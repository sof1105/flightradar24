<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\User;
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
     * @Route("/{id}", name="tickets_index", methods="GET")
     */
    public function index($id, EntityManagerInterface $manager): Response
    {

        $ticket = $manager->getRepository(Ticket::class)->findOneById($id);
        if(!$ticket){
            throw $this->createNotFoundException(sprintf('no ticket with the referance %s',$id));
        }
        return $this->json(
            [
                'id' => $this->getUser()->getId(),
                'departure_time' => $this->getUser()->getUsername(),
                'source' => $this->getUser()->getUsername(),
                'destination' => $this->getUser()->getUsername(),
            ]
        );
    }

    /**
     * @Route("/create", name="tickets_create", methods={"POST"})
     */
    public function add(Request $request, UserPasswordHasherInterface $encoder, EntityManagerInterface $em, ValidatorInterface $validator)
    {

        $decoded = json_decode($request->getContent());
        $departureTime  = $decoded->departure_time;
        $source = $decoded->source;
        $destination = $decoded->destination;

        $ticket = new Ticket();
        $ticket->setSource($source);
        $ticket->setDestination($destination);
        $ticket->setDepartureTime(($departureTime !== null ) ? new \DateTimeImmutable($departureTime) : null);

        $errors = $validator->validate($ticket);
        if ($errors->count() > 0) {
            return new View($errors, Response::HTTP_BAD_REQUEST);
        }
        $em->persist($ticket);
        $em->flush();
        return $this->json(['message' => 'Ticket Created Successfully'],201);
    }

}