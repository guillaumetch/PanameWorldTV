<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use AppBundle\Entity\Subscriber;
use AppBundle\Entity\User;
use AppBundle\Entity\Video;
use AppBundle\Form\SubscriberType;
use AppBundle\Form\VideoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="intro")
     */
    /*public function introAction(Request $request) {

        return $this->render('Front/base.html.twig');
    }*/

    /**
     * @Route("/",name="home")
     */
    public function indexAction(Request $request){
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Video');
        $repository_gallery = $this->getDoctrine()->getManager()->getRepository('AppBundle:Image');
        $videos = $repository->findAll();
        $videos = array_reverse($videos);
        $gallery = $repository_gallery->findAll();
        

        return $this->render('Front/index.html.twig',array('videos'=>$videos,'gallery'=>$gallery));
    }

    /**
     * @Route("/galerie", name="galerie")
     */
    public function galerieAction(Request $request)
    {
        return $this->render('Front/galerie.html.twig');
    }

    /**
     * @Route("/getImg", name="get_img")
     */
    public function getImgAction(Request $request)
    {
        $src = $request->get('src');
        $img = $this->renderView(':Front:img.html.twig',array('src'=>$src));
        return new JsonResponse(array('img'=>$img));
    }

    /****
     *  DASHBOARD
     **/

    /**
     * @Route("/dashboard" ,name="dashboard")
     */
    public function dashboardAction(Request $request){
        return $this->render('Dashboard/index.html.twig');
    }

    /**
     * @Route("/dashboard/videos", name="dashboard_videos")
     */
    public function listVideosAction(Request $request){
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Video');
        $videos = $repository->findAll();

        return $this->render('Dashboard/videos.html.twig',array('videos'=>$videos));
    }

    /**
     * @Route("/dashboard/video/add", name="video_add")
     */
    public function addVideosAction(Request $request)
    {
        $video = new Video();
        $form_create = $this->createForm(VideoType::class,$video);
        $form_create->handleRequest($request);

        if($request->isMethod('POST')){

            $em = $this->getDoctrine()->getManager();
            $em->persist($video);
            $em->flush();

            return $this->redirectToRoute('dashboard_videos');

        }

        return $this->render('Dashboard/video_add.html.twig',array('form_create'=>$form_create->createView()));
    }

    /**
     * @Route("/dashboard/video/update/{id}", name="video_update")
     */
    public function updateVideoAction(Request $request, Video $video){

        $form_update = $this->createForm(VideoType::class,$video);
        $form_update->handleRequest($request);

        if($form_update->isSubmitted() && $form_update->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('dashboard_videos');
        }

        $id = $video->getId();

        return $this->render('Dashboard/video_update.html.twig',array('id'=>$id,'form_update'=>$form_update->createView()));

    }

    /**
     * @Route("/dashboard/video/remove/{id}", name="video_remove")
     */
    public function videoRemoveAction(Video $video)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($video);
        $em->flush();

        return $this->redirectToRoute('dashboard_videos');
    }

    /**
     * @Route("/dashboard/gallery", name="dashboard_gallery")
     */
    public function galleryAction(Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Image');
        $gallery = $repository->findAll();

        return $this->render('Dashboard/gallery.html.twig',array('gallery'=>$gallery));
    }

    /**
     * @Route("/dashboard/gallery/add", name="gallery_add")
     */
    public function galleryAddAction(Request $request)
    {
        $image = new Image();

        $form_create  = $this->createForm('AppBundle\Form\ImageType',$image);
        $form_create->handleRequest($request);

        if($form_create->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $imageName = $image->getImageName();
            $name = uniqid();
            $image->setImageName($name);
            rename('./gallery/'.$imageName,'./gallery/'.$name);
            $em->flush();
            return $this->redirectToRoute('dashboard_gallery');

        }
        return $this->render('Dashboard/gallery_add.html.twig',array('form_create'=>$form_create->createView()));
    }

    /**
     * @Route("/dashboard/gallery/update/{id}", name="gallery_update")
     */
    public function galleryUpdateAction(Request $request,Image $image)
    {
        $form_update  = $this->createForm('AppBundle\Form\ImageType',$image);
        $form_update->handleRequest($request);

        if($form_update->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->merge($image);
            $em->flush();

            return $this->redirectToRoute('dashboard_gallery');

        }
        return $this->render('Dashboard/gallery_update.html.twig',array('form_update'=>$form_update->createView()));
    }

    /**
     * @Route("/dashboard/gallery/remove/{id}", name="gallery_remove")
     */
    public function galleryRemoveAction(Request $request, Image $image)
    {
        $name= $image->getImageName();
        unlink('./gallery/'.$name);
        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

        return $this->redirectToRoute('dashboard_gallery');
    }
}
