<?php

/**
 * UserController
 * @copyright (c) 2013, Nick Gejadze
 */
class UserController extends MovieDatabaseWrapperController {

    protected $page = 1;

    /**
     * Genres page
     * @acces public
     * @return void
     */
    public function IndexAction() {
        if (isset($this->requests['key']) and is_numeric($this->requests['key']) and isset($this->requests['value']) and !empty($this->requests['value'])):   
            $users = new Users();
            $userData = $users->getUserWithID($this->requests['key']);

            if(isset($userData) and !empty($userData)):

                $httpClient = new ngfw\Httpclient();
                $httpClient->setUri('http://graph.facebook.com/'.$userData['FacebookID'].'/picture?width=200&height=200&redirect=false');
                $response = $httpClient->request();
                $avatarContent = json_decode($response['content'], true);
                $avatar = $avatarContent['data']['url'];

               
                $this->view->avatar = $avatar;
                $this->view->user = $userData;

                $this->view->canonical = ngfw\Uri::baseUrl() . ngfw\Registry::get("requestedLanguage") . "/user/" . $userData['UserID'] . "/" . urlencode($userData['Name']);
                $this->view->og = array(
                            "title" => $userData['Name'],
                            "type" => "profile",
                            "url" => $this->view->canonical,
                            "image" => $avatar,
                            "site_name" => ngfw\Uri::baseUrl(),
                            "description" => $userData['Name'],
                        );
                // set page title
                $this->view->title = $userData['Name'];
                // set page meda description
                $this->view->metaDescription = $userData['Name'];
                // set page meta keywords
                $this->view->metaKeywords = $userData['Name'];
                if(isset($this->requests['key2']) and $this->requests['key2'] == "page" and isset($this->requests['value2']) and $this->requests['value2'] == "settings"):
                    $this->view->page = "settings";
                else:
                    $this->view->page = "profile";
                endif;
            else:
                //user not found
                ngfw\Route::redirect(ngfw\Uri::baseUrl() . "en/error/notfound", "404");
            endif;

        else:
            //error, return 404 errpo and redirect to error page
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . "en/error/notfound", "404");
            exit();
        endif;
    }

}