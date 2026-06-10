<?php
// ============================================================
// config/db.php
// Responsabilidad: Establecer la conexión a la base de datos.
// Patrón usado: Clase con método conectar() que retorna un
// objeto PDO listo para hacer consultas seguras.
// ============================================================

class Database
{
    // Datos de conexión al servidor MySQL local (XAMPP)
    private $hostname = "127.0.0.1";
    private $database = "proyecto_asistencia";
    private $username = "root";
    private $password = "";
    private $charset = "utf8mb4"; // Soporta tildes, ñ y emojis

    function conectar()
    {
        try {
            // DSN = Data Source Name: le indica a PDO qué motor, host y BD usar
            $dsn = "mysql:host={$this->hostname};dbname={$this->database};charset={$this->charset}";

            // Opciones de seguridad del objeto PDO:
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones si hay error SQL
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa prepared statements reales (evita SQL injection)
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC        // Retorna filas como array asociativo ['columna' => valor]
            ];

            $pdo = new PDO($dsn, $this->username, $this->password, $options);
            return $pdo; // Se retorna el objeto PDO para usarlo en los demás archivos

        } catch(PDOException $e) {
            // Si la BD no está disponible, detiene todo y muestra el error
            echo '<strong>Error de Conexión:</strong> ' . $e->getMessage();
            echo '<br><strong>Código:</strong> ' . $e->getCode();
            exit;
        }
    }
}
?>