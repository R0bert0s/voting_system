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

    $pass  = bin2hex(openssl_random_pseudo_bytes(6));;


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $out = [];
    try {
        
        $q= "SELECT u.*, s.id as skeretarz_id, a.id as admin_id FROM `uzytkownicy` AS u LEFT JOIN sekretarze as s ON s.id_uzytkownika = u.id LEFT JOIN administracja as a ON a.id_uzytkownika_admin = u.id WHERE u.deleted = 0 ORDER BY u.nazwisko ASC";
        $res = $conn->query($q);
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                array_push($out, [$row['id'],$row['imie'], $row['nazwisko'], $row['email'],$row['data_utworzenia'],$row['skeretarz_id'], $row['admin_id']]);
            }
            echo json_encode($out);
            http_response_code(200);
        }else{
            echo json_encode([]);
            var_dump(http_response_code(204));
        }
    } catch (mysqli_sql_exception $exception) {
      
        http_response_code(501);
        throw $exception;
    }
    $conn->close();
    exit;
     
?>