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
    //pre_r($_FILES);
    //$ext_error = false;

    // Set file allowable extension error flag
    $file_ext = explode('.', $_FILES['userfile']['name']);
    $file_ext = end($file_ext);
    $ext_error = !in_array($file_ext, $extensions);
    //pre_r($file_ext);
    //pre_r($ext_error);

    if ($ext_error) {
        echo "<p>Invalid file extension, upload on pdf, doc, docx, ppt, pptx</p>";
    } else {
        // Check for file upload errors
        if ($_FILES['userfile']['error']) {
            echo $phpFileUploadErrors[$_FILES['userfile']['error']];
        } else {
            // Can now move and then store file details
            echo "File uploaded <br>";
            require_once 'dbConnect.php';
            $documentTitle = mysqli_real_escape_string($db, $_POST['title']);
            $documentName = mysqli_real_escape_string($db, $_FILES['userfile']['name']);

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
            $query = "CREATE TABLE IF NOT EXISTS `Documents` (
                          `docID` int(4) not null auto_increment,
                          `title` nvarchar(128),
                          `name` nvarchar(256),
                           PRIMARY KEY(`docID`));";
            $result = $db->query($query);
            $query = "INSERT INTO Documents (`title`, `name`) VALUES (?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param('ss', $documentTitle, $documentName);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                echo  "<p>Document details inserted into the database.</p>";
            } else {
                echo "<p>An error has occurred.<br/>
              The item was not added.</p>";
            }

            // Read value from database
            $docID = 1;
            $query = "SELECT title, name FROM Documents WHERE docID = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param('i', $docID);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($documentTitle, $documentName);
            $stmt->fetch();

            // Display stored file
            echo '<embed src="documents/' . $documentName . '" width="600" height="500" alt="pdf" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html">';
            echo '<br><br><a href="documents/' . $documentName . '" target="_blank">Read More</a>';
            $stmt->free_result();
            $db->close();
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