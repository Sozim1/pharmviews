<?php
require_once 'connect.php';

//function handleError($errno, $errstr, $errfile, $errline) {
//    error_log("Error [$errno]: $errstr in $errfile on line $errline");
//}
//set_error_handler('handleError');

$action = $_POST['action'] ?? '';
//error_log("Action: $action");

if ($action == 'add') {
    $codigo_acao = $_POST['codigo_acao'];
    $data_prevista = $_POST['data'];
    $investimento = $_POST['investimento'];
    $data_cadastro = date('Y-m-d');

    $connection = connect();
    $stmt = $connection->prepare("INSERT INTO acao (codigo_acao, investimento, data_prevista, data_cadastro) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $codigo_acao, $investimento, $data_prevista, $data_cadastro);
    $stmt->execute();
    $stmt->close();
    $connection->close();
} elseif ($action == 'list') {
    $connection = connect();
    $result = $connection->query("SELECT a.id, t.nome_acao AS acao, a.investimento, a.data_prevista FROM acao a JOIN tipo_acao t ON a.codigo_acao = t.codigo_acao");
    $actions = [];
    while ($row = $result->fetch_assoc()) {
        $actions[] = $row;
    }
    echo json_encode($actions);
    $connection->close();
} elseif ($action == 'delete') {
    $id = $_POST['id'];

    $connection = connect();
    $stmt = $connection->prepare("DELETE FROM acao WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $connection->close();
} elseif ($action == 'edit') {
    $id = $_POST['id'];
    $codigo_acao = $_POST['codigo_acao'];
    $data_prevista = $_POST['data'];
    $investimento = $_POST['investimento'];

    $connection = connect();
    $stmt = $connection->prepare("UPDATE acao SET codigo_acao = ?, investimento = ?, data_prevista = ? WHERE id = ?");
    $stmt->bind_param("issi", $codigo_acao, $investimento, $data_prevista, $id);
    $stmt->execute();
    $stmt->close();
    $connection->close();
} elseif ($action == 'get') {
    $id = $_POST['id'];

    $connection = connect();
    $stmt = $connection->prepare("SELECT a.id, a.codigo_acao, t.nome_acao AS acao, a.investimento, a.data_prevista FROM acao a JOIN tipo_acao t ON a.codigo_acao = t.codigo_acao WHERE a.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $action = $result->fetch_assoc();
    echo json_encode([$action]);
    $stmt->close();
    $connection->close();
}
?>