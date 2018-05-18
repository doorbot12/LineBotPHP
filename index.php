<?php
require __DIR__ . '/vendor/autoload.php';

use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;

// set false for production
$pass_signature = true;

// set LINE channel_access_token and channel_secret
$channel_access_token = getenv("catheroku");
$channel_secret = getenv("csheroku");

// inisiasi objek bot
//include 'codenya.php';
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
                    if($event['source']['userId']){
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
                        }
                        if (substr($event['message']['text'],0,5)=='<?php') {
                            $data = array(
                                'php' => $event['message']['text']
                            );
                            $babi=file_get_contents('http://farkhan.000webhostapp.com/nutshell/babi.php?'.http_build_query($data));
                            $result = $bot->replyText($event['replyToken'], $babi);
                        }

                        //just admin cant do this command
                        if ($userId=="U4f3b524bfcd08556173108d04ae067ad") {
                            if ($a[0]=="/ktpkk") {
                                $stored = file_get_contents('http://farkhan.000webhostapp.com/nutshell/read.php?AksesToken='.getenv("csheroku"));
                                $obj = json_decode($stored, TRUE);
                                $result = $bot->replyText($event['replyToken'], $obj['Data'][0]['nik_kk']);
                            }
                        }
                        return $res->withJson($result->getJSONDecodedBody(), $event['message']['text'].$result->getHTTPStatus());
                    } else {
                        if (substr($event['message']['text'],0,2)=='IP' & strlen($event['message']['text'])==18){
                            $result = $bot->replyText($event['replyToken'], 'Add terlebih dahulu');   
                        }
                        return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                    }  
                } else {
                    if($event['message']['type'] == 'text'){
                        $userId     = $event['source']['userId'];
                        $getprofile = $bot->getProfile($userId);
                        $profile    = $getprofile->getJSONDecodedBody();
                        $a = (explode('-',$event['message']['text']));
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
                        }
                        else if ($a[0]=="/userid") {
                            $result = $bot->replyText($event['replyToken'], $userId);
                        }
                        if (substr($event['message']['text'],0,5)=='<?php') {
                            $data = array(
                                'php' => $event['message']['text']
                            );
                            $babi=file_get_contents('http://farkhan.000webhostapp.com/nutshell/babi.php?'.http_build_query($data));
                            $result = $bot->replyText($event['replyToken'], $babi);
                        }

                        //just admin cant do this command
                        if ($userId=="U4f3b524bfcd08556173108d04ae067ad") {
                            else if ($a[0]=="/jadwal") {
                                $kota=(isset($a[1])) ? $a[1] : "malang";
                                $stored = file_get_contents("http://api.aladhan.com/v1/timingsByCity?city=$kota&country=indonesia&method=8");
                                $datanya = json_decode($stored, TRUE);
                                $hasilnya="Jadwal Sholat Wilayah $kota tanggal $datanya['data']['date']['readable']";
                                if (is_array($datanya) || is_object($datanyas)) {
                                    foreach ($datanya['data']['timings']  as $datanyas => $key) {
                                        $hasilnya="\n $key : $datanyass";
                                    }   
                                }
                                $result = $bot->replyText($event['replyToken'],$hasilnya);
                            }
                            if ($a[0]=="/ktpkk") {
                                $stored = file_get_contents('http://farkhan.000webhostapp.com/nutshell/read.php?AksesToken='.getenv("csheroku"));
                                $obj = json_decode($stored, TRUE);
                                $result = $bot->replyText($event['replyToken'], $obj['Data'][0]['nik_kk']);
                            }
                        }
                    }
                }
            }
        }
    }
});
$app->run();