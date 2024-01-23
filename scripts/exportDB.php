<?php
    require_once('_dbConnection.php');
    $conn->set_charset("utf8");

    // Get All Table Names From the Database
    $tables = array();
    $sql = "SHOW TABLES";
    $result = mysqli_query($conn, $sql);
    
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }

    $sqlScript = "";
    foreach ($tables as $table) {
        // Prepare SQLscript for creating table structure
        $query = "SHOW CREATE TABLE $table";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_row($result);
        $sqlScript .= "\n\n" . $row[1] . ";\n\n";
        $query = "SELECT * FROM $table";
        $result = mysqli_query($conn, $query);
        $columnCount = mysqli_num_fields($result);
        
        // Prepare SQLscript for dumping data for each table
        for ($i = 0; $i < $columnCount; $i ++) {
            while ($row = mysqli_fetch_row($result)) {
                $sqlScript .= "INSERT INTO $table VALUES(";
                for ($j = 0; $j < $columnCount; $j ++) {
                    $row[$j] = $row[$j];
                    
                    if (isset($row[$j])) {
                        $sqlScript .= '"' . $row[$j] . '"';
                    } else {
                        $sqlScript .= '""';
                    }
                    if ($j < ($columnCount - 1)) {
                        $sqlScript .= ',';
                    }
                }
                $sqlScript .= ");\n";
            }
        }
        $sqlScript .= "\n"; 
    }

    if(!empty($sqlScript))
    {
        // Save the SQL script to a backup file
        $backup_file_name ='_backup_' . time() . '.sql';
        $zipFileName ='_backup_' . time() . '.zip';

        $fileHandler = fopen($backup_file_name, 'w+');
        $number_of_lines = fwrite($fileHandler, $sqlScript);
        fclose($fileHandler); 

        $zip = new ZipArchive();
        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile($backup_file_name, basename($backup_file_name));
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename=' . $zipFileName);
            readfile($zipFileName);
            
            // Możesz usunąć plik zip po wysłaniu, aby nie zajmował miejsca na serwerze
            unlink($zipFileName);
            unlink($backup_file_name);
        } 
        
    }

?>