<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Entity\Admin\Messages;
use App\Entity\Admin\Category;
use App\Form\UserType;
use App\Form\ShopcartType;
use App\Repository\ShopcartRepository;
use App\Form\Admin\MessagesType;
use App\Repository\UserRepository;
use App\Repository\Admin\ImageRepository;
use App\Repository\Admin\SettingRepository;
use App\Repository\Admin\ProductRepository;
use App\Repository\Admin\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index(SettingRepository $settingRepository, ShopcartRepository $shopcartRepository)
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
        $data= $settingRepository->findAll();

        //get data for slider
        $em=$this->getDoctrine()->getManager();
        $sql="SELECT * FROM product WHERE title='slider'";
        $statement=$em->getConnection()->prepare($sql);
        //$statement->bindValue('parentid',$parent);
        $statement->execute();
        $sliders=$statement->fetchAll();
        //dump($sliders);
        //die();

        $cats=$this->fetchCategoryTreeList();

        //dump($cats);
        //print_r($cats);
        //die();

        $cats[0]='<ul id="menu-v">';

        //print_r($cats);
        //die();

        //get data for slider
        $emlatestproducts=$this->getDoctrine()->getManager();
        $sqllatestproducts="SELECT * FROM product WHERE amount>=15";
        $statementlatestproducts=$emlatestproducts->getConnection()->prepare($sqllatestproducts);
        $statementlatestproducts->execute();
        $latestproducts=$statementlatestproducts->fetchAll();

        $featuredproductsone=$this->getDoctrine()->getManager();
        $sqlfeaturedproductsone="SELECT * FROM product WHERE category_id=7 LIMIT 4";
        $statementfeaturedproductsone=$featuredproductsone->getConnection()->prepare($sqlfeaturedproductsone);
        $statementfeaturedproductsone->execute();
        $featuredproductsone=$statementfeaturedproductsone->fetchAll();

        $featuredproductstwo=$this->getDoctrine()->getManager();
        $sqlfeaturedproductstwo="SELECT * FROM product WHERE category_id=11 AND sprice>=40 LIMIT 4";
        $statementfeaturedproductstwo=$featuredproductstwo->getConnection()->prepare($sqlfeaturedproductstwo);
        $statementfeaturedproductstwo->execute();
        $featuredproductstwo=$statementfeaturedproductstwo->fetchAll();

        $featuredproductsthree=$this->getDoctrine()->getManager();
        $sqlfeaturedproductsthree="SELECT * FROM product WHERE category_id=12 AND sprice>=28 LIMIT 4";
        $statementfeaturedproductsthree=$featuredproductsthree->getConnection()->prepare($sqlfeaturedproductsthree);
        $statementfeaturedproductsthree->execute();
        $featuredproductsthree=$statementfeaturedproductsthree->fetchAll();

        $featuredproductsfour=$this->getDoctrine()->getManager();
        $sqlfeaturedproductsfour="SELECT * FROM product WHERE category_id=17 AND sprice>=28 LIMIT 4";
        $statementfeaturedproductsfour=$featuredproductsfour->getConnection()->prepare($sqlfeaturedproductsfour);
        $statementfeaturedproductsfour->execute();
        $featuredproductsfour=$statementfeaturedproductsfour->fetchAll();

        return $this->render('home/index.html.twig', [
            'data' => $data,
            'cats' => $cats,
            'sliders' => $sliders,
            'count' => $count,
            'total' => $total,
            'latestproducts' => $latestproducts,
            'featuredproductsone' => $featuredproductsone,
            'featuredproductstwo' => $featuredproductstwo,
            'featuredproductsthree' => $featuredproductsthree,
            'featuredproductsfour' => $featuredproductsfour,
        ]);
    }

    /**
     * @Route("/latestproducts", name="latestproducts")
     */
    public function latestproducts(SettingRepository $settingRepository, CategoryRepository $categoryRepository, ShopcartRepository $shopcartRepository)
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

        $cats=$this->fetchCategoryTreeList();

        $cats[0]='<ul id="menu-v">';

        $em=$this->getDoctrine()->getManager();
        $sql="SELECT * FROM product WHERE amount>=15";
        $statement=$em->getConnection()->prepare($sql);
        $statement->execute();
        $latestproducts=$statement->fetchAll();

        return $this->render('home/latestproducts.html.twig', [
            'latestproducts' => $latestproducts,
            'cats' => $cats,
            'total' => $total,
            'count' => $count,
        ]);
    }

    /**
     * @Route("/topproducts", name="topproducts")
     */
    public function topproducts(SettingRepository $settingRepository, CategoryRepository $categoryRepository, ShopcartRepository $shopcartRepository)
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

        $cats=$this->fetchCategoryTreeList();

        $cats[0]='<ul id="menu-v">';

        $em=$this->getDoctrine()->getManager();
        $sql="SELECT * FROM product WHERE amount<=10";
        $statement=$em->getConnection()->prepare($sql);
        $statement->execute();
        $topproducts=$statement->fetchAll();

        return $this->render('home/topproducts.html.twig', [
            'topproducts' => $topproducts,
            'cats' => $cats,
            'total' => $total,
            'count' => $count,
        ]);
    }

    /**
     * @Route("/specialoffers", name="specialoffers")
     */
    public function specialoffers(SettingRepository $settingRepository, ShopcartRepository $shopcartRepository, CategoryRepository $categoryRepository)
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

        $cats=$this->fetchCategoryTreeList();

        $cats[0]='<ul id="menu-v">';

        $em=$this->getDoctrine()->getManager();
        $sql="SELECT * FROM product WHERE sprice<=50";
        $statement=$em->getConnection()->prepare($sql);
        $statement->execute();
        $specialoffers=$statement->fetchAll();

        //if (is_granted('IS_AUTHENTICATED_FULLY'))
        //{
            //$user=$this->getUser();
            //$userid=$user->getId();
            //$count = $shopcartRepository->getUserShopCartCount($userid);
       // }
       // else{
            //$count=0;
        //}

        return $this->render('home/specialoffers.html.twig', [
            'specialoffers' => $specialoffers,
            'cats' => $cats,
            'total' => $total,
            'count' => $count,
        ]);
    }

    /**
     * @Route("/hakkimizda", name="hakkimizda")
     */
    public function hakkimizda(SettingRepository $settingRepository, ShopcartRepository $shopcartRepository)
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

        $data= $settingRepository->findAll();

        return $this->render('home/hakkimizda.html.twig', [
            'data' => $data,
            'total' => $total,
            'count' => $count,
        ]);
    }

    /**
     * @Route("/delivery", name="delivery")
     */
    public function delivery(SettingRepository $settingRepository, ShopcartRepository $shopcartRepository)
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

        $data= $settingRepository->findAll();

        return $this->render('home/delivery.html.twig', [
            'data' => $data,
            'total' => $total,
            'count' => $count,
        ]);
    }

    /**
     * @Route("/referanslar", name="referanslar")
     */
    public function referanslar(SettingRepository $settingRepository, ShopcartRepository $shopcartRepository)
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

        $data= $settingRepository->findAll();

        return $this->render('home/referans.html.twig', [
            'data' => $data,
            'total' => $total,
            'count' => $count,
        ]);
    }

    /**
     * @Route("/iletisim", name="iletisim", methods="GET|POST")
     */
    public function iletisim(SettingRepository $settingRepository, Request $request, ShopcartRepository $shopcartRepository)
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

        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);

        $submittedToken=$request->request->get('token');

        if ($form->isSubmitted()) {

            if($this->isCsrfTokenValid('form-message',$submittedToken)){

                $em = $this->getDoctrine()->getManager();
                $em->persist($message);
                $em->flush();
                $this->addFlash('warning','Your message has been sent successfully');
                return $this->redirectToRoute('iletisim');
            }
        }

        $data= $settingRepository->findAll();

        return $this->render('home/iletisim.html.twig', [
            'data' => $data,
            'count' => $count,
            'total' => $total,
            'form' => $form->createView(),
            'message' => $message,
        ]);
    }

    public function fetchCategoryTreeList($parent=0, $user_tree_array='')
    {
        if(!is_array($user_tree_array))
            $user_tree_array=array();
        
        $em=$this->getDoctrine()->getManager();
        $sql="SELECT * FROM category WHERE status='true' AND id!=18 AND parentid=".$parent;
        $statement=$em->getConnection()->prepare($sql);
        $statement->bindValue('parentid',$parent);
        $statement->execute();
        $result=$statement->fetchAll();

        if(count($result)>0){
            $user_tree_array[]="<ul>";
            foreach($result as $row)
            {
                $user_tree_array[] = "<li> <a href='/category/".$row['id']."'>".$row['title']."</a>";
                $user_tree_array=$this->fetchCategoryTreeList($row['id'],$user_tree_array);
            }
            $user_tree_array[]="</li></ul>";
        }
        return $user_tree_array;
    }

    /**
     * @Route("/category/{catid}", name="category_products", methods="GET")
     */
    public function CategoryProducts($catid, CategoryRepository $categoryrepository, ShopcartRepository $shopcartRepository)
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

        $cats=$this->fetchCategoryTreeList();

        $cats[0]='<ul id="menu-v">';

        $data = $categoryrepository->findBy(['id' => $catid]);
       
        //dump($data);
        //die();

        $em=$this->getDoctrine()->getManager();
        $sql="SELECT * FROM product WHERE status='true' AND category_id=$catid"; //Bu sefer catid icin parametre kullaniyorum.Parametre kullanma sebebim guvenlik icin
        $statement=$em->getConnection()->prepare($sql);
        $statement->bindValue('catid',$catid);
        $statement->execute();
        $products=$statement->fetchAll();
        //dump($result);
        //die();

        return $this->render('home/products.html.twig', [
            'data' => $data,
            'cats' => $cats,
            'total' => $total,
            'count' => $count,
            'products' => $products,
        ]);
    }


    /**
     * @Route("/product/{id}", name="product_detail", methods="GET")
     */
    public function ProductDetail($id, ProductRepository $productrepository, ImageRepository $imageRepository, CommentRepository $commentRepository, ShopcartRepository $shopcartRepository)
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

        $cats=$this->fetchCategoryTreeList();
        $cats[0]='<ul id="menu-v">'; // changing first line for menu css

        $data = $productrepository->findBy(['id' => $id]);
       
        //dump($result);
        //die();

        $imagelist = $imageRepository->findBy(
            ['product_id' => $id]
        ); 

        $comment= $commentRepository->findBy(['productid' => $id]);

        return $this->render('home/product_detail.html.twig', [
            'data' => $data,
            'cats' => $cats,
            'count' => $count,
            'total' => $total,
            'imagelist' => $imagelist,
            'commentss' => $comment,
        ]);
    }

    /**
     * @Route("/newuser", name="new_user", methods="GET|POST")
     */
    public function newuser(Request $request, UserRepository $userRepository, ShopcartRepository $shopcartRepository): Response
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

        $user= new User();
        $form= $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $submittedToken=$request->request->get('token');

        if($this->isCsrfTokenValid('user-form',$submittedToken)) {
            if ($form->isSubmitted())
            {
                $emaildata=$userRepository->findBy(
                    ['email' => $user->getEmail()]
                );
                if($emaildata==null){
                    $em=$this->getDoctrine()->getManager();
                    $user->setRoles("ROLE_USER");
                    $em->persist($user);
                    $em->flush();
                    $this->addFlash('warning','Your registration has been completed successfully. You can log in.');
                    return $this->redirectToRoute('app_login');
                }
                else{
                    $this->addFlash('error',$user->getEmail()." mail address already belongs to another user!");
                    //return $this->redirectToRoute('new_user');
                    return $this->render('home/newuser.html.twig',[
                        'form' => $form->createView(),
                        'user' => $user,
                    ]);
                }
            }
        }

        return $this->render('home/newuser.html.twig',[
            'form' => $form->createView(),
            'user' => $user,
            'total' => $total,
            'count' => $count,
        ]);
    }

}
