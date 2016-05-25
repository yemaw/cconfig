<?php

class RConfig{

    private $files_dir = 'files';

    public $client;
    public $version;
    public $channel;

    private function mergeFileToArray($filePath, $kvs){
        if(file_exists($filePath)){
            $content = file_get_contents($filePath);
            $content = empty($content) ? '{}' : $content;
            $kv      = json_decode($content, true);
            $kvs     = array_replace_recursive($kvs, $kv);
        }
        return $kvs;
    }

    public function __construct($client = '', $version = '', $channel = ''){
        $this->client  = $client;
        $this->version = $version;
        $this->channel = $channel;
    }

    public function setFilesDir($files_dir) {
        $this->files_dir = $files_dir;
    }
    public function readData(){
        $kvs  = array();

        $dirs = explode('.', $this->client.'.'.$this->version);
        array_unshift($dirs, '');

        $path = $this->files_dir;
        for($i=0; $i<sizeof($dirs); $i++){
            $dir = $dirs[$i];
            $path .= $dir.DIRECTORY_SEPARATOR;

            $kvs = $this->mergeFileToArray($path.'default.json', $kvs);
            $kvs = $this->mergeFileToArray($path.'channel-'.$this->channel.'.json', $kvs);
        }

        return $kvs;
    }

    public function get($path = '', $fallbackValue = null){
        $prev = $this->readData();
        if(!$path){
            return $prev;
        }

        $parts = explode('.',$path);
        for($i=0; $i<sizeof($parts); $i++){
            $part = $parts[$i];
            $current = $prev;
            if(isset($current[$part])){
                $prev = $current[$part];
                continue;
            } else {
                return $fallbackValue;
            }
        }
        return $prev;
    }
}
