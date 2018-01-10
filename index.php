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
$channel_access_token = "0D4FDkVzamcCm3LG91cvHGf/NiMjfyayLzg+dUisnA8CenfKNEI51yXcdlYmZ1nRxddVWSy2zEL8PN6Uc+Jm2eBqwiA8sBVGN9OZCubv3Nf1iNTdCVK2AyLgYlq9XlusG6tEQEWTqPh+kxK8l361rAdB04t89/1O/w1cDnyilFU=";
$channel_secret = "d43c0fabfce492506371af1608e09ee4";
 
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
                if(
                 $event['source']['type'] == 'group' or
                 $event['source']['type'] == 'room'
                ){
                //message from group / room
                    if($event['source']['userId']){
                        $userId     = $event['source']['userId'];
                        $getprofile = $bot->getProfile($userId);
                        $profile    = $getprofile->getJSONDecodedBody();
                        $greetings  = new TextMessageBuilder("Halo, ".$profile['displayName']);
                     
                        $result = $bot->replyMessage($event['replyToken'], $greetings);
                        return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                     
                    } else {
                        // send same message as reply to user
                        $result = $bot->replyText($event['replyToken'], $event['message']['text']);
                        return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                    }  
                } else {
                    //message from single usert
                    if($event['message']['type'] == 'text')
                    {
                        // send same message as reply to user
                        if (strcasecmp($event['message']['text'],'token')==0) {
                            $result = $bot->replyText($event['replyToken'], $event['replyToken']);
                        }
                        $result = $bot->replyText($event['replyToken'], $event['message']['text']);
                        
                    
         
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
