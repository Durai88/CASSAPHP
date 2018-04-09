<?php
class comAPi{
		function getApiDBConnection(){
			$server = 'localhost'; // this may be an ip address instead
			$user = 'root';
			$pass = '';
			$database = 'books';
			$connection = mysqli_connect($server, $user, $pass,$database);
			return $connection;
		}
		function getUsers() {
    $sql_query = "SELECT id, book_name, book_price FROM books";
    try {
        $dbCon  = $this->getApiDBConnection();
        //mysql_select_db($dbCon, "books") or die ("no database"); 
        $stmt   = mysqli_query($dbCon,$sql_query);
        while($row = mysqli_fetch_assoc($stmt)){
            $rowRtn[]= $row;
        }
        $dbCon  = null;
       return $rowRtn;
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }    
}

		function chekAuthCode($auth){
			$key              = 'TradeBeesAPiOnetd';
			$getDecode   = $this->APIAuth_decode($auth,$key);
			$exPlodeData      = explode('-',$getDecode);
			$userID			  = $exPlodeData[0];
			$userType		  = $exPlodeData[1];

			if($userID !='' && $userType != '')	{
				$dbCon  	  = $this->getApiDBConnection();
				//$chkQuery	  = "SELECT count(*) AS cnt from api_users WHERE api_utoken = '$auth' AND api_uid = '$userID' AND api_utype = '$userType' AND api_ustatus = 'active'"	;
				$chkQuery	  = "SELECT count(*) AS cnt from api_users WHERE api_utoken = '$auth'  AND api_utype = '$userType' AND api_ustatus = 'active'"	;
				$exeQuery     =  mysqli_query($dbCon,$chkQuery);
				$cntQuery    =  mysqli_fetch_array($exeQuery);
				$getCnt      = $cntQuery['cnt'];

				if($getCnt == 0){
					return $this->rtnStatus(5);//Unauthorised User
				}

			}else{
				return $this->rtnStatus(4);//Access token Error
			}
			


			//$
	    }

		function APIAuth_encode($authToken,$key){	
			$encoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $authToken, MCRYPT_MODE_CBC, md5(md5($key))));
			$encoded_alter = str_replace("+","1PLU1",$encoded);
			$encoded_alter = str_replace("/","2SLA2",$encoded_alter);
			$encoded_alter = str_replace("=","3EQU3",$encoded_alter);
			return $encoded_alter;
		}

		function APIAuth_decode($authToken,$key){	
			$decoded_alter = str_replace("1PLU1","+",$authToken);
			$decoded_alter = str_replace("2SLA2","/",$decoded_alter);
			$decoded_alter = str_replace("3EQU3","=",$decoded_alter);	
			$decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($decoded_alter), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
			return $decoded;
		}

		function rtnStatus($val){
		    if($val == 1){
		        return  array("Result=>Insert Success","status=>1");
		    }elseif($val == 2){
		         return  array("Result=>Update Success","status=>1");
		    }elseif($val == 3){
		         return  array("Result=>Delete Success","status=>1");
		    }elseif($val == 4){
		         return  array("Result=>Failure","status=>0","msg=>Access Token is wrong");
		    }elseif($val == 5){
		         return  array("Result=>Failure","status=>0","msg=>Unauthorized User");
		    }elseif($val == 0){
		         return  array("Result=>Failure","status=>0");
		    }    
		}
}	