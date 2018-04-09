<?php
error_reporting(0);
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