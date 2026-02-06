<?php
// Gracefully handle missing Box SDK
$box_autoload = __DIR__ . '/../box-jwt-php/bootstrap/autoload.php';
$box_helpers  = __DIR__ . '/../box-jwt-php/helpers/helpers.php';
$BOX_AVAILABLE = file_exists($box_autoload) && file_exists($box_helpers);
if ($BOX_AVAILABLE) {
    require_once $box_autoload;
    require_once $box_helpers;
} else {
    http_response_code(503);
    exit("File downloads unavailable: Box SDK not installed.");
}

use Box\Auth\BoxJWTAuth;
use Box\BoxClient;
use Box\Config\BoxConstants;
use Box\Models\Request\BoxFileRequest;

global $user;
if (!$user->is_user()) exit('Invalid user');
if (count($data) != 2) exit('Invalid url');

$table = $data[0];
$id = $data[1];

$dbConn = get_connection();

$result = mysqli_query($dbConn, "select * from $table where ${table}_id=$id" . get_auth_sql());
$row = mysqli_fetch_assoc($result);

mysqli_close($dbConn);

if (!$row){
    exit("Invalid $table");
}

// To accommodate old data used file name
if ($table == 'consultant'){
    $boxFileId = $row['consultant_box_file_id'];
    $fileType = $row['consultant_file_type'];
    $fileName = $row['consultant_file_name'];
}
else if ($table == 'data'){
    $boxFileId = $row['data_box_file_id'];
    $fileType = $row['data_file_type'];
    $fileName = $row['data_file_name'];
}

$boxJwt     = new BoxJWTAuth();
$boxConfig  = $boxJwt->getBoxConfig();
$adminToken = $boxJwt->adminToken();
$boxClient  = new BoxClient($boxConfig, $adminToken->access_token);


// download file
$res = $boxClient->filesManager->downloadFile($boxFileId, 'downloadedFile.txt', null);

$downloadLink = $res->getHeader('Location')[0];

$tempDir = sys_get_temp_dir();
$fpath = $tempDir . '/' . $fileName;

if(!file_put_contents($fpath, file_get_contents($downloadLink))){
    exit("Unable to download $fileName");
}

if (!is_readable($fpath)) {
    unlink($fpath);
    exit("$fileName is not readable");
}

header('Content-type: "' . $fileType . '"');
header('Content-Disposition: attachment; filename="'.$fileName.'"');


$handle = fopen($fpath, "rb");
while (!feof($handle)) {
    echo fread($handle, 8192);
}
fclose($handle);
unlink($fpath);
exit(0);






