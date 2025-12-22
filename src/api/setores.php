<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once './config/database.php';
include_once './models/Setor.php';

$database = new Database();
$db = $database->getConnection();

$setor = new Setor($db);

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        $setor->evento_id = isset($_GET['evento_id']) ? intval($_GET['evento_id']) : null;
        
        $result = $setor->read();
        $num = $result->num_rows;

        if ($num > 0) {
            $setores_arr = array();
            while ($row = $result->fetch_assoc()) {
                extract($row);
                $setor_item = array(
                    "id" => $id,
                    "evento_id" => $evento_id,
                    "evento_nome" => $evento_nome,
                    "nome" => $nome,
                    "capacidade" => $capacidade
                );
                array_push($setores_arr, $setor_item);
            }
            http_response_code(200);
            echo json_encode($setores_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Nenhum setor encontrado."));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->evento_id) && !empty($data->nome) && !empty($data->capacidade)) {
            $setor->evento_id = $data->evento_id;
            $setor->nome = $data->nome;
            $setor->capacidade = $data->capacidade;

            if ($setor->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Setor criado com sucesso."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível criar o setor."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Dados incompletos."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id) && !empty($data->evento_id) && !empty($data->nome) && !empty($data->capacidade)) {
            $setor->id = $data->id;
            $setor->evento_id = $data->evento_id;
            $setor->nome = $data->nome;
            $setor->capacidade = $data->capacidade;

            if ($setor->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Setor atualizado com sucesso."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível atualizar o setor."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Dados incompletos."));
        }
        break;

    case 'DELETE':
        if (!empty($_GET["id"])) {
            $setor->id = intval($_GET["id"]);

            if ($setor->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Setor deletado com sucesso."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível deletar o setor."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "ID do setor não fornecido."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método não permitido."));
        break;
}
?>
