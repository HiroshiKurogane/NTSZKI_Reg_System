<?php 
    include_once __DIR__ . '/../controller.php';
    header('Content-Type: application/json');

    if($_SERVER["REQUEST_METHOD"] == "GET") {
        $controller = new Controller();
        $result = $controller->getAllKolisJelentkezo($_GET["vegzos"]); 
        echo json_encode($result);
    }

    else {
        http_response_code(405);
    }
?>