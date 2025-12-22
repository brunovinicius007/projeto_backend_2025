<?php
class Setor {
    private $conn;
    private $table_name = "setor";

    public $id;
    public $evento_id;
    public $nome;
    public $capacidade;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ler todos os setores ou por evento
    function read() {
        $query = "SELECT s.id, s.evento_id, s.nome, s.capacidade, e.nome as evento_nome 
                  FROM " . $this->table_name . " s
                  LEFT JOIN evento e ON s.evento_id = e.id";
        
        if ($this->evento_id) {
            $query .= " WHERE s.evento_id = ?";
        }
        
        $query .= " ORDER BY s.nome";
        
        $stmt = $this->conn->prepare($query);

        if ($this->evento_id) {
            $stmt->bind_param("i", $this->evento_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }

    // Criar setor
    function create() {
        $query = "INSERT INTO " . $this->table_name . " (evento_id, nome, capacidade) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        $this->evento_id = htmlspecialchars(strip_tags($this->evento_id));
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->capacidade = htmlspecialchars(strip_tags($this->capacidade));

        $stmt->bind_param("isi", $this->evento_id, $this->nome, $this->capacidade);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Atualizar setor
    function update() {
        $query = "UPDATE " . $this->table_name . " SET evento_id = ?, nome = ?, capacidade = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->evento_id = htmlspecialchars(strip_tags($this->evento_id));
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->capacidade = htmlspecialchars(strip_tags($this->capacidade));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bind_param("isii", $this->evento_id, $this->nome, $this->capacidade, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Deletar setor
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
