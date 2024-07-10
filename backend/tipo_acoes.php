<?php
require_once 'connect.php';

$action = $_POST['action'];

if ($action == 'list') {
    $connection = connect();
    $result = $connection->query("SELECT * FROM tipo_acao");
    $tipoAcoes = [];
    while ($row = $result->fetch_assoc()) {
        $tipoAcoes[] = $row;
    }
    echo json_encode($tipoAcoes);
    $connection->close();
}
?>
