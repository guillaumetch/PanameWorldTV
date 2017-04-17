<?php

namespace AppBundle\Controller;

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
    public function introAction(Request $request) {

        return $this->render('base.html.twig');
    }

    /**
     * @Route("/home",name="home")
     */
    public function indexAction(Request $request){

        $subscriber = new Subscriber();
        $form_create = $this->createForm(SubscriberType::class,$subscriber);
        $form_create->handleRequest($request);

        if($request->isMethod('POST')){

            $em = $this->getDoctrine()->getManager();
            $em->persist($subscriber);
            $em->flush();

            return $this->redirectToRoute('home');

        }

        return $this->render('index.html.twig',array('form_create'=>$form_create->createView()));
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request){

        if($request->isMethod('POST'))
        {
            $password = $request->get('password');
            $password = hash('sha512',$password);

            $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');

            $finder = $repository->findBy(array('password'=>$password));

            if(empty($finder)){
                return $this->redirectToRoute('login');
            }
            else{
                return $this->redirectToRoute('dashboard');
            }
        }

        return $this->render('login.html.twig');
    }

    /**
     * @Route("/dashboard" ,name="dashboard")
     */
    public function dashboardAction(Request $request){
        return $this->render('dashboard.html.twig');
    }

    /**
     * @Route("/dashboard/videos", name="dashboard_videos")
     */
    public function listVideosAction(Request $request){
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Video');
        $videos = $repository->findAll();

        return $this->render('videos.html.twig',array('videos'=>$videos));
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

        return $this->render('video_add.html.twig',array('form_create'=>$form_create->createView()));
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

        return $this->render('video_update.html.twig',array('id'=>$id,'form_update'=>$form_update->createView()));

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
}
