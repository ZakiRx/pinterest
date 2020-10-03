<?php

namespace App\Controller\Admin;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ManagePinController extends AbstractController
{

    private $em;
    private $repository;

    public  function  __construct(EntityManagerInterface $em,PinRepository $repository)
    {
        $this->em=$em;
        $this->repository=$repository;
    }

    /**
     * @Route("/admin/pin", name="admin_pins_index")
     */
    public function index():Response
    {
        $pins=$this->repository->findAll();

        return $this->render("Admin/pin/index.html.twig",compact("pins"));
    }

    /**
     * @Route("/admin/pin/create",name="admin_create_pin",methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function  new(Request $request,UserRepository $userRepository):Response{
        $pin = new Pin();
        $user= $userRepository->findOneBy(["username"=>"zaki"]);


        $form=$this->createForm(PinType::class,$pin);

        $form->handleRequest($request);
        if($this->isCsrfTokenValid("pin". $pin->getId(),$request->get("_token"))) {
            if ($form->isSubmitted() && $form->isValid()) {
                $pin->setUser($user);
                $this->em->persist($pin);
                $this->em->flush();
                $this->addFlash("success","Pin Has Been Added");
                return $this->redirectToRoute("admin_pins_index");

            }else{
                return $this->render("Admin/pin/new.html.twig",[
                    "pin"=>$pin,
                    "form"=>$form->createView(),

                ]);
            }
        }

        return $this->render("Admin/pin/new.html.twig",[
            "pin"=>$pin,
            "form"=>$form->createView(),

        ]);
    }

    /**
     * @Route("/admin/pin/{id}/edit",name="admin_edit_pin",methods={"GET","PUT"})
     * @param Request $request
     * @param Pin $pin
     * @return Response
     */
    public function  edit(Request $request,Pin $pin){

        $form=$this->createForm(PinType::class,$pin,[
            "method"=>"PUT"
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($this->isCsrfTokenValid("pin". $pin->getId(),$request->get("_token"))) {
                $this->em->persist($pin);
                $this->em->flush();
                $this->addFlash("success","Pin Has Been updated");
                return $this->redirectToRoute("admin_pins_index");
            }else{
                return $this->render("Admin/pin/edit.html.twig",[
                    "form"=>$form->createView(),
                    "pin"=>$pin
                ]);
            }


        }

        return $this->render("Admin/pin/edit.html.twig",[
            "form"=>$form->createView(),
            "pin"=>$pin
        ]);
    }

    /**
     * @Route("/admin/pin/{id}/delete",name="admin_delete_pin",methods={"DELETE"})
     * @param Request $request
     * @param Pin $pin
     * @return Response
     */
    public function  delete(Request $request,Pin $pin){


          if($this->isCsrfTokenValid("delete_pin". $pin->getId(),$request->get("_token"))){
              $this->em->remove($pin);
              $this->em->flush();
              $this->addFlash("success","Pin Has Been Deleted");
              return $this->redirectToRoute("admin_pins_index");
          }
            return $this->redirectToRoute("admin_pins_index");


    }
}
