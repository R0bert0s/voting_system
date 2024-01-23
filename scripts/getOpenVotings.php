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


    if (isset($token->id) != True)
    {
        http_response_code(401);
        exit;
    }


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $q = 'SELECT `id`, `tytul`,`opis`,`rozpoczecie`,`zakonczenie`,`kworum` FROM `glosowania` WHERE `zakonczenie` > NOW() AND `rozpoczecie` <= NOW();';
    $res = $conn->query($q);

    $out = [];

    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            array_push($out, [$row['id'],$row['tytul'], $row['opis'], $row['rozpoczecie'],$row['zakonczenie'],$row['kworum']]);
        }
    }else{
        var_dump(http_response_code(204));
    }

    echo json_encode($out);
    $conn->close();

    exit;
    

?>