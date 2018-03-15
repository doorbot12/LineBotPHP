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

include 'codenya.php';
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
                        //$stored = file_get_contents('http://farkhan.000webhostapp.com/tae/stupid.php?data='.urlencode($event['message']['text']).'&groupid='.$event['source']['groupId']);
                        $a = (explode('-',$event['message']['text']));
                        if ($a[0]=="/tambah") {
                            $stored = file_get_contents('http://farkhan.000webhostapp.com/tae/storeData.php?groupid='.$event['source']['groupId'].'&nama_jadwal='.urlencode($a[1]).'&isi_jadwal='.urlencode($a[2]));
                            $obj = json_decode($stored, TRUE);
                            $result = $bot->replyText($event['replyToken'], $obj['message']);
                        }
                        else if ($a[0]=="/semua") {
                            $stored = file_get_contents('http://farkhan.000webhostapp.com/tae/GetData.php?groupid='.$event['source']['groupId']);
                            $datanya = json_decode($stored, TRUE);
                            $hasilnya="Note Yang Disimpan";
                            if (is_array($datanya) || is_object($datanyas)) {
                                foreach ($datanya as $datanyas) {
                                    echo $datanyas['jadwal'];
                                    foreach($datanyas as $datanyass)
                                    {
                                        $hasilnya=$hasilnya."\n".$datanyass['nama_jadwal'];
                                    }
                                }   
                            }
                            
                            $result = $bot->replyText($event['replyToken'],$hasilnya);
                        }else if ($a[0]=="/detail") {
                            $stored = file_get_contents('http://farkhan.000webhostapp.com/tae/GetData.php?groupid='.$event['source']['groupId'].'&nama_jadwal='.urlencode($a[1]));
                            $datanya = json_decode($stored, TRUE);
                            $hasilnya="Detail Note ".$a[1];
                            if (is_array($datanya) || is_object($datanyas)) {
                                foreach ($datanya as $datanyas) {
                                    echo $datanyas['jadwal'];
                                    foreach($datanyas as $datanyass)
                                    {
                                        $hasilnya=$hasilnya."\n".$datanyass['detail'];
                                    }
                                }   
                            }
                            
                            $result = $bot->replyText($event['replyToken'],$hasilnya);
                        }else if ($a[0]=="/hapus") {
                            $stored = file_get_contents('http://farkhan.000webhostapp.com/tae/deleteNote.php?groupid='.$event['source']['groupId'].'&nama_jadwal='.urlencode($a[1]));
                            $obj = json_decode($stored, TRUE);
                            $result = $bot->replyText($event['replyToken'], $obj['message']);
                        }else if ($a[0]=="/help") {
                            $help="menambah note\n/tambah-nama note-detail note\nmelihat semua note\n/semua\nmelihat detail note\n/detail-nama note\nmenghapus note\n/hapus-nama note";
                            $result = $bot->replyText($event['replyToken'], $help);
                        }
                        // if (substr($event['message']['text'],0,2)=='IP' & strlen($event['message']['text'])==18) {
                        //     $gg ="p" . substr($event['message']['text'],3);
                        //     $bb= substr($gg ,8);
                        //     $sc = new Scrape($gg , $bb);
                        //     $hasil = $sc->login();
                        //     if ($hasil!='Transit') {
                        //         $result = $bot->replyText($event['replyToken'], $event['message']['text'] . $hasil);
                        //     }else{
                        //         $result = $bot->replyText($event['replyToken'], $event['message']['text'] .'Tidak Dapat Diakses');
                        //     }                                
                        // }else if (substr($event['message']['text'],0,3)=='IPK' & strlen($event['message']['text'])==19){
                        //     $gg ="p" . substr($event['message']['text'],4);
                        //     $bb= substr($gg ,8);
                        //     $sc = new Scrape($gg , $bb);
                        //     $hasil = $sc->login2();
                        //     if ($hasil!='t;html') {
                        //         $result = $bot->replyText($event['replyToken'], $event['message']['text'] . $hasil);
                        //     }else{
                        //         $result = $bot->replyText($event['replyToken'], $event['message']['text'] .'Tidak Dapat Diakses');
                        //     }
                        // }  
                        return $res->withJson($result->getJSONDecodedBody(), $event['message']['text'].$result->getHTTPStatus());
                    } else {
                        if (substr($event['message']['text'],0,2)=='IP' & strlen($event['message']['text'])==18){
                            $result = $bot->replyText($event['replyToken'], 'Add terlebih dahulu');   
                        }
                        return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                    }  
                } else {
                    if($event['message']['type'] == 'text')
                    {
                        $userId     = $event['source']['userId'];
                        $getprofile = $bot->getProfile($userId);
                        $profile    = $getprofile->getJSONDecodedBody();
                        $a = (explode('-',$event['message']['text']));
                        if ($userId=="U4f3b524bfcd08556173108d04ae067ad") {
                            if ($a[0]=="/betatest") {
                                $result = $bot->replyText($event['replyToken'], "anda masuk beta test");
                            }
                        }

                        if ($a[0]=="/tambah") {
                            $stored = file_get_contents('http://farkhan.000webhostapp.com/tae/storeData.php?groupid='.$event['source']['userId'].'&nama_jadwal='.urlencode($a[1]).'&isi_jadwal='.urlencode($a[2]));
                            $obj = json_decode($stored, TRUE);
                            $result = $bot->replyText($event['replyToken'], $obj['message']);
                        }
                        else if ($a[0]=="/semua") {
                            $stored = file_get_contents('http://farkhan.000webhostapp.com/tae/GetData.php?groupid='.$event['source']['userId']);
                            $datanya = json_decode($stored, TRUE);
                            $hasilnya="Note Yang Disimpan";
                            if (is_array($datanya) || is_object($datanyas)) {
                                foreach ($datanya as $datanyas) {
                                    echo $datanyas['jadwal'];
                                    foreach($datanyas as $datanyass)
                                    {
                                        $hasilnya=$hasilnya."\n".$datanyass['nama_jadwal'];
                                    }
                                }   
                            }
                            
                            $result = $bot->replyText($event['replyToken'],$hasilnya);
                        }else if ($a[0]=="/detail") {
                            $stored = file_get_contents('http://farkhan.000webhostapp.com/tae/GetData.php?groupid='.$event['source']['userId'].'&nama_jadwal='.urlencode($a[1]));
                            $datanya = json_decode($stored, TRUE);
                            $hasilnya="Detail Note ".$a[1];
                            if (is_array($datanya) || is_object($datanyas)) {
                                foreach ($datanya as $datanyas) {
                                    echo $datanyas['jadwal'];
                                    foreach($datanyas as $datanyass)
                                    {
                                        $hasilnya=$hasilnya."\n".$datanyass['detail'];
                                    }
                                }   
                            }
                            
                            $result = $bot->replyText($event['replyToken'],$hasilnya);
                        }else if ($a[0]=="/hapus") {
                            $stored = file_get_contents('http://farkhan.000webhostapp.com/tae/deleteNote.php?groupid='.$event['source']['userId'].'&nama_jadwal='.urlencode($a[1]));
                            $obj = json_decode($stored, TRUE);
                            $result = $bot->replyText($event['replyToken'], $obj['message']);
                        }else if ($a[0]=="/help") {
                            $help="menambah note\n/tambah-nama note-detail note\nmelihat semua note\n/semua\nmelihat detail note\n/detail-nama note\nmenghapus note\n/hapus-nama note";
                            $result = $bot->replyText($event['replyToken'], $help);
                        }else if ($a[0]=="/userid") {
                            $result = $bot->replyText($event['replyToken'], $userId);
                        }
                        // if (substr($event['message']['text'],0,2)=='IP' & strlen($event['message']['text'])==18) {
                        //     $gg ="p" . substr($event['message']['text'],3);
                        //     $bb= substr($gg ,8);
                        //     $sc = new Scrape($gg , $bb);
                        //     $raw = $sc->login();
                        //     $pos = strpos($raw,'IP Lulus');
                        //     $hasil = substr($raw,$pos+56,7);                          
                        // }else if (substr($event['message']['text'],0,3)=='IPK' & strlen($event['message']['text'])==19){
                        //     $gg ="p" . substr($event['message']['text'],4);
                        //     $bb= substr($gg ,8);
                        //     $sc = new Scrape($gg , $bb);
                        //     $raw = $sc->login();
                        //     $pos = strpos($raw, 'KUMULATIF');
                        //     $hasil= substr($raw,$pos+153,6);
                        // }
                        // if (($hasil=='t;html') or ($hasil=='Transit')) {
                        //     $result = $bot->replyText($event['replyToken'], $event['message']['text'] .' Tidak Dapat Diakses');
                        // }else{
                        //     $result = $bot->replyText($event['replyToken'], $event['message']['text'] . $hasil);
                        // }
                        return $response->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                    }
                    // if(
                    //     $event['message']['type'] == 'image' or
                    //     $event['message']['type'] == 'video' or
                    //     $event['message']['type'] == 'audio' or
                    //     $event['message']['type'] == 'file'
                    // ){
                    //     $basePath  = $request->getUri()->getBaseUrl();
                    //     $contentURL  = $basePath."/content/".$event['message']['id'];
                    //     $contentType = ucfirst($event['message']['type']);
                    //     $result = $bot->replyText($event['replyToken'],
                    //         $contentType. " yang Anda kirim bisa diakses dari link:\n " . $contentURL);
                     
                    //     return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                    // }
                }
            }
        }
    }
});
$app->run();
