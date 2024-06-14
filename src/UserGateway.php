<?php

class UserGateway
{
    private $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM user ";

        $stmt = $this->conn->query($sql);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

    public function getById(string $id):array | false {

        $sql = "SELECT * FROM user 
        WHERE iduser = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data;
    }
    public function create(array $data): string{
        $sql = "INSERT INTO `user` (`iduser`, `firstname`, `lastname`, `date_of_birth`, `username`, `password`) VALUES (:iduser, :firstname, :lastname, :date_of_birth, :username, :password); ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":iduser", NULL, PDO::PARAM_NULL);
        $stmt->bindValue(":firstname", $data["firstname"], PDO::PARAM_STR);
        $stmt->bindValue(":lastname", $data["lastname"], PDO::PARAM_STR);
        $stmt->bindValue(":date_of_birth", $data["date_of_birth"], PDO::PARAM_STR);
        $stmt->bindValue(":username", $data["username"], PDO::PARAM_STR);
        $stmt->bindValue(":password", $data["password"], PDO::PARAM_STR);

        $stmt->execute();
        return $this->conn->lastInsertId();

    }
    public function update(string $id, array $data) {

        $fields = [];
        if(!empty($data["firstname"])){
            $fields["firstname"] = [$data["firstname"],PDO::PARAM_STR];
        }
        if(!empty($data["lastname"])){
            $fields["lastname"] = [$data["lastname"],PDO::PARAM_STR];
        }
        if(!empty($data["date_of_birth"])){
            $fields["date_of_birth"] = [$data["date_of_birth"],PDO::PARAM_STR];
        }
        if(!empty($data["username"])){
            $fields["username"] = [$data["username"],PDO::PARAM_STR];
        }
        if(!empty($data["password"])){
            $fields["password"] = [$data["password"],PDO::PARAM_STR];
        }

        if(empty($fields)){
            return 0;
        } else {

            $sets = array_map(function($value){

                return "$value = :$value";

            }, array_keys($fields));

            $sql = "UPDATE user" 
            ." SET " . implode(",", $sets)
            . " WHERE iduser = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            foreach ($fields as $name => $values) {
                $stmt->bindValue(":$name",$values[0],$values[1]);
            }

            $stmt->execute();
            
            return $this->conn->lastInsertId();
        }
    }

    public function delete(string $id): int {

        $sql = "DELETE FROM user 
        WHERE userid = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}