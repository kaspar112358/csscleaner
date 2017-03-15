<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        
        require_once 'cleaner/cleaner.class.php';
        require_once 'cleaner/utility.class.php';
        
        
        set_time_limit(0);
        $cleaner = new cleaner("http://kasparbirk.com/testcss/");
        
        echo "<h2>Stylesheets:</h2><br/>";
        echo "<pre>";
        print_r($cleaner->getSheets());
        echo "<pre>";
        
        echo "<h2>Urls and contents:</h2><br/>";
        echo "<pre>";
        print_r($cleaner->getProcessed());
        echo "<pre>";
        
        ?>
    </body>
</html>
