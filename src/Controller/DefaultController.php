<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;

use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    public function connectAction(Request $request, $app_id, $app_secret, $my_url ){

        $session = new Session();

        //get get variables
        $code = $request->query->get('code');
        $state = $request->query->get('state');

        //use deezer oauth api
        if(empty($code)){
            $session->set('state',  md5(uniqid(rand(), TRUE)));//CSRF protection
            $dialog_url = "https://connect.deezer.com/oauth/auth.php?app_id=".$app_id
                ."&redirect_uri=".urlencode($my_url)."&perms=manage_library,delete_library"
                ."&state=". $session->get('state');

            header("Location: ".$dialog_url);
            exit;

        }

        //get info from deezer oauth api
       if($state == $session->get('state')) {
            $token_url = "https://connect.deezer.com/oauth/access_token.php?app_id="
                .$app_id."&secret="
                .$app_secret."&code=".$code;

           //get data from deezer api
            $response  = file_get_contents($token_url);
            $params    = null;
            parse_str($response, $params);

            //create cookie to store token
           $response = new Response();
           $response->prepare($request);
           $response->headers->clearCookie('token');//clear previous token
           $response->headers->setCookie(new Cookie('token', $params['access_token']));
           $response->send();

           return $this->redirectToRoute('favoriteTracks');

        }else{
           //thrown error 500
           throw new \Exception("The state does not match. You may be a victim of CSRF.");
        }

    }

    /*
     * Method to get Data in array form API
     */
    function getApiData($token){

        //get data from api
        $ch=curl_init("http://api.deezer.com/user/me/tracks?access_token=".$token);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $answer = curl_exec($ch);

        //close curl
        curl_close($ch);

        //get json data
        $resArr = array();
        $resArr = json_decode($answer);

        return $resArr;
    }
    
    /*
     * Method to add a song to the playlist
     */
    function addSong($token,$formData){

        $data = $formData->getData();

        $ch=curl_init("http://api.deezer.com/user/me/tracks?access_token=".$token);
        
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, "track_id=".$data['TrackId']);
        
        $answer = curl_exec($ch);
        curl_close($ch);

        //get boolean return
        $resArr = json_decode($answer);

        return $resArr;
    }

    /**
     * Main Page which list favoriteTracks
     *
     */
    public function favoriteTracksAction(Request $request,$returnValue="",$modificationText="")
    {

        //get token
        $token=$request->cookies->get("token");

        //redirect to connect if no token found
        if($token==""){
            return $this->redirectToRoute('connect');
            exit;
        }

        /*create form to add song */
        $form = $this->createFormBuilder()
            ->add('TrackId', IntegerType::class,array('label' => 'Add a favorite track Id'))
            ->getForm();

        ;

        //handle form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $returnValue=self::addSong($token,$form);

            if($returnValue === true){
                $modificationText="adding a new favorite song";
            }else{
                $returnValue = false;
                $modificationText="track id does not exist";
            }
        }



        //to get data from the API
        $favoriteTracks=self::getApiData($token);

        return $this->render('view.html.twig',
                array(
                    'form' => $form->createView(),
                    'favoriteTracks' => $favoriteTracks,
                    'Modification' => $returnValue,
                    'modificationText' => $modificationText
                    )
                );
    }
    
    
    public function DeleteTracksAction(Request $request)
    {
        //get trackid
        $trackid=(int)$request->query->get('track_id');

        //get token
        $token=$request->cookies->get("token");
        
        $ch=curl_init("http://api.deezer.com/user/me/tracks?access_token=".$token."&track_id=".$trackid);
        
        //set request parameter
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "DELETE");
        
     
        $answer = curl_exec($ch);
        $resArr = json_decode($answer);

        //close curl
        curl_close($ch);

        if($resArr !== true){
            $resArr = false;
        }

        //notice message
        $modificationText="deleting a favorite song";

        //forward to favorite tracks page
        return $this->forward('App\Controller\DefaultController::favoriteTracksAction',array('returnValue' => $resArr,'modificationText' => $modificationText));
    }
}
