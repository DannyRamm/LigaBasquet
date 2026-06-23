<?php
session_start();
include __DIR__ . '/../config/conexion.php';
include __DIR__ . '/../config/funciones.php';

if (!esAdmin()) {
    header('Location: ../login.php');
    exit;
}

function obtenerCodRolJugador($conector)
{
    $stmt = $conector->prepare('SELECT codRol FROM Rol WHERE LOWER(nomRol) LIKE ? LIMIT 1');
    $rolBusqueda = '%jugador%';
    $stmt->bind_param('s', $rolBusqueda);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();

    if ($resultado) {
        return (int) $resultado['codRol'];
    }

    $stmt = $conector->prepare('INSERT INTO Rol (nomRol) VALUES (?)');
    $rolNombre = 'Jugador';
    $stmt->bind_param('s', $rolNombre);
    $stmt->execute();

    return $conector->insert_id;
}

$accion = $_POST['accion'] ?? ($_GET['accion'] ?? '');

// Si es una llamada AJAX, devuelve JSON
if ($accion) {
    header('Content-Type: application/json');
    $respuesta = ['exito' => false, 'mensaje' => ''];

    if ($accion === 'crear' || $accion === 'editar' || $accion === 'eliminar') {
        $codJug = (int) ($_POST['codJug'] ?? 0);
        $nomJug = trim($_POST['nomJug'] ?? '');
        $edaJug = (int) ($_POST['edaJug'] ?? 0);
        $altJug = trim($_POST['altJug'] ?? '');
        $codEqui = (int) ($_POST['codEqui'] ?? 0);
        $tipoContrato = trim($_POST['tipoContrato'] ?? 'estandar');

        // CREAR
        if ($accion === 'crear' && $nomJug !== '') {
            if ($altJug === '') {
                $altJug = null;
            } else {
                $altJug = str_replace(',', '.', $altJug);
                if (!is_numeric($altJug)) {
                    $altJug = null;
                }
            }

            $stmt = $conector->prepare('INSERT INTO Jugador (nomJug, edaJug, altJug) VALUES (?, ?, ?)');
            $stmt->bind_param('sid', $nomJug, $edaJug, $altJug);

            if ($stmt->execute()) {
                $jugId = $conector->insert_id;
                $usuarioCreado = false;
                $credenciales = '';

                if ($jugId > 0) {
                    $emailJugador = 'jugador' . $jugId . '@leaguedan.local';
                    $passTemporal = 'LeagueDan' . $jugId;
                    $hashPass = password_hash($passTemporal, PASSWORD_DEFAULT);
                    $codRolJugador = obtenerCodRolJugador($conector);

                    $stmtUsr = $conector->prepare('
                        INSERT INTO Usuario (nomUsu, apeUsu, corUsu, pasUsu, codRol)
                        VALUES (?, ?, ?, ?, ?)
                    ');
                    $apeJug = '';
                    $stmtUsr->bind_param('ssssi', $nomJug, $apeJug, $emailJugador, $hashPass, $codRolJugador);
                    if ($stmtUsr->execute()) {
                        $usuarioCreado = true;
                        $usuarioId = $conector->insert_id;
                        $credenciales = "Usuario: $emailJugador / Contraseña temporal: $passTemporal";

                        if ($codEqui > 0) {
                            $stmt3 = $conector->prepare('
                                INSERT INTO Usuario_Equipo (codUsu, codEqui, rolequiUsu)
                                VALUES (?, ?, ?)
                            ');
                            $rolequi = 'Jugador';
                            $stmt3->bind_param('iis', $usuarioId, $codEqui, $rolequi);
                            $stmt3->execute();
                        }
                    }
                }

                if ($codEqui > 0) {
                    $stmt2 = $conector->prepare('
                        INSERT INTO Jugador_Equipo (codJug, codEqui, feciniJugEqui, tipoContrato)
                        VALUES (?, ?, CURDATE(), ?)
                    ');
                    $stmt2->bind_param('iis', $jugId, $codEqui, $tipoContrato);
                    $stmt2->execute();
                }

                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Jugador creado correctamente.';
                $respuesta['credenciales'] = $usuarioCreado ? $credenciales : '';
                if ($usuarioCreado) {
                    $respuesta['usuario'] = $emailJugador;
                    $respuesta['contrasena'] = $passTemporal;
                }
            } else {
                $respuesta['mensaje'] = 'No se pudo crear el jugador.';
            }
        }

        // EDITAR
        if ($accion === 'editar' && $codJug > 0) {
            if ($altJug === '') {
                $altJug = null;
            } else {
                $altJug = str_replace(',', '.', $altJug);
                if (!is_numeric($altJug)) {
                    $altJug = null;
                }
            }

            $stmt = $conector->prepare('
                UPDATE Jugador
                SET nomJug = ?, edaJug = ?, altJug = ?
                WHERE codJug = ?
            ');
            $stmt->bind_param('sidi', $nomJug, $edaJug, $altJug, $codJug);

            if ($stmt->execute()) {
                if ($codEqui > 0) {
                    $stmt2 = $conector->prepare('
                        SELECT codJugEqui
                        FROM Jugador_Equipo
                        WHERE codJug = ?
                        LIMIT 1
                    ');
                    $stmt2->bind_param('i', $codJug);
                    $stmt2->execute();
                    $registro = $stmt2->get_result()->fetch_assoc();

                    if ($registro) {
                        $stmt3 = $conector->prepare('
                            UPDATE Jugador_Equipo
                            SET codEqui = ?, tipoContrato = ?
                            WHERE codJugEqui = ?
                        ');
                        $stmt3->bind_param('isi', $codEqui, $tipoContrato, $registro['codJugEqui']);
                        $stmt3->execute();
                    } else {
                        $stmt3 = $conector->prepare('
                            INSERT INTO Jugador_Equipo (codJug, codEqui, feciniJugEqui, tipoContrato)
                            VALUES (?, ?, CURDATE(), ?)
                        ');
                        $stmt3->bind_param('iis', $codJug, $codEqui, $tipoContrato);
                        $stmt3->execute();
                    }
                }

                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Jugador actualizado correctamente.';
            } else {
                $respuesta['mensaje'] = 'No se pudo actualizar el jugador.';
            }
        }

        // ELIMINAR
        if ($accion === 'eliminar' && $codJug > 0) {
            $conector->begin_transaction();
            $correoJugador = 'jugador' . $codJug . '@leaguedan.local';

            $stmtRel = $conector->prepare('DELETE FROM Jugador_Equipo WHERE codJug = ?');
            $stmtRel->bind_param('i', $codJug);
            $ok = $stmtRel->execute();

            $stmtUsuEqu = $conector->prepare('DELETE ue FROM Usuario_Equipo ue JOIN Usuario u ON u.codUsu = ue.codUsu WHERE u.corUsu = ?');
            $stmtUsuEqu->bind_param('s', $correoJugador);
            $ok = $ok && $stmtUsuEqu->execute();

            $stmtUsu = $conector->prepare('DELETE FROM Usuario WHERE corUsu = ?');
            $stmtUsu->bind_param('s', $correoJugador);
            $ok = $ok && $stmtUsu->execute();

            $stmt = $conector->prepare('DELETE FROM Jugador WHERE codJug = ?');
            $stmt->bind_param('i', $codJug);
            $ok = $ok && $stmt->execute();

            if ($ok) {
                $conector->commit();
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Jugador eliminado correctamente.';
            } else {
                $conector->rollback();
                $respuesta['mensaje'] = 'No se pudo eliminar el jugador.';
            }
        }

        echo json_encode($respuesta);
        exit;
    }

    // OBTENER TODOS
    if ($accion === 'obtener') {
        $jugadores = [];

        $result = $conector->query('
            SELECT
                j.codJug,
                j.nomJug,
                j.edaJug,
                j.altJug,
                e.nomEqui,
                je.tipoContrato
            FROM Jugador j
            LEFT JOIN Jugador_Equipo je ON je.codJug = j.codJug
            LEFT JOIN Equipo e ON e.codEqui = je.codEqui
            ORDER BY j.nomJug
        ');

        if ($result) {
            $jugadores = $result->fetch_all(MYSQLI_ASSOC);
        }

        echo json_encode($jugadores);
        exit;
    }

    // OBTENER UNO
    if ($accion === 'obtenerUno') {
        $codJug = (int) $_GET['codJug'];

        $stmt = $conector->prepare("\n            SELECT\n                j.codJug,\n                j.nomJug,\n                j.edaJug,\n                j.altJug,\n                je.codEqui,\n                je.tipoContrato,\n                u.corUsu AS userEmail\n            FROM Jugador j\n            LEFT JOIN Jugador_Equipo je ON je.codJug = j.codJug\n            LEFT JOIN Usuario u ON u.corUsu = CONCAT('jugador', j.codJug, '@leaguedan.local')\n            WHERE j.codJug = ?\n        ");

        $stmt->bind_param('i', $codJug);
        $stmt->execute();

        $jugador = $stmt->get_result()->fetch_assoc();

        echo json_encode($jugador ?: []);
        exit;
    }

    // OBTENER EQUIPOS
    if ($accion === 'obtenerEquipos') {
        $equipos = [];

        $result = $conector->query('
            SELECT codEqui, nomEqui
            FROM Equipo
            ORDER BY nomEqui
        ');

        if ($result) {
            $equipos = $result->fetch_all(MYSQLI_ASSOC);
        }

        echo json_encode($equipos);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Jugadores - Admin | LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/themes/datatables/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }
        .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }
        .player-badge {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
            color: white;
        }
        .badge-info-stat {
            display: inline-block;
            background: #f8f9fa;
            padding: 0.25rem 0.6rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }
    </style>
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>
    
    <main class="container-fluid px-4 py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="h2 fw-bold mb-1"><i class="fas fa-users me-2"></i>Gestión de Jugadores</h1>
                <p class="text-muted">Administra todos los jugadores y sus asignaciones</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalJugador">
                    <i class="fas fa-plus me-2"></i>Nuevo Jugador
                </button>
            </div>
        </div>

        <!-- Alert -->
        <div id="alertContainer"></div>

        <!-- Tabla -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="jugadoresTable" class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="25%"><i class="fas fa-user me-2"></i>Jugador</th>
                                <th width="12%"><i class="fas fa-birthday-cake me-2"></i>Edad</th>
                                <th width="12%"><i class="fas fa-ruler-vertical me-2"></i>Altura</th>
                                <th width="25%"><i class="fas fa-shield me-2"></i>Equipo</th>
                                <th width="26%" class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Crear/Editar -->
    <div class="modal fade" id="modalJugador" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Nuevo Jugador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formJugador">
                        <input type="hidden" name="accion" id="accion" value="crear">
                        <input type="hidden" name="codJug" id="codJug" value="">
                        
                        <div class="mb-3">
                            <label class="form-label fw-500">Nombre del Jugador</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" name="nomJug" id="nomJug" placeholder="Ej: LeBron James..." required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-500">Edad</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
                                    <input type="number" class="form-control" name="edaJug" id="edaJug" placeholder="25" min="14">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-500">Altura (m)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-ruler-vertical"></i></span>
                                    <input type="text" class="form-control" name="altJug" id="altJug" placeholder="2.03">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-500">Equipo</label>
                            <select class="form-select" name="codEqui" id="codEqui">
                                <option value="0">Sin equipo</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-500">Tipo de Contrato</label>
                            <select class="form-select" name="tipoContrato" id="tipoContrato">
                                <option value="estandar">Estándar</option>
                                <option value="two-way">Two-Way</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-500">Cuenta generada</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                                <input type="text" class="form-control" id="usuarioJugador" placeholder="Usuario generado" readonly>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="text" class="form-control" id="contrasenaJugador" placeholder="Contraseña temporal" readonly>
                            </div>
                            <small class="text-muted">En edición se muestra la cuenta automática. La contraseña es temporal.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardar">Guardar Jugador</button>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../sections/footer.php'; ?>

    <script src="../assets/themes/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/themes/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/js/sweetalert2.all.min.js"></script>
    
    <script>
        let dataTable;
        const urlBase = 'players.php';

        // Inicializar
        $(document).ready(function() {
            cargarEquipos();
            cargarJugadores();
            inicializarEventos();
        });

        // Cargar equipos en select
        function cargarEquipos() {
            $.ajax({
                url: urlBase,
                method: 'GET',
                data: { accion: 'obtenerEquipos' },
                dataType: 'json',
                success: function(data) {
                    let html = '<option value="0">Sin equipo</option>';
                    data.forEach(equipo => {
                        html += `<option value="${equipo.codEqui}">${equipo.nomEqui}</option>`;
                    });
                    $('#codEqui').html(html);
                }
            });
        }

        // Cargar tabla
        function cargarJugadores() {
            $.ajax({
                url: urlBase,
                method: 'GET',
                data: { accion: 'obtener' },
                dataType: 'json',
                success: function(data) {
                    let html = '';
                    data.forEach(jugador => {
                        const inicial = jugador.nomJug.charAt(0).toUpperCase();
                        const colorClase = ['bg-primary', 'bg-success', 'bg-danger', 'bg-warning', 'bg-info'][
                            Math.floor(Math.random() * 5)
                        ];
                        
                        html += `
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="player-badge ${colorClase}">
                                            ${inicial}
                                        </div>
                                        <div class="ms-3">
                                            <strong>${jugador.nomJug}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-info-stat">${jugador.edaJug || '-'} años</span>
                                </td>
                                <td>
                                    <span class="badge-info-stat">${jugador.altJug || '-'} m</span>
                                </td>
                                <td>
                                    ${jugador.nomEqui ? 
                                        `<span class="badge bg-light text-dark">${jugador.nomEqui}</span>` : 
                                        `<span class="badge bg-secondary">Sin equipo</span>`
                                    }
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-icon btn-outline-primary me-2" onclick="editarJugador(${jugador.codJug})" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-icon btn-outline-danger" onclick="eliminarJugador(${jugador.codJug})" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    let tabla = document.querySelector('#jugadoresTable tbody');
                    tabla.innerHTML = html;
                    
                    if (dataTable) {
                        dataTable.destroy();
                    }
                    dataTable = $('#jugadoresTable').DataTable({
                        "language": { "url": "../assets/js/spanish.json" },
                        "paging": true,
                        "pageLength": 15,
                        "responsive": true
                    });
                }
            });
        }

        // Eventos
        function inicializarEventos() {
            $('#modalJugador').on('show.bs.modal', function() {
                if ($(this).data('editar')) {
                    $('#accion').val('editar');
                    $('#modalTitle').text('Editar Jugador');
                } else {
                    limpiarFormulario();
                    $('#accion').val('crear');
                    $('#modalTitle').text('Nuevo Jugador');
                }
            });

            $('#btnGuardar').off('click').on('click', function() {
                guardarJugador();
            });

            $('#formJugador').on('keypress', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    guardarJugador();
                }
            });
        }

        // Guardar
        function guardarJugador() {
            const accion = $('#accion').val();
            const nomJug = $('#nomJug').val();
            
            if (!nomJug) {
                mostrarAlerta('error', 'El nombre del jugador es obligatorio');
                return;
            }

            const datos = {
                accion: accion,
                nomJug: nomJug,
                edaJug: $('#edaJug').val(),
                altJug: $('#altJug').val(),
                codEqui: $('#codEqui').val(),
                tipoContrato: $('#tipoContrato').val(),
                codJug: $('#codJug').val()
            };

            $.ajax({
                url: urlBase,
                method: 'POST',
                data: datos,
                dataType: 'json',
                beforeSend: function() {
                    $('#btnGuardar').prop('disabled', true).text('Guardando...');
                },
                success: function(respuesta) {
                    if (respuesta.exito) {
                        let mensaje = respuesta.mensaje || 'Jugador guardado correctamente.';
                        if (respuesta.credenciales) {
                            copiarAlPortapapeles(respuesta.credenciales);
                            mensaje += ' Credenciales copiadas al portapapeles.';
                        }
                        mostrarAlerta('success', mensaje);
                        const modalEl = document.getElementById('modalJugador');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modalInstance.hide();
                        cargarJugadores();
                    } else {
                        mostrarAlerta('error', respuesta.mensaje || 'No se pudo guardar el jugador.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error, xhr.responseText);
                    let respuesta = 'Error guardando jugador.';
                    if (xhr.responseText) {
                        const msg = xhr.responseText.trim().replace(/\s+/g, ' ');
                        respuesta += ' ' + msg.substring(0, 250);
                    }
                    mostrarAlerta('error', respuesta);
                },
                complete: function() {
                    $('#btnGuardar').prop('disabled', false).text('Guardar Jugador');
                }
            });
        }

        // Editar
        function editarJugador(codJug) {
            $.ajax({
                url: urlBase,
                method: 'GET',
                data: { accion: 'obtenerUno', codJug: codJug },
                dataType: 'json',
                success: function(jugador) {
                    $('#codJug').val(jugador.codJug);
                    $('#nomJug').val(jugador.nomJug);
                    $('#edaJug').val(jugador.edaJug);
                    $('#altJug').val(jugador.altJug);
                    $('#codEqui').val(jugador.codEqui || 0);
                    $('#tipoContrato').val(jugador.tipoContrato || 'estandar');
                    $('#usuarioJugador').val(jugador.userEmail || `jugador${jugador.codJug}@leaguedan.local`);
                    $('#contrasenaJugador').val(`LeagueDan${jugador.codJug}`);

                    let modal = document.getElementById('modalJugador');
                    modal.setAttribute('data-editar', 'true');

                    new bootstrap.Modal(modal).show();
                }
            });
        }

        // Eliminar
        function eliminarJugador(codJug) {
            Swal.fire({
                title: '¿Eliminar jugador?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: urlBase,
                        method: 'POST',
                        data: { accion: 'eliminar', codJug: codJug },
                        dataType: 'json',
                        success: function(respuesta) {
                            if (respuesta.exito) {
                                mostrarAlerta('success', respuesta.mensaje);
                                cargarJugadores();
                            } else {
                                mostrarAlerta('error', respuesta.mensaje);
                            }
                        }
                    });
                }
            });
        }

        // Limpiar formulario
        function limpiarFormulario() {
            $('#formJugador')[0].reset();
            $('#codJug').val('');
            $('#usuarioJugador').val('');
            $('#contrasenaJugador').val('');
            document.getElementById('modalJugador').removeAttribute('data-editar');
        }

        // Mostrar alerta
        function copiarAlPortapapeles(texto) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(texto).catch(function() {
                    console.warn('No fue posible copiar al portapapeles automáticamente.');
                });
            }
        }

        function mostrarAlerta(tipo, mensaje) {
            const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
            const icono = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            const alert = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas ${icono} me-2"></i>
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            const container = document.getElementById('alertContainer');
            container.innerHTML = alert;
            
            setTimeout(() => {
                const alertElement = container.querySelector('.alert');
                if (alertElement) {
                    new bootstrap.Alert(alertElement).close();
                }
            }, 5000);
        }
    </script>
</body>
</html>