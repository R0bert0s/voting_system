<?php
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    require_once('../vendor/autoload.php');
    require_once('_dbConnection.php');

   

    if (! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
        http_response_code(400);
        echo 'Token not found in request';
        exit;
    }

    $jwt = $matches[1];
    if (! $jwt) {
        // No token was able to be extracted from the authorization header
        http_response_code(400);
        exit;
    }


    $token = JWT::decode($jwt, new Key($secretKey, 'HS256'));
    $now = new DateTimeImmutable();


    if ($token->admin != True)
    {
        http_response_code(401);
        exit;
    }


    $vid = $_POST['vid'];


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    try {
        $q2 = "DELETE FROM `glosowania` WHERE `id` = $vid;";

      
        $conn->query($q2);
        
        
    } catch (mysqli_sql_exception $exception) {
        if($exception->getCode() == 1062){
            http_response_code(409);
        }else{
            http_response_code(501);
        }
        throw $exception;
    }
    http_response_code(200);
    $conn->close();
    exit;
     
?>