<?php

namespace App\Controller;

use App\Entity\Favourite;
use App\Form\FavouriteType;
use App\Form\Admin\ProductType;
use App\Form\ShopcartType;
use App\Repository\ShopcartRepository;
use App\Repository\FavouriteRepository;
use App\Repository\Admin\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/favourite")
 */
class FavouriteController extends Controller
{
    /**
     * @Route("/", name="favourite_index", methods="GET")
     */
    public function index(FavouriteRepository $favouriteRepository, ShopcartRepository $shopcartRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); //login controlu icin bu guvenligi sagliyor
        
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

        $user=$this->getUser(); //verileri getirmek icin get user
 
        $em=$this->getDoctrine()->getManager();
        $sql="SELECT p.title, p.sprice, f.*
              FROM favourite f, product p
              WHERE f.productid = p.id and userid= :userid";
        $statement=$em->getConnection()->prepare($sql);
        $statement->bindValue('userid', $user->getId());
        $statement->execute();
        $favourites=$statement->fetchAll();

        return $this->render('favourite/index.html.twig', ['favourites' => $favourites,
        'count' => $count,
        'total' => $total,
        ]);
    }

    /**
     * @Route("/new", name="favourite_new", methods="GET|POST")
     */
    public function new(Request $request, ShopcartRepository $shopcartRepository): Response
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

        $favourite = new Favourite();
        $form = $this->createForm(FavouriteType::class, $favourite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($favourite);
            $em->flush();

            return $this->redirectToRoute('favourite_index');
        }

        return $this->render('favourite/new.html.twig', [
            'favourites' => $favourite,
            'count' => $count,
            'total' => $total,
            'form' => $form->createView(),
        ]);
    }


     /**
     * @Route("/add/{id}", name="favourite_add", methods="GET|POST")
     */
    public function add($id,Request $request,ProductRepository $productRepository, ShopcartRepository $shopcartRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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

        $favourite = new Favourite();
        $form = $this->createForm(FavouriteType::class, $favourite);
        $form->handleRequest($request);

        //$productid=$productRepository->findBy();

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $product=$request->request->get('productid');
        $favourite->setUserId($user->getid());
        $favourite->setStatus(('true'));
        $favourite->setProductid($id);
        $em->persist($favourite);
        $em->flush();
        //dump($favourite);
        //die();

        return $this->redirectToRoute('favourite_index');
       
    }



    /**
     * @Route("/{id}", name="favourite_show", methods="GET")
     */
    public function show(Favourite $favourite, ShopcartRepository $shopcartRepository): Response
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

        return $this->render('favourite/show.html.twig', ['favourite' => $favourite,'count' => $count,'total' => $total,]);
    }

    /**
     * @Route("/{id}/edit", name="favourite_edit", methods="GET|POST")
     */
    public function edit(Request $request, Favourite $favourite, ShopcartRepository $shopcartRepository): Response
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

        $form = $this->createForm(FavouriteType::class, $favourite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('favourite_edit', ['id' => $favourite->getId()]);
        }

        return $this->render('favourite/edit.html.twig', [
            'favourite' => $favourite,
            'count' => $count,
            'total' => $total,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="favourite_delete", methods="DELETE")
     */
    public function delete(Request $request, Favourite $favourite): Response
    {
        if ($this->isCsrfTokenValid('delete'.$favourite->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($favourite);
            $em->flush();
        }

        return $this->redirectToRoute('favourite_index');
    }
}
