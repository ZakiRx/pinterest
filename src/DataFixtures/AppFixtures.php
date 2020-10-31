<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Pin;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $users=[];
    private $pins=[];
    protected $faker;
    public function  __construct(UserPasswordEncoderInterface  $encoder){
        $this->encoder=$encoder;
        $this->faker=Factory::create();
    }
    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setFullName($this->faker->name);
        $admin->setUsername("admin");
        $admin->setEmail($this->faker->email);
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword($this->encoder->encodePassword($admin, "zaki123"));
        $manager->persist($admin);
        $this->users[]=$admin;

        for($i=1;$i<20;$i++) {
            $user = new User();
            $user->setFullName($this->faker->name);
            $user->setUsername($this->faker->userName);
            $user->setEmail($this->faker->email);
            $user->setPassword($this->encoder->encodePassword($user, "zaki123"));
            $manager->persist($user);
            $this->users[]=$user;
        }
        $manager->flush();

        $this->loadPin($manager);
        $this->loadComment($manager);

    }
    public function loadPin(ObjectManager $manager)
    {

        for($i=0;$i<25;$i++) {
            $pin = new Pin();
            $pin->setTitle("Pin".$i);
            $pin->setDescription($this->faker->text(500));
            $pin->setApproved(0);
            $pin->setUser($this->users[rand(0,20)]);
            $manager->persist($pin);
            $this->pins[]=$pin;

        }
        $manager->flush();


    }
    public function loadComment(ObjectManager $manager)
    {

        for($i=0;$i<100;$i++) {
            $comment = new Comment();
            $comment->setComment($this->faker->text(152));
            $comment->setUserid($this->users[rand(0,19)]);
            $comment->setPinid($this->pins[rand(0,24)]);
            $manager->persist($comment);

        }
        $manager->flush();


    }
}
