<?php
class Database {    // private $host = '24.144.89.231';
    // private $user = 'engranetmx';
    // private $password = 'huaweiP20!';
    // private $dbname = 'RapiClean';

    private $host = '24.144.89.231';
    private $user = 'engranetmx';
    private $password = 'huaweiP20!';
    private $dbname = 'RapiClean';
    
    public $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}
?>
