<?php
class Lote {
    private $conn;
    private $table_name = "lote";

    public $id;
    public $setor_id;
    public $preco;
    public $limite; // Mapeado de 'quantidade' no formulário

    // Campos com valores padrão ou não presentes no form principal
    public $periodo_vigencia_ini;
    public $periodo_vigencia_fim;
    public $status = 'ativo'; // Valor Padrão

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ler todos os lotes ou por setor
    function read() {
        $query = "SELECT l.id, l.setor_id, l.preco, l.limite, l.status, l.periodo_vigencia_ini, l.periodo_vigencia_fim, s.nome as setor_nome
                  FROM " . $this->table_name . " l
                  LEFT JOIN setor s ON l.setor_id = s.id";

        if ($this->setor_id) {
            $query .= " WHERE l.setor_id = ?";
        }

        $query .= " ORDER BY l.id";

        $stmt = $this->conn->prepare($query);

        if ($this->setor_id) {
            $stmt->bind_param("i", $this->setor_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }

    // Criar lote
    function create() {
        $query = "INSERT INTO " . $this->table_name . " (setor_id, preco, limite, status, periodo_vigencia_ini, periodo_vigencia_fim) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        $this->setor_id = htmlspecialchars(strip_tags($this->setor_id));
        $this->preco = htmlspecialchars(strip_tags($this->preco));
        $this->limite = htmlspecialchars(strip_tags($this->limite));
        $this->periodo_vigencia_ini = htmlspecialchars(strip_tags($this->periodo_vigencia_ini));
        $this->periodo_vigencia_fim = htmlspecialchars(strip_tags($this->periodo_vigencia_fim));
        $this->status = !empty($this->status) ? htmlspecialchars(strip_tags($this->status)) : 'ativo';

        $stmt->bind_param("idisss", $this->setor_id, $this->preco, $this->limite, $this->status, $this->periodo_vigencia_ini, $this->periodo_vigencia_fim);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Atualizar lote
    function update() {
        $query = "UPDATE " . $this->table_name . " SET setor_id = ?, preco = ?, limite = ?, periodo_vigencia_ini = ?, periodo_vigencia_fim = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->setor_id = htmlspecialchars(strip_tags($this->setor_id));
        $this->preco = htmlspecialchars(strip_tags($this->preco));
        $this->limite = htmlspecialchars(strip_tags($this->limite));
        $this->periodo_vigencia_ini = htmlspecialchars(strip_tags($this->periodo_vigencia_ini));
        $this->periodo_vigencia_fim = htmlspecialchars(strip_tags($this->periodo_vigencia_fim));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bind_param("idissi", $this->setor_id, $this->preco, $this->limite, $this->periodo_vigencia_ini, $this->periodo_vigencia_fim, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Deletar lote
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
