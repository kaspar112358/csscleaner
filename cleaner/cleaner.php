<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cleaner
 *
 * @author Kaspar
 */
class cleaner {
    private $url;
  
    private $crawled;
    private $processed;
    
    public function __construct($url) {
        $this->url = $url;
        $this->crawled = [];
        $this->processed = [];

        $this->getUrlsFrom($url);
    }
    
    public function getUrlsFrom($url){
        $contents = $this->getSiteContent($url);
        
        if(!empty($contents)){
           
            $doc = new DOMDocument();
            libxml_use_internal_errors(true);
            $doc->loadHTML($contents);
            libxml_clear_errors();

            $path = new DOMXPath($doc);

            $nodes = $path->query("//a");

            $urls = [];

            foreach($nodes as $node){
                $nodeurl = $node->getAttribute('href');
                if(strpos($nodeurl, $this->url) !== false && !in_array($nodeurl, $this->processed)){
                    $urls[] = $nodeurl;
                }
            }
            $this->processed[] = $url;
            $this->crawled = array_merge($this->processed, $urls);

            $this->loopThroughCrawledUrls(); 
        }
        
    }
    
    private function loopThroughCrawledUrls(){
        foreach($this->crawled as $crawled){
            if(!in_array($crawled, $this->processed)){
                
                $this->getUrlsFrom($crawled);
            }                
            
        }
    }
    
    public function getProcessed(){
        return $this->processed;
    }
    
    private function getSiteContent($url = ""){
        
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL,(empty($url) ? $this->url : $url));
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $query = curl_exec($handle);
        curl_close($handle);
        
        return $query;
    }
}
