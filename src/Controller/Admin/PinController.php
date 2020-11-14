<?php

namespace App\Controller\Admin;

use App\Entity\Pin;
use App\Entity\User;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PinController extends AbstractController
{

    private $em;
    private $repository;
    private $isVerified="";


    public  function  __construct(EntityManagerInterface $em,PinRepository $repository,Security $security)
    {
        $this->em=$em;
        $this->repository=$repository;
        if($security->getUser()) {
            $user = $security->getUser();
            if ($user instanceof User) {
                $this->isVerified = $user->isVerified();
            }
        }
    }

    /**
     * @Route("/admin/pins", name="admin_pins_index")
     */
    public function index():Response
    {
        $pins=$this->repository->findBy([],['createdAt'=>'DESC']);

        return $this->render("Admin/pin/index.html.twig",[
            "pins"=>$pins
            ]);
    }
    /*Pins Each User*/
    /**
     * @Route("/user/profile/pins", name="user_pins")
     */
    public function PinsUSer():Response
    {
        $pins=$this->repository->findBy(['user'=>$this->getUser()],['createdAt'=>'DESC']);

        return $this->render("user/pins.html.twig",[
            "pins"=>$pins
        ]);
    }

    /**
     * @Route("/", name="pins_index")
     */
    public function pins():Response
    {
        $pins=$this->repository->approvedPins();

        return $this->render("pin/index.html.twig",[
            "pins"=>$pins
        ]);
    }
    /**
     * @Route("/pin/create",name="create_pin",methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function  new(Request $request,UserRepository $userRepository):Response{
        if($this->isVerified) {
            $pin = new Pin();

            $form = $this->createForm(PinType::class, $pin);

            $form->handleRequest($request);
            if ($this->isCsrfTokenValid("pin" . $pin->getId(), $request->get("_token"))) {
                if ($form->isSubmitted() && $form->isValid()) {
                    $pin->setUser($this->getUser());
                    if(in_array("ROLE_ADMIN" , $this->getUser()->getRoles())){
                        $pin->setApproved(1);

                    }
                    else
                    $pin->setApproved(0);

                    $this->em->persist($pin);
                    $this->em->flush();
                    $this->addFlash("success", "Pin Has Been Added Wait Confirm Your Pin By Moderator ");
                    return $this->redirectToRoute("pins_index");

                } else {
                    return $this->render("pin/new.html.twig", [
                        "pin" => $pin,
                        "form" => $form->createView(),

                    ]);
                }
            }

            return $this->render("pin/new.html.twig", [
                "pin" => $pin,
                "form" => $form->createView(),

            ]);
        }
        else
        {
            return $this->redirectToRoute("pins_index");
        }
    }

    /**
     * @Route("/pin/{id}",name="show_pin",methods={"GET"})
     * @param Pin $pin
     * @param Request $request
     * @return Response
     */

    public  function show(Pin $pin,Request $request){

        return $pin->getApproved() ? $this->render("pin/show.html.twig",compact("pin")) : $this->redirectToRoute("pins_index");
    }

    /**
     * @Route("/pin/{id}/edit",name="edit_pin",methods={"GET","PUT"})
     * @param Request $request
     * @param Pin $pin
     * @return Response
     */
    public function  edit(Request $request,Pin $pin){
        if($this->isVerified && $pin->getUser() == $this->getUser()) {

            $form = $this->createForm(PinType::class, $pin, [
                "method" => "PUT"
            ]);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($this->isCsrfTokenValid("pin" . $pin->getId(), $request->get("_token"))) {
                    $this->em->persist($pin);
                    $this->em->flush();
                    $this->addFlash("success", "Pin Has Been updated");
                    return $this->redirectToRoute("admin_pins_index");
                } else {
                    return $this->render("pin/edit.html.twig", [
                        "form" => $form->createView(),
                        "pin" => $pin
                    ]);
                }


            }

            return $this->render("pin/edit.html.twig", [
                "form" => $form->createView(),
                "pin" => $pin
            ]);
        }
        else
           return $this->redirectToRoute("pins_index");
    }
    /**
     * @Route("/pin/approved/{id}",name="approve_pin",methods={"POST"})
     * @param Pin $pin
     * @param Request $request
     * @return Response
     */

    public  function approvedPin(Pin $pin,Request $request){
        $pin->setApproved(1);
        $this->em->persist($pin);
        $this->em->flush();
        $this->addFlash("success","Pin Has Benn Approved");
        return $this->redirectToRoute("admin_pins_index");
    }

    /**
     * @Route("/pin/{id}",name="delete_pin",methods={"DELETE"})
     * @param Request $request
     * @param Pin $pin
     * @return Response
     */
    public function  delete(Request $request,Pin $pin)
    {

        if ($this->isVerified && $pin->getUser() == $this->getUser()) {

            if ($this->isCsrfTokenValid("delete_pin" . $pin->getId(), $request->get("_token"))) {
                $this->em->remove($pin);
                $this->em->flush();
                $this->addFlash("success", "Pin Has Been Deleted");
                return $this->redirectToRoute("pins_index");
            }
            return $this->redirectToRoute("pins_index");


        }
        else
           return $this->redirectToRoute("pins_index");
    }


}
