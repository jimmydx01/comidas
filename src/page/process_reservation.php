<?php
// Incluir el archivo de configuración de la base de datos
require 'config.php';

// Definir una variable para el mensaje de agradecimiento
$mensaje = '';

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario y verificar si están presentes
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $package = isset($_POST['package']) ? $_POST['package'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';
     $reservation_datetime = isset($_POST['reservation_datetime']) ? $_POST['reservation_datetime'] : '';

     // Preparar la consulta SQL para insertar los datos
    $sql = "INSERT INTO reservations (name, email, phone, package, message, reservation_datetime) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);


    if ($stmt === false) {
        // Si hay un error en la preparación de la consulta
        die('Error de preparación de la consulta: ' . $conn->error);
    }

    /// Vincular los parámetros y ejecutar la consulta
    $stmt->bind_param("ssssss", $name, $email, $phone, $package, $message, $reservation_datetime);

    if ($stmt->execute()) {
        // Si la inserción fue exitosa, mostrar un mensaje de agradecimiento
        $mensaje = '¡Gracias por su reserva!';
    } else {
        // Si hubo un error en la ejecución de la consulta
        die('Error de ejecución de la consulta: ' . $stmt->error);
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gracias por su reserva</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="container mx-auto p-4">
        <?php if (!empty($mensaje)) : ?>
            <div class="bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md" role="alert">
                <div class="flex">
                    <div class="py-1"><svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 1C4.477 1 0 5.477 0 11s4.477 10 10 10 10-4.477 10-10S15.523 1 10 1zm1 15H9v-2h2v2zm0-4H9V7h2v5z"/></svg></div>
                    <div>
                        <p class="font-bold">¡Reserva exitosa!</p>
                        <p class="text-sm"><?php echo $mensaje; ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="mt-6 text-center">
            <a href="./contacto.html" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Volver al inicio
            </a>
        </div>
    </div>
</body>
</html>
