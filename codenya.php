<?php

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
                        'submit'    => 'https://siam.ub.ac.id/index.php?action=process'
                        );
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
                return htmlspecialchars($this->content);

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
?>