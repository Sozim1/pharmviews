<?php
require_once 'connect.php';

//function handleError($errno, $errstr, $errfile, $errline) {
//    error_log("Error [$errno]: $errstr in $errfile on line $errline");
//}
//set_error_handler('handleError');

$action = $_POST['action'] ?? '';
//error_log("Action: $action");

if ($action == 'add') {
    $acao = $_POST['acao'] ?? '';
    $data = $_POST['data'] ?? '';
    $investimento = $_POST['investimento'] ?? 0;
    error_log("Add Action: $acao, $data, $investimento");

    try {
        $connection = connect();
        $stmt = $connection->prepare("INSERT INTO acoes (acao, data, investimento) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $connection->error);
        }
        $stmt->bind_param("ssd", $acao, $data, $investimento);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $stmt->close();
        $connection->close();
    } catch (Exception $e) {
        error_log("Exception: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        http_response_code(500);
        exit;
    }
} elseif ($action == 'list') {
    $connection = connect();
    $result = $connection->query("SELECT * FROM acoes");
    $actions = [];
    while ($row = $result->fetch_assoc()) {
        $actions[] = $row;
    }
    echo json_encode($actions);
    $connection->close();
} elseif ($action == 'delete') {
    $id = $_POST['id'];

    $connection = connect();
    $stmt = $connection->prepare("DELETE FROM acoes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $connection->close();
} elseif ($action == 'edit') {
    $id = $_POST['id'];
    $acao = $_POST['acao'];
    $data = $_POST['data'];
    $investimento = $_POST['investimento'];

    $connection = connect();
    $stmt = $connection->prepare("UPDATE acoes SET acao = ?, data = ?, investimento = ? WHERE id = ?");
    $stmt->bind_param("ssdi", $acao, $data, $investimento, $id);
    $stmt->execute();
    $stmt->close();
    $connection->close();
} elseif ($action == 'get') {
    $id = $_POST['id'];

    $connection = connect();
    $stmt = $connection->prepare("SELECT * FROM acoes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $action = $result->fetch_assoc();
    echo json_encode([$action]);
    $stmt->close();
    $connection->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ação não reconhecida']);
    http_response_code(400);
    exit;
}
?>
