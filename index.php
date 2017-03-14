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
        
        require_once 'cleaner/cleaner.php';
        
        echo "<pre>";
        set_time_limit(0);
        $cleaner = new cleaner("http://brandmonkey.dk/");
        print_r($cleaner->getProcessed());
        echo "<pre>";
        ?>
    </body>
</html>
