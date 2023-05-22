<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;


/**
 * @Route("/api/users", name="api_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/me", name="user", methods="GET")
     * @Rest\Get("/me")
     * @Rest\View()
     */
    public function index()
    {
        return $this->getUser();
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

        return $this->json(['message' => 'Registered Successfully'],201);
    }

}