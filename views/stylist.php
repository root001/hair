<?php


	$mysqli = new mysqli("localhost", "root", "", "hairluks");
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	//this should be run only once on a new db. Would have to be edited later...
	if (!$mysqli->query("DROP PROCEDURE IF EXISTS getStylist") ||
			!$mysqli->query('CREATE PROCEDURE getStylist() READS SQL DATA BEGIN SELECT * FROM stylist; END;')) {
			echo "Stored procedure creation failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	
	if($_POST["addstylist"]) {
	//User hit the save button, handle accordingly
		insertData($mysqli);
		//user should be redirected to another page using js or php depending on the design but no page to redirect to asides form page...
		//Getting stylist data from the DB.		
			fetchData($mysqli); 
			die;
	}
	
	if($_POST["resetForm"]) {
	  //User hit the Submit for reset form button, handle accordingly
	  
	}
	
	function insertData($mysqli){

		if (!$mysqli->query("CALL insertStylist('".$_POST["username"]."', '".$_POST["firstName"]."', '".$_POST["lastName"]."', '".$_POST["email"]."', '".$_POST["mobile1"]."', '".$_POST["mobile2"]."', '".$_POST["password"]."', '".$_POST["logo"]."', '".$_POST["street"]."', '".$_POST["city"]."', '".$_POST["state"]."', '".$_POST["country"]."', '".$_POST["about"]."', '".$_POST["fromtime"]."', '".$_POST["totime"]."', '".$_POST["workday"]."', '".$_POST["picture"]."', '".$_POST["portfolio"]."')" ) ) {
			echo "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
			if(!$mysqli->query("INSERT INTO stylist(username, first_name, last_name, email, mobile_1, mobile_2, password, logo, street, city, state, country, about, weekday_hours, weekend_hours, work_days, picture, portfolio) VALUES ('".$_POST["username"]."', '".$_POST["firstName"]."', '".$_POST["lastName"]."', '".$_POST["email"]."', '".$_POST["mobile1"]."', '".$_POST["mobile2"]."', '".$_POST["password"]."', '".$_POST["logo"]."', '".$_POST["street"]."', '".$_POST["city"]."', '".$_POST["state"]."', '".$_POST["country"]."', '".$_POST["about"]."', '".$_POST["fromtime"]."', '".$_POST["totime"]."', '".$_POST["workday"]."', '".$_POST["picture"]."', '".$_POST["portfolio"]."')") ){
				echo "Data insertion failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
			echo "Stylist created successfully";
		}
	}
	
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

?>