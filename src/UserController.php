<?php

class UserController {



    public function __construct(private UserGateway $gateway) {
        
    }

    public function processRequest(string $method, ?string $id): void {

        if ($id === null) {
            
            if ($method == "GET") {
                
                echo json_encode($this->gateway->getAll());

            } elseif ($method == "POST") {
                
                $data = (array) json_decode(file_get_contents("php://input"), true);
               
                $errors = $this->getValidationErrors($data);

                if (!empty($errors)) {
               
                    $this->respondUnprocessableEntity($errors);
                    return;
                }
                $id = $this->gateway->create($data);

                $this->respondCreated($id);
            } else {
                $this->respondMethodNotAllowed("GET, POST");
            }
        } else {
            
            $user = $this->gateway->getById($id);
            if ($user === false) {
                $this->respondNotFound($id);
                return;
            }
            switch ($method) {
                
                case "GET":
                    echo json_encode($user);
                    break;
                
                case "PATCH":

                    $data = (array) json_decode(file_get_contents("php://input"), true);
               
                    $errors = $this->getValidationErrors($data,false);

                    if (!empty($errors)) {
               
                        $this->respondUnprocessableEntity($errors);
                        return;
                    
                    }
                    $rows = $this->gateway->update($id, $data);
                    echo json_encode(["message" => "User updated"]);
                    
                    break;
                    
                case "DELETE":
                    $rows = $this->gateway->delete($id);
                    echo json_encode(["message"=> "User deleted"]);
                    break;
                default:
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE");
            }
        }
    }
    private function respondMethodNotAllowed(string $allowed_methods): void {
        
        http_response_code(405);
        header("Allow: $allowed_methods");
    }
    private function respondNotFound(string $id) {
        
        http_response_code(404);
        echo json_encode(["message" => "User with that ID $id not found"]);
    }
    private function respondCreated(string $id):void {
        http_response_code(201);
        echo json_encode(["message"=> "User created", "id" => $id]);
    }
    /**
     * Validates datas
     *
     * @param array $data
     * @param boolean $is_new
     * @return array
     */
    private function getValidationErrors(array $data, $is_new = true): array {
        $errors = [];

        if($is_new && empty($data["firstname"])) {
            $errors[] = "firstname is required";
        } else if($is_new && empty($data["lastname"])) {
            $errors[] = "lastname is required";
        } else if($is_new && empty($data["date_of_birth"])) {
            $errors[] = "date of birth is required";
        } else if($is_new && empty($data["username"])) {
            $errors[] = "username is required";
        } else if($is_new && empty($data["lastname"])) {
            $errors[] = "password is required";
        }
        return $errors;
    }
    private function respondUnprocessableEntity(array $errors): void {
        http_response_code(422);
        echo json_encode(["errors" => $errors]);
    }
}


?>