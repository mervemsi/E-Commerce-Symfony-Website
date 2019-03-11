<?php

namespace App\Controller\Admin;

use App\Entity\Orders;
use App\Entity\OrderDetail;
use App\Repository\OrdersRepository;
use App\Repository\ShopcartRepository;
use App\Repository\OrderDetailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Admin\Responce;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }   
   

    /**
     * @Route("/admin/orders/{slug}", name="admin_orders_index")
     */
    public function orders($slug, OrdersRepository $ordersRepository)
    {
        $orders = $ordersRepository->findBy(['status' => $slug ]);
        //dump($orders);
        //die();
        return $this->render('admin/orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }   


    /**
     * @Route("/admin/orders/show/{id}", name="admin_orders_show", methods="GET")
     */
    public function show($id, Orders $orders, OrderDetailRepository $orderDetailRepository)
    {
        $orderdetail = $orderDetailRepository->findBy(['orderid' => $id ]);
        //dump($orderdetail);
        //die();
        return $this->render('admin/orders/show.html.twig', [
            'order' => $orders,
            'orderdetail' => $orderdetail,
        ]);
    }  


     /**
     * @Route("/admin/orders/{id}/update", name="admin_orders_update", methods="POST")
     */
    public function update($id, Request $request, Orders $orders)
    {
        $shipinfo=$request->get("shipinfo");
        $note=$request->get("note");
        $status=$request->get("status");

        $em = $this->getDoctrine()->getManager();
        $sql="UPDATE orders SET shipinfo=:shipinfo, status=:status, note=:note WHERE id=:id";
        $statement=$em->getConnection()->prepare($sql);
        $statement->bindValue('shipinfo', $request->request->get('shipinfo'));
        $statement->bindValue('note', $request->request->get('note'));
        $statement->bindValue('status', $request->request->get('status'));
        $statement->bindValue('id', $id);
        $statement->execute();
        $this->addFlash('success','Ordering information updating is successful');

        return $this->redirectToRoute('admin_orders_show', array('id' => $id));
    }    

}
