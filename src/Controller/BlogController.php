<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ChapterRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(ChapterRepository $repo)
    {
        $chapters = $repo->findAll();

        return $this->render('blog/home.html.twig', [
            'chapters' => $chapters
        ]);
    }

    /**
     * @Route("/chapter/{id}", name="chapter_show")
     */
    public function show(Chapter $chapter, ChapterRepository $repo, Request $request, EntityManagerInterface $em) 
    {
        $chapters = $repo->findAll();
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setChapter($chapter);
            $comment->setCreatedAt(new \DateTime());
            
            $em->persist($comment);
            $em->flush();

            $this->addFlash('notice', 'Votre commentaire a bien été ajouté.');

            return $this->redirectToRoute('chapter_show', ['id' => $chapter->getId()]);
        }

        return $this->render('blog/chapter.html.twig', [
            'chapters' => $chapters,
            'chapter' => $chapter,
            'formComment' => $form->createView()
        ]);
    }




 
    /*
    
    public function chapter(Chapter $chapter, ChapterRepository $repo, Comment $comment, Request $request, EntityManagerInterface $em)
    {
        $chapters = $repo->findAll();
        $comment = new Comment();
        
        
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

                $comment->setChapter($chapter);
                $comment->setCreatedAt(new \DateTime());
            

            $em->persist($comment);
            $em->flush();

            $this->addFlash('notice', 'Votre commentaire a bien été ajouté.');

            return $this->redirectToRoute('chapter', ['id' => $chapter->getId()]);
        }

        return $this->render('blog/chapter.html.twig', [
            'chapters' => $chapters,
            'chapter' => $chapter,
            'formComment' => $form->createView()
        ]);
    } */

    
    /**
     * @Route("/blog/{id}", name="signaled")
    */
    public function signalComment(Chapter $chapter, ChapterRepository $repo, Comment $comment, EntityManagerInterface $em, Request $request) 
    {
        $chapters = $repo->findAll();
        $request = Request::createFromGlobals();

        if($request->getBoolean($default)) {
            $comment->setSignaled(true);
        
            $em->persist($comment);
            $em->flush();

            $this->addFlash('notice', 'Le commentaire a bien été signalé.');

            return $this->redirectToRoute('chapter', ['id' => $chapter->getId()]);
        }else {
            return $this->redirectToRoute('chapter', ['id' => $chapter->getId()]);
        }

        return $this->render('blog/chapter.html.twig', [
            'chapters' => $chapters,
            'chapter' => $chapter,
            'comment' =>$comment,
            'signaled' => $signaled
        ]);
    }
    
}

