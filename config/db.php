<?php
class Database
{
    private $hostname = "127.0.0.1";
    private $database = "proyecto_asistencia";
    private $username = "root";
    private $password = "";
    private $charset = "utf8mb4"; 

    function conectar()
    {
        try {
            

            $dsn = "mysql:host={$this->hostname};dbname={$this->database};charset={$this->charset}";
           
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //Si ocurre un error SQL, lanza una excepción (PDOException)
                PDO::ATTR_EMULATE_PREPARES => false, // previene inyección SQL
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC //Los resultados de las consultas llegan como array asociativo
            ];

            $pdo = new PDO($dsn, $this->username, $this->password, $options);
            return $pdo;
            
        } catch(PDOException $e) {
            //Mostrar error detallado en desarrollo
            echo '<strong>Error de Conexión:</strong> ' . $e->getMessage();
            echo '<br><strong>Código:</strong> ' . $e->getCode();
            exit;
        }
    }
}
?>