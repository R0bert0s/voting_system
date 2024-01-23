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
    
    $from = $_POST['from'];
    $to = $_POST['to'];


    try {
        $out = array();
        $q = "SELECT w.*, g.zakonczenie, g.rozpoczecie, g.tytul FROM `wyniki` as w LEFT JOIN glosowania as g on g.id = w.id_glosowania WHERE g.zakonczenie >= '$from 00:00:00' AND g.zakonczenie <= '$to 23:59:59'";
        $res = $conn->query($q);
        if($res -> num_rows > 0){
            while($row = $res -> fetch_assoc()){

                array_push($out,[$row['id_glosowania'],
                $row['voting_users'],
                $row['all_users'],
                $row['winner_opt'],
                $row['winner_votes'],
                $row['kworum_type'],
                $row['kworum_ok'],
                $row['zakonczenie'],
                $row['rozpoczecie'],
                $row['tytul'],
                $row['generation_time'],
                $row['powod']
             ]);

            }
        }else{
            http_response_code(404);
            exit;

        }

        echo json_encode(array_values($out));

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        http_response_code(501);
        throw $exception;
    }
    http_response_code(201);
    $conn->close();
    exit;
     
?>