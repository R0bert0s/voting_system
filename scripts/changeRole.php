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


    $uid = $_POST['uid'];
    $role = $_POST['role'];


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->begin_transaction();
    try {
        $a_del = "DELETE FROM administracja WHERE `administracja`.`id_uzytkownika_admin` = $uid";
        $s_del = "DELETE FROM sekretarze WHERE `sekretarze`.`id_uzytkownika` = $uid";
        $a_ins = "INSERT INTO `administracja` (`id_uzytkownika_admin`) VALUES ($uid)";
        $s_ins = "INSERT INTO `sekretarze` (`id_uzytkownika`) VALUES ($uid)";

        switch($role){
            case 0:
                $conn->query($s_del);
                $conn->query($a_del);
                break;

            case 1:
                $conn->query($a_del);
                $conn->query($s_ins);
                break;
            
            case 2:

                $has_voted = "SELECT * FROM `glosy` WHERE id_uzytkownika = $uid";

                $res = $conn->query($has_voted);

                echo $res -> num_rows;

                if($res -> num_rows > 0){
                    $conn->close();
                    http_response_code(403);
                    exit;
                }


                $conn->query($s_del);
                $conn->query($a_ins);
                break;
            default:
                http_response_code(501);

        }
        $conn->commit();
        
        
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