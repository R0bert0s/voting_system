<?php
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    require_once('../vendor/autoload.php');
    require_once('../scripts/_dbConnection.php');


    //check auth
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

    //arrays of random data
    $random_title_and_desc_list = [
        ["Czy zwięksić nakłady na badania kosmiczne?","Głosowanie dotyczy zwiększenia budżetu przeznaczonego na badania kosmiczne w celu rozwoju technologii kosmicznych i eksploracji kosmosu."],
        ["Czy zalegalizować małżeństwa homoseksualne?","Głosowanie dotyczy legalizacji małżeństw osób tej samej płci, co ma na celu zapewnienie równych praw i możliwości wszystkim obywatelom."],
        ["Czy wprowadzić podatek od plastiku?","Głosowanie dotyczy wprowadzenia nowego podatku mającego na celu ograniczenie zużycia plastiku i zmniejszenie negatywnego wpływu na środowisko."],
        ["Czy zwiększyć fundusze na ochronę dzikich zwierząt?","Głosowanie dotyczy zwiększenia budżetu przeznaczonego na ochronę dzikich zwierząt oraz ich naturalnych siedlisk w celu zapobiegania wyginięciu gatunków."],
        ["Czy zezwolić na budowę nowych elektrowni węglowych?","Głosowanie dotyczy decyzji o zezwoleniu na budowę nowych elektrowni węglowych, mimo potencjalnego wpływu na emisję gazów cieplarnianych."],
        ["Czy wprowadzić obowiązkowe szczepienia dzieci?","Głosowanie dotyczy wprowadzenia obowiązku szczepień dla dzieci w celu ochrony zdrowia publicznego i zapobiegania epidemii chorób zakaźnych."],
        ["Czy zwiększyć fundusze na walkę z bezrobociem?","Głosowanie dotyczy zwiększenia środków przeznaczonych na programy aktywizacji zawodowej i walkę z bezrobociem."],
        ["Czy zezwolić na uprawę konopi?","Głosowanie dotyczy decyzji o zezwoleniu na uprawę konopi w celach medycznych lub rekreacyjnych."],
        ["Czy zwiększyć fundusze na ochronę zabytków?","Głosowanie dotyczy zwiększenia środków finansowych na restaurację i ochronę zabytków kultury materialnej."],
        ["Czy zezwolić na wydobycie ropy naftowej w okolicach parku narodowego?","Głosowanie dotyczy decyzji o zezwoleniu na wydobycie ropy naftowej w okolicach parku narodowego i potencjalnego wpływu na środowisko naturalne."],
        ["Czy wprowadzić obowiązkowe ubezpieczenie zdrowotne dla wszystkich obywateli?","Głosowanie dotyczy wprowadzenia obowiązku posiadania ubezpieczenia zdrowotnego dla wszystkich obywateli w celu zapewnienia powszechnego dostępu do opieki zdrowotnej."],
        ["Czy zwiększyć minimalną emeryturę?","Głosowanie dotyczy decyzji o zwiększeniu minimalnej kwoty emerytury w celu poprawy warunków życia osób starszych."],
        ["Czy wprowadzić podatek od loterii?","Głosowanie dotyczy wprowadzenia nowego podatku od gier losowych, którego dochody mogą być przeznaczone na cele charytatywne lub społeczne."],
        ["Czy zezwolić na stosowanie energii jądrowej?","Głosowanie dotyczy decyzji o zezwoleniu na stosowanie energii jądrowej jako alternatywnej formy produkcji energii."],
        ["Czy zwiększyć fundusze na badania nad nowymi lekami?","Głosowanie dotyczy zwiększenia środków przeznaczonych na badania naukowe nad nowymi lekami i terapiami."],
        ["Czy zalegalizować eutanazję?","Głosowanie dotyczy legalizacji eutanazji, czyli świadomego i dobrowolnego zakończenia życia pacjenta cierpiącego na nieuleczalną chorobę."],
        ["Czy zwiększyć minimalną stawkę godzinową?","Głosowanie dotyczy decyzji o podwyższeniu minimalnej stawki godzinowej dla pracowników, w celu poprawy warunków pracy i godziwego wynagrodzenia."],
        ["Czy wprowadzić obowiązkowe testy na obecność substancji psychoaktywnych w organizmie kierowców?","Głosowanie dotyczy wprowadzenia obowiązkowych testów na obecność substancji psychoaktywnych u kierowców w celu zapobiegania wypadkom drogowym spowodowanym przez nietrzeźwych kierowców."],
        ["Czy zezwolić na prowadzenie hazardu online?","Głosowanie dotyczy decyzji o zezwoleniu na prowadzenie hazardu online i regulacji rynku gier hazardowych w sieci."],
        ["Czy zwiększyć fundusze na ochronę lasów?","Głosowanie dotyczy zwiększenia środków finansowych na ochronę lasów i zrównoważone zarządzanie zasobami leśnymi."],
        ["Czy zezwolić na stosowanie pestycydów w rolnictwie?","Głosowanie dotyczy decyzji o zezwoleniu na stosowanie pestycydów w celu ochrony upraw rolnych przed szkodnikami i chorobami."],
        ["Czy wprowadzić obowiązkowe zajęcia sportowe w szkołach?","Głosowanie dotyczy wprowadzenia obowiązku organizacji zajęć sportowych w szkołach w celu promocji zdrowego stylu życia i aktywności fizycznej."],
        ["Czy zwiększyć fundusze na walkę z korupcją?","Głosowanie dotyczy zwiększenia środków przeznaczonych na działania antykorupcyjne i zapobieganie nadużyciom władzy publicznej."],
        ["Czy zezwolić na eksperymenty na zwierzętach w celach naukowych?","Głosowanie dotyczy decyzji o zezwoleniu na przeprowadzanie eksperymentów na zwierzętach w celach naukowych i medycznych."],
        ["Czy wprowadzić podatek od produktów wysoko przetworzonych?","Głosowanie dotyczy wprowadzenia nowego podatku od produktów wysoko przetworzonych w celu promocji zdrowych nawyków żywieniowych oraz ograniczenia spożycia niezdrowych produktów spożywczych."]
    ];
    $answers = [
        ["Anna Kowalska", "Jan Nowak", "Magdalena Wiśniewska"],
        ["Piotr Zając", " Monika Lewandowska", " Bartosz Kowalczyk", " Karolina Szymańska"],
        ["Katarzyna Woźniak", " Michał Dąbrowski", " Agnieszka Jankowska"],
        ["Andrzej Nowak", " Aleksandra Kowalczyk", " Paweł Wiśniewski"],
        ["Karolina Nowak", " Tomasz Kowalski", " Patrycja Wiśniewska", " Artur Dąbrowski"],
        ["12-05-2023", "15-09-2022", "03-11-2023"],
        ["07-04-2022", "28-06-2022", "19-10-2022", "22-12-2022"],
        ["30-01-2023", "14-08-2023", "25-12-2023"],
        ["03-03-2022", "21-07-2022", "09-11-2022"],
        ["16-09-2022", "05-11-2022", "27-12-2022", "18-02-2023"],
        ["Laptop", " Smartfon", " Aparat fotograficzny"],
        ["Ołówek", " Notes", " Długopis", " Gumka do mazania"],
        ["Książka", " Zeszyt", " Dziennik"],
        ["Zegarek", " Bransoletka", " Kolczyki"],
        ["Okulary", " Etui na telefon", " Portfel", " Plecak"]
    ];

    //get one radome title,desc
    $rk_1 = array_rand($random_title_and_desc_list, 1);
    $title_desc = $random_title_and_desc_list[$rk_1];

   //get post data
    $v_type = $_POST["sym_voting_type"];
    $kworum_type = $_POST["sym_kworum_type"];
    $kworum_result = $_POST["syn_kworum_res"];
    $start_date = $_POST["sym_start"];
    $end_date = $_POST["sym_end"];


    //set vote type (zwyczajne / niestandardowe) 
    $v_type_db = 2;


    //niestadnardowe
    if($v_type == 1){                  
        //wylosuj zzestaw odpowidzi dla niestandardowego
        $rk_2 = array_rand($answers, 1);
        $options = $answers[$rk_2];
        array_push($options, "Wstrzymuję się");
        $zwykle = 0;
        $v_type_db = 3;
    }
    //standardowe - bezwzgledne
    else if($v_type == 2){             
        $zwykle = 1;
        $options = ["Za", "Przeciw", "Wstrzymuję się"];
    }
    //standardowe - wzgledne
    else{                              
        $zwykle = 2;
        $options = ["Za", "Przeciw", "Wstrzymuję się"];
    }
    

    //calculate kworum fraction
    $k_fraction = 0;

    //kworum 66.66%
    if($kworum_type == 1){
        $k_fraction = 2/3;
    }
    //kworum 50.00%
    else if($kworum_type == 2){
        $k_fraction = 1/2;
    }

    //calculate start and end
    $a = new DateTime();
    $b = new DateTime();

    //if start in 10 min
    if($start_date == 2){
        $a->modify("+10 minutes");
        $b->modify("+10 minutes");

    }
    $start = $a->format('Y-m-d H:i:s');

    //if end in 10 min
    if($end_date == 2){
        $b->modify("+10 minutes");
    }
    $end = $b->format('Y-m-d H:i:s');


    //create voting query
    $insert_voting = 'INSERT INTO `glosowania` (`tytul`, `opis`, `rozpoczecie`, `zakonczenie`, `kworum`,  `zwykle`, `autor_id`, `voting_type`) VALUES (\''.$title_desc[0].'\', \''.$title_desc[1].'\', \''.$start.'\', \''.$end.'\', \''.$kworum_type.'\',\''.$zwykle.'\', \''.$admin_id.'\', \''.$v_type_db.'\');';


    //connecting to db
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $conn->begin_transaction();

    try {


        //get next id for voting, options and user count
        $voting_id = 0;
        $option_id = 0;
        $user_count = 0;
        $q = 'SELECT (SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = \''.$db_name.'\' AND TABLE_NAME = \'glosowania\') as g_ai, (SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = \''.$db_name.'\' AND TABLE_NAME = \'opcje\') as o_ai, (SELECT count(*) FROM uzytkownicy as u LEFT JOIN administracja as a on a.id_uzytkownika_admin = u.id WHERE a.id_uzytkownika_admin is null AND u.data_utworzenia < \''.$end.'\') as u_count;';
        $res = $conn->query($q);
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $voting_id = $row['g_ai'];
                $option_id = $row['o_ai'];
                $user_count = $row['u_count'];
            }
        }

        //create insert query for options
        $insert_options = 'INSERT INTO opcje ( `id_glosowania`, `nazwa`) VALUES';
        foreach($options as $opt){
            $insert_options .= ' (\''.$voting_id.'\',\''.$opt.'\'),';
        }
        $insert_options = substr($insert_options, 0, -1);

        //calculate votes limit count base on kworum 
        if($kworum_result == 1){
            //spelnione
            $kworum_poepole_limit = rand((intval($user_count)*$k_fraction)+1, intval($user_count));
        }else if($kworum_result == 2){
            //nie spelnione
            $kworum_poepole_limit = rand(0, (intval($user_count)*$k_fraction)-1);
        }else{
            //brak
            $kworum_result == rand(0, intval($user_count));
        }

        //get users id for adding votes
        $create_votes_q = 'SELECT u.id FROM uzytkownicy as u LEFT JOIN administracja as a on a.id_uzytkownika_admin = u.id WHERE a.id_uzytkownika_admin is null AND u.data_utworzenia < \''.$end.'\' LIMIT '.$kworum_poepole_limit .';';

        //create votes query for every user
        $res = $conn->query(strval($create_votes_q));
        $insert_votes = 'INSERT INTO glosy ( `id_uzytkownika`, `id_opcji`, `data_dodania`) VALUES';
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $insert_votes .=  '(\''.$row['id'].'\', \''.rand($option_id, $option_id+count($options)-1).'\', current_timestamp()),';
            }
        }
        $insert_votes = substr($insert_votes, 0, -1);

        //excec
        $conn->query($insert_voting);
        $conn->query($insert_options);
        $conn->query($insert_votes);
        $conn->commit();

    } catch (mysqli_sql_exception $exception) {
    
        echo $exception->getCode();
        http_response_code(501);
        throw $exception;
    }
    http_response_code(201);
    $conn->close();
    exit;



?>