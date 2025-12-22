<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once './config/database.php';
include_once './models/Lote.php';

$database = new Database();
$db = $database->getConnection();

$lote = new Lote($db);

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        $lote->setor_id = isset($_GET['setor_id']) ? intval($_GET['setor_id']) : null;
        
        $result = $lote->read();
        $num = $result->num_rows;

        if ($num > 0) {
            $lotes_arr = array();
            while ($row = $result->fetch_assoc()) {
                extract($row);
                $lote_item = array(
                    "id" => $id,
                    "setor_id" => $setor_id,
                    "setor_nome" => $setor_nome,
                    "preco" => $preco,
                    "limite" => $limite,
                    "status" => $status,
                    "periodo_vigencia_ini" => $periodo_vigencia_ini,
                    "periodo_vigencia_fim" => $periodo_vigencia_fim
                );
                array_push($lotes_arr, $lote_item);
            }
            http_response_code(200);
            echo json_encode($lotes_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Nenhum lote encontrado."));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->setor_id) && !empty($data->preco) && !empty($data->quantidade)) {
            $lote->setor_id = $data->setor_id;
            $lote->preco = $data->preco;
            $lote->limite = $data->quantidade;
            $lote->periodo_vigencia_ini = !empty($data->periodo_vigencia_ini) ? $data->periodo_vigencia_ini : null;
            $lote->periodo_vigencia_fim = !empty($data->periodo_vigencia_fim) ? $data->periodo_vigencia_fim : null;

            if ($lote->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Lote criado com sucesso."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível criar o lote."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Dados incompletos."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id) && !empty($data->setor_id) && !empty($data->preco) && !empty($data->quantidade)) {
            $lote->id = $data->id;
            $lote->setor_id = $data->setor_id;
            $lote->preco = $data->preco;
            $lote->limite = $data->quantidade;
            $lote->periodo_vigencia_ini = !empty($data->periodo_vigencia_ini) ? $data->periodo_vigencia_ini : null;
            $lote->periodo_vigencia_fim = !empty($data->periodo_vigencia_fim) ? $data->periodo_vigencia_fim : null;

            if ($lote->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Lote atualizado com sucesso."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível atualizar o lote."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Dados incompletos para atualização."));
        }
        break;

    case 'DELETE':
        if (!empty($_GET["id"])) {
            $lote->id = intval($_GET["id"]);

            if ($lote->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Lote deletado com sucesso."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível deletar o lote."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "ID do lote não fornecido."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método não permitido."));
        break;
}
?>
