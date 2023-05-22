<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api", name="api_")
 */
class TicketController extends AbstractController
{
    /**
     * @Route("/users/me", name="user", methods="GET")
     */
    public function index(): Response
    {
        return $this->json(
            [
                'id' => $this->getUser()->getId(),
                'username' => $this->getUser()->getUsername(),

            ]
        );
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordHasherInterface $encoder, ManagerRegistry $manager): Response
    {

        $decoded = json_decode($request->getContent());
        $email = $decoded->email;
        $password = $decoded->password;
        $em = $manager->getManager();
        $user = new User();
        $user->setPassword($encoder->hashPassword($user, $password));
        $user->setEmail($email);
        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'Registered Successfully']);
    }

}