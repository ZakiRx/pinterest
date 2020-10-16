<?php


namespace App\Controller\Admin;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends  AbstractController
{
    private  $userRepository;
    private  $em;
    public  function  __construct(UserRepository $userRepository,EntityManagerInterface $em)
    {
        $this->userRepository=$userRepository;
        $this->em=$em;
    }

    /**
     * @Route("/admin/users",name="admin_users")
     * @return Response
     */
    public  function index(){
        $users=$this->userRepository->findAll();
        return $this->render("Admin/user/index.html.twig",compact("users"));
    }

    /**
     * @Route("/user/{id}",name="delete_user",methods={"DELETE"})
     * @param User $user
     * @param Request $request
     * @return RedirectResponse
     */
    public function  delete(User $user,Request $request){
        if($this->isCsrfTokenValid("delete_user".$user->getId(),$request->get("_token"))){
            $this->em->remove($user);
            $this->em->flush();
            $this->addFlash("success","User Has Ben Delete");
            return $this->redirectToRoute("admin_users");
        }

        return $this->redirectToRoute("admin_users");
    }
}