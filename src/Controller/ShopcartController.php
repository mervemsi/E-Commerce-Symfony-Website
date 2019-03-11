<?php

namespace App\Controller;

use App\Entity\Shopcart;
use App\Form\ShopcartType;
use App\Form\Admin\ProductType;
use App\Repository\ShopcartRepository;
use App\Repository\Admin\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/shopcart")
 */
class ShopcartController extends Controller
{
    /**
     * @Route("/", name="shopcart_index", methods="GET")
     */
    public function index(ShopcartRepository $shopcartRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); //login controlu icin bu guvenligi sagliyor
       
        $user=$this->getUser(); //verileri getirmek icin get user
        //dump($user);
        //echo $user->getId();
        //die();

        $em=$this->getDoctrine()->getManager();
        $sql="SELECT p.title, p.sprice, s.*
              FROM shopcart s, product p
              WHERE s.productid = p.id and userid= :userid";
        $statement=$em->getConnection()->prepare($sql);
        $statement->bindValue('userid', $user->getId());
        $statement->execute();
        $shopcarts=$statement->fetchAll();

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

        return $this->render('shopcart/index.html.twig', ['shopcarts' =>  $shopcarts,'count' => $count,'total' => $total,]);
    }

    /**
     * @Route("/new", name="shopcart_new", methods="GET|POST")
     */
    public function new(Request $request, ShopcartRepository $shopcartRepository): Response
    {
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

        $shopcart = new Shopcart();
        $form = $this->createForm(ShopcartType::class, $shopcart);
        $form->handleRequest($request);

        $submittedToken=$request->request->get('token');
        if($this->isCsrfTokenValid('add-item', $submittedToken)){
            //do something, like deleting an object

            if ($form->isSubmitted()) {
                $em = $this->getDoctrine()->getManager();
                //echo  $request->request->get('quantity');
                $user = $this->getUser();

            // $shopcart->setQuantity($request->   request->get('quantity'));
                $shopcart->setUserId($user->getid());
                $em->persist($shopcart);
                $em->flush();

                return $this->redirectToRoute('shopcart_index');
            }
        }   
        return $this->render('shopcart/new.html.twig', [
            'shopcart' => $shopcart,
            'count' => $count,
            'total' => $total,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/newdirect/{id}", name="shopcart_new_direct", methods="GET|POST")
     */
    public function newdirect($id, Request $request, ShopcartRepository $shopcartRepository): Response
    {
        $shopcart = new Shopcart();
        $form = $this->createForm(ShopcartType::class, $shopcart);
        $form->handleRequest($request);

        $user = $this->getUser();
        $userid=$user->getid();
        $shopcart->setUserid($userid);
        $shopcart->setProductId($id);
        $shopcart->setQuantity(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($shopcart);
        $em->flush();

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

        return $this->redirectToRoute('shopcart_index');

    }

    /**
     * @Route("/{id}", name="shopcart_show", methods="GET")
     */
    public function show(Shopcart $shopcart, ShopcartRepository $shopcartRepository): Response
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

        return $this->render('shopcart/show.html.twig', ['shopcart' => $shopcart,'count' => $count,'total' => $total,]);
    }

    /**
     * @Route("/{id}/edit", name="shopcart_edit", methods="GET|POST")
     */
    public function edit(Request $request, Shopcart $shopcart, ShopcartRepository $shopcartRepository): Response
    {        
        $form = $this->createForm(ShopcartType::class, $shopcart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('shopcart_edit', ['id' => $shopcart->getId()]);
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

        return $this->render('shopcart/edit.html.twig', [
            'shopcart' => $shopcart,
            'count' => $count,
            'total' => $total,
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/{id}/del", name="shopcart_del", methods="GET|POST")
     */
    public function del(Request $request, Shopcart $shopcart): Response
    {
        $em=$this->getDoctrine()->getManager();
        $em->remove($shopcart);
        $em->flush();
        $this->addFlash('success','Deleting product from basket is successful');
        return $this->redirectToRoute('shopcart_index');
    }

    /**
     * @Route("/{id}", name="shopcart_delete", methods="DELETE")
     */
    public function delete(Request $request, Shopcart $shopcart): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shopcart->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($shopcart);
            $em->flush();
        }

        return $this->redirectToRoute('shopcart_index');
    }
}
