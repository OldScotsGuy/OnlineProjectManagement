<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 03/03/2019
 * Time: 10:23
 */

// Extensions allowed to be uploaded
$extensions = array('pdf','doc', 'docx','ppt','pptx');

// File upload error messages
$phpFileUploadErrors = array(
    0 => 'File uploaded successfully',
    1 => 'File exceeds the upload_max_filesize directive in php.ini',
    2 => 'File exceeds the MAX_FILE_SIZE directive specified in the HTML form',
    3 => 'File only partially uploaded',
    4 => 'File not uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk',
    8 => 'A PHP extension stopped teh file upload'
);

if (isset($_FILES['userfile'])) {
    pre_r($_FILES);
    //$ext_error = false;

    // Set file allowable extension error flag
    $file_ext = explode('.', $_FILES['userfile']['name']);
    $file_ext = end($file_ext);
    $ext_error = !in_array($file_ext, $extensions);
    pre_r($file_ext);
    pre_r($ext_error);

    if ($ext_error) {
        echo "Invalid file extension, upload on pdf, doc, docx, ppt, pptx";
    } else {
        // Check for file upload errors
        if ($_FILES['userfile']['error']) {
            echo $phpFileUploadErrors[$_FILES['userfile']['error']];
        } else {
            echo "File uploaded";
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'documents/' . $_FILES['userfile']['name']);
        }
    }
}
function pre_r($array) {
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Prototyping</title>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="userfile" />
        <input type="submit" value="Upload" />
    </form>
</body>

</body>
</html>