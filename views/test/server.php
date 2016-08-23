
<?php
$name = $_GET['param3']; 
// The name.n ow replacing all the $file_name with $name
$url = $_GET['param1'];
$text = $_GET['param'];
$title = $_GET['param2'];
$upload_dir = $url;
$num_files = 1;
//the file size in bytes.
$size_bytes =104857600; //51200 bytes = 50KB.
//Extensions you want files uploaded limited to.
$limitedext = array(".tif",".gif",".png",".jpeg",".jpg");
//check if the directory exists or not.
if (!is_dir("$upload_dir")) {
  die ("Error: The directory <b>($upload_dir)</b> doesn't exist.  ");
}
//check if the directory is writable.
if (!is_writeable("$upload_dir")){
  die ("Error: The directory <b>($upload_dir)</b> .  ");
}
if (isset($_POST['upload_form'])){

   echo "<h3>Upload results:</h3><br>";

   //do a loop for uploading files based on ($num_files) number of files.
   for ($i = 1; $i <= $num_files; $i++) {

       //define variables to hold the values.
       $new_file = $_FILES['file'.$i];
       $name = $new_file['name'];
       //to remove spaces from file name we have to replace it with "_".
       $name = str_replace(' ', '_', $name);
       $file_tmp = $new_file['tmp_name'];
       $file_size = $new_file['size'];

       #-----------------------------------------------------------#
       # this code will check if the files was selected or not.    #
       #-----------------------------------------------------------#

       if (!is_uploaded_file($file_tmp)) {
          //print error message and file number.
          echo "File: Not selected.<br><br>";
       }else{
             #-----------------------------------------------------------#
             # this code will check file extension                       #
             #-----------------------------------------------------------#

             $ext = strrchr($name,'.');
             if (!in_array(strtolower($ext),$limitedext)) {
                echo "File $i: ($name) Wrong file extension.  <br><br>";
             }else{
                   #-----------------------------------------------------------#
                   # this code will check file size is correct                 #
                   #-----------------------------------------------------------#

                   if ($file_size > $size_bytes){
   echo "File : ($name) Faild to upload. File must be no larger than <b>100   MB</b> in size.";
                   }else{
                #-----------------------------------------------------------#
                # this code check if file is Already EXISTS.                #
                #-----------------------------------------------------------#
                         if(file_exists($upload_dir.$name)){
                             echo "File: ($name) already exists.    <br><br>";
                         }else{
                               #-------------------------------#
                               # this function will upload the files.         #
                               #-------------------------------#
                               if     (move_uploaded_file($file_tmp,$upload_dir.$name)) {
                                   $sql = "INSERT INTO table_name(field1, field2) VALUES('$field1', '$field2');";
                                   echo "File: ($name) has been uploaded successfully." . "<img src='uploads/$name'/>";  

                               }else{
                                    echo "File: Faild to upload.  <br><br>";
                               }#end of (move_uploaded_file).

                         }#end of (file_exists).

                   }#end of (file_size).

             }#end of (limitedext).

       }#end of (!is_uploaded_file).

   }#end of (for loop).
   # print back button.
 ////////////////////////////////////////////////////////////////////////////////
 //else if the form didn't submitted then show it.
}else{
echo "<form method=\"post\" action=\"$_SERVER[PHP_SELF]\" enctype=\"multipart/form-        data\">";
       // show the file input field based on($num_files).
       for ($i = 1; $i <= $num_files; $i++) {
           echo "<b>Image: </b><input type=\"file\" size=\"70\"             name=\"file". $i ."\" style=\"width:45%\">";
       }
echo " <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$size_bytes\">
       <input type=\"submit\" name=\"upload_form\" value=\"Upload\">
       </form>";
}
?>