<?php 
    include_once __DIR__ . '/../controller.php';
    header('Content-Type: application/json');

    if($_SERVER["REQUEST_METHOD"] == "DELETE") {
        $controller = new Controller();
        $result = $controller->deleteJelentkezo($_GET['id']);
        echo json_encode($result);
    }

    else {
        http_response_code(405);
    }
?>