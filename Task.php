<?php

class Task {
    private $conn;
    private $table = 'tasks';

    public $id;
    public $project_id;
    public $title;
    public $description;
    public $status;
    public $assigned_to;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Создать задачу
    public function create() {
        $query = "INSERT INTO {$this->table} (project_id, title, description, status, assigned_to) 
                  VALUES (:project_id, :title, :description, :status, :assigned_to)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':project_id', $this->project_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':assigned_to', $this->assigned_to);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Обновить статус задачи
    public function updateStatus() {
        $query = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Удалить задачу
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Получить задачу по ID
    public function read() {
        $query = "SELECT id, project_id, title, description, status, assigned_to 
                  FROM {$this->table} 
                  WHERE id = :id 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Получить все задачи по project_id
    public function readByProject() {
        $query = "SELECT id, project_id, title, description, status, assigned_to 
                  FROM {$this->table} 
                  WHERE project_id = :project_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':project_id', $this->project_id);
        $stmt->execute();

        return $stmt;
    }
}