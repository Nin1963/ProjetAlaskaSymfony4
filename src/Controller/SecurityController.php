<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\ChapterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(ChapterRepository $repo, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder) 
    {   
        $chapters = $repo->findAll(); 
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('security/registration.html.twig', [
            'chapters' => $chapters,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="login")
    */
    public function login(ChapterRepository $repo)
    {
        $chapters = $repo->findAll(); 

        return $this->render('security/login.html.twig', [
            'chapters' => $chapters
        ]);
    }

    /**
     * @Route("/deconnexion", name="logout")
    */
    public function logout() 
    {
        $this->addFlash('notice', 'Vous êtes déconnecté.');
    }
}
