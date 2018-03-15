<?php

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