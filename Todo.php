<?php

class Todo
{
    public $conn;
    public $table_name = "todos";

    private $id;
    private $title;
    private $description;
    private $status;
    private $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function setTitle($title)
    {
        $this->title = htmlspecialchars(strip_tags($title));
    }

    public function setDescription($description)
    {
        $this->description = htmlspecialchars(strip_tags($description));
    }

    // CRUD Operations
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                 (title, description, status, created_at) 
                 VALUES (:title, :description, :status, :created_at)";

        $stmt = $this->conn->prepare($query);

        // Clean and bind data
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->status = "pending";
        $this->created_at = date('Y-m-d H:i:s');

        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":created_at", $this->created_at);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                 SET title = :title, description = :description, status = :status 
                 WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}