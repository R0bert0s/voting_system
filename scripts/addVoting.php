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


    $title = $_POST['title'];
    $desc = $_POST['desc'];
    $options = $_POST['options'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    $kworum = $_POST['kworum'];
    $zwykle = $_POST['zwykle'];
    $v_type = $_POST['v_type'];
    $admin_id = $token->admin_id;


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    

    /* Start transaction */
    $conn->begin_transaction();

    try {
        $voting_id = 0;
        $q = 'SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = \''.$db_name .'\' AND TABLE_NAME = \'glosowania\';';
        $res = $conn->query($q);
        $out = [];
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $voting_id = $row['AUTO_INCREMENT'];
            }
        }

        
        $insert_options = 'INSERT INTO opcje ( `id_glosowania`, `nazwa`) VALUES';
        foreach(explode(',', $options) as $opt){
            $insert_options .= ' (\''.$voting_id.'\',\''.$opt.'\'),';
        }
    
        $insert_options = substr($insert_options, 0, -1);
        $insert_voting = 'INSERT INTO `glosowania` (`tytul`, `opis`, `rozpoczecie`, `zakonczenie`, `kworum`,  `zwykle`, `autor_id`, `voting_type`) VALUES (\''.$title.'\', \''.$desc.'\', \''.$start.'\', \''.$end.'\', \''.$kworum.'\',\''.$zwykle.'\', \''.$admin_id.'\', \''.$v_type .'\');';
        $insert_query = $insert_voting." ".$insert_options;

        $conn->query($insert_voting);
        $conn->query($insert_options);
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