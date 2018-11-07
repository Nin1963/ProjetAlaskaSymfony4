<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Comment;
use App\Form\ChapterType;
use App\Repository\ChapterRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin(ChapterRepository $repo) 
    {
        $chapters = $repo->findAll();

        $this->addFlash('notice', 'Vous êtes connecté.');
        
        return $this->render('admin/admin.html.twig', [
            'chapters' => $chapters
            
        ]);
        
    }

    /**
     * @Route("/admin/add_chapter", name="add_chapter")
     */
    public function addChapter(ChapterRepository $repo, Request $request, EntityManagerInterface $em)
    {
        $chapters = $repo->findAll();
        $chapter = new Chapter();
        
        $form = $this->createForm(ChapterType::class, $chapter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$chapter->getId()) {
                $chapter->setCreatedAt(new \DateTime());
            }

            $em->persist($chapter);
            $em->flush();

            $this->addFlash('notice', 'Votre nouveau chapitre a bien été ajouté.');

            return $this->redirectToRoute('adminchapter', ['id' => $chapter->getId()]);
        }

        return $this->render('admin/add_chapter.html.twig', [
            'chapters' => $chapters,
            'chapter' => $chapter,
            'formChapter' =>$form->createView()
        ]);
    }

    /**
     * @Route("/admin/adminChapter/{id}", name="adminChapter")
     */
    public function adminChapter(Chapter $chapter, ChapterRepository $repo, Comment $comment, ChapterRepository $repoComment, Request $request, EntityManagerInterface $em)
    {
        $chapters = $repo->findAll();
        $comments = $repoComment->findAll();
        
        $form = $this->createForm(ChapterType::class, $chapter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$chapter->getId()) {
                $chapter->setCreatedAt(new \DateTime());
            }

            $em->persist($chapter);
            $em->flush();

            $this->addFlash('notice', 'Votre chapitre a bien été modifié.');

            return $this->redirectToRoute('adminChapter', ['id' => $chapter->getId()]);
        }

        return $this->render('admin/adminChapter.html.twig', [
            'chapters' => $chapters,
            'chapter' => $chapter,
            'formChapter' =>$form->createView()
        ]);

    }

    /**
     * @Route("/admin/signaled", name="comment_signal")
     */
    public function signaled(ChapterRepository $repo, CommentRepository $repoSignaled, CommentRepository $repoComment) 
    {
        $chapters = $repo->findAll();
        $signaled = $repoSignaled->findAll();
        $comment = $repoComment->findAll();

        return $this->render('admin/signaled.html.twig', [
            'chapters' => $chapters,
            'signaled' => $signaled, 
            'comment' => $comment
        ]);
    }
    

}
