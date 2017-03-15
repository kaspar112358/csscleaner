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
    private $sheets;
    
    public function __construct($url) {
        $this->url = $url;
        $this->crawled = [];
        $this->processed = [];
        $this->sheets = [];

        $this->getUrlsFrom($url);
    }
    
    private function getUrlsFromXpath(DOMXPath $path){
        $nodes = $path->query("//a");
        $urls = [];

        foreach($nodes as $node){
            $nodeurl = $node->getAttribute('href');
            if(strpos($nodeurl, $this->url) !== false && !utility::inMultiArray($nodeurl, $this->processed)){
                $urls[] = $nodeurl;
            }
        }
        
        return $urls;
    }
    
    
    private function getStyleSheetsFromXpath(DOMXPath $path){
        $sheets = $path->query('//*[@rel="stylesheet" or @media="all" or @media="screen"]');   
        $output = [];
        $real = [];
        
        foreach($sheets as $sheet){
            $url = $sheet->getAttribute('href');
            if(!utility::inMultiArray($url, $this->sheets)){
                $output[] = $url;
            }
            $real[] = $url;
        }
        
        return ["output" => $output, "real" => $real];
    }
    
    private function getTagsFromDom(DOMDocument $dom){
        $classes = $dom->getElementsByTagName('*');
        $output = [];
        
        foreach($classes as $class){
            if($class->hasAttributes()){
                foreach($class->attributes as $attr){
                    if($attr->nodeName == "id" || $attr->nodeName == "class"){
                        $output[] = ["name" => $attr->nodeName, "value" => $attr->nodeValue];
                    }
                }
            }
        }
        
        return $output;
    }
    
    private function getUrlsFrom($url){
        $contents = $this->getSiteContent($url);
        
        if(!empty($contents)){
           
            $doc = new DOMDocument();
            libxml_use_internal_errors(true);
            $doc->loadHTML($contents);
            libxml_clear_errors();

            $path = new DOMXPath($doc);

            $elements = $this->getTagsFromDom($doc);
            $stylesheets = $this->getStyleSheetsFromXpath($path);
            $urls = $this->getUrlsFromXpath($path);

            $this->sheets = array_merge($this->sheets, $stylesheets["output"]);
            
            $locations = array_values(array_intersect($this->sheets, $stylesheets["real"]));
            
            $this->processed[] = ["url" => $url, "elements" => $elements, "stylesheets" => $locations];
            
            $this->crawled = array_merge($this->processed, $urls);

            $this->loopThroughCrawledUrls(); 
        }
        
    }
    
    private function loopThroughCrawledUrls(){
        foreach($this->crawled as $crawled){
            if(!utility::inMultiArray($crawled, $this->processed)){
                
                $this->getUrlsFrom($crawled);
            }                
            
        }
    }
    
    public function getSheets(){
        return $this->sheets;
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
