<?php

class TaskManager extends Todo
{
    public function markAsComplete($id)
    {
        $query = "UPDATE " . $this->table_name . " SET status = 'completed' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    public function getCompletedTasks()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'completed'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE todos SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
    
        $status = $status ?: 'pending';
    
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
    
        return $stmt->execute();
    }
}
