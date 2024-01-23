<?php

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    require_once('../vendor/autoload.php');
    require_once('_dbConnection.php');

    $option_id = $_POST['option_id'];
    $vote_id = $_POST['vote_id'];



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


    if (isset($token->id) != True)
    {
        http_response_code(401);
        exit;
    }

    if ($token->admin == True)
    {
        http_response_code(403);
        exit;
    }


    if ($conn->connect_error) {
        http_response_code(500);
        die("Connection failed: " . $conn->connect_error);
    }
    $q = "SELECT `rozpoczecie`,`zakonczenie` FROM `glosowania` WHERE `id` = $vote_id;";
    $res = $conn->query($q);
    if($res->num_rows > 0){
        if($row = $res->fetch_assoc()){
            $zak = new DateTime($row['zakonczenie']);
            $start = new DateTime($row['rozpoczecie']);
            if( new DateTime()  > $zak || new DateTime < $start){
                http_response_code(406);
                exit;
            }
        }
    }

    $q = "SELECT * FROM glosy WHERE id_opcji IN (SELECT id FROM `opcje` WHERE id_glosowania = $vote_id AND id_uzytkownika = $token->id);";
    $res = $conn->query($q);
    $q_final = '';

    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            $prev_option_id =  $row['id'];
            $q_final = "UPDATE glosy SET id_opcji = $option_id WHERE id =  $prev_option_id AND id_uzytkownika = $token->id";
        }
        http_response_code(202);
    }else{
        $q_final = "INSERT INTO `glosy` (`id_uzytkownika`, `id_opcji`, `data_dodania`) VALUES ($token->id, $option_id, current_timestamp());";
        http_response_code(201);
     }
     $res2 = $conn->query($q_final);
    
    $conn->close();
    exit;
    

?>