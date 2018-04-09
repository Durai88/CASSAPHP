<?php
error_reporting(0);
define('ENCRYPTION_KEY', 'd0a7e7997b6d5fcd55f4b5c32611b87cd923e88837b63bf2941ef819dc8ca282');

function connect_db() {
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
        $dbCon  = connect_db();
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

function rtnStatus($val){
    if($val == 1){
        return  array("Result=>Insert Success","status=>1");
    }elseif($val == 2){
         return  array("Result=>Update Success","status=>1");
    }elseif($val == 3){
         return  array("Result=>Delete Success","status=>1");
    }else{
         return  array("Result=>Failure","status=>0");
    }
    
}



function updateUserbyID($id,$postDet){
    $dbCon  = connect_db();

    if($postDet['book_name'] != ''){        
        $book_name = addslashes($postDet['book_name']);
        $book_name = trim($book_name);
    }    

    if($postDet['book_price'] != ''){        
        $book_price = addslashes($postDet['book_price']);
        $book_price = trim($book_price);
    }

    if($postDet['book_author'] != ''){
        $book_author = addslashes($postDet['book_author']);
        $book_author = trim($book_author);
     }

     $upQury = "UPDATE books SET book_name = '$book_name', book_price = '$book_price', book_author = '$book_author' WHERE id = '$id'";
   
    if(mysqli_query($dbCon,$upQury)){
         $rtnSt = rtnStatus(2);
    }else{
         $rtnSt = rtnStatus(0);
    }
  return $rtnSt;

}

function mc_encrypt($encrypt){
    $encrypt = serialize($encrypt);
    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
    $key = pack('H*', ENCRYPTION_KEY);
    $mac = hash_hmac('sha256', $encrypt, substr(bin2hex($key), -32));
    $passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
    $encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
    return $encoded;
}
// Decrypt Function
function mc_decrypt($decrypt){
    $decrypt = explode('|', $decrypt.'|');
    $decoded = base64_decode($decrypt[0]);
    $iv = base64_decode($decrypt[1]);
    if(strlen($iv)!==mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)){ return false; }
    $key = pack('H*', ENCRYPTION_KEY);
    $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
    $mac = substr($decrypted, -64);
    $decrypted = substr($decrypted, 0, -64);
    $calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
    if($calcmac!==$mac){ return false; }
    $decrypted = unserialize($decrypted);
    return $decrypted;
}