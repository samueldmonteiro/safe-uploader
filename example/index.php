<?php


require __DIR__ . "/../vendor/autoload.php";

use Samueldmonteiro\SafeUploader\File;

if (isset($_FILES['files'])) {
    $file = new File("../storage/files");

    try {

        $result = $file->upload($_FILES['files'], 9);

        echo "RESULTADO:<br>";
        var_dump($result);
        echo "<br><br>";

    } catch (Exception $e) {
        echo "\nERRO: {$e->getMessage()}\n";
    }

    echo json_encode($_FILES['files']);
}

/** 
if (isset($_FILES['files']['name'][1])) {

    foreach ($_FILES['files']['name'] as $index => $name) {

        if ($_FILES['files']['error'][$index] === UPLOAD_ERR_OK) {
            echo "Arquivo recebido: " . $name . "<br>";
        } else {
            echo "Erro no arquivo: " . $name . "<br>";
        }
    }
}

if (isset($_FILES['files']) && !isset($_FILES['files']['name'][1])) {
    var_dump($_FILES['files']);
    echo "\nRecebido um único arquivo: " . $_FILES['files']['name'][0];
}

*/




/** 
if(isset($_FILES['file'])){

    var_dump($_FILES['file']);
    
    echo "\nMIME TYPE=\n";
    exit;

    try {
        $u = $image->upload($_FILES['file'], $_POST['name'], 900, 90);
        echo $u;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    echo "<br><br>";
}

 **/
require __DIR__ . "/form.php";
