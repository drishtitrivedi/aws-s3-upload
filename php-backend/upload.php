<?php

use AWS\S3\Exception\S3Exception;

include 'app/start.php';
include 'conn.php';

if(isset($_REQUEST['submit'])) {

    // file details
    $file = $_FILES['file'];
    $name = $file['name'];
    $tmp_name = $file['tmp_name'];

    $extention = explode('.', $name);
    $extention = strtolower(end($extention));

    // Temp file details
    $key = md5(uniqid());
    $tmp_file_name = "{$key}.{$extention}";
    $tmp_file_path = "files/{$tmp_file_name}";
    $file_type = $file['type'];
    if (strpos($file['type'], 'audio') !== false) {
        $file_type = 'audio';
    }
    if (strpos($file['type'], 'video') !== false) {
      $file_type = 'video';
    }
    if (strpos($file['type'], 'image') !== false) {
      $file_type = 'image';
    }

    // save file in temp location
    move_uploaded_file($tmp_name,$tmp_file_path);

    // move encrypted file to aws s3 bucket 
    try{
        $s3->putObject([ 
            'Bucket'=> $config['s3']['bucket'], 
            'Key' => "{$tmp_file_name}", 
            'Body' => fopen($tmp_file_path, 'r'), 
            'ACL' => $_REQUEST['acl'],
            'x-amz-algorithm' => "AWS4-HMAC-SHA256",
            'ServerSideEncryption' => 'AES256',
         ]);
            
            // Insert data into database
            $sql = "INSERT INTO `files` (`id`, `file_name`, `acl`,`mime_type`) VALUES (NULL, '{$tmp_file_name}', '{$_REQUEST['acl']}','{$file_type}');";
 
            if ($conn->query($sql) === FALSE) {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            // after file upload delete locally stored file
            unlink($tmp_file_path); 

            echo '<script>alert("File uploaded successfully!")</script>';
        }
        catch(S3Exception $e) 
        { 
            print_r("Error: " .$e->getMessage()); 
            } 
        } 
?>

<!DOCTYPE html>

<html lang="en">
  <head>
    <meta charset="utf-8" />

    <title>File Upload</title>
 

    <link
      rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
      integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
      crossorigin="anonymous"
    />
    <script
      src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
      integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
      integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
      integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
      crossorigin="anonymous"
    ></script>
  </head>

  <body>
    <script src="js/scripts.js"></script>
    <div class="container">
      <form action="upload.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label> Upload File</label>
          <input type="file" class="form-control-file" id="file" name="file" />
        </div>
        <div class="form-group">
          <label>Select Access type</label>
          <select name="acl" id="acl">
            <option value="-">Select access control type</option>
            <option value="private">private</option>
            <option value="public-read">public-read</option>
            <option value="public-read-write">public-read-write</option>
            <option value="aws-exec-read">aws-exec-read</option>
            <option value="authenticated-read"> authenticated-read </option>
            <option value="bucket-owner-read"> bucket-owner-read </option>
            <option value="bucket-owner-full-control">
              bucket-owner-full-control
            </option>
            <option value="log-delivery-write"> log-delivery-write </option>
          </select>
        </div>

        <button type="submit" name="submit" class="btn btn-primary mb-2">
          Submit
        </button>
      </form>
    </div>
  </body>
</html>