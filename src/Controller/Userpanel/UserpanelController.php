<?php

namespace App\Controller\Userpanel;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Form\ShopcartType;
use App\Repository\ShopcartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
  * @Route("/userpanel")
  */

class UserpanelController extends Controller
{
    /**
     * @Route("/", name="userpanel")
     */
    public function index()
    {
        return $this->redirectToRoute('userpanel_show');
    }


    /**
     * @Route("/edit", name="userpanel_edit", methods="GET|POST")
     */
    public function edit(Request $request, ShopcartRepository $shopcartRepository): Response
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


        $usersession=$this->getUser(); //Calling user data
        $user=$this->getDoctrine()
             ->getRepository(User::class)
             ->find($usersession->getid());       

        if ($request->isMethod('POST'))
        {
            $submittedToken=$request->request->get('token');
            if($this->isCsrfTokenValid('user-form',$submittedToken)) {
                $user->setName($request->request->get("name"));
                $user->setPassword($request->request->get("password")); //login user id
                $user->setAddress($request->request->get("address"));
                $user->setCity($request->request->get("city"));
                $user->setPhone($request->request->get("phone"));
                $this->getDoctrine()->getManager()->flush();    
                $this->addFlash('success','Your editing profile has been done successfully.');

                return $this->redirectToRoute('userpanel_show');
            }
        }

        return $this->render('userpanel/edit.html.twig', [
            'user' => $user,
            'total' => $total,
            'count' => $count,
        ]);
    }


    /**
     * @Route("/show", name="userpanel_show", methods="GET")
     */
    public function show(Request $request, ShopcartRepository $shopcartRepository): Response
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
        return $this->render('userpanel/show.html.twig', [
            'count' => $count,
            'total' => $total,
        ]);
    }


}
