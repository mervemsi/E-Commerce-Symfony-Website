<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Product;
use App\Form\Admin\ProductType;
use App\Repository\Admin\CategoryRepository;
use App\Repository\Admin\ProductRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/product")
 */
class ProductController extends Controller
{
    /**
     * @Route("/", name="admin_product_index", methods="GET")
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('admin/product/index.html.twig', ['products' => $productRepository->findAll()]);
    }

    /**
     * @Route("/new", name="admin_product_new", methods="GET|POST")
     */
    public function new(Request $request, CategoryRepository $categoryrepository): Response
    {

        $product = new Product();
        
        $catlist = $categoryrepository->findAll();
        $catname = $categoryrepository->findBy(['id' =>  $product->getCategoryId()]);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('admin/product/new.html.twig', [
            'product' => $product,
            'catname' => $catname,
            'catlist' => $catlist,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_product_show", methods="GET")
     */
    public function show(Product $product): Response
    {
        return $this->render('admin/product/show.html.twig', ['product' => $product]);
    }

    /**
     * @Route("/{id}/edit", name="admin_product_edit", methods="GET|POST")
     */
    public function edit(Request $request, Product $product, CategoryRepository $categoryrepository): Response
    {

        $catlist = $categoryrepository->findAll(); // repository depo anlaminda veriler depo ediliyor

        //echo $product->getCategoryId(); deneme amacli yazdik bunu formdan ne aliyoruz diye. 
        //mesela category idsi 2 olani edit yapiyormusuz

        $catname = $categoryrepository->findBy(['id' =>  $product->getCategoryId()]);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success','Updating product is successful');

            return $this->redirectToRoute('admin_product_edit', ['id' => $product->getId()]);
        }

        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'catlist' => $catlist,
            'catname' => $catname,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/iedit", name="admin_product_iedit", methods="GET|POST")
     */
    public function iedit(Request $request, $id, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_product_edit', ['id' => $product->getId()]);
        }

        return $this->render('admin/product/image_edit.html.twig', [
            'product' => $product,
            'id' => $id,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/iupdate", name="admin_product_iupdate", methods="POST")
     */
    public function iupdate(Request $request, $id, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

            //dump($request);
            //die();
            $file=$request->files->get('imagename'); //imagename comes from the value name of the file input type on image_edit.html.twig file
            $fileName=$this->generateUniqueFileName().'.'.$file->guessExtension();
            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter('images_directory'), //service.yaml da tanımladıgımız resim yolu
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            $product->setImage($fileName);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_product_iedit', ['id' => $product->getId()]);
    }


    /**
     * @Route("/{id}", name="admin_product_delete", methods="DELETE")
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('admin_product_index');
    }

     /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

}
