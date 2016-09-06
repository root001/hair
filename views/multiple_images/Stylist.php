<?php
//defined('BASEPATH') OR exit('');
  $mysqli = new mysqli("localhost", "root", "", "hairluks");
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	//this should be run only once on a new db. Would have to be edited later...
/**	if (!$mysqli->query("DROP PROCEDURE IF EXISTS getStylist") ||
			!$mysqli->query('CREATE PROCEDURE getStylist() READS SQL DATA BEGIN SELECT * FROM stylist; END;')) {
			echo "Stored procedure creation failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	if (!$mysqli->query("DROP PROCEDURE IF EXISTS addStyles") ||
			!$mysqli->query('CREATE PROCEDURE addStyles(user_id int(11), styles varchar(225)) BEGIN INSERT styles(user_id, styles)VALUES(user_id, styles); END;')) {
			echo "Stored procedure creation failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}	
	
	if (!$mysqli->query("DROP PROCEDURE IF EXISTS add_pimage") ||
			!$mysqli->query('CREATE PROCEDURE add_pimage(stylist_id int(11), image_url varchar(50)) BEGIN INSERT portfolioimages(stylist_id, image_url)VALUES(stylist_id, image_url); END;')) {
			echo "Stored procedure creation failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		
	if (!$mysqli->query("DROP PROCEDURE IF EXISTS add_deals") ||
			!$mysqli->query('CREATE PROCEDURE add_deals(stylist_id int(11), name varchar(25), description varchar(125), image varchar(100)) BEGIN INSERT deals(stylist_id, name, description, image)VALUES(stylist_id, name, description, image); END;')) {
			echo "Stored procedure creation failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}	**/
	
	if( !empty($_POST["form"]) && $_POST["form"] == "submitDeal") {
	//User hit the save button, and handle accordingly
	echo "Adding deals to stylist";
		addDeals();

	}
	
	if(!empty($_POST["form"]) && $_POST["form"] == "uploadForm"){
	  //User hit the Submit button, and handle accordingly
	  echo "Adding style to stylist";
	  addStylesToDB();
	}
	
	if(empty($_POST["form"]) ){
	  //User hit the Submit button, and handle accordingly
	  echo "Adding image to stylist";
	  add();
	}
	
 
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * Get more details about a users, not just bio. Might include number of created projects and the like
     */
    function get_user_more_details(){
        $this->genlib->ajaxOnly();
        
        $user_id = $this->input->post('user_id', TRUE);
        
        //call model to get info
        $user_info = $this->user->get_user_more_details($user_id);
        
        if($user_info){
            
            foreach($user_info as $get){
                $data['logo_url'] = $get->logo ? base_url() . $get->logo : "../aura_users/default_logo.jpg";
                $street = $get->street ? $get->street . ", " : "";
                $city = $get->city ? $get->city . ", " : "";
                $state = $get->state ? $get->state . ", " : "";
                $country = $get->country ? $get->country : "";
                $data['address'] = $street . $city . $state . $country;
                $data['total_projects_created'] = $get->total_projects_created;
                $data['reg_date'] = date('jS M, Y h:ia', strtotime($get->signup_date));
            }
            
            $this->load->view('users/user_details', $data);
        }
        
        else{
            echo "";
        }
    }
    
	/*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    function addStylesToDB(){
		global $mysqli;
		$user_id = 1;	
		$json['status'] = 0;
		
		//	var_dump($_POST); var_dump($_FILES); die;
		
            //move logo to disk and get url if logo was uploaded
            if(!empty($_POST['input_tag']) ){
				
				$logo_info['msg'] = !$mysqli->query("CALL addStyles('".$user_id."', '".$_POST['input_tag']."')" ) ? $mysqli->errno ." : ". $mysqli->error : $json['status'] = 1;
				
                $json['logo_error'] = $logo_info['msg'];
            }
            else{
                $json['status'] = 0;
				$json['logo_error'] = "no data found";
            }
        
			echo json_encode($json);
			
    }
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * To add new user (admin can do this, perhaps for a limited time)
     */
    function addDeals(){
		global $mysqli;
		
		$logo_info = "";		
		$inserted_id = "";
		$user_id = 1;
		$json['status'] = 0;
		$user_email = "new_user_".$user_id;
		
		//	var_dump($_POST); var_dump($_FILES); die;
		
            //move logo to disk and get url if logo was uploaded
            if(!empty($_FILES['logo']['tmp_name']) ){
                /*
                 * upload_logo method will try to upload file and return status based on the success or failure of the upload
                 * The status and msg will be returned to the client.
                 */
				
                $logo_info = upload_logo($_FILES['logo'], $user_email, "../hair_stylists/Deals" );
				$deals = $logo_info['logo_url'];
			//	var_dump($portfolio); die;
                //insert details if logo was uploaded successfully
                $inserted_id = $logo_info['status'] === 1 
                    ? 
						$logo_info = add_deals($mysqli, $user_id, $deals)
                    : 
					"";
					
                $json['logo_error'] = $logo_info['logo_error_msg'];
                $json['status'] = $logo_info['status'];
			//	var_dump($logo_info);
            }
            else{
            echo "send data to db minus imgs";
				$deals = "";
				
				$logo_info = add_deals($mysqli, $user_id, $deals);
			//	var_dump($logo_info);
                $json['logo_error'] = $logo_info['msg'];
                $json['status'] = $logo_info['status'];
            }
        
			header('Content-type: application/json');
			echo "Message output: ".json_encode($json);
			
    }
	/*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * To add new user (admin can do this, perhaps for a limited time)
     */
    function add(){
		global $mysqli;
		
		$logo_info = "";		
		$inserted_id = "";
		$user_id = 1;
		$user_email = "new_user_".$user_id;
		
		//	var_dump($_POST); var_dump($_FILES); die;
		
            //move logo to disk and get url if logo was uploaded
            if(!empty($_FILES['file']['tmp_name']) ){
                /*
                 * upload_logo method will try to upload file and return status based on the success or failure of the upload
                 * The status and msg will be returned to the client.
                 */
				
                $logo_info = upload_logo($_FILES['file'], $user_email, "../hair_stylists/portfolio" );
				$portfolio = $logo_info['logo_url'];
			//	var_dump($portfolio); die;
                //insert details if logo was uploaded successfully
                $inserted_id = $logo_info['status'] === 1 
                    ? 
						$logo_info['logo_error_msg'] = addImage($mysqli, $user_id, $portfolio)
                    : 
					"";
					
                $json['status'] = $inserted_id ? 1 : 0;
                $json['logo_error'] = $logo_info['logo_error_msg'];
            }
            else{
            echo "send data to db minus imgs";

                $json['status'] = $inserted_id ? 1 : 0;
				$json['logo_error'] = "file not seen.";
            }
        
			header('Content-type: application/json');
			echo "Message output: ".json_encode($json);
			
    }
    
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    
    function upload_logo($file, $email, $upload_dir){
        $json = [];
		$num_files = 1;
        
        if(!empty($file)){
            
            /*
             * We replace the '.' and '@' chars from the email to prevent folder naming error as it
             * will be used as the name of the user's folder
             */
            $stringified_email = str_replace(['@', '.'], ['at', 'dot'], $email);
			
            //make dir to upload logo
            file_exists($upload_dir) ?  : mkdir($upload_dir);
            if(!file_exists($upload_dir."/{$stringified_email}")){
                mkdir($upload_dir."/{$stringified_email}");
            }
			if (!is_writeable($upload_dir)){
			  die ("Error: The directory <b>($upload_dir)</b> is not writable.  ");
			}
            
            $config['file_name'] = "my_logo";//use this as the name of all user's logos
            $config['upload_path'] = "../hair_stylist/{$stringified_email}/";//files are stored outside the app root
            $config['allowed_types'] = array(".gif",".png",".jpeg",".jpg");//'jpg|png|jpeg|jpe';
            $config['file_ext_tolower'] = FALSE;
            $config['encrypt_name'] = TRUE;
            $config['max_size'] = 500000;//in kb

            for ($i = 1; $i <= $num_files; $i++) {

			   //define variables to hold the values.
			//   $new_file = $_FILES['file'.$i];
				$new_file = $file;
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
				  $status = 0;
				  $logo_url = "";
					$msg = "File: Not selected.";
			   }else{
					 #-----------------------------------------------------------#
					 # this code will check file extension                       #
					 #-----------------------------------------------------------#

					 $ext = strrchr($name,'.');
					 if (!in_array(strtolower($ext),$config['allowed_types'])) {
						echo "File $i: ($name) Wrong file extension.  <br><br>";
						$status = 0;
						$logo_url = "";
						$msg = "File $i: ($name) Wrong file extension.";
					 }else{
						   #-----------------------------------------------------------#
						   # this code will check file size is correct                 #
						   #-----------------------------------------------------------#

						   if ($file_size > $config['max_size']){
								echo "File : ($name) Faild to upload. File must be no larger than <b>($file_size)</b> in size.";
								$status = 0;
								$logo_url = "";
								$msg = "File : ($name) Faild to upload. File must be no larger than <b>($file_size)</b> in size.";
						   }else{
						#-----------------------------------------------------------#
						# this code check if file is Already EXISTS.                #
						#-----------------------------------------------------------#
						$uploadfile = $upload_dir."/{$stringified_email}/".$name;
								 if(file_exists($uploadfile)){
									 echo "File: ($name) already exists.    <br><br>";
									 $status = 0;
									 $logo_url = "";
									 $msg = "File: ($name) already exists.";
								 }else{
									   #-------------------------------#
									   # this function will upload the files.         #
									   #-------------------------------#
									//	var_dump(move_uploaded_file($file_tmp, $uploadfile)); die;
									   if(move_uploaded_file($file_tmp, $uploadfile) === FALSE) {
										   
											$msg = "Sorry, file not uploaded!";
											$logo_url = "";
											$status = 0;
										//	$json = ['logo_error_msg'=>$msg, 'status'=>0];
											echo "File: Faild to upload.  <br><br>";
										
									   }else{
										   //   $sql = "INSERT INTO table_name(field1, field2) VALUES('$field1', '$field2');";
										   move_uploaded_file($file_tmp, $uploadfile);
										   
										   echo "File: ($name) has been uploaded successfully.";

											//set values to insert into db
											$file_name = $name;//new file name with the extension
											$logo_url = "download/logo/{$stringified_email}/{$file_name}";//link that will be visible to users
											
											//$json = ['status'=>1, 'logo_url'=>$logo_url, 'logo_error_msg'=>''];
											$status = 1;
											$msg = "";

									   }#end of (move_uploaded_file).

								 }#end of (file_exists).

						   }#end of (file_size).

					 }#end of (limitedext).

			   }#end of (!is_uploaded_file).       
			}
			
	}   
        else{
         //   $json = ['status'=>0, 'logo_error_msg'=>"No image was selected"];
		 $status = 0;
			$msg = "No image was selected";
			$logo_url = "";
        }
        
       $json = ['status'=>$status, 'logo_url'=>$logo_url, 'logo_error_msg'=>$msg]; 
        return $json;
    }

	/****************************************
	runs a quick check of string against string value
	*****************************************/
	
	function compare($key, $whole){
		if (preg_match("/.*{$key}.*/", strtolower($whole))) {
		//	Echo $key . "found in:" . $whole . '<br />';
			return true;
			}
	}

    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    function crosscheckFieldData($mysqli, $fieldData, $user_id, $field){
		
        //check db to ensure email was previously used for user with $user_id i.e. the same user we're updating his details

        $sql = "SELECT id, $field FROM Stylist";

	//	echo "$sql";
		$result = mysqli_query($mysqli, $sql);
		$sqlresult = "";
		
		if (mysqli_num_rows($result) > 0) {
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				
			//	echo "id: " . $row["id"]. " - Name: " . $row["$field"]. "<br>";
				$sqlresult = $sqlresult." ".$row["$field"];
			}
		} else {
			echo "0 results";
		}
	//	var_dump($sqlresult); //die;
			//if email does not exist or it exist but was used by current user
			if(compare($fieldData, $sqlresult)){
				echo"<br>$field Exists, another $field!";
				return FALSE;
			}			
			else{
			//	$this->form_validation->set_message('crosscheckEmail', 'This email is already used by another user');	
				echo"$field Not exists!";				
				return TRUE;
			}
				
		$mysqli->close();	
		
    }
	
	/*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    
	add deals to db, using prepared statement already created on the db and on faliure an sql statement
    */
	function add_deals($mysqli, $user_id, $deals){

		if (!$mysqli->query("CALL add_deals('".$user_id."', '".$_POST["name"]."', '".$_POST["description"]."', '".$deals."')" ) ) { 
			echo "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
			$json['msg'] = $mysqli->errno ." : ". $mysqli->error;
			$json['status'] = 0;
		}else{
			$json['msg'] = $mysqli->errno;
			$json['status'] = 1;
			echo "Stylist updated successfully";}
		
		return $json;	
	}
	/*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    
	Update stylist with portfolio info, using prepared statement already created on the db and on faliure an sql statement
    */
	function updateStylistprofile($mysqli, $user_id, $portfolio){

		if (!$mysqli->query("CALL updateStyPortfolio('".$user_id."', '".$portfolio."')" ) ) {
			echo "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
			
		}else{echo "Stylist updated successfully";}
	}
	
	/*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    
	Update stylist with portfolio info, using prepared statement already created on the db and on faliure an sql statement
    */
	function addImage($mysqli, $user_id, $portfolio){
		
	//	$logo_info['msg'] = !$mysqli->query("CALL add_pimage('".$user_id."', '".$portfolio."')" ) ? $mysqli->errno ." : ". $mysqli->error : $json['status'] = 1;
		
		if (!$mysqli->query("CALL add_pimage('".$user_id."', '".$portfolio."')" ) ) { 
			echo "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
			$json['msg'] = $mysqli->errno ." : ". $mysqli->error;
			$json['status'] = 0;
		}else{
			$json['msg'] = $mysqli->errno;
			$json['status'] = 1;
			echo "Stylist updated successfully";}
		
		return $json;	
	}
	
	/*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    
	Update stylist with portfolio info, using prepared statement already created on the db and on faliure an sql statement
    */
	function fetchData($mysqli){
		
		if (!($stmt = $mysqli->prepare("CALL getStylist()"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}

		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}

		do {
			if ($res = $stmt->get_result()) {
				printf("---\n");
				var_dump(mysqli_fetch_all($res));
				echo "<br>";
				mysqli_free_result($res);
				echo "<br>";
			} else {
				if ($stmt->errno) {
					echo "Store failed: (" . $stmt->errno . ") " . $stmt->error;
				}
			}
		} while ($stmt->more_results() && $stmt->next_result());
	}
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    