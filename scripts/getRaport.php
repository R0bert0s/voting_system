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


    if ($token->admin != True && $token->sekretarz != True)
    {
        http_response_code(401);
        exit;
    }


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $vid = $_POST['vid'];

    /* Start transaction */
    $conn->begin_transaction();

    try {
        
        $q = "SELECT * FROM `wyniki` WHERE id_glosowania = $vid";
        $res = $conn->query($q);
        if($row = $res -> fetch_assoc()){
            echo json_encode( array('id_glosowania'=> $row['id_glosowania'], 'voting_users'=> $row['voting_users'],'all_users'=> $row['all_users'],'winner_opt'=> $row['winner_opt'],'winner_votes'=> $row['winner_votes'],'kworum_type'=> $row['kworum_type'],'kworum_ok'=> $row['kworum_ok'],'generation_time'=> $row['generation_time']));
        }else{
            http_response_code(404);
            exit;
        }

        $conn->commit();
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        http_response_code(501);
        throw $exception;
    }
    http_response_code(201);
    $conn->close();
    exit;
     
?>