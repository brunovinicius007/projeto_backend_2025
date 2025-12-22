<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once './config/database.php';
include_once './models/Local.php';

$database = new Database();
$db = $database->getConnection();

$local = new Local($db);

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        $result = $local->read();
        $num = $result->num_rows;

        if ($num > 0) {
            $locais_arr = array();
            while ($row = $result->fetch_assoc()) {
                extract($row);
                $local_item = array(
                    "id" => $id,
                    "endereco" => $endereco,
                    "capacidade_total" => $capacidade_total
                );
                array_push($locais_arr, $local_item);
            }
            http_response_code(200);
            echo json_encode($locais_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Nenhum local encontrado."));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->endereco) && !empty($data->capacidade_total)) {
            $local->endereco = $data->endereco;
            $local->capacidade_total = $data->capacidade_total;

            if ($local->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Local criado com sucesso."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível criar o local."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Dados incompletos."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id) && !empty($data->endereco) && !empty($data->capacidade_total)) {
            $local->id = $data->id;
            $local->endereco = $data->endereco;
            $local->capacidade_total = $data->capacidade_total;

            if ($local->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Local atualizado com sucesso."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível atualizar o local."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Dados incompletos para atualização."));
        }
        break;

    case 'DELETE':
        if (!empty($_GET["id"])) {
            $local->id = intval($_GET["id"]);

            if ($local->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Local deletado com sucesso."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível deletar o local."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "ID do local não fornecido."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método não permitido."));
        break;
}
?>
