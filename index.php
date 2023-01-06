<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

</head>

<body>
    <div class="container-fluid">
        <div class="row  m-5">
            <div class="col-12 d-flex justify-content-center align-middle">
                <form action="index.php" method="post">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" required aria-describedby="nameHelp" name="name" value="<?php if (isset($_POST['name'])) {
                                                                                                                            echo htmlentities($_POST['name']);
                                                                                                                        } ?>" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Surname</label>
                        <input type="text" class="form-control" required aria-describedby="surnameHelp" name="surname" value="<?php if (isset($_POST['surname'])) {
                                                                                                                                    echo htmlentities($_POST['surname']);
                                                                                                                                } ?>" />
                    </div>
                    <div class=" mb-3">
                        <label class="form-label required">ID Number</label>
                        <input type="text" class="form-control" aria-describedby="idHelp" require name="identityNr" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Of Birth</label>
                        <input type="date" class="form-control" required aria-describedby="dateOfBirthHelp" name="birthday" value="<?php
                                                                                                                                    if (isset($_POST['birthday'])) {
                                                                                                                                        echo htmlentities($_POST['birthday']);
                                                                                                                                    } ?>" />
                    </div>
                    <div class="w-100 d-flex justify-content-between my-3">
                        <button type="submit" class="btn btn-primary">POST</button>
                        <button type="reset" class="btn btn-danger">CANCEL</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>
<?php


require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use MongoDB\Client;

try {

    if ($_POST)
        $client = new Client($_ENV['DATABASE_URL']); // add connection string here
    $db = $client->selectDatabase("Users");
    $collection = $db->userDetails;

    // post body
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $idNr = $_POST['identityNr'];
    $birth = $_POST['birthday'];

    $dateFormat = strtotime($birth);
    $newDate = date("d/m/Y", $dateFormat);

    // retrieves id from db
    $findId = $collection->findOne(
        ['identityNr' =>  $idNr]
    );

    // collection data
    $document =
        [
            'name' => $name,
            'surname' => $surname,
            'identityNr' =>  $idNr,
            'birthday' => $newDate
        ];

    if ($findId['identityNr'] !== $idNr && strlen($_POST['identityNr']) == 13) {
        $collection->insertOne($document);
        echo "<h3>user uploaded</h3>";
    } else {
        echo "<h3>Field Error. \n Please make sure the ID number is correct</h3>";
        http_response_code(401);
    }
} catch (Exception $e) {
    echo 'Message: ' . $e->getMessage();
}
