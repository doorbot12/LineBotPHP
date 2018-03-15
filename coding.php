<?php
    function taik(){
        $taicok = func_get_args();
        $a = (explode('-',$taicok);
        if ($a[0]=="/tambah") {
            $stored = file_get_contents('http://farkhan.000webhostapp.com/tae/storeData.php?groupid='.$event['source']['userId'].'&nama_jadwal='.urlencode($a[1]).'&isi_jadwal='.urlencode($a[2]));
            $obj = json_decode($stored, TRUE);
            return $obj['message'];
        }
        else if ($a[0]=="/semua") {
            $stored = file_get_contents('http://farkhan.000webhostapp.com/tae/GetData.php?groupid='.$event['source']['userId']);
            $datanya = json_decode($stored, TRUE);
            $hasilnya="Note Yang Disimpan";
            if (is_array($datanya) || is_object($datanyas)) {
                foreach ($datanya as $datanyas) {
                    echo $datanyas['jadwal'];
                    foreach($datanyas as $datanyass){
                        $hasilnya=$hasilnya."\n".$datanyass['nama_jadwal'];
                    }
                }   
            }
            return $hasilnya;
        }else if ($a[0]=="/detail") {
            $stored = file_get_contents('http://farkhan.000webhostapp.com/tae/GetData.php?groupid='.$event['source']['userId'].'&nama_jadwal='.urlencode($a[1]));
            $datanya = json_decode($stored, TRUE);
            $hasilnya="Detail Note ".$a[1];
            if (is_array($datanya) || is_object($datanyas)) {
                foreach ($datanya as $datanyas) {
                    echo $datanyas['jadwal'];
                    foreach($datanyas as $datanyass){
                        $hasilnya=$hasilnya."\n".$datanyass['detail'];
                    }
                }   
            }
            return $hasilnya;
        }else if ($a[0]=="/hapus") {
            $stored = file_get_contents('http://farkhan.000webhostapp.com/tae/deleteNote.php?groupid='.$event['source']['userId'].'&nama_jadwal='.urlencode($a[1]));
            $obj = json_decode($stored, TRUE);
            return $obj['message'];
        }else if ($a[0]=="/help") {
            return "menambah note\n/tambah-nama note-detail note\nmelihat semua note\n/semua\nmelihat detail note\n/detail-nama note\nmenghapus note\n/hapus-nama note";
        }
    }
?>



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