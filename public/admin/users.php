<?php
session_start();
include __DIR__ . '/../config/conexion.php';
include __DIR__ . '/../config/funciones.php';

if (!esAdmin()) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $codUsu = (int) ($_POST['codUsu'] ?? 0);
    $nomUsu = trim($_POST['nomUsu'] ?? '');
    $apeUsu = trim($_POST['apeUsu'] ?? '');
    $corUsu = trim($_POST['corUsu'] ?? '');
    $pasUsu = trim($_POST['pasUsu'] ?? '');
    $codRol = (int) ($_POST['codRol'] ?? 0);
    $codEqui = (int) ($_POST['codEqui'] ?? 0);

    $rolequiUsu = '';
    if ($codRol > 0) {
        $stmtRol = $conector->prepare('SELECT nomRol FROM Rol WHERE codRol = ? LIMIT 1');
        $stmtRol->bind_param('i', $codRol);
        $stmtRol->execute();
        $rolData = $stmtRol->get_result()->fetch_assoc();
        $rolequiUsu = $rolData['nomRol'] ?? '';
    }

    if ($accion === 'crear' && $nomUsu !== '' && $corUsu !== '' && $pasUsu !== '') {
        $hashedPass = password_hash($pasUsu, PASSWORD_DEFAULT);
        $stmt = $conector->prepare('INSERT INTO Usuario (nomUsu, apeUsu, corUsu, pasUsu, codRol) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssi', $nomUsu, $apeUsu, $corUsu, $hashedPass, $codRol);
        if ($stmt->execute()) {
            $nuevoUsuario = $conector->insert_id;
            if ($codEqui > 0) {
                $stmt2 = $conector->prepare('INSERT INTO Usuario_Equipo (codUsu, codEqui, rolequiUsu) VALUES (?, ?, ?)');
                $stmt2->bind_param('iis', $nuevoUsuario, $codEqui, $rolequiUsu);
                $stmt2->execute();
            }
            $mensaje = 'Usuario creado correctamente.';
        } else {
            $error = 'No se pudo crear el usuario.';
        }
    }

    if ($accion === 'editar' && $codUsu > 0) {
        $stmt = $conector->prepare('UPDATE Usuario SET nomUsu = ?, apeUsu = ?, corUsu = ?, codRol = ? WHERE codUsu = ?');
        $stmt->bind_param('sssii', $nomUsu, $apeUsu, $corUsu, $codRol, $codUsu);
        if ($stmt->execute()) {
            $stmt2 = $conector->prepare('SELECT codEqui FROM Usuario_Equipo WHERE codUsu = ? LIMIT 1');
            $stmt2->bind_param('i', $codUsu);
            $stmt2->execute();
            $registro = $stmt2->get_result()->fetch_assoc();

            if ($codEqui > 0) {
                if ($registro) {
                    $stmt3 = $conector->prepare('UPDATE Usuario_Equipo SET codEqui = ?, rolequiUsu = ? WHERE codUsu = ?');
                    $stmt3->bind_param('isi', $codEqui, $rolequiUsu, $codUsu);
                    $stmt3->execute();
                } else {
                    $stmt3 = $conector->prepare('INSERT INTO Usuario_Equipo (codUsu, codEqui, rolequiUsu) VALUES (?, ?, ?)');
                    $stmt3->bind_param('iis', $codUsu, $codEqui, $rolequiUsu);
                    $stmt3->execute();
                }
            } elseif ($registro) {
                $stmt3 = $conector->prepare('DELETE FROM Usuario_Equipo WHERE codUsu = ?');
                $stmt3->bind_param('i', $codUsu);
                $stmt3->execute();
            }
            $mensaje = 'Usuario actualizado correctamente.';
        } else {
            $error = 'No se pudo actualizar el usuario.';
        }
    }

    if ($accion === 'eliminar' && $codUsu > 0) {
        $stmt = $conector->prepare('DELETE FROM Usuario WHERE codUsu = ?');
        $stmt->bind_param('i', $codUsu);
        if ($stmt->execute()) {
            $mensaje = 'Usuario eliminado correctamente.';
        } else {
            $error = 'No se pudo eliminar el usuario.';
        }
    }
}

$roles = [];
$result = $conector->query('SELECT codRol, nomRol FROM Rol ORDER BY nomRol');
if ($result) {
    $roles = $result->fetch_all(MYSQLI_ASSOC);
}

$equipos = [];
$result = $conector->query('SELECT codEqui, nomEqui FROM Equipo ORDER BY nomEqui');
if ($result) {
    $equipos = $result->fetch_all(MYSQLI_ASSOC);
}

$usuarios = [];
$result = $conector->query('SELECT u.codUsu, u.nomUsu, u.apeUsu, u.corUsu, r.nomRol, ue.codEqui, e.nomEqui AS equipo
    FROM Usuario u
    LEFT JOIN Rol r ON r.codRol = u.codRol
    LEFT JOIN Usuario_Equipo ue ON ue.codUsu = u.codUsu
    LEFT JOIN Equipo e ON e.codEqui = ue.codEqui
    ORDER BY u.nomUsu');
if ($result) {
    $usuarios = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - Admin | LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>
    <main class="container-fluid px-3 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3">Gestión de Usuarios</h1>
                <p class="text-secondary">Crea, edita y elimina usuarios del sistema.</p>
            </div>
            <a class="btn btn-danger" href="users.php">Actualizar</a>
        </div>

        <?php if ($mensaje !== '') : ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <?php if ($error !== '') : ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <form method="post" class="row gy-3 gx-3 align-items-end">
                    <input type="hidden" name="accion" value="crear">
                    <div class="col-md-3">
                        <label class="form-label">Nombre</label>
                        <input class="form-control" name="nomUsu" type="text" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Apellido</label>
                        <input class="form-control" name="apeUsu" type="text">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Correo</label>
                        <input class="form-control" name="corUsu" type="email" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Contraseña</label>
                        <input class="form-control" name="pasUsu" type="password" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Rol</label>
                        <select class="form-select" name="codRol" required>
                            <option value="">Seleccionar</option>
                            <?php foreach ($roles as $rol) : ?>
                                <option value="<?php echo $rol['codRol']; ?>"><?php echo htmlspecialchars($rol['nomRol']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Equipo</label>
                        <select class="form-select" name="codEqui">
                            <option value="0">Sin equipo</option>
                            <?php foreach ($equipos as $equipo) : ?>
                                <option value="<?php echo $equipo['codEqui']; ?>"><?php echo htmlspecialchars($equipo['nomEqui']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary w-100" type="submit">Crear</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="usuariosTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Equipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['codUsu']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['nomUsu']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['apeUsu']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['corUsu']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['nomRol']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['equipo'] ?: 'Sin equipo'); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editarUsuario(<?php echo $usuario['codUsu']; ?>, '<?php echo addslashes($usuario['nomUsu']); ?>', '<?php echo addslashes($usuario['apeUsu']); ?>', '<?php echo addslashes($usuario['corUsu']); ?>', <?php echo $usuario['codRol'] ?? 0; ?>, <?php echo $usuario['codEqui'] ?? 0; ?>)">Editar</button>
                                    <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(<?php echo $usuario['codUsu']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal para editar -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="codUsu" id="editCodUsu">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input class="form-control" name="nomUsu" id="editNomUsu" type="text" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Apellido</label>
                            <input class="form-control" name="apeUsu" id="editApeUsu" type="text">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo</label>
                            <input class="form-control" name="corUsu" id="editCorUsu" type="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol</label>
                            <select class="form-select" name="codRol" id="editCodRol" required>
                                <?php foreach ($roles as $rol) : ?>
                                    <option value="<?php echo $rol['codRol']; ?>"><?php echo htmlspecialchars($rol['nomRol']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Equipo</label>
                            <select class="form-select" name="codEqui" id="editCodEqui">
                                <option value="0">Sin equipo</option>
                                <?php foreach ($equipos as $equipo) : ?>
                                    <option value="<?php echo $equipo['codEqui']; ?>"><?php echo htmlspecialchars($equipo['nomEqui']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../sections/footer.php'; ?>
    <script src="../assets/themes/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#usuariosTable').DataTable({
                "language": {
                    "url": "../assets/js/spanish.json"
                }
            });
        });

        function editarUsuario(codUsu, nomUsu, apeUsu, corUsu, codRol, codEqui) {
            document.getElementById('editCodUsu').value = codUsu;
            document.getElementById('editNomUsu').value = nomUsu;
            document.getElementById('editApeUsu').value = apeUsu;
            document.getElementById('editCorUsu').value = corUsu;
            document.getElementById('editCodRol').value = codRol;
            document.getElementById('editCodEqui').value = codEqui || 0;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        function eliminarUsuario(codUsu) {
            if (confirm('¿Estás seguro de eliminar este usuario?')) {
                const form = document.createElement('form');
                form.method = 'post';
                form.innerHTML = '<input name="accion" value="eliminar"><input name="codUsu" value="' + codUsu + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>