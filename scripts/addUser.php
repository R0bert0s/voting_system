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


    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $email = $_POST['email'];
    $admin_id = $token->admin_id;
    $pass  = bin2hex(openssl_random_pseudo_bytes(6));;


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    try {
        
        $q= "INSERT INTO `uzytkownicy` (`id`, `imie`, `nazwisko`, `email`, `haslo`, `data_utworzenia`, `added_by`,`deleted`) VALUES (NULL, '$f_name', '$l_name', '$email', '$pass', current_timestamp(), '$admin_id', 0)";
        $conn->query($q);
        $conn->commit();
    } catch (mysqli_sql_exception $exception) {
        if($exception->getCode() == 1062){
            http_response_code(409);
        }else{
            http_response_code(501);
        }
        throw $exception;
    }
    http_response_code(201);
    $conn->close();
    exit;
     
?>