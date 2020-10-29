<?php


namespace App\Controller\Admin;


use App\Entity\Comment;
use App\Entity\Pin;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\TimeBundle\DateTimeFormatter;
class CommentController extends AbstractController
{
    private $em;
    private $repository;
    public  function  __construct(EntityManagerInterface $em,CommentRepository $repository)
    {
        $this->em=$em;
        $this->repository=$repository;
    }

    /**
     * @Route("/pin/{id}/comment",name="pin_add_comment")
     * @param Pin $pin
     * @param Request $request
     * @param DateTimeFormatter|null $dateTimeFormatter
     * @return JsonResponse
     */

    public  function new(Pin $pin,Request $request,?DateTimeFormatter $dateTimeFormatter): JsonResponse
    {
        $user= $this->getUser();
        if($user){
            $comment= new Comment();
            $data=$request->getContent();
            $data=json_decode($data);
            $commentdata=$data->comment;
            $comment->setComment($commentdata);
            $comment->setUserid($user);
            $comment->setPinid($pin);
            $this->em->persist($comment);
            $this->em->flush();

            $now = new \DateTime();
            $createdAt = $dateTimeFormatter->formatDiff($comment->getCreatedAt(), $now);

            return $this->json([
                "message"=>"Comment added",
                "comment"=> $commentdata,
                "createdAt"=>  $createdAt
            ],200);

        }
        else{
            return $this->json([
                "message"=>"Not Authorized "
            ],403);
        }

    }
    /**
     * @Route("/pin/{id}/comment/delete",name="pin_delete_comment")
     * @param Pin $pin
     * @param Request $request
     * @param DateTimeFormatter|null $dateTimeFormatter
     * @return JsonResponse
     */
    public  function delete(Pin $pin,Request $request,?DateTimeFormatter $dateTimeFormatter): JsonResponse
    {
        $comment=$this->repository->findOneBy(['pinid'=>$pin,
            'userid'=>$this->getUser()]);
        if($comment){
            $this->em->remove($comment);
            $this->em->flush();
        }
    }



}