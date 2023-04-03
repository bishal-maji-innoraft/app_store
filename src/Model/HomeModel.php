<?php

namespace App\Model;

use App\Entity\MusicTable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class HomeModel extends  AbstractController
{


    public function isAppUploaded($model,$em): bool
    {
        $em->persist($model);
        if($em->flush()==null){
            return true;
        }else{
            return false;
        }
    }
}