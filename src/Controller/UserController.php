<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Form\ShopcartType;
use App\Repository\ShopcartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends Controller
{
    /**
     * @Route("/admin/user", name="user_index", methods="GET")
     */
    public function index(UserRepository $userRepository, ShopcartRepository $shopcartRepository): Response
    {
        $users=$this->getDoctrine()
               ->getRepository(User::class)
               ->findAll();
 // print_r ($uyeler);
 // exit();

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

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'count' => $count,
            'total' => $total,
        ]);;
    }

    /**
     * @Route("/admin/user/new", name="user_new", methods="GET|POST")
     */
    public function new(Request $request, ShopcartRepository $shopcartRepository): Response
    {
        $user= new User();
        $form= $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if( $form->isSubmitted()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('user_index');
        }

        if($this->isGranted('ROLE_USER'))
        {
            $userr=$this->getUser();
            $useridd=$userr>getId();
            $count=$shopcartRepository->getUserShopCartCount($useridd);
            $total = $shopcartRepository->getUserShopCartTotal($useridd);
        }
        else{
            $count=0;
            $total=0;
        }

        return $this->render('admin/user/create_form.html.twig',[
            'form' => $form->createView(),
            'count' => $count,
            'total' => $total,
        ]);
    }

    /**
     * @Route("/admin/user{id}", name="user_show", methods="GET")
     */
    public function show(User $user, ShopcartRepository $shopcartRepository): Response
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

        return $this->render('user/show.html.twig', ['user' => $user,'count' => $count,'total' => $total,]);
    }

    /**
     * @Route("/admin/user/{id}/edit", name="user_edit", methods="GET|POST")
     */
    public function edit(Request $request, User $users, ShopcartRepository $shopcartRepository): Response
    {
        $form= $this->createForm(UserType::class, $users);
        $form->handleRequest($request);

        if( $form->isSubmitted()){
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_index');
        }

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

        return $this->render('admin/user/edit_form.html.twig',[
            'user'=>$users,
            'count' => $count,
            'total' => $total,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="user_delete", methods="DELETE")
     */
    public function delete(Request $request, User $user): Response
    {
        if($this->isCsrfTokenValid('delete'.$user->getId(),$request->request->get('_token'))){
            $em=$this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            
       }  
        return $this->redirectToRoute('user_index'); 
    }
}
