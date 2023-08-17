<?php

interface Db{
    public function get_connection(): mysqli;
    public function close(): void;
    public function query(string $query): bool|mysqli_result;
    public function all(): array;
    public function all_where(string $where): array;
    public function delete(string $id): void;
    public function update(string $upd_id, array $params): void;
    public function insert(array $data): int|string;
    public function count(string $where);
}

class Database implements Db{
    private mysqli $connection;
    private string $table_name;

    public function __construct(string $table_name)
    {
        $this->table_name = $table_name;
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->connection = new mysqli(hostname: "mysql", username: "root", password: "1111", database: "not_x", port: 3406);
        $this->connection->set_charset('utf8mb4');
        $this->connection->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
    }

    /**
     * Outputs all fields in the table as an associative array.
     * @return array
     */
    public function all(): array{
        $res = $this->connection->query("SELECT * FROM $this->table_name");
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Outputs table fields that match the given condition as an associative array.
     * @param string $where Withdrawal Conditions.
     * @return array
     */
    public function all_where(string $where): array{
        $res = $this->connection->query("SELECT * FROM $this->table_name WHERE $where");
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Deletes a table field by the selected ID.
     * @param string $id
     * @return void
     */
    public function delete(string $id): void {
        $stmt = $this->connection->prepare("DELETE FROM $this->table_name WHERE id=?");
        if (!$stmt->execute(array($id))){
            throw new $stmt->error;
        }
        $stmt->close();
    }

    /**
     * Updates the data of a single field.
     * @param string $upd_id Field Id.
     * @param array $params An associative array with new parameters.
     * @return void
     */
    public function update(string $upd_id, array $params): void{
        $upd_str_fields = $this->update_fields_to_str(array_keys($params));
        $values = array_values($params);
        $stmt = $this->connection->prepare("UPDATE $this->table_name SET $upd_str_fields WHERE id=$upd_id");
        if (!$stmt->execute($values)){
            throw new $stmt->error;
        }
        $stmt->close();
    }

    /**
     * Converts an array of fields to be updated into a special string.
     * @param array $fields Fields to update.
     * @return string
     */
    private function update_fields_to_str(array $fields): string{
        $str = "";
        for ($i = 0; $i < count($fields); $i++) {
            if ($i == count($fields)-1){
                $str .= "$fields[$i]=?";
            } else{
                $str .= "$fields[$i]=?,";
            }
        }
        return $str;
    }

    /**
     * Creates a new field in the table.
     * @param array $data An associative array of data to be inserted.
     * @return int|string ID of the new field.
     */
    public function insert(array $data): int|string{
        $fields = $this->insert_fields_to_str(array_keys($data));
        $values = array_values($data);
        $str_values = $this->num_values_to_string($data);
        $stmt = $this->connection->prepare("INSERT INTO $this->table_name $fields VALUES $str_values");
        if ($stmt->execute($values)){
            $insert_id = $this->connection->insert_id;
            $stmt->close();
            return $insert_id;
        } else{
            $stmt->close();
            throw new $stmt->error;
        }
    }

    /**
     * Converts an array of fields to be inserted into a special string.
     * @param array $values Inserted fields.
     * @return string
     */
    private function insert_fields_to_str(array $values): string{
        $val = "(";
        for ($i = 0; $i < count($values); $i++) {
            if ($i == count($values)-1){
                $val .= $values[$i] . ")";
            } else{
                $val .= $values[$i] . ", ";
            }
        }
        return $val;
    }

    /**
     * Converts the number of array elements to a string for insertion. For example, 2 equals (?,?).
     * @param array $values
     * @return string
     */
    private function num_values_to_string(array $values): string{
        $val = "(";
        for ($i = 0; $i < count($values); $i++) {
            if ($i == count($values)-1){
                $val .= "?)";
            } else {
                $val .= "?,";
            }
        }
        return $val;
    }

    public function get_connection(): mysqli{
        return $this->connection;
    }

    public function query(string $query): bool|mysqli_result{
        return $this->connection->query($query);
    }

    public function close(): void{
        $this->connection->close();
    }

    public function __destruct()
    {
        $this->close();
    }

    public function count(string $where): array{
        $res = $this->connection->query("SELECT COUNT(*) FROM $this->table_name WHERE $where");
        return $res->fetch_row();
    }

    /**
     * Outputs data from a selected table that is linked using foreign key.
     * @param string $fk_table_name The name of the table that has a foreign key relationship to the current table.
     * @param string $fk_field_name The name of the foreign key field.
     * @param int $count Number of output results.
     * @param string $where Condition.
     * @return array
     */
    public function all_fk(string $fk_table_name, string $fk_field_name, int $count = 0, string $where = ''): array{
        $limit = '';
        if ($count){
            $limit = "LIMIT $count";
        }
        $res = $this->connection->query("SELECT $fk_table_name.* FROM $this->table_name
                                        JOIN $fk_table_name ON $fk_table_name.$fk_field_name = $this->table_name.id
                                        WHERE $where $limit;");
        return $res->fetch_all(MYSQLI_ASSOC);
    }
}