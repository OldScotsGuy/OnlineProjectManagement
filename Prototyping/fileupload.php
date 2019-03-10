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

// Database Connection
require_once ("../Objects/Model/DocumentsModel.php");
// TODO Handle exception when cannot open connection
$db = new \Model\DocumentsModel();

if (isset($_FILES['userfile'])) {

    // Set file allowable extension error flag
    $file_ext = explode('.', $_FILES['userfile']['name']);
    $file_ext = end($file_ext);
    $ext_error = !in_array($file_ext, $extensions);

    if ($ext_error) {
        echo "<p>Invalid file extension, upload on pdf, doc, docx, ppt, pptx</p>";
    } else {
        // Check for file upload errors
        if ($_FILES['userfile']['error']) {
            echo $phpFileUploadErrors[$_FILES['userfile']['error']];
        } else {
            // Can now move and then store file details
            echo "File uploaded <br>";
            $documentTitle = $_POST['title'];
            $documentName = $_FILES['userfile']['name'];

            // Prepend with time() to try an avoid filename conflicts
            $documentName = time() . str_replace(' ', '_', $documentName);
            echo "Document Title: " . $documentTitle . "<br>";
            echo "Document Name: " . $documentName . "<br>";

            // Move file to storage location
            if (move_uploaded_file($_FILES['userfile']['tmp_name'], 'documents/' . $documentName)) {
                echo "File successfully moved<br>";
            } else {
                echo "File move failed<br>";
            }

            // Store file details in database
            if ($db->insertDocument($documentTitle, $documentName)) {
                echo  "<p>Document details inserted into the database.</p>";
            } else {
                echo "<p>An error has occurred.<br/>
              The item was not added.</p>";
            }

            // Read value from database
            $results = $db->retrieveDocument(1);

            // Display stored file
            echo '<embed src="documents/' . $results['docName'] . '" width="600" height="500" alt="pdf" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html">';
            echo '<br><br><a href="documents/' . $results['docName'] . '" target="_blank">Read More</a>';
            //$stmt->free_result();
            //$db->close();
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
        <label for="title">Document Title:</label>
        <input type="text" name="title" id="title" size="100"  maxlength="1280" required />
        <br><br>
        <input type="file" name="userfile" />
        <input type="submit" value="Upload" />
    </form>
</body>

</body>
</html>