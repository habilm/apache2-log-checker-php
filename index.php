<?php


$path = __DIR__."/logs";
$files = scandir($path);
$files = array_slice($files,2,count($files));

$excludes = [ ".js",".css", ".png", ".gif", ".jpg", ".svg", ".ico", "wp-cron.php","::1",'.mp4',"robots.txt",".jpeg" ];

$limit = 100;


set_error_handler('exceptions_error_handler');

function exceptions_error_handler($severity, $message, $filename, $lineno) {
  if (error_reporting() == 0) {
    return;
  }
  if (error_reporting() & $severity) {
    throw new ErrorException($message, 0, $severity, $filename, $lineno);
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <table class="table w-100 table-striped table-bordered" style="font-size:14px;width:100%">
        <tr>
            <th>
                IP
            </th>
            <th>
                Date
            </th>
            <th>
                Method
            </th>
            <th>
                URL
            </th>
            <th>
                STATUS
            </th>
            <th>
                REF
            </th>
            <th>
                AGENT
            </th>
        </tr>
        <?php
        foreach( $files as $file ){
            $f_handle = fopen($path ."/".$file, "r" );
            while(!feof($f_handle)) {
                $line =  fgets($f_handle);
                
                foreach( $excludes as $exc ){
                    if( strpos( $line , $exc ) !== false ){
                        continue 2;
                    }
                }

                $pattern = '/^(\S+) (\S+) (\S+) \[([^\]]+)\] "([^"]+)" (\d+) (\d+) "([^"]+)" "([^"]+)"$/';
                preg_match($pattern, $line , $matches);
                try{
                    $ipAddress = $matches[1];
                    $dash1 = $matches[2];
                    $dash2 = $matches[3];
                  
                    
                    $request = $matches[5];
                    $status = $matches[6];
                    $bytesSent = $matches[7];
                    $referer = $matches[8];
                    $userAgent = $matches[9];




                    $time_stamp = strtotime(preg_replace( "/\[\]/","",$matches[4] ));
                    if( !($time_stamp > strtotime("09/02/2023 18:00:00")) || !($time_stamp < strtotime("09/03/2023 07:00:00"))  ){
                        continue;
                    }
                    if( empty($request) || trim($request) == "-" ){
                        throw new Exception("Invalid request");
                        continue;
                    }
                    $time = date("d m Y H:i:s",$time_stamp );

                    ?>
                        <tr>
                            <td>
                                <?= $ipAddress ?>
                            </td>
                            <td>
                            <?= $time ?>
                            </td>
                            <td>
                            -
                            </td>
                            <td>
                            <?= $request ?>
                            </td>
                            <td>
                            <?= $status ?>
                            </td>
                            <td>
                            <?= $referer ?>
                            </td>
                            <td>
                            <?= $userAgent ?>
                            </td>
                        </tr>
                    <?php

                }catch(Exception $th){
                    ?>
                    <tr>
                        <td colspan="7">
                            <?= $line ?>
                        </td>
                    </tr>
                <?php
                }
                
            }
           
            
        }
        ?>
       
       
    </table>
</body>
</html>
