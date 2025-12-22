<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once './config/database.php';
include_once './models/Evento.php';

$database = new Database();
$db = $database->getConnection();

$evento = new Evento($db);

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        if (!empty($_GET["id"])) {
            $evento->id = intval($_GET["id"]);
            // Adicionar lógica para buscar um único evento se necessário
        }
        $result = $evento->read();
        $num = $result->num_rows;

        if ($num > 0) {
            $eventos_arr = array();
            while ($row = $result->fetch_assoc()) {
                extract($row);
                $evento_item = array(
                    "id" => $id,
                    "nome" => $nome,
                    "descricao" => $descricao,
                    "data_inicio" => $data_inicio,
                    "data_fim" => $data_fim,
                    "local_id" => $local_id,
                    "politica_cancelamento" => $politica_cancelamento,
                    "endereco" => $endereco
                );
                array_push($eventos_arr, $evento_item);
            }
            http_response_code(200);
            echo json_encode($eventos_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Nenhum evento encontrado."));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->nome) && !empty($data->data_inicio) && !empty($data->data_fim) && !empty($data->local_id)) {
            $evento->nome = $data->nome;
            $evento->data_inicio = $data->data_inicio;
            $evento->data_fim = $data->data_fim;
            $evento->local_id = $data->local_id;
            $evento->descricao = !empty($data->descricao) ? $data->descricao : '';
            $evento->politica_cancelamento = !empty($data->politica_cancelamento) ? $data->politica_cancelamento : '';

            if ($evento->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Evento criado com sucesso."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível criar o evento."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Dados incompletos."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id) && !empty($data->nome) && !empty($data->data_inicio) && !empty($data->data_fim) && !empty($data->local_id)) {
            $evento->id = $data->id;
            $evento->nome = $data->nome;
            $evento->data_inicio = $data->data_inicio;
            $evento->data_fim = $data->data_fim;
            $evento->local_id = $data->local_id;
            $evento->descricao = !empty($data->descricao) ? $data->descricao : '';
            $evento->politica_cancelamento = !empty($data->politica_cancelamento) ? $data->politica_cancelamento : '';

            if ($evento->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Evento atualizado com sucesso."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível atualizar o evento."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Dados incompletos."));
        }
        break;

    case 'DELETE':
        if (!empty($_GET["id"])) {
            $evento->id = intval($_GET["id"]);

            if ($evento->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Evento deletado com sucesso."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível deletar o evento."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "ID do evento não fornecido."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método não permitido."));
        break;
}
?>
