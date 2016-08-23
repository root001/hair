<?php
defined('BASEPATH') OR exit("Access Denied!");

/**
 * Description of Download
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 19-Mar-2016
 */
class Download extends CI_Controller{
    
    public function __construct() {
        parent::__construct();
    }
    
    
    
    public function logo($stringified_email, $img_name){
        $img_full_link = "../aura_users/{$stringified_email}/$img_name";//set image full link
        $ext = "." . explode('.', $img_name)[1];//get the image's extension
        
        if(file_exists($img_full_link)){
            $this->output->set_content_type($ext)->set_output(file_get_contents($img_full_link));
        }
    }
}
