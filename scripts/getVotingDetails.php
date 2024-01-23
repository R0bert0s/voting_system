<?php

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    require_once('../vendor/autoload.php');
    require_once('_dbConnection.php');

    $vid = $_POST['vid'];
   

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


    if ($token->id != True)
    {
        http_response_code(401);
        exit;
    }


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $q = "SELECT g.* FROM `glosowania` as g WHERE g.id = $vid GROUP BY g.id";
    $res = $conn->query($q);

    $out = [];
 

    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            $out = array($row['id'],$row['tytul'], $row['opis'], $row['rozpoczecie'],$row['zakonczenie'],$row['kworum'],$row['zwykle'] );
        
        }
    }else{
        var_dump(http_response_code(401));
    }

    $q = 'SELECT * FROM `opcje` WHERE `id_glosowania` = '.$vid;
    $res = $conn->query($q);
    $options =[];
    $voted = 'Nie oddano głosu';
    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            array_push( $options, [$row['id'],$row['nazwa']]);
        }

        //get info if voted
        $q2 = "SELECT o.nazwa FROM `opcje` as o JOIN glosy as g ON g.id_opcji = o.id and g.id_uzytkownika = $token->id WHERE id_glosowania = $vid;";
        $res2 = $conn->query($q2);
        if($res2->num_rows > 0){
            while($row2 = $res2->fetch_assoc()){
                $voted = $row2['nazwa'];

            }
        }
    }else{
        var_dump(http_response_code(401));
    }
    
    array_push($out, $options, $voted);


    echo json_encode($out);
    $conn->close();

    exit;
    

?>