<?php

namespace App\Controller;

use App\Entity\AppListTable;
use App\Entity\FavouriteTable;
use App\Entity\MusicTable;
use App\Entity\UpdateValidation;
use App\Entity\UserTable;
use App\Model\AuthModel;
use App\Model\HomeModel;

use Doctrine\DBAL\Types\IntegerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HomeController extends AbstractController
{

    /*
     * @Route("my_app", name="my_apps_route")
     */
    public function my_apps(){

    }

    /**
     * @Route("update_app", name="update_app_route")
     *
     */
    public function update_profile(EntityManagerInterface $entityManager, Request $request,ValidatorInterface $validator)
    {
        $error_arr=array();
        $form = $this->createFormBuilder([])
            ->add('app_name', TextType::class, ['label' => 'App Name'])
            ->add('description', TextType::class, ['label' => 'App Description'])
            ->add('image', FileType::class, ['label' => 'App Cover Image'])
            ->add('developer', TextType::class, ['label' => 'Developer Name'])
            ->add('apk_file_link', FileType::class, ['label' => 'Apk File'])
            ->add('update', SubmitType::class, ['label' => 'Update'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $app_list_model = new AppListTable();
            $data = $form->getData();
            $app_list_model->setAppName($data['app_name']);
            $app_list_model->setDeveloper($data['description']);
            $app_list_model->setImage($data['image']);
            $app_list_model->setDescription($data['developer']);
            $app_list_model->setApkFileLink($data['apk_file_link']);
            $errors = $validator->validate($app_list_model);//form field validation
            if (count($errors) > 0) {
                $i = 0;
                foreach ($errors as $error) {
                    $error_arr[$i] = $error->getMessage();
                    $i = $i + 1;
                }
            }else{
                $session = new Session();
                $id = $session->get('uid');
                $app = $entityManager->getRepository(AppListTable::class)->find($id);

                $entityManager->persist($app);
                if ($entityManager->flush() == null) {
                    return $this->redirectToRoute('success_route', ['message' => 'App  Updated', 'nav_route' => 'home_page_route']);
                } else {
                    return $this->redirectToRoute('error_route', ['message' => 'App not Updated', 'nav_route' => 'update_app_route']);
                }
            }

        }
        return $this->render('home/update_app.html.twig', ['update_app_form' => $form->createView(),'errors'=>$error_arr,'attr' => ['class' => 'flex']]);


    }

    /**
     * @Route("/add_app",name="add_app_route")
     * @param Request $request ;
     *
     */
    public function add_app(EntityManagerInterface $entityManager, Request $request)
    {
        $session = new Session();
        $session->start();
        $app_review_id=$session->get('uid');


        $add_app_form = $this->createFormBuilder([])
            ->add('app_name', TextType::class, ['label' => 'App Name'])
            ->add('description', TextType::class, ['label' => 'App Description'])
            ->add('image', FileType::class, ['label' => 'App Cover Image'])
            ->add('developer', TextType::class, ['label' => 'Developer Name'])
            ->add('apk_file_link', FileType::class, ['label' => 'Apk File'])
            ->add('submit_app', SubmitType::class, ['label' => 'Submit App'])
            ->getForm();
        $add_app_form->handleRequest($request);

        if ($add_app_form->isSubmitted() and $add_app_form->isValid()) {
            $data = $add_app_form->getData();
            $model = new HomeModel();
            $article = new AppListTable();
            $article->setAppName($data['app_name']);
            $article->setDeveloper($data['description']);
            $article->setImage($data['image']);
            $article->setDescription($data['developer']);
            $article->setApkFileLink($data['apk_file_link']);
            $article->setDownloadCount(0);
            $article->setUserReviewsId($app_review_id);
            $app_file = $add_app_form->get('apk_file_link')->getData();
            $image_file = $add_app_form->get('image')->getData();
            $app_fileName = md5(uniqid()) . '.' . $app_file->guessExtension();
            $image_fileName = md5(uniqid()) . '.' . $image_file->guessExtension();
            $app_file->move($this->getParameter('app_directory'), $app_fileName);
            $image_file->move($this->getParameter('image_directory'), $image_fileName);
            $article->setApkFileLink($app_fileName);
            $article->setImage($image_fileName);

            if ($model->isAppUploaded($article, $entityManager)) {
                return $this->redirectToRoute('success_route', ['message' => 'Application file added successfully', 'nav_route' => 'home_page_route']);
            } else {
                return $this->redirectToRoute('error_route', ['message' => 'Upload Failed', 'nav_route' => 'home_page_route']);
            }
        }
        return $this->render('home/upload_app.html.twig', ['add_app_form' => $add_app_form->createView()]);

    }


    /**
     * @Route("/home", name="home_page_route")
     */
    public function home(EntityManagerInterface $entityManager, Request $request): Response
    {
        $session = new Session();
        $session->start();
        $login = $session->get('login');
        if (!$login) {
            return $this->redirectToRoute('index');
        }
        //home buttons form
        $form = $this->createFormBuilder([])
            ->add('add_app', SubmitType::class, ['label' => 'Add App'])
            ->add('logout', SubmitType::class, ['label' => 'Logout'])
            ->add('update_app', SubmitType::class, ['label' => 'Update App'])
            ->add('my_apps', SubmitType::class, ['label' => 'My Apps'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            if ($form->get('add_app')->isClicked()) {
                return $this->redirectToRoute('add_app_route');
            }
            if ($form->get('logout')->isClicked()) {
                return $this->redirectToRoute('logout_route');
            }
            if ($form->get('update_app')->isClicked()) {
                return $this->redirectToRoute('update_app_route');
            }
            if ($form->get('my_apps')->isClicked()) {
                return $this->redirectToRoute('my_apps_route');
            }


        }

        $app_list_repo = $entityManager->getRepository(AppListTable::class);
        $app_list = $app_list_repo->findAll();

        $uid = $session->get('uid');
        return $this->render('home/home_page.html.twig',  ['home_buttons_form' => $form->createView(),'app_list' => $app_list, 'uid' => $uid,'attr' => ['class' => 'flex']]);
    }

    /**
     *
     * @Route("/user_reviews{id}", name="user_reviews_route")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param $id
     *
     */
    public function get_user_review(EntityManagerInterface $entityManager, Request $request, $id)
    {

    }


}
