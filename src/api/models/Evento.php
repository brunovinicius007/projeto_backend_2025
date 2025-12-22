<?php
class Evento {
    private $conn;
    private $table_name = "evento";

    public $id;
    public $nome;
    public $descricao;
    public $data_inicio;
    
    // Campos não utilizados no formulário inicial, mas presentes no DB
    public $organizacao_id = 1; // Valor padrão/fixo
    public $local_id = 1; // Valor padrão/fixo
    public $status = 'publicado'; // Valor padrão/fixo
    public $politica_cancelamento = 'Padrão'; // Valor padrão/fixo
    public $data_fim;


    public function __construct($db) {
        $this->conn = $db;
    }

    // Ler todos os eventos
    function read() {
        $query = "SELECT e.id, e.nome, e.descricao, e.data_inicio, e.data_fim, e.local_id, e.politica_cancelamento, l.endereco 
                  FROM " . $this->table_name . " e
                  LEFT JOIN local l ON e.local_id = l.id
                  ORDER BY e.data_inicio DESC";
        $result = $this->conn->query($query);
        return $result;
    }

    // Criar evento
    function create() {
        $query = "INSERT INTO " . $this->table_name . " (nome, descricao, data_inicio, data_fim, organizacao_id, local_id, status, politica_cancelamento) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->data_inicio = htmlspecialchars(strip_tags($this->data_inicio));
        $this->data_fim = htmlspecialchars(strip_tags($this->data_fim));
        $this->local_id = htmlspecialchars(strip_tags($this->local_id));
        $this->politica_cancelamento = htmlspecialchars(strip_tags($this->politica_cancelamento));

        // Mantém valores padrão para campos não presentes no form
        $this->organizacao_id = $this->organizacao_id ? $this->organizacao_id : 1;
        $this->status = $this->status ? $this->status : 'publicado';

        $stmt->bind_param("ssssiiss", $this->nome, $this->descricao, $this->data_inicio, $this->data_fim, $this->organizacao_id, $this->local_id, $this->status, $this->politica_cancelamento);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Atualizar evento
    function update() {
        $query = "UPDATE " . $this->table_name . " SET nome = ?, descricao = ?, data_inicio = ?, data_fim = ?, local_id = ?, politica_cancelamento = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->data_inicio = htmlspecialchars(strip_tags($this->data_inicio));
        $this->data_fim = htmlspecialchars(strip_tags($this->data_fim));
        $this->local_id = htmlspecialchars(strip_tags($this->local_id));
        $this->politica_cancelamento = htmlspecialchars(strip_tags($this->politica_cancelamento));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bind_param("ssssisi", $this->nome, $this->descricao, $this->data_inicio, $this->data_fim, $this->local_id, $this->politica_cancelamento, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Deletar evento
    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bind_param("i", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
