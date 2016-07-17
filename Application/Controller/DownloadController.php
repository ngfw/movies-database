<?php

class DownloadController extends MovieDatabaseWrapperController {

    
    /**
     * Index Action
     * @access public
     * @retrun void
     */
    public function IndexAction() {
        $this->view->enableLayout(false);
        $this->view->enableView(false);
        if (isset($this->requests['key']) and !empty($this->requests['key']) and isset($this->requests['value']) and !empty($this->requests['value'])):
            $imageURL = base64_decode($this->requests['value']);
            $pos = strpos($imageURL, $this->view->tmdbConfig["images"]["base_url"]);
            if (filter_var($imageURL, FILTER_VALIDATE_URL) and $pos !== false):
                $parse = parse_url(ngfw\Uri::baseUrl());
                header('Content-Description: File Transfer');
                header('Content-type: application/octet-stream');
                header('Content-Disposition: attachment; filename="[' . $parse['host'] . ']-' . basename($imageURL));
                readfile($imageURL);
                exit();
            else:
                echo 'Sorry, unknown request';
            endif;
        else:
            echo 'Sorry, Download link is expired';
        endif;
    }

}