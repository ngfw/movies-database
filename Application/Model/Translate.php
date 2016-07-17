<?php

/**
 * Translate
 * @project Movie Database 2
 * @copyright (c) 2013, Nick Gejadze
 */
class Translate {

    /**
     * Default Language
     * @access proteted
     * @var string
     */
    protected $defaultLanguage = "en";

    /**
     * Available Languages
     * @access proteted
     * @var array
     */
    protected $availableLanguages;

    /**
     * Requested language
     * @access proteted
     * @var string
     */
    protected $requestedLanguage;

    /**
     * holds Table name
     * @access proteted
     * @var string
     */
    protected $_table = "Translation";

    /**
     * holds db connection
     * @access proteted
     * @var object 
     */
    protected $db;

    /**
     * sets DB object
     * @access public
     * @return void
     */
    public function __construct() {
        $this->db = ngfw\Registry::get('db');
    }

    /**
     * Delete single translation for each language
     * @access public
     * @return mixed
     */
    public function deleteTranslation($translationID){
        $query = new ngfw\Query();
        $query->select("Source")->from($this->_table)->where("translationID = ?", $translationID);
        $result = $this->db->fetchRow($query->getQuery());
        $query = new ngfw\Query();
        $query->delete()->from($this->_table)->where("Source = ?", $result['Source']);
        return $this->db->query($query->getQuery());
    }

    /**
     * delete language
     * @access public
     * @return mixed
     */
    public function deleteLanguage($language){
        $query = new ngfw\Query();
        $query->delete()->from($this->_table)->where("Language = ?", $language);
        return $this->db->query($query->getQuery());
    }

    /**
     * Gets all PHP file filenames and sets as available language
     * @access public
     * @return array
     */
    public function getAvailableLanguages() {
        if (!isset($this->availableLanguages) or empty($this->availableLanguages)):
            $query = new ngfw\Query();
            $query->select("DISTINCT Language")
                    ->from($this->_table);
            $languages = $this->db->query($query->getQuery());
            foreach ($languages as $key => $value):
                $this->availableLanguages[] = $value['Language'];
            endforeach;
        endif;
        return $this->availableLanguages;
    }

    /**
     * Gets Count of total translations and translated phrases for specific language
     * @access public 
     * @param string $language
     * @return array
     */
    public function getTranslationCount($language) {
        $query = 'SELECT * from (SELECT count(*) as Empty FROM `' . $this->_table . '` WHERE Language = "' . $language . '" and Translation = "")z, (SELECT count(*) as Total FROM `' . $this->_table . '` WHERE Language = "' . $language . '")y';
        $result = $this->db->fetchRow($query);
        return $result;
    }

    /**
     * Returns Resquested Language code or if not set defualut language code
     * @access public
     * @return string
     */
    public function getRequestedLanguage() {
        if (!isset($this->requestedLanguage) OR empty($this->requestedLanguage)):
            $this->requestedLanguage = $this->defaultLanguage;
        endif;
        return $this->requestedLanguage;
    }

    /**
     * Sets Requested Language
     * @access public
     * @return void
     */
    public function setLanguage($lang) {
        $availableLanguages = $this->getAvailableLanguages();
        if (isset($lang) and strlen($lang) == 2 and is_array($availableLanguages) and in_array($lang, $availableLanguages)):
            $this->requestedLanguage = $lang;
        endif;
    }

    /**
     * Returns Translation file
     * @access public
     * @return mixed
     */
    public function getTranslation($language = null, $fullArray = false) {
        $query = new ngfw\Query();
        $query->select()->from($this->_table);
        if (isset($language) and !empty($language)):
            $query->where("Language = ?", $language);
        else:
            $query->where("Language = ?", $this->getRequestedLanguage());
        endif;
        $translationData = $this->db->query($query->getQuery());
        foreach ($translationData as $value):
            if ($fullArray):
                $returnData[$value["Source"]] = $value;
            else:
                $returnData[$value["Source"]] = $value["Translation"];
            endif;
        endforeach;
        return isset($returnData) ? $returnData : false;
    }

    /**
     * updates Translation for selected language
     * @access public
     * @param int $translationID
     * @param string $source
     * @param string $translation
     * @return boolean
     */
    public function updateTranslation($translationID, $source, $translation) {
        // Check if we need to update source for all the languages
        $query = new ngfw\Query();
        $query->select("Source")->from($this->_table)->where("translationID =?", $translationID);
        $sourceResult = $this->db->fetchRow($query->getQuery());
        if ($sourceResult["Source"] == $source):
            //update just a translation
            $data = array(
                "Translation" => $translation
            );
            $query->update($this->_table, $data)
                    ->where("translationID =?", $translationID);
            $this->db->query($query->getQuery());
        else:
            //update translation and source for all the languages
            $data = array(
                "Source" => $source
            );
            $query->update($this->_table, $data)
                    ->where("Source =?", $sourceResult["Source"]);
            $this->db->query($query->getQuery());
            // update translation
            $data = array(
                "Translation" => $translation
            );
            $query->update($this->_table, $data)
                    ->where("translationID =?", $translationID);
            $this->db->query($query->getQuery());

        endif;
        return true;
    }

    /**
     * Added new Translation for language
     * @access public
     * @param string $source
     * @param string $translation
     * @param string $language
     * @return void
     */
    public function addTranslation($source, $translation, $language) {
        // Added Source for all the languages
        $languages = $this->getAvailableLanguages();
        foreach ($languages as $lang):
            $query = new ngfw\Query();
            $data = array(
                "Source" => $source,
                "Translation" => "",
                "Language" => $lang
            );
            $query->insert($this->_table, $data);
            $this->db->query($query->getQuery());
        endforeach;
        // Update Translation for current language
        $query = new ngfw\Query();
        $data = array(
            "Translation" => $translation
        );
        $query->update($this->_table, $data)
                ->where("Source = ?", $source)
                ->andWhere("Language = ?", $language);
        $this->db->query($query->getQuery());
    }

    /**
     * add new language
     * @access public
     * @param string $language
     * @param return void
     */
    public function addLanguage($language) {
        $languages = $this->getAvailableLanguages();
        if (isset($languages) and is_array($languages)):
            $translations = $this->getTranslation($languages[0]);
            if (isset($translations) and is_array($translations)):
                foreach ($translations as $key => $translation):
                    $query = new ngfw\Query();
                    $data = array(
                        "Source" => $key,
                        "Language" => $language
                    );
                    $query->insert($this->_table, $data);
                    $this->db->query($query->getQuery());
                endforeach;
            else:
                $this->initEmptyLanguage($language);
            endif;
        else:
            $this->initEmptyLanguage($language);
        endif;
    }

    /**
     * initial empty language
     * @param string $language
     * @access private
     * @return void
     */
    private function initEmptyLanguage($language) {
        $query = new ngfw\Query();
        $data = array(
            "Source" => "Site_name",
            "Language" => $language
        );
        $query->insert($this->_table, $data);
        $this->db->query($query->getQuery());
    }

}