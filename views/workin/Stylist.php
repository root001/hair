<?php
//defined('BASEPATH') OR exit('');
  $mysqli = new mysqli("localhost", "root", "", "hairluks");
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
/**	if (!$mysqli->query("DROP PROCEDURE IF EXISTS insertStylist") ||
			!$mysqli->query('CREATE PROCEDURE insertStylist(username varchar(25), first_name varchar(20), last_name varchar(20), email varchar(100), mobile_1 varchar(15),mobile_2 varchar(15), password char(60), logo varchar(100), street text, city varchar(20), state varchar(20), country varchar(20),about varchar(60), weekday_hours  TIME, weekend_hours TIME,	work_days varchar(20), picture varchar(100), portfolio varchar(100) )
			BEGIN
			INSERT users(username, first_name, last_name, email, mobile_1, mobile_2, password, logo, street, city, state, country, about, weekday_hours, weekend_hours, work_days, picture, portfolio) VALUES(username, first_name, last_name, email, mobile_1, mobile_2, password, logo, street, city, state, country, about, weekday_hours, weekend_hours, work_days, picture, portfolio);
			END;')) {
			echo "Stored procedure creation failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	//this should be run only once on a new db. Would have to be edited later...
	if (!$mysqli->query("DROP PROCEDURE IF EXISTS getStylist") ||
			!$mysqli->query('CREATE PROCEDURE getStylist() READS SQL DATA BEGIN SELECT * FROM stylist; END;')) {
			echo "Stored procedure creation failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}**/
		
//	echo "starting....";
	
	add();
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * lau_ = "Load all users"
     */
    function lau_(){
        //set the sort order
        $order_by = $this->input->get('orderBy', TRUE) ? $this->input->get('orderBy', TRUE) : "first_name";
        $order_format = $this->input->get('orderFormat', TRUE) ? $this->input->get('orderFormat', TRUE) : "ASC";
        
        //count the total users in db
        $total_users = $this->db->count_all('users');
        
        $this->load->library('pagination');
        
        $page_number = $this->uri->segment(3, 0);//set page number to zero if the page number is not set in the third segment of uri
	
        $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10;//show $limit per page
        $start = $page_number == 0 ? 0 : ($page_number - 1) * $limit;//start from 0 if $page_number is 0, else start from the next iteration
        
        //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes, $uri_segment=3) in genlib to configure pagination
        $config = $this->genlib->setPaginationConfig($total_users, "users/lau_", $limit, ['class'=>'lnp'], "");
        
        $this->pagination->initialize($config);//initialize the library class
        
        //get all users from db
        $data['all_users'] = $this->user->get_all($order_by, $order_format, $start, $limit);
        $data['range'] = $total_users > 0 ? ($start+1) . "-" . ($start + count($data['all_users'])) . " of " . $total_users : "";
        $data['links'] = $this->pagination->create_links();//page links
        $data['sn'] = $start+1;
        
        $json['usersTable'] = $this->load->view('users/all_users', $data, TRUE);//get view with populated customers table

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
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
    
    /**
     * To add new user (admin can do this, perhaps for a limited time)
     */
    function add(){
		global $mysqli;
		$logo_info = "";
		$profile_info = "";
		
	//	$inserted_id = "";
		
		$fieldmbl = "mobile_1";
		$fieldDatambl = $_POST['mobile_1'];
		$fielduser = "username";
		$fieldDatauser = $_POST['username'];
		$fieldem = "email";
		$fieldDataem = $_POST['email'];
		
		if(crosscheckFieldData($mysqli, $fieldDatambl, "", $fieldmbl) || crosscheckFieldData($mysqli, $fieldDatauser, "", $fielduser)|| crosscheckFieldData($mysqli, $fieldDataem, "", $fieldem) !== FALSE){

		//	var_dump($_POST); var_dump($_FILES); die;
		//	echo $_POST["lastName"] . $_POST('email');
		
            //move logo to disk and get url if logo was uploaded
            if(!empty($_FILES['logo']['tmp_name']) and !empty($_FILES['profile']['tmp_name'])){
                /*
                 * upload_logo method will try to upload file and return status based on the success or failure of the upload
                 * The status and msg will be returned to the client.
                 */
				
                $logo_info = upload_logo($_FILES['logo'], $_POST['email'], "../hair_stylists/logo" );
				$profile_info = upload_logo($_FILES['profile'], $_POST['email'], "../hair_stylists/profile" );
				
                //insert details if logo was uploaded successfully
                $inserted_id = $logo_info['status'] === 1 
                    ? 
						insertData($mysqli, $logo_info, $profile_info)
                    : 
					"";
					
                $json['status'] = $inserted_id ? 1 : 0;
                $json['logo_error'] = $logo_info['logo_error_msg'];
            }
            else{
         //   echo "send data to db minus imgs";
                /**
                 * insert info into db
                 * function header: add($username, $first_name, $last_name, $email, $profession, $mobile_1, $mobile_2, $password, $logo
                 * $street, $city, $state, $country)**/
                 
                $inserted_id = insertData($mysqli, $logo_info, $profile_info);
                //send welcome email to user
                //$inserted_id ? $this->genlib->sendWelcomeMessage($membershipId, $memberName, set_value('email')) : "";

                $json['status'] = $inserted_id['status'];
				$json['logo_error'] = $inserted_id['msg'];
			//	var_dump($json, $inserted_id); die;
            }
        }
        else{
            //return all error messages
         //   $json = $this->form_validation->error_array();//get an array of all errors
            
            $json['msg'] = "One or more required fields are empty or not correctly filled";
            $json['status'] = 0;
        }          
	//	var_dump($json); //exit;
	//		header('Content-type: application/json');
		 echo(json_encode($json));
	//	 echo json_encode($output);
			
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
    
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    
    /**
     * 
     */
    function update(){
        $this->genlib->ajaxOnly();
        
        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');
        
        $this->form_validation->set_rules('title', 'Title', ['trim', 'max_length[25]', 'strtolower', 'ucfirst']);
        $this->form_validation->set_rules('firstName', 'First name', ['required', 'trim', 'max_length[20]', 'strtolower', 'ucfirst'], 
                ['required'=>"required"]);
        $this->form_validation->set_rules('lastName', 'Last name', ['required', 'trim', 'max_length[20]', 'strtolower', 'ucfirst'], 
                ['required'=>"required"]);
        $this->form_validation->set_rules('otherName', 'Other names', ['trim', 'max_length[30]', 'strtolower', 'ucfirst']);
        $this->form_validation->set_rules('mobile1', 'Phone number', ['required', 'trim', 'numeric', 'max_length[15]', 
            'min_length[11]', 'callback_crosscheckMobile['. $this->input->post('custId', TRUE).']'], ['required'=>"required"]);
        $this->form_validation->set_rules('mobile2', 'Other number', ['trim', 'numeric', 'max_length[15]', 'min_length[11]']);
        $this->form_validation->set_rules('email', 'Email', ['required', 'trim', 'valid_email', 'callback_crosscheckEmail['. $this->input->post('custId', TRUE).']']);
        $this->form_validation->set_rules('gender', 'Gender', ['required', 'trim'], ['required'=>"required"]);
        $this->form_validation->set_rules('membershipId', 'Membership ID', ['required', 'trim', 'numeric', 
            'callback_crosscheckMembershipId['. $this->input->post('custId', TRUE).']'], ['required'=>"required"]);
        $this->form_validation->set_rules('address', 'Address', ['required'], ['required'=>"required"]);
        $this->form_validation->set_rules('city', 'City', ['required'], ['required'=>"required", 'strtolower', 'ucfirst']);
        $this->form_validation->set_rules('state', 'State', ['required'], ['required'=>"required", 'strtolower', 'ucfirst']);
        $this->form_validation->set_rules('country', 'Country', ['required'], ['required'=>"required", 'strtolower', 'ucfirst']);
        
        if($this->form_validation->run() !== FALSE){
            $this->db->trans_start();
            
            /**
             * update info in db
             * function header: update($customerId, $firstName, $lastName, $otherName, $mobile1, $mobile2, $email, $gender, $address, $city, $state, $country)
             */
				
            $customerId = $this->input->post('custId', TRUE);

            $updated = $this->customer->update(set_value('title'), $customerId, set_value('firstName'), set_value('lastName'),
                    set_value('otherName'), set_value('mobile1'), set_value('mobile2'), set_value('email'), set_value('gender'), 
                    set_value('address'), set_value('city'), set_value('state'), set_value('country'));
            
            $membershipId = $this->genmod->gettablecol('customers', 'membershipId', 'custId', $customerId);
            
            //insert into eventlog
            //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
            $desc = "The details of member with membership ID '$membershipId' was updated";
            
            $updated ? $this->genmod->addevent("Member details update", $customerId, $desc, "customers", $this->session->admin_id) : "";
            
            $this->db->trans_complete();
            
            $json = $updated ? 
                    ['status'=>1, 'msg'=>"Member info successfully updated"] 
                    : 
                    ['status'=>0, 'msg'=>"Oops! Unexpected server error! Pls contact administrator for help. Sorry for the embarrassment"];
            
            //notify member of update
            $memberName = set_value('firstName')." ".set_value('lastName')." ".set_value('otherName');
            $this->genlib->sendMemberUpdateMsg($memberName, set_value('email'));
        }
        
        else{
            //return all error messages
            $json = $this->form_validation->error_array();//get an array of all errors
            
            $json['msg'] = "One or more required fields are empty or not correctly filled";
            $json['status'] = 0;
        }
                    
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
    
    
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    
    /**
     * Used as a callback while updating user's info to ensure 'mobile_1' field does not contain a number already used by another user
     * @param type $mobile_number
     * @param type $user_id
     */
    function crosscheckMobile($mobile_number, $user_id){
        //check db to ensure number was previously used for user with $user_id i.e. the same user we're updating
        $user_with_num = $this->genmod->getTableCol('users', 'id', 'mobile_1', $mobile_number);
        
        //if number does not exist or it exist but was used by current user
        if(!$user_with_num || ($user_with_num == $user_id)){
            return TRUE;
        }
        
        else{//if it exist and was used by another customer
            $this->form_validation->set_message('crosscheckMobile', 'This number is already used by another user');
                
            return FALSE;
        }
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
		//		echo"<br>$field Exists, another $field!";
				return FALSE;
			}			
			else{
			//	$this->form_validation->set_message('crosscheckEmail', 'This email is already used by another user');	
		//		echo"$field Not exists!";				
				return TRUE;
			}
				
		$mysqli->close();	
		
    }
    
    /**
     * Used as a callback while updating cust info to ensure 'email' field does not contain an email already used by another user
     * @param type $email
     * @param type $user_id
     */
    function crosscheckEmail($email, $user_id){
        //check db to ensure email was previously used for user with $user_id i.e. the same user we're updating his details
        $user_with_email = $this->genmod->getTableCol('users', 'id', 'email', $email);
        
        //if email does not exist or it exist but was used by current user
        if(!$user_with_email || ($user_with_email == $user_id)){
            return TRUE;
        }
        
        else{
            $this->form_validation->set_message('crosscheckEmail', 'This email is already used by another user');
                
            return FALSE;
        }
    }
    
    
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    
    
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * To get user's biodata only
     */
    function get_user_bio(){
        $this->genlib->ajaxOnly();
        
        $user_id = $this->input->post('user_id', TRUE);
        
        //call model to get info
        $user_info = $this->customer->getCustBio($user_id);
        
        if($user_info){
            
            foreach($user_info as $get){
                $json['title'] = $get->title;
                $json['firstName'] = $get->firstName;
                $json['lastName'] = $get->lastName;
                $json['otherName'] = $get->otherName;
                $json['custId'] = "CUS-ID-".$get->custId;
                $json['mobile1'] = $get->mobile1;
                $json['mobile2'] = $get->mobile2;
                $json['email'] = $get->email;
                $json['gender'] = $get->gender;
                $json['membershipId'] = $get->membershipId;
                $json['address'] = $get->address;
                $json['city'] = $get->city;
                $json['state'] = $get->state;
                $json['country'] = $get->country;
            }
            
            $json['status'] = 1;
        }
        
        else{
            $json = ['status'=>0];
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
    
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    
    /**
     * Get all projects created by a user
     */
    function get_user_projects(){
        $this->genlib->ajaxOnly();
        
        $this->load->model('project');
        $this->load->helper('text');
        
        $user_id = $this->input->get('user_id', TRUE);
        
        //set the sort order (itemName, quantity, unitPrice, totalPrice, transDate[default])
        $order_by = $this->input->get('order_by') ? $this->input->get('order_by', TRUE) : "projects.date_created";
        $order_format = $this->input->get('order_format') ? $this->input->get('order_format', TRUE) : "DESC";
        
        //count the total number of transactions customer was involved
        $total_projects = count($this->project->get_user_projects($user_id, $order_by, $order_format, '', ''));
        
        $this->load->library('pagination');
        
        $page_number = $this->uri->segment(3, 0);//set page number to zero if the page number is not set in the third segment of uri
	
        $limit = $this->input->get('limit') ? $this->input->get('limit', TRUE) : 10;//show $limit per page
        $start = $page_number == 0 ? 0 : ($page_number - 1) * $limit;//start from 0 if pageNumber is 0, else start from the next iteration
        
        //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
        $config = $this->genlib->setPaginationConfig($total_projects, "users/get_user_projects", $limit, ['class'=>'lupnp']);
        
        $this->pagination->initialize($config);//initialize the library class
        
        //get projects
        $user_projects = $this->project->get_user_projects($user_id, $order_by, $order_format, $start, $limit);
        
        if($user_projects){//if at least one result is returned
            $data['user_projects'] = $user_projects;
            $data['sn'] = $start+1;//table SN
            
            //load transactions table
            $json['userProjectListTable'] = $this->load->view('users/user_project_list_table', $data, TRUE);
            
            //other info to return
            $json['range'] = $total_projects > 0 ? ($start+1) . "-" . ($start + count($user_projects)) . " of " . $total_projects : "";//range being displayed
            $json['links'] = $this->pagination->create_links();//page links
            $json['userName'] = trim($this->genmod->gettablecol('users', 'CONCAT_WS(" ", first_name, last_name)', 'id', $user_id));
            $json['status'] = 1;
        }
        
        else{
            $json = ['status'=>0];
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
    
	/*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    password blowfish encryption and salt generation*/
	  // Original PHP code by Chirp Internet: www.chirp.com.au rounds can be 7, 10, 15
	function passwrd_crypt($input, $rounds = 10)
	{
		$salt = "";
		$salt_chars = array_merge(range('A','Z'), range('a','z'), range(0,9));
		for($i=0; $i < 22; $i++) {
		  $salt .= $salt_chars[array_rand($salt_chars)];
		}
		return crypt($input, sprintf('$2a$%02d$', $rounds) . $salt);
	}
	
	/*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    inserting stylists data to the database*/
	function insertData($mysqli, $logo_info, $profile_info){
		$_POST["portfolio"] = "";
		
	//	check the value of logo_url and init to null if it does not exist
		if(empty($logo_info['logo_url'])){
			$logo = "No image";
		}else
		{$logo = $logo_info['logo_url'];}
		if(empty($profile_info['logo_url'])){
			$profile = "No image";
		}else
		{$profile = $profile_info['logo_url'];}
	
	//hashed password
	$password = passwrd_crypt($_POST["password"]);
		
/**	
		if (!$mysqli->query("CALL insertStylist('".$_POST["username"]."', '".$_POST["first_name"]."', '".$_POST["last_name"]."', '".$_POST["email"]."', '".$_POST["mobile_1"]."', '".$_POST["mobile_2"]."', '".$_POST["password"]."', '".$logo."', '".$_POST["street"]."', '".$_POST["city"]."', '".$_POST["state"]."', '".$_POST["country"]."', '".$_POST["about"]."', '".$_POST["from_time"]."', '".$_POST["to_time"]."', '".$_POST["work_day"]."', '".$profile."', '".$_POST["portfolio"]."')" ) ) {
		//	echo "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
			*/
		if(!$mysqli->query("INSERT INTO stylist(username, first_name, last_name, email, mobile_1, mobile_2, password, logo, street, city, state, country, about, weekday_hours, weekend_hours, work_days, picture, portfolio) VALUES ('".$_POST["username"]."', '".$_POST["first_name"]."', '".$_POST["last_name"]."', '".$_POST["email"]."', '".$_POST["mobile_1"]."', '".$_POST["mobile_2"]."', '".$password."', '".$logo."', '".$_POST["street"]."', '".$_POST["city"]."', '".$_POST["state"]."', '".$_POST["country"]."', '".$_POST["about"]."', '".$_POST["from_time"]."', '".$_POST["to_time"]."', '".$_POST["work_day"]."', '".$profile."', '".$_POST["portfolio"]."')") ){
			//	echo "Data insertion failed: (" . $mysqli->errno . ") " . $mysqli->error; 
				$json['msg'] = $mysqli->errno ." : ". $mysqli->error;
				$json['status'] = 0;
			}else{
				$json['msg'] = $mysqli->errno;
			$json['status'] = 1;
		//	echo "Stylist created successfully";
		}
		return $json;
	}
	
	/*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    fetching stylists data from the database*/
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
    