<?php
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: login.php'); // Redirigir a la página de login si no está autenticado
    exit();
}

// Incluir el archivo de configuración de la base de datos
require 'config.php';

// Obtener las reservaciones desde la base de datos
$sql = "SELECT * FROM reservations ORDER BY created_at DESC";
$result = $conn->query($sql);

// Procesar la solicitud de eliminación si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete_reservation' && isset($_POST['reservation_id'])) {
        $reservation_id = $_POST['reservation_id'];
        
        // Consulta para eliminar la reserva
        $delete_sql = "DELETE FROM reservations WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $reservation_id);
        
        if ($stmt->execute()) {
            // Éxito al eliminar
            echo '<script>alert("Reserva eliminada correctamente.");</script>';
            echo '<meta http-equiv="refresh" content="0">'; // Actualizar la página después de eliminar
        } else {
            // Error al eliminar
            echo '<script>alert("Error al eliminar la reserva.");</script>';
        }
        
        $stmt->close();
    } elseif ($_POST['action'] === 'edit_reservation' && isset($_POST['reservation_id'])) {
        $reservation_id = $_POST['reservation_id'];
        
        // Consulta para obtener los detalles de la reserva específica
        $select_sql = "SELECT * FROM reservations WHERE id = ?";
        $stmt = $conn->prepare($select_sql);
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $result_edit = $stmt->get_result();
        
        if ($result_edit->num_rows == 1) {
            // Mostrar formulario de edición con los datos actuales de la reserva
            $row = $result_edit->fetch_assoc();
            $edit_name = $row['name'];
            $edit_phone = $row['phone'];
            $edit_email = $row['email'];
            $edit_package = $row['package'];
            $edit_message = $row['message'];
            $edit_reservation_datetime = $row['reservation_datetime'];
        }
        
        $stmt->close();
    } elseif ($_POST['action'] === 'update_reservation') {
        // Procesar la actualización de la reserva
        $reservation_id = $_POST['reservation_id'];
        $name = $_POST['edit_name'];
        $phone = $_POST['edit_phone'];
        $email = $_POST['edit_email'];
        $package = $_POST['edit_package'];
        $message = $_POST['edit_message'];
        $reservation_datetime = $_POST['edit_reservation_datetime'];
        
        // Consulta para actualizar la reserva
        $update_sql = "UPDATE reservations SET name = ?, phone = ?, email = ?, package = ?, message = ?, reservation_datetime = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssii", $name, $phone, $email, $package, $message, $reservation_datetime, $reservation_id);
        
        if ($stmt->execute()) {
            // Éxito al actualizar
            echo '<script>alert("Reserva actualizada correctamente.");</script>';
            echo '<meta http-equiv="refresh" content="0">'; // Actualizar la página después de actualizar
        } else {
            // Error al actualizar
            echo '<script>alert("Error al actualizar la reserva.");</script>';
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <div class="relative h-[400px] bg-gradient-to-tr from-indigo-600 via-indigo-700 to-violet-800">
        <div class="flex flex-col gap-4 justify-center items-center w-full h-full px-3 md:px-0">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white">Administracion de Reservas</h1>
            <p class="text-gray-300">Revisa las reservas editalas o eliminalas </p>
        </div>
    </div>
    
    <div class="shadow-lg rounded-lg overflow-hidden mx-3 md:mx-4 my-4">
        <table class="w-full table-fixed">
            <thead>
                <tr class="bg-gray-100">
                    <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">ID</th>
                    <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Nombre</th>
                    <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Teléfono</th>
                    <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Email</th>
                    <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Paquete</th>
                    <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Sugerencia</th>
                    <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Fecha de Reserva</th>
                    <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Acciones</th>
                </tr>
            </thead>
            <?php if ($result->num_rows > 0): ?>
                <tbody class="bg-white">
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="py-4 px-6 border-b border-gray-200"><?php echo $row['id']; ?></td>
                            <td class="py-4 px-6 border-b border-gray-200"><?php echo $row['name']; ?></td>
                            <td class="py-4 px-6 border-b border-gray-200"><?php echo $row['phone']; ?></td>
                            <td class="py-4 px-6 border-b border-gray-200"><?php echo $row['email']; ?></td>
                            <td class="py-4 px-6 border-b border-gray-200"><?php echo $row['package']; ?></td>
                            <td class="py-4 px-6 border-b border-gray-200"><?php echo $row['message']; ?></td>
                            <td class="py-4 px-6 border-b border-gray-200"><?php echo $row['reservation_datetime']; ?></td>
                            <td class="py-4 px-6 border-b border-gray-200">
                                <form method="post">
                                    <input type="hidden" name="reservation_id" value="<?php echo $row['id']; ?>">
                                   

                                    <button type="submit" name="action" value="edit_reservation" class="cursor-pointer font-semibold overflow-hidden relative z-100 border border-green-500 group px-8 py-2">
                                            <span class="relative z-10 text-green-500 group-hover:text-white text-xl duration-500">Editar</span>
                                            <span class="absolute w-full h-full bg-green-500 -left-32 top-0 -rotate-45 group-hover:rotate-0 group-hover:left-0 duration-500"></span>
                                            <span class="absolute w-full h-full bg-green-500 -right-32 top-0 -rotate-45 group-hover:rotate-0 group-hover:right-0 duration-500"></span>
                                            </button>

                                    
                                    <button    type="submit" name="action" value="delete_reservation"  class="flex justify-center items-center gap-2 w-28 h-12 cursor-pointer rounded-md shadow-2xl text-white font-semibold bg-gradient-to-r from-[#fb7185] via-[#e11d48] to-[#be123c] hover:shadow-xl hover:shadow-red-500 hover:scale-105 duration-300 hover:from-[#be123c] hover:to-[#fb7185]"type="submit" name="action" value="edit_reservation" type="submit" name="action" value="delete_reservation">
                                        <svg viewBox="0 0 15 15" class="w-5 fill-white">
                                        <svg
                                        class="w-6 h-6"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        >
                                        <path
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                                            stroke-linejoin="round"
                                            stroke-linecap="round"
                                        ></path>
                                        </svg>
                                        Button
                                    </svg>
                                    </button>

                                   
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            <?php else: ?>
                <tr>
                    <td colspan="8">No hay reservaciones</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
    
   

    <?php if (isset($result_edit) && $result_edit->num_rows == 1): ?>
        <!-- Formulario de edición -->
        <h2 class="text-black font-bold text-lg text-center">Editar Reserva</h2>
<form method="post" class="mt-4 flex flex-col bg-white rounded-lg p-4 shadow-sm max-w-md mx-auto">
    <input type="hidden" name="reservation_id" value="<?php echo $reservation_id; ?>">
    <div class="mt-4">
        <label class="text-black" for="edit_name">Nombre:</label>
        <input type="text" id="edit_name" name="edit_name" value="<?php echo $edit_name; ?>" required class="w-full bg-gray-200 rounded-md border-gray-400 text-black px-2 py-1">
    </div>
    <div class="mt-4">
        <label class="text-black" for="edit_phone">Teléfono:</label>
        <input type="text" id="edit_phone" name="edit_phone" value="<?php echo $edit_phone; ?>" required class="w-full bg-gray-200 rounded-md border-gray-400 text-black px-2 py-1">
    </div>
    <div class="mt-4">
        <label class="text-black" for="edit_email">Email:</label>
        <input type="email" id="edit_email" name="edit_email" value="<?php echo $edit_email; ?>" required class="w-full bg-gray-200 rounded-md border-gray-400 text-black px-2 py-1">
    </div>
    <div class="mt-4">
        <label class="text-black" for="edit_package">Paquete:</label>
        <input type="text" id="edit_package" name="edit_package" value="<?php echo $edit_package; ?>" required class="w-full bg-gray-200 rounded-md border-gray-400 text-black px-2 py-1">
    </div>
    <div class="mt-4">
        <label class="text-black" for="edit_message">Sugerencia:</label>
        <textarea id="edit_message" name="edit_message" class="w-full bg-gray-200 rounded-md border-gray-400 text-black px-2 py-1"><?php echo $edit_message; ?></textarea>
    </div>
    <div class="mt-4">
        <label class="text-black" for="edit_reservation_datetime">Fecha de Reserva:</label>
        <input type="datetime-local" id="edit_reservation_datetime" name="edit_reservation_datetime" value="<?php echo date('Y-m-d\TH:i', strtotime($edit_reservation_datetime)); ?>" required class="w-full bg-gray-200 rounded-md border-gray-400 text-black px-2 py-1">
    </div>
    <div class="mt-4 flex justify-end">
        <button type="submit" name="action" value="update_reservation" class="bg-blue-500 text-white rounded-md px-4 py-1 hover:bg-blue-600 transition-all duration-200">Actualizar Reserva</button>
    </div>
</form>


    <?php endif; ?>
        
        <section class=" flex justify-center">
               <a href="./logout.php"
                    class="relative inline-block overflow-hidden w-32 h-12 bg-black text-white rounded-md text-xl font-bold cursor-pointer group shadow-md">
                    Cerrar Sesión
                    <span class="absolute w-36 h-32 -top-8 -left-2 bg-green-200 rounded-full transform scale-x-0 group-hover:scale-x-100 transition-transform group-hover:duration-500 duration-1000 origin-bottom"></span>
                    <span class="absolute w-36 h-32 -top-8 -left-2 bg-green-400 rounded-full transform scale-x-0 group-hover:scale-x-100 transition-transform group-hover:duration-700 duration-700 origin-bottom"></span>
                    <span class="absolute w-36 h-32 -top-8 -left-2 bg-green-600 rounded-full transform scale-x-0 group-hover:scale-x-100 transition-transform group-hover:duration-1000 duration-500 origin-bottom"></span>
                    <span class="group-hover:opacity-100 group-hover:duration-1000 duration-100 opacity-0 absolute top-2.5 left-6 z-10">Adios!</span>
                    </a>
        </section>   

  

</body>
</html>

<?php
$conn->close();
?>
