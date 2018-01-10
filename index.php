<?php
require __DIR__ . '/vendor/autoload.php';
 
use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;
 
// set false for production
$pass_signature = true;
 
// set LINE channel_access_token and channel_secret
$channel_access_token = "WQype6QKq4cOcploJwDdQLYkq+zxkTXtxQk+etGJTs1uBTDLk8o3pyE/3SR4aYmNdHPMzxEtIoooWSHJumrrpyTKw0LElDOwEZNSs++CiHfsf0pRKpZKODxzkmKrxyJPLf0XD6cQUWD83CvIImyfmAdB04t89/1O/w1cDnyilFU=";
$channel_secret = "a5920a4e3fd0d66d6a10f92c32868c55";
 
// inisiasi objek bot
$httpClient = new CurlHTTPClient($channel_access_token);
$bot = new LINEBot($httpClient, ['channelSecret' => $channel_secret]);

$configs =  [
    'settings' => ['displayErrorDetails' => true],
];
$app = new Slim\App($configs);
$bot->getProfile(userId);
$bot->getMessageContent(messageId);
// buat route untuk url homepage
$app->get('/', function($req, $res)
{
  echo "Welcome at Slim Framework";
});
 
// buat route untuk webhook
$app->post('/webhook', function ($request, $response) use ($bot, $pass_signature)
{
    // get request body and line signature header
    $body        = file_get_contents('php://input');
    $signature = isset($_SERVER['HTTP_X_LINE_SIGNATURE']) ? $_SERVER['HTTP_X_LINE_SIGNATURE'] : '';
 
    // log body and signature
    file_put_contents('php://stderr', 'Body: '.$body);
 
    if($pass_signature === false)
    {
        // is LINE_SIGNATURE exists in request header?
        if(empty($signature)){
            return $response->withStatus(400, 'Signature not set');
        }
 
        // is this request comes from LINE?
        if(! SignatureValidator::validateSignature($body, $channel_secret, $signature)){
            return $response->withStatus(400, 'Invalid signature');
        }
    }
 
    // kode aplikasi nanti disini
    $data = json_decode($body, true);
    if(is_array($data['events'])){
        foreach ($data['events'] as $event)
        {
            if ($event['type'] == 'message')
            {
                $userId     = $event['source']['userId'];
                $getprofile = $bot->getProfile($userId);
                $profile    = $getprofile->getJSONDecodedBody();
                $greetings  = new TextMessageBuilder("Halo, ".$profile['displayName']);
                if(
                 $event['source']['type'] == 'group' or
                 $event['source']['type'] == 'room'
                ){
                //message from group / room
                    if($event['source']['userId']){
                        

                        //$result = $bot->replyText($event['replyToken'], $userId);    
                        if (substr($event['message']['text'],0,2)=='IP' & strlen($event['message']['text'])==18){
                            $gg ="p" . substr($event['message']['text'],3);
                            $bb= substr($gg ,8);
                            $sc = new Scrape($gg , $bb);
                            $hasil = $sc->login();
                            if ($hasil!='Transit') {
                                # code...
                                $result = $bot->replyText($event['replyToken'], $event['message']['text'] . $hasil);
                            }else{
                                $result = $bot->replyText($event['replyToken'], $event['message']['text'] .'Tidak Dapat Diakses');
                            }
                            //$result = $bot->replyText($event['replyToken'], $event['message']['text']);
                            
                            
                        }
                        else if (substr($event['message']['text'],0,3)=='IPK' & strlen($event['message']['text'])==19){
                            $gg ="p" . substr($event['message']['text'],4);
                            $bb= substr($gg ,8);
                            $sc = new Scrape($gg , $bb);
                            $hasil = $sc->login2();
                            if ($hasil!='Transit') {
                                # code...
                                $result = $bot->replyText($event['replyToken'], $event['message']['text'] . $hasil);
                            }else{
                                $result = $bot->replyText($event['replyToken'], $event['message']['text'] .'Tidak Dapat Diakses');
                            }
                            //$result = $bot->replyText($event['replyToken'], $event['message']['text']);
                            
                            
                        }
                        // if (strpos($event['message']['text'], 'ip') !== false) {
                            
                        //     $gg ="p" . substr($event['message']['text'],3);
                        //     $bb= substr($gg ,8);
                        //     $sc = new Scrape($gg , $bb);
                        //     $hasil = $sc->login();
                        //     //$result = $bot->replyText($event['replyToken'], $event['message']['text']);
                            
                        //     $result = $bot->replyText($event['replyToken'], $event['message']['text'] . $hasil);
                        // }
                        
                        
                        return $res->withJson($result->getJSONDecodedBody(), $event['message']['text'].$result->getHTTPStatus());
                     
                    } else {
                        // send same message as reply to user
                        if (substr($event['message']['text'],0,2)=='IP' & strlen($event['message']['text'])==18){
                            // $gg ="p" . substr($event['message']['text'],3);
                            // $bb= substr($gg ,8);
                            // $sc = new Scrape($gg , $bb);
                            // $hasil = $sc->login();
                            // if ($hasil!='Transit') {
                            //     # code...
                            //     $result = $bot->replyText($event['replyToken'], $event['message']['text'] . $hasil);
                            // }else{
                            //     $result = $bot->replyText($event['replyToken'], $event['message']['text'] .'Tidak Dapat Diakses');
                            // }
                            $result = $bot->replyText($event['replyToken'], 'Add terlebih dahulu');   
                        }
                        return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                    }  
                } else {
                    //message from single usert
                    if($event['message']['type'] == 'text')
                    {
                        // send same message as reply to user
                        if (strcasecmp($event['message']['text'],'token')==0) {
                            $userId     = $event['source']['userId'];
                            $getprofile = $bot->getProfile($userId);
                            $profile    = $getprofile->getJSONDecodedBody();
                            $greetings  = new TextMessageBuilder("Halo, ".$profile['displayName']);
                            //$result = $bot->replyText($event['replyToken'], );
                            $result = $bot->replyText($event['replyToken'], $userId);
                        }
                        if (substr($event['message']['text'],0,2)=='IP' & strlen($event['message']['text'])==18){
                            $gg ="p" . substr($event['message']['text'],3);
                            $bb= substr($gg ,8);
                            $sc = new Scrape($gg , $bb);
                            $hasil = $sc->login();
                            if ($hasil!='Transit') {
                                # code...
                                $result = $bot->replyText($event['replyToken'], $event['message']['text'] . $hasil);
                            }else{
                                $result = $bot->replyText($event['replyToken'], $event['message']['text'] .'Tidak Dapat Diakses');
                            }
                            //$result = $bot->replyText($event['replyToken'], $event['message']['text']);
                            
                            
                        }
                        // or we can use replyMessage() instead to send reply message
                        // $textMessageBuilder = new TextMessageBuilder($event['message']['text']);
                        // $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
         
                        return $response->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                    }
                    if(
                        $event['message']['type'] == 'image' or
                        $event['message']['type'] == 'video' or
                        $event['message']['type'] == 'audio' or
                        $event['message']['type'] == 'file'
                    ){
                        $basePath  = $request->getUri()->getBaseUrl();
                        $contentURL  = $basePath."/content/".$event['message']['id'];
                        $contentType = ucfirst($event['message']['type']);
                        $result = $bot->replyText($event['replyToken'],
                            $contentType. " yang Anda kirim bisa diakses dari link:\n " . $contentURL);
                     
                        return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                    }
                }
            }
        }
    }

    $bot->replyText($replyToken, 'ini pesan balasan');
 
});

class Scrape{
    public $cookies=null;
    private $user = null;
    private $pass = null;

    /*Data generated from cURL*/
    public $content = null;
    public $response = null;

    /* Links */
    private $url = array(
                        'login'     => 'https://siam.ub.ac.id/index.php',
                        'submit'    => 'https://siam.ub.ac.id/index.php?action=process',
                        'logout'    => 'https://siam.ub.ac.id/logout.php'
                        
                        );

    /* Fields */
    public $data = array();

    public function __construct ($user, $pass)
    {

        $this->user = $user;
        $this->pass = $pass;        

    }

    public function login()
    {

                $this->cURL($this->url['login']);

                if($form = $this->getFormFields($this->content, 'login'))
                {
                    $form['username'] = $this->user;
                    $form['password'] =$this->pass;
                    //echo "<pre>".print_r($form,true);exit;
                    $this->cURL($this->url['submit'], $form);
                    //echo $this->content;exit;
                }
                //echo $this->content;exit;   
                //$html = file_get_contents('http://siam.ub.ac.id/khs.php'); //get the html returned from the following url
                $hasil =htmlspecialchars($this->content);
                //echo $hasil;
                $findme   = 'IP Lulus';
                $pos = strpos($hasil, $findme);
                //echo $pos;
                //echo "ip lulus";
                return substr($hasil,$pos+56,7);
    }
    public function login2()
    {

                $this->cURL($this->url['login']);

                if($form = $this->getFormFields($this->content, 'login'))
                {
                    $form['username'] = $this->user;
                    $form['password'] =$this->pass;
                    //echo "<pre>".print_r($form,true);exit;
                    $this->cURL($this->url['submit'], $form);
                    //echo $this->content;exit;
                }
                //echo $this->content;exit;   
                //$html = file_get_contents('http://siam.ub.ac.id/khs.php'); //get the html returned from the following url
                $hasil =htmlspecialchars($this->content);
                //echo $hasil;
                $pos = strpos($hasil, 'KUMULATIF');
                //echo $pos;
                //echo "ip lulus";
                return substr($hasil,$pos+153,6);
    }

    /* Scan for form */
    private function getFormFields($data, $id)
    {
            if (preg_match('/(<form.*?name=.?'.$id.'.*?<\/form>)/is', $data, $matches)) {
                $inputs = $this->getInputs($matches[1]);

                return $inputs;
            } else {
                return false;
            }

    }

    /* Get Inputs in form */
    private function getInputs($form)
    {
        $inputs = array();

        $elements = preg_match_all('/(<input[^>]+>)/is', $form, $matches);

        if ($elements > 0) {
            for($i = 0; $i < $elements; $i++) {
                $el = preg_replace('/\s{2,}/', ' ', $matches[1][$i]);

                if (preg_match('/name=(?:["\'])?([^"\'\s]*)/i', $el, $name)) {
                    $name  = $name[1];
                    $value = '';

                    if (preg_match('/value=(?:["\'])?([^"\']*)/i', $el, $value)) {
                        $value = $value[1];
                    }

                    $inputs[$name] = $value;
                }
            }
        }

        return $inputs;
    }

    /* Perform curl function to specific URL provided */
    public function cURL($url, $post = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.101 Safari/537.36");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookies);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookies);
        curl_setopt($ch, CURLOPT_HEADER, 0);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        if($post)   //if post is needed
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }

        curl_setopt($ch, CURLOPT_URL, $url); 
        $this->content = curl_exec($ch);
        $this->response = curl_getinfo( $ch );
        $this->url['last_url'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
    }
}
$app->get('/pushmessage', function($req, $res) use ($bot)
{
    // send push message to user
    $userId = 'U4f3b524bfcd08556173108d04ae067ad';
    $textMessageBuilder = new TextMessageBuilder('Halo, ini pesan push');
    $result = $bot->pushMessage($userId, $textMessageBuilder);
   
    return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
});
$app->get('/profile/{userId}', function($req, $res) use ($bot)
{
    // get user profile
    $route  = $req->getAttribute('route');
    $userId = $route->getArgument('userId');
    $result = $bot->getProfile($userId);
             
    return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
});
$app->get('/content/{messageId}', function($req, $res) use ($bot)
{
    // get message content
    $route      = $req->getAttribute('route');
    $messageId = $route->getArgument('messageId');
    $result = $bot->getMessageContent($messageId);
 
    // set response
    $res->write($result->getRawBody());
 
    return $res->withHeader('Content-Type', $result->getHeader('Content-Type'));
});
$app->run();
