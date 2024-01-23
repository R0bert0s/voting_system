<?php
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    require_once('../vendor/autoload.php');
    require_once('_dbConnection.php');
    $email = $_POST['email'];
    $passwd = $_POST['passwd'];

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $q = "SELECT u.id, u.imie, u.nazwisko, u.email, administracja.id as admin_ID, sekretarze.id AS sekretarz_id FROM `uzytkownicy` as u LEFT JOIN `administracja` ON administracja.id_uzytkownika_admin = u.id LEFT JOIN sekretarze ON sekretarze.id_uzytkownika = u.id WHERE u.email = '$email' AND u.haslo='$passwd' AND u.deleted =0;";
    $res = $conn->query($q);
    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            $payload = [];
            $payload = [
               'id' => $row['id'],
               'name' => $row['imie'],
               'lastname' => $row['nazwisko'],
               'email' => $row['email'],
               'admin' => ($row['admin_ID'] == null ? False : True),
               'admin_id' => $row['admin_ID'],
               'sekretarz' => ($row['sekretarz_id'] == null ? False : True),
               'sekretarz_id' => $row['sekretarz_id']
            ];
            echo JWT::encode($payload, $secretKey, 'HS256');
        }
        http_response_code(200);
    }else{
        var_dump(http_response_code(401));
    }
    $conn->close();

    exit;
    
?>