<?php 

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    require_once('../vendor/autoload.php');
    require_once('../scripts/_dbConnection.php');

     $users = [["Adam", "Kowalski", "adam.kowalski@system.com"],
    ["Anna", "Nowak", "anna.nowak@system.com"],
    ["Piotr", "Wiśniewski", "piotr.wisniewski@system.com"],
    ["Katarzyna", "Dąbrowska", "katarzyna.dabrowska@system.com"],
    ["Marcin", "Lewandowski", "marcin.lewandowski@system.com"],
    ["Magdalena", "Wójcik", "magdalena.wojcik@system.com"],
    ["Kamil", "Kamiński", "kamil.kaminski@system.com"],
    ["Aleksandra", "Kowalczyk", "aleksandra.kowalczyk@system.com"],
    ["Michał", "Zieliński", "michal.zielinski@system.com"],
    ["Karolina", "Szymańska", "karolina.szymanska@system.com"],
    ["Łukasz", "Wojciechowski", "lukasz.wojciechowski@system.com"],
    ["Natalia", "Kozłowska", "natalia.kozlowska@system.com"],
    ["Marek", "Jankowski", "marek.jankowski@system.com"],
    ["Monika", "Mazur", "monika.mazur@system.com"],
    ["Tomasz", "Wojcik", "tomasz.wojcik@system.com"],
    ["Ewa", "Kwiatkowska", "ewa.kwiatkowska@system.com"],
    ["Bartłomiej", "Krawczyk", "bartlomiej.krawczyk@system.com"],
    ["Joanna", "Majewska", "joanna.majewska@system.com"],
    ["Szymon", "Nowakowski", "szymon.nowakowski@system.com"],
    ["Agata", "Adamczyk", "agata.adamczyk@system.com"],
    ["Krzysztof", "Piotrowski", "krzysztof.piotrowski@system.com"],
    ["Patrycja", "Grabowska", "patrycja.grabowska@system.com"],
    ["Kacper", "Nowak", "kacper.nowak@system.com"],
    ["Martyna", "Pawłowska", "martyna.pawlowska@system.com"],
    ["Damian", "Michalski", "damian.michalski@system.com"],
    ["Aneta", "Nowicka", "aneta.nowicka@system.com"],
    ["Artur", "Witkowski", "artur.witkowski@system.com"],
    ["Dominika", "Kucharska", "dominika.kucharska@system.com"],
    ["Rafał", "Olszewski", "rafal.olszewski@system.com"],
    ["Julia", "Jablonska", "julia.jablonska@system.com"],
    ["Konrad", "Wojcik", "konrad.wojcik@system.com"],
    ["Alicja", "Kaczmarek", "alicja.kaczmarek@system.com"],
    ["Grzegorz", "Szewczyk", "grzegorz.szewczyk@system.com"],
    ["Natalia", "Piotrowska", "natalia.piotrowska@system.com"],
    ["Maciej", "Krajewski", "maciej.krajewski@system.com"],
    ["Agnieszka", "Sawicka", "agnieszka.sawicka@system.com"],
    ["Dawid", "Lis", "dawid.lis@system.com"],
    ["Kinga", "Zając", "kinga.zajac@system.com"],
    ["Łukasz", "Górski", "lukasz.gorski@system.com"],
    ["Klaudia", "Kowal", "klaudia.kowal@system.com"],
    ["Paweł", "Sikora", "pawel.sikora@system.com"],
    ["Aleksandra", "Ostrowska", "aleksandra.ostrowska@system.com"],
    ["Mateusz", "Baran", "mateusz.baran@system.com"],
    ["Agnieszka", "Duda", "agnieszka.duda@system.com"],
    ["Przemysław", "Walczak", "przemyslaw.walczak@system.com"],
    ["Weronika", "Czarnecka", "weronika.czarnecka@system.com"],
    ["Łukasz", "Rutkowski", "lukasz.rutkowski@system.com"],
    ["Angelika", "Michalak", "angelika.michalak@system.com"],
    ["Tomasz", "Sobczak", "tomasz.sobczak@system.com"],
    ["Karolina", "Baranowska", "karolina.baranowska@system.com"],
    ["Kamil", "Olszewska", "kamil.olszewska@system"],
    ["Marta", "Dąbrowska", "marta.dabrowska@system.com"],
    ["Kacper", "Zawadzki", "kacper.zawadzki@system.com"],
    ["Kinga", "Jakubowska", "kinga.jakubowska@system.com"],
    ["Filip", "Krawczyk", "filip.krawczyk@system.com"],
    ["Agnieszka", "Pawlak", "agnieszka.pawlak@system.com"],
    ["Dominik", "Michalak", "dominik.michalak@system.com"],
    ["Oliwia", "Witkowska", "oliwia.witkowska@system.com"],
    ["Bartosz", "Sawicki", "bartosz.sawicki@system.com"],
    ["Natalia", "Wojciechowska", "natalia.wojciechowska@system.com"],
    ["Krzysztof", "Jabłoński", "krzysztof.jablonski@system.com"],
    ["Paulina", "Kowalczyk", "paulina.kowalczyk@system.com"],
    ["Maciej", "Duda", "maciej.duda@system.com"],
    ["Justyna", "Wójcik", "justyna.wojcik@system.com"],
    ["Piotr", "Lewandowski", "piotr.lewandowski@system.com"],
    ["Kamila", "Sikorska", "kamila.sikorska@system.com"],
    ["Damian", "Zając", "damian.zajac@system.com"],
    ["Aleksandra", "Górska", "aleksandra.gorska@system.com"],
    ["Tomasz", "Witkowski", "tomasz.witkowski@system.com"],
    ["Aneta", "Szymańska", "aneta.szymanska@system.com"],
    ["Bartłomiej", "Jaworski", "bartlomiej.jaworski@system.com"],
    ["Karolina", "Kaczmarczyk", "karolina.kaczmarczyk@system.com"],
    ["Marcin", "Zalewski", "marcin.zalewski@system.com"],
    ["Alicja", "Piotrowska", "alicja.piotrowska@system.com"],
    ["Rafał", "Szczepański", "rafal.szczepanski@system.com"],
    ["Monika", "Kowalik", "monika.kowalik@system.com"],
    ["Kamil", "Kubiak", "kamil.kubiak@system.com"],
    ["Natalia", "Mazurek", "natalia.mazurek@system.com"],
    ["Paweł", "Olszewski", "pawel.olszewski@system.com"],
    ["Joanna", "Gajewska", "joanna.gajewska@system.com"],
    ["Kacper", "Wesołowski", "kacper.wesolowski@system.com"],
    ["Natalia", "Jasińska", "natalia.jasinska@system.com"],
    ["Mateusz", "Marczak", "mateusz.marczak@system.com"],
    ["Karolina", "Lis", "karolina.lis@system.com"],
    ["Piotr", "Sobczyk", "piotr.sobczyk@system.com"],
    ["Justyna", "Baran", "justyna.baran@system.com"],
    ["Bartosz", "Kwiatkowski", "bartosz.kwiatkowski@system.com"],
    ["Aleksandra", "Głowacka", "aleksandra.glowacka@system.com"],
    ["Krzysztof", "Sokołowski", "krzysztof.sokolowski@system.com"],
    ["Agata", "Zawadzka", "agata.zawadzka@system.com"],
    ["Michał", "Brzeziński", "michal.brzezinski@system.com"],
    ["Weronika", "Kaczmarek", "weronika.kaczmarek@system.com"],
    ["Maciej", "Wójcik", "maciej.wojcik@system.com"],
    ["Natalia", "Jaworska", "natalia.jaworska@system.com"],
    ["Paweł", "Szymański", "pawel.szymanski@system.com"],
    ["Karolina", "Wojciechowska", "karolina.wojciechowska@system.com"],
    ["Bartłomiej", "Kowalik", "bartlomiej.kowalik@system.com"],
    ["Katarzyna", "Sikorska", "katarzyna.sikorska@system.com"],
    ["Rafał", "Górski", "rafal.gorski@system.com"],
    ["Monika", "Witkowska", "monika.witkowska@system.com"]];
   


    $q = "INSERT INTO uzytkownicy (imie, nazwisko, email, haslo, added_by, deleted) VALUES ";
    foreach ($users as &$user){
        $pass  = bin2hex(openssl_random_pseudo_bytes(6));;
        $imie = $user[0];
        $naziwsko = $user[1];
        $email = $user[2];
        $q_user = "('$imie', '$naziwsko', '$email', '$pass', 1, 0),";
        $q .= $q_user;
    }

    $q =  rtrim($q, ',');

    $q = strval($q);

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

    $admin_id = $token->admin_id;


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    try {
         $conn->query($q);
    } catch (mysqli_sql_exception $exception) {
       
      
        http_response_code(501);
        throw $exception;
    }
    http_response_code(201);
    $conn->close();
    exit;

   /*  glosowanie za-przeiw bezwzgledne kworum 1
    glosowanie za-przeiw bezwzgledne kworum 2
    glosowanie za-przeiw bezwzgledne kworum 3
    glosowanie za-przeiw wzgledne kworum 1
    glosowanie za-przeiw wzgledne kworum 2
    glosowanie za-przeiw wzgledne kworum 3
    glosowanie wlasne kworum 1
    glosowanie wlasne kworum 2
    glosowanie wlasne kworum 3
    glosowanie za-przeiw bezwzgledne kworum 1  niespełnione
    glosowanie za-przeiw bezwzgledne kworum 2 niespełnione
    glosowanie za-przeiw bezwzgledne kworum 3 niespełnione
    glosowanie za-przeiw wzgledne kworum 1 niespełnione
    glosowanie za-przeiw wzgledne kworum 2 niespełnione
    glosowanie za-przeiw wzgledne kworum 3 niespełnione
    glosowanie wlasne kworum 1 niespełnione
    glosowanie wlasne kworum 2 niespełnione
    glosowanie wlasne kworum 3 niespełnione */
?>