<?php
include 'conexion.php';

// Conectar a la base de datos
conectar();

// Mensajes de éxito o error
$mensaje = "";

// Insertar nueva tarea
if (isset($_POST['agregar_tarea'])) {
    $nombre_tarea = $_POST['nombre'];
    $archivo = null;

    // Manejar archivo subido
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'documentos/'; // Carpeta para guardar archivos PDF

        // Verificar si la carpeta 'documentos' existe, si no, crearla
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Crear la carpeta si no existe
        }

        $archivo = $upload_dir . basename($_FILES['archivo']['name']);
        move_uploaded_file($_FILES['archivo']['tmp_name'], $archivo);
        $mensaje = 'Archivo subido correctamente.';
    }

    // Insertar tarea en la base de datos
    $sql = "INSERT INTO tareas (nombre, archivo) VALUES ('$nombre_tarea', '$archivo')";
    if ($con->query($sql)) {
        $mensaje = 'Tarea agregada correctamente.';
    } else {
        $mensaje = 'Error al agregar la tarea.';
    }
}

// Completar tarea
if (isset($_POST['completar_tarea'])) {
    $id_tarea = $_POST['id'];
    if ($con->query("UPDATE tareas SET estado='completada', fecha_completada=NOW() WHERE id=$id_tarea")) {
        $mensaje = 'Tarea completada correctamente.';
    } else {
        $mensaje = 'Error al completar la tarea.';
    }
}

// Eliminar tarea (borrado lógico)
if (isset($_POST['eliminar_tarea'])) {
    $id_tarea = $_POST['id'];
    if ($con->query("UPDATE tareas SET eliminado=1 WHERE id=$id_tarea")) {
        $mensaje = 'Tarea eliminada correctamente.';
    } else {
        $mensaje = 'Error al eliminar la tarea.';
    }
}

// Consultar tareas pendientes
$sql_pendientes = "SELECT * FROM tareas WHERE estado='pendiente' AND eliminado=0 ORDER BY fecha_creacion DESC";
$result_pendientes = $con->query($sql_pendientes);

// Consultar tareas completadas
$sql_completadas = "SELECT nombre, fecha_completada FROM tareas WHERE estado='completada' AND eliminado=0 ORDER BY fecha_completada DESC";
$result_completadas = $con->query($sql_completadas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tareas</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="js/script.js" defer></script>
</head>
<body class="container">
    <h3>Lista de Tareas</h3>
    
    <!-- Formulario para agregar tarea -->
    <form action="index.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <input type="text" name="nombre" class="form-control" placeholder="Nueva tarea" required>
        </div>
        <div class="form-group">
            <input type="file" name="archivo" class="form-control-file">
        </div>
        <button type="submit" name="agregar_tarea" class="btn btn-success">Agregar</button>
    </form>

    <!-- Mensaje de alerta después de agregar, completar o eliminar tarea -->
    <script>
        <?php if (!empty($mensaje)): ?>
            alert("<?php echo $mensaje; ?>");
        <?php endif; ?>
    </script>

    <!-- Listado de tareas pendientes -->
    <h3>Tareas Pendientes</h3>
    <?php while ($row = $result_pendientes->fetch_assoc()) { ?>
        <div class="d-flex justify-content-between align-items-center border p-2 mb-2 tarea">
            <div><?= htmlspecialchars($row['nombre']); ?></div>
            <div>
                <?php if ($row['archivo']) { ?>
                    <!-- Botón para ver PDF -->
                    <a href="<?= htmlspecialchars($row['archivo']); ?>" class="btn btn-info btn-sm" target="_blank">Ver PDF</a>
                <?php } ?>
                <!-- Botón para completar tarea -->
                <form action="index.php" method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                    <button type="submit" name="completar_tarea" class="btn btn-primary btn-sm">Completar</button>
                </form>
                <!-- Botón para eliminar tarea -->
                <form action="index.php" method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                    <button type="submit" name="eliminar_tarea" class="btn btn-danger btn-sm">Eliminar</button>
                </form>
            </div>
        </div>
    <?php } ?>

    <!-- Listado de tareas completadas -->
    <h3>Tareas Completadas</h3>
    <?php while ($row = $result_completadas->fetch_assoc()) { ?>
        <div class="border p-2 mb-2">
            <?= htmlspecialchars($row['nombre']); ?> - Completado el: <?= $row['fecha_completada']; ?>
        </div>
    <?php } ?>

</body>
</html>

<?php
// Desconectar
desconectar();
?>
