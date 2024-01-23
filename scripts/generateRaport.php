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
        
        $tytul = 0;
        $opis = 0;
        $rozpoczecie = 0;
        $zakonczenie = 0;
        $kworum = 0;
        $zwykle = 0;
        $votes_count = 0;
        $users_count = 0;
        $v_type = 0;

        $voting_details = "SELECT g.*, COUNT(glosy.id) as licz_glosy, (SELECT count(*) FROM uzytkownicy as u LEFT JOIN administracja as a on a.id_uzytkownika_admin = u.id WHERE a.id_uzytkownika_admin is null AND u.data_utworzenia < g.zakonczenie) as liczba_u FROM `glosowania` as g LEFT JOIN opcje as o ON o.id_glosowania = g.id LEFT JOIN glosy ON glosy.id_opcji = o.id WHERE g.id = $vid AND g.zakonczenie < NOW() GROUP BY g.id";
        $res = $conn->query($voting_details);
        if($row = $res->fetch_assoc()){
            $id = $row['id'];
            $tytul = $row['tytul'];
            $opis = $row['opis'];
            $rozpoczecie = $row['rozpoczecie'];
            $zakonczenie = $row['zakonczenie'];
            $kworum = $row['kworum'];
            $zwykle = $row['zwykle'];
            $votes_count = $row['licz_glosy'];
            $users_count = $row['liczba_u'];
            $v_type = $row['voting_type'];
        }else{
            http_response_code(400);
            exit;
        }

        $voting_results = "SELECT o.nazwa, COUNT(g.id) as ilosc FROM `opcje` as o Left JOIN glosy as g ON g.id_opcji = o.id WHERE id_glosowania = $vid GROUP BY o.nazwa ORDER BY COUNT(g.id) DESC;";
        $res = $conn->query($voting_results);
        $voting_results_array = [];
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                array_push($voting_results_array, [$row['nazwa'], $row['ilosc']]);
            }
        }

        //calculating kworum
        $not_voting_count = $users_count - $votes_count;
        $kworum_percent_got = $votes_count / $users_count;
        $kworum_percent = 0;

        if ($kworum == 1) {
            $kworum_percent = 2 / 3;
        } else if ($kworum == 2) {
            $kworum_percent = 0.5;
        }

        if ($kworum_percent_got >= $kworum_percent) {
            $kworum_ans = 1;
        }else{
            $kworum_ans = 0;
        }

        $powod= '';


        //calculating winner
        if($voting_results_array[0][1] == $voting_results_array[1][1]){
            $winner = "Remis - nie rozstrzygnięte";
            $powod = 'Głosowanie nie ważne - brak jednoznacznego zwyciężcy.';
        }
        else if ($zwykle == 1) {
            if($voting_results_array[0][0] == "Przeciw"){
                $winner = "Przeciw";
                $powod = 'Głosowanie odrzucone - brak wymaganaej liczby głosów za.';
                $winner_vote_count = $voting_results_array[0][1];
            }
            else if($voting_results_array[0][0] == "Wstrzymuję się"){
                $winner = "Wstrzymuję się";
                $powod = 'Głosowanie odrzucone - brak wymaganaej liczby głosów za.';
                $winner_vote_count = $voting_results_array[0][1];
            }
            elseif($voting_results_array[0][0] == 'Za' && $voting_results_array[0][1] > $voting_results_array[1][1] + $voting_results_array[2][1]){
                $powod = 'Głosowanie przyjete - za uzyskało wymaganą liczbę głosów';

                $winner = "Za";
                $winner_vote_count = $voting_results_array[0][1];
            }
            else{
                $powod = 'Głosowanie odrzucone - brak uzyskanej większości bezwzględnej';

                $winner = "Przeciw + Wstrzymanie się";
                $winner_vote_count = 0;
                if($voting_results_array[0][0] == "Przeciw"){$winner_vote_count += $voting_results_array[0][1];}
                if($voting_results_array[1][0] == "Przeciw"){$winner_vote_count += $voting_results_array[1][1];}
                if($voting_results_array[2][0] == "Przeciw"){$winner_vote_count += $voting_results_array[2][1];}
                if($voting_results_array[0][0] == "Wstrzymuję się"){$winner_vote_count += $voting_results_array[0][1];}
                if($voting_results_array[1][0] == "Wstrzymuję się"){$winner_vote_count += $voting_results_array[1][1];}
                if($voting_results_array[2][0] == "Wstrzymuję się"){$winner_vote_count += $voting_results_array[2][1];}
            }
        }else{
            $powod = 'Głosowanie ważne - jednoznacznie wybrana opcja';
            
            $winner = $voting_results_array[0][0];
            $winner_vote_count = $voting_results_array[0][1];
        }
        

        $insert_raport = "INSERT INTO `wyniki` ( `id_glosowania`, `voting_users`, `all_users`, `winner_opt`, `winner_votes`, `kworum_type`, `kworum_ok`, `powod`, `generation_time`) VALUES ('$vid', '$votes_count', '$users_count', '$winner', '$winner_vote_count', '$kworum_percent', '$kworum_ans', '$powod', current_timestamp())";
        echo  $insert_raport;
        $conn->query($insert_raport);
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