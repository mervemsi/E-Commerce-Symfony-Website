<?php

namespace App\Controller;

use App\Form\ShopcartType;
use App\Repository\ShopcartRepository;
use App\Repository\Admin\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, SettingRepository $settingRepository, ShopcartRepository $shopcartRepository): Response
    {

        $data= $settingRepository->findAll();
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

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
        
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername,
         'error' => $error,
         'count' => $count,
         'total' => $total,
         'data' => $data]);
    }


     /**
     * @Route("/loginerror", name="app_login_error")
     */
    public function loginerror(AuthenticationUtils $authenticationUtils, SettingRepository $settingRepository, ShopcartRepository $shopcartRepository): Response
    {
        $data= $settingRepository->findAll();
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $this->addFlash('error','You do not have access to the page you want to access.');

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

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername,
         'error' => $error,
         'data' => $data,
         'total' => $total,
         'count' => $count,
         ]);
    }


}
