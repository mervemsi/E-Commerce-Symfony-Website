<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Form\ShopcartType;
use App\Repository\ShopcartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comment")
 */
class CommentController extends Controller
{
    /**
     * @Route("/", name="comment_index", methods="GET")
     */
    public function index(CommentRepository $commentRepository, ShopcartRepository $shopcartRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); //login controlu icin bu guvenligi sagliyor
       
        $user=$this->getUser(); //verileri getirmek icin get user
        //dump($user);
        //echo $user->getId();
        //die();
        $userid=$user->getid();

        if($this->isGranted('ROLE_USER'))
        {
            $userr=$this->getUser();
            $useridd=$userr->getId();
            $count=$shopcartRepository->getUserShopCartCount($useridd);
            $total = $shopcartRepository->getUserShopCartTotal($useridd);
        }
        else{
            $count=0;
            $total=0;
        }


        return $this->render('comment/index.html.twig', ['comments' => $commentRepository->findBy(['userid' => $userid]),'count' => $count,'total' => $total,
        ]);
    }

    /**
     * @Route("/new/{id}", name="comment_new", methods="GET|POST")
     */
    public function new($id, Request $request, ShopcartRepository $shopcartRepository): Response
    {
        if($this->isGranted('ROLE_USER'))
        {
            $userr=$this->getUser();
            $userid=$userr->getId();
            $count=$shopcartRepository->getUserShopCartCount($userid);
            $total = $shopcartRepository->getUserShopCartTotal($userid);
        }
        else{
            $count=0;
            $total=0;
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $user = $this->getUser();
            $comment->setUserId($user->getid());
            $comment->setStatus(('true'));
            $comment->setProductid($id);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            $this->addFlash('warning','Your comment has been sent successfully');

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'count' => $count,
            'total' => $total,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_show", methods="GET")
     */
    public function show(Comment $comment, ShopcartRepository $shopcartRepository): Response
    {
        if($this->isGranted('ROLE_USER'))
        {
            $user=$this->getUser();
            $userid=$user->getId();
            $count=$shopcartRepository->getUserShopCartCount($userid);
            $total = $shopcartRepository->getUserShopCartTotal($userid);
        }
        else{
            $count=0;
            $total=0;
        }

        return $this->render('comment/show.html.twig', ['comment' => $comment,'count' => $count,'total' => $total,]);
    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods="GET|POST")
     */
    public function edit(Request $request, Comment $comment, ShopcartRepository $shopcartRepository): Response
    {
        if($this->isGranted('ROLE_USER'))
        {
            $user=$this->getUser();
            $userid=$user->getId();
            $count=$shopcartRepository->getUserShopCartCount($userid);
            $total = $shopcartRepository->getUserShopCartTotal($userid);
        }
        else{
            $count=0;
            $total=0;
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_edit', ['id' => $comment->getId()]);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'count' => $count,
            'total' => $total,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/del", name="comment_del", methods="GET|POST")
     */
    public function del(Request $request, Comment $comment): Response
    {
        $em=$this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        $this->addFlash('success','Deleting comment is successful');
        return $this->redirectToRoute('comment_index');
    }

    /**
     * @Route("/{id}", name="comment_delete", methods="DELETE")
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        }

        return $this->redirectToRoute('comment_index');
    }
}
