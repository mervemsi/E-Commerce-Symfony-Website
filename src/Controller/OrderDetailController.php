<?php

namespace App\Controller;

use App\Entity\OrderDetail;
use App\Form\OrderDetailType;
use App\Form\ShopcartType;
use App\Repository\ShopcartRepository;
use App\Repository\OrderDetailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order/detail")
 */
class OrderDetailController extends Controller
{
    /**
     * @Route("/", name="order_detail_index", methods="GET")
     */
    public function index(OrderDetailRepository $orderDetailRepository, ShopcartRepository $shopcartRepository): Response
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

        return $this->render('order_detail/index.html.twig', ['order_details' => $orderDetailRepository->findAll()]);
    }

    /**
     * @Route("/new", name="order_detail_new", methods="GET|POST")
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

        $orderDetail = new OrderDetail();
        $form = $this->createForm(OrderDetailType::class, $orderDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($orderDetail);
            $em->flush();

            return $this->redirectToRoute('order_detail_index');
        }

        return $this->render('order_detail/new.html.twig', [
            'order_detail' => $orderDetail,
            'form' => $form->createView(),
            'count' => $count,
            'total' => $total,
        ]);
    }

    /**
     * @Route("/{id}", name="order_detail_show", methods="GET")
     */
    public function show(OrderDetail $orderDetail, ShopcartRepository $shopcartRepository): Response
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


        return $this->render('order_detail/show.html.twig', ['order_detail' => $orderDetail, 'count' => $count,'total' => $total,]);
    }

    /**
     * @Route("/{id}/edit", name="order_detail_edit", methods="GET|POST")
     */
    public function edit(Request $request, OrderDetail $orderDetail, ShopcartRepository $shopcartRepository): Response
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

        
        $form = $this->createForm(OrderDetailType::class, $orderDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('order_detail_edit', ['id' => $orderDetail->getId()]);
        }

        return $this->render('order_detail/edit.html.twig', [
            'order_detail' => $orderDetail,
            'form' => $form->createView(),
            'count' => $count,
            'total' => $total,
        ]);
    }

    /**
     * @Route("/{id}", name="order_detail_delete", methods="DELETE")
     */
    public function delete(Request $request, OrderDetail $orderDetail): Response
    {
        if ($this->isCsrfTokenValid('delete'.$orderDetail->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($orderDetail);
            $em->flush();
        }

        return $this->redirectToRoute('order_detail_index');
    }
}
