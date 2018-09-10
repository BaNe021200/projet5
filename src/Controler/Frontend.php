<?php
namespace App\Controler;


use App\Model\InfosuserManager;
use App\Model\MailsManager;
use App\Model\Manager;
use App\Model\Projet5_mails;
use App\Model\Projet5_user;
use App\Model\Session;
use App\Model\UserManager;

require_once 'twig.php';
require_once 'lib/functions.php';
class Frontend
{
    public function firstPage()
    {
        visit();
        setcookie("first_time_done", 'first_time_done', time() + 3600 * 24 * 365, '', '', false, true);
        require_once 'public/theme/wonder.php';


    }

    public static function home()
    {
        visit();
        $userManager = new UserManager();
        $profils= $userManager->getProfils();

        //require_once 'src/templates/frontend/home2.php';
        twigRender('frontend/home.html.twig','userdata', $profils);
    }

    public function signUp()
    {visit();
       
        twigRender('frontend/signUp.html.twig','session',$_SESSION);
    }

    public function get_registry()
    {
        visit();
        $hashPwd = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $token= str_random(60);

        $user = new Projet5_user();
        $manager= new Manager('projet5_user');



        $user
            ->setGender($_POST['gender'])
            ->setFirstName($_POST['first_name'])
            ->setLastName($_POST['last_name'])
            ->setUsername($_POST['username'])
            ->setBirthday($_POST['birthday'])
            ->setEmail($_POST['email'])
            ->setPassword($hashPwd)
            ->setConfirmationToken($token);
           // ->setRole($JSON);


        $userManager= new UserManager();
        $addUser= $userManager->save($user);
        $finalUpdate=$userManager->newUserFinalUpdate($user);
        $newUser=$manager->read($user->getId());


        if($addUser)
        {
            /*$session= new Session();
            $session->setFlash("Un email vous a été envoyé pour valider votre compte",'success');
            $session->flash();*/

            session_destroy();
            $userId=$user->getId();

            mail_utf8($newUser->getEmail(), 'Confirmation de votre compte', "Bonjour ".$newUser->getFirstName(). "
           <br> Notez votre pseudo : ".$newUser->getUsername()." <br> Votre mot de passe est celui que vous avez tapé pour vous inscrire.<br> 
            Afin de valider votre compte merci de cliquer sur ce lien<br><br>\n\nhttp://projet5-wholewildworld.thatisrockandroll.fr/confirm?id=$userId&token=$token
            <br><br>Veuillez noter que pour apparaitre sur le site vous devez impérativement orner votre profil d'une photo. Merci d'avance et à bientôt
            
            ");
            mail_utf8('benoit.rouley@thatisrockandroll.fr',"Quelqu'un vient de s'inscrire",$newUser->getUsername()." vient de s'inscrire avec l'id N° ".$newUser->getId());
            //twigRender('frontend/home.html.twig','session',$session);
            //Frontend::home('session',$session);
           // twigRender('frontend/registrySucces.html.twig','','');
            exit();
        }






    }

    public function homeUserFront($userId)
    {
        visit();
        $userManager= new UserManager();
       // $InfosManager= new InfosuserManager();
        $data= $userManager->getProfil($userId);
       // $infos=$InfosManager->read($userId);
        $infosManager= new Manager('projet5_infosuser');
        $infos=$infosManager->readUsers($userId,'user_id');


        $data=[
         'data'=>$data,
         'userInfos' =>$infos
        ];



        twigRender('frontend/homeUserFront.html.twig','data',$data);
    }

    public function sendMessage($expeditor, $receiver)
    {

        visit();
        $message = new Projet5_mails();
        $message
            ->setExpeditor(intval($_GET['expeditor']))
            ->setReceiver(intval($_GET['receiver']))
            ->setTitle($_POST['title'])
            ->setMessage($_POST['message']);

        $mailManager = new MailsManager();
        $sendMessage = $mailManager->create($message);

        if(intval($_GET['receiver'])===0)
        {
            mail_utf8('tirr@thatisrockandroll.fr',"nouveau message de ".$expeditor,"l'utilisateur N° ".$expeditor." vous a envoyé un message");
        }

        $Session = new Session();
        if ($sendMessage)
        {

            $Session->setFlash('votre message est envoyé','success');
            $Session->flash();
            //header('Location:messages');

            twigRender('messages.html.twig','','','','');
        }
        else
        {
            //$Session = new Session();
            $Session->setFlash('Une erreur est survenue votre message n\'est pas envoyé','danger');
            $Session->flash();
            twigRender('homeUser.html.twig','','','','');
        }
    }

    public function userGalerie($userId,$username)
    {
        visit();
        $imageManager = new Manager('projet5_thumbnails');
        $usermanager=new Manager('projet5_user');

        //$userGalerie= $user->frontUsergalerie($userId,$username);
        $userGalerie= $imageManager->readUsers($userId,'user_id');
        //$username=$usermanager->readQItemUser($username,"username");





        twigRender('frontend/userGalerie.html.twig','images',$userGalerie);
    }

    public function frontGalerieViewer($imageId,$username)
    {
        visit();
        $imageManager=new Manager('projet5_images');
        $view = $imageManager->readUser($imageId,'id');


        twigRender('frontend/frontGalerieViewer.html.twig','view',$view);
    }

    public function forgottenPassword()
    {
        visit();
        twigRender('frontend/forgottenPassword.html.twig','','');
    }


    public function resetPassword()
    {
        visit();
        if(!empty($_POST) && !empty($_POST['email']))
        {
            $userManager = new UserManager();
            $email = $userManager->resetPassword($_POST['email']);


            if($email)
            {
                $reset_Token = str_random(60);

                $saveResetToken = $userManager->saveResetToken($reset_Token,$email->id);
                $session = new Session();
                $session->setFlash('la démarche de réinitialisation vous a été envoyé par email','success');
                $session->flash();
                twigRender('connexion.html.twig','session',$session);
                mail_utf8($email->email, 'Réinitialisation de votre compte', "Bonjour ".$email->first_name. "
           <br> 
            Afin de réinitialiser votre compte merci de cliquer sur ce lien<br><br>\n\nhttp://projet5-wholewildworld.thatisrockandroll.fr/newPassword?id={$email->id}&token=$reset_Token
            <br> à bientôt
            
            ");
            }
            else{
                $session= new Session();
                $session->setFlash('Aucun compte ne correspond à cette adresse','danger');
                $session->flash();
                twigRender('frontend/forgottenPassword.html.twig','session',$session);

            }

        }



    }



}