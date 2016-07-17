<?php

/**
 * Filecache
 * @project Movie Database 2
 * @copyright (c) 2013, Nick Gejadze
 */
class Filecache{
    
    /**
     * Cache Directory
     * @access protected
     * @var string 
     */
    protected $cacheDir;

    /**
     * Cache file extension
     * @access protected
     * @var string
     */
    protected $cacheExtension = ".cache";

    /**
     * Sets Cache default iretory
     * @access public
     * @return void
     */
    public function __construct() {
        $this->cacheDir = ROOT . DS . "TMP" . DS . "CACHE";
    }

    /**
     * Checks if directory exsists, and creates 
     * @param string $dir
     * @access private
     * @return void
     */
    private function validateCacheDir($dir) {
        if (!is_dir($dir)):
            mkdir($dir, 0777, true);
        endif;
    }

    /**
     * Sets Cache directory
     * @access public
     * @param string $dir
     * @return void
     */
    public function setCacheDirectory($dir) {
        $this->cacheDir = $dir;
        $this->validateCacheDir($dir);
    }

    /**
     * Sets Cache file path
     * @access private
     * @param string $key
     * @return string
     */
    private function cacheFile($key) {
        $cacheFilename = sha1($key);
        $subdir = DS.substr($cacheFilename, 0, 3);
        $this->validateCacheDir($this->cacheDir.$subdir);
        return sprintf("%s/%s", $this->cacheDir.$subdir, sha1($key)) . $this->cacheExtension;
    }

    /**
     * Sets Cache 
     * @param string $key
     * @param mixed $data
     * @return boolean
     */
    public function set($key, $data) {
        $cacheFilePath = $this->cacheFile($key);
        if (!$fp = fopen($cacheFilePath, 'wb')):
            return false;
        endif;
        if (flock($fp, LOCK_EX)):
            fwrite($fp, serialize($data));
            flock($fp, LOCK_UN);
        else:
            return false;
        endif;
        fclose($fp);
        return true;
    }

    /**
     * Get cached file if not expired, removes expired cache files
     * @accss public
     * @param string $key
     * @param int $expiration time in seconds
     * @return boolean
     */
    public function get($key, $expiration = 3600) {
        $cacheFilePath = $this->cacheFile($key);
        if (!@file_exists($cacheFilePath)):
            return false;
        endif;
        if (filemtime($cacheFilePath) < (time() - $expiration)):
            $this->delete($key);
            return false;
        endif;
        if (!$fp = @fopen($cacheFilePath, 'rb')):
            return false;
        endif;
        flock($fp, LOCK_SH);
        $cache = unserialize(fread($fp, filesize($cacheFilePath)));
        flock($fp, LOCK_UN);
        fclose($fp);
        return $cache;
    }

    /**
     * Removes cache file
     * @param string $key
     * @return boolean
     */
    public function delete($key) {
        $cache_path = $this->cacheFile($key);
        if (file_exists($cache_path)):
            unlink($cache_path);
            return true;
        endif;
        return false;
    }

    /**
     * delete_all
     * @param mixed $path
     * @access public
     * @return mixed 
     */
    public function delete_all($path = null) {
        $entries = array();
        if(!isset($path) or empty($path)):
            $path = $this->cacheDir;
        endif;
        $dir = dir($path);
        while (false !== ($entry = $dir->read())):
            $entries[] = $entry;
        endwhile;
        $dir->close();
        foreach ($entries as $entry):
            $fullname = $path . "/" . $entry;
            if ($entry != '.' and $entry != '..' and is_dir($fullname)):
                $this->delete_all($fullname);
                rmdir($fullname);
            else:
                $file_parts = pathinfo($fullname);
                if($file_parts['extension'] == substr($this->cacheExtension,1)):
                    unlink($fullname);
                endif;
            endif;
        endforeach;
    }

}