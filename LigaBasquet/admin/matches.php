
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

include __DIR__ . '/../config/conexion.php';
include __DIR__ . '/../config/funciones.php';

/*
|--------------------------------------------------------------------------
| VALIDAR SESIÓN
|--------------------------------------------------------------------------
*/
if (!function_exists('esAdmin')) {
    die('Error: función esAdmin() no existe en funciones.php');
}

if (!isset($conector)) {
    die('Error: conexión $conector no existe en conexion.php');
}

if (!esAdmin()) {
    header('Location: ../login.php');
    exit;
}

$accion = $_POST['accion'] ?? ($_GET['accion'] ?? '');

/*
|--------------------------------------------------------------------------
| PETICIONES AJAX
|--------------------------------------------------------------------------
*/
if ($accion) {

    header('Content-Type: application/json; charset=utf-8');

    $respuesta = [
        'exito' => false,
        'mensaje' => ''
    ];

    $codMat = (int) ($_POST['codMat'] ?? $_GET['codMat'] ?? 0);

    /*
    |--------------------------------------------------------------------------
    | CREAR / EDITAR / ELIMINAR
    |--------------------------------------------------------------------------
    */
    if (in_array($accion, ['crear', 'editar', 'eliminar'])) {

        $codTem = (int) ($_POST['codTem'] ?? 0);
        $codequilocMat = (int) ($_POST['codequilocMat'] ?? 0);
        $codequivisMat = (int) ($_POST['codequivisMat'] ?? 0);
        $codCan = (int) ($_POST['codCan'] ?? 0);
        $fecMat = $_POST['fecMat'] ?? null;
        $estMat = trim($_POST['estMat'] ?? 'pendiente');

        $punlocMat = ($_POST['punlocMat'] !== '') ? (int) $_POST['punlocMat'] : null;
        $punvisMat = ($_POST['punvisMat'] !== '') ? (int) $_POST['punvisMat'] : null;

        // ======================
        // CREAR
        // ======================
        if ($accion === 'crear') {

            $stmt = $conector->prepare("
                INSERT INTO Partido (
                    codTem,
                    codequilocMat,
                    codequivisMat,
                    codCan,
                    fecMat,
                    estMat,
                    punlocMat,
                    punvisMat
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            if (!$stmt) {
                die(json_encode([
                    'exito' => false,
                    'mensaje' => 'Error SQL CREAR: ' . $conector->error
                ]));
            }

            $stmt->bind_param(
                "iiiissii",
                $codTem,
                $codequilocMat,
                $codequivisMat,
                $codCan,
                $fecMat,
                $estMat,
                $punlocMat,
                $punvisMat
            );

            if ($stmt->execute()) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Partido creado correctamente.';
            } else {
                $respuesta['mensaje'] = 'Error al crear partido: ' . $stmt->error;
            }
        }

        // ======================
        // EDITAR
        // ======================
        if ($accion === 'editar' && $codMat > 0) {

            $stmt = $conector->prepare("
                UPDATE Partido
                SET
                    codTem = ?,
                    codequilocMat = ?,
                    codequivisMat = ?,
                    codCan = ?,
                    fecMat = ?,
                    estMat = ?,
                    punlocMat = ?,
                    punvisMat = ?
                WHERE codMat = ?
            ");

            if (!$stmt) {
                die(json_encode([
                    'exito' => false,
                    'mensaje' => 'Error SQL EDITAR: ' . $conector->error
                ]));
            }

            $stmt->bind_param(
                "iiiissiii",
                $codTem,
                $codequilocMat,
                $codequivisMat,
                $codCan,
                $fecMat,
                $estMat,
                $punlocMat,
                $punvisMat,
                $codMat
            );

            if ($stmt->execute()) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Partido actualizado correctamente.';
            } else {
                $respuesta['mensaje'] = 'Error al actualizar partido: ' . $stmt->error;
            }
        }

        // ======================
        // ELIMINAR
        // ======================
        if ($accion === 'eliminar' && $codMat > 0) {

            $stmt = $conector->prepare("DELETE FROM Partido WHERE codMat = ?");

            if (!$stmt) {
                die(json_encode([
                    'exito' => false,
                    'mensaje' => 'Error SQL ELIMINAR: ' . $conector->error
                ]));
            }

            $stmt->bind_param("i", $codMat);

            if ($stmt->execute()) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Partido eliminado correctamente.';
            } else {
                $respuesta['mensaje'] = 'Error al eliminar partido: ' . $stmt->error;
            }
        }

        echo json_encode($respuesta);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER TODOS
    |--------------------------------------------------------------------------
    */
    if ($accion === 'obtener') {

        $partidos = [];

        $sql = "
            SELECT
                p.*,
                t.nomTem,
                el.nomEqui AS local,
                ev.nomEqui AS visitante,
                c.nomCan AS cancha
            FROM Partido p
            LEFT JOIN Temporada t ON p.codTem = t.codTem
            LEFT JOIN Equipo el ON p.codequilocMat = el.codEqui
            LEFT JOIN Equipo ev ON p.codequivisMat = ev.codEqui
            LEFT JOIN Cancha c ON p.codCan = c.codCan
            ORDER BY p.fecMat DESC
        ";

        $result = $conector->query($sql);

        if (!$result) {
            die(json_encode([
                'exito' => false,
                'mensaje' => 'Error SQL OBTENER: ' . $conector->error
            ]));
        }

        $partidos = $result->fetch_all(MYSQLI_ASSOC);

        echo json_encode($partidos);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER UNO
    |--------------------------------------------------------------------------
    */
    if ($accion === 'obtenerUno' && $codMat > 0) {

        $stmt = $conector->prepare("
            SELECT *
            FROM Partido
            WHERE codMat = ?
        ");

        if (!$stmt) {
            die(json_encode([
                'exito' => false,
                'mensaje' => 'Error SQL OBTENER UNO: ' . $conector->error
            ]));
        }

        $stmt->bind_param("i", $codMat);
        $stmt->execute();

        $partido = $stmt->get_result()->fetch_assoc();

        echo json_encode($partido ?: []);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | TEMPORADAS
    |--------------------------------------------------------------------------
    */
    if ($accion === 'obtenerTemporadas') {

        $result = $conector->query("
            SELECT codTem, nomTem
            FROM Temporada
            ORDER BY codTem DESC
        ");

        if (!$result) {
            die(json_encode([
                'exito' => false,
                'mensaje' => 'Error SQL TEMPORADAS: ' . $conector->error
            ]));
        }

        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | CANCHAS
    |--------------------------------------------------------------------------
    */
    if ($accion === 'obtenerCanchas') {

        $result = $conector->query("
            SELECT codCan, nomCan
            FROM Cancha
            ORDER BY nomCan
        ");

        if (!$result) {
            die(json_encode([
                'exito' => false,
                'mensaje' => 'Error SQL CANCHAS: ' . $conector->error
            ]));
        }

        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | EQUIPOS
    |--------------------------------------------------------------------------
    */
    if ($accion === 'obtenerEquipos') {

        $result = $conector->query("
            SELECT codEqui, nomEqui
            FROM Equipo
            ORDER BY nomEqui
        ");

        if (!$result) {
            die(json_encode([
                'exito' => false,
                'mensaje' => 'Error SQL EQUIPOS: ' . $conector->error
            ]));
        }

        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Partidos - Admin | LeagueDan</title>
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
        .match-vs {
            text-align: center;
            font-weight: bold;
            color: #6c757d;
            margin: 0 10px;
        }
        .score-badge {
            display: inline-block;
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: bold;
            font-size: 1.25rem;
        }
        .state-badge {
            display: inline-block;
            padding: 0.35rem 0.65rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>
    
    <main class="container-fluid px-4 py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="h2 fw-bold mb-1"><i class="fas fa-calendar me-2"></i>Registro de Partidos</h1>
                <p class="text-muted">Administra el calendario y resultados de los partidos</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalPartido">
                    <i class="fas fa-plus me-2"></i>Nuevo Partido
                </button>
            </div>
        </div>

        <!-- Alert -->
        <div id="alertContainer"></div>

        <!-- Tabla -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="partidosTable" class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="15%"><i class="fas fa-calendar me-2"></i>Fecha</th>
                                <th width="12%"><i class="fas fa-trophy me-2"></i>Temporada</th>
                                <th width="30%">Encuentro</th>
                                <th width="15%"><i class="fas fa-map-marker me-2"></i>Cancha</th>
                                <th width="12%">Resultado</th>
                                <th width="16%" class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Crear/Editar -->
    <div class="modal fade" id="modalPartido" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Nuevo Partido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formPartido">
                        <input type="hidden" name="accion" id="accion" value="crear">
                        <input type="hidden" name="codMat" id="codMat" value="">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-500">Temporada</label>
                                <select class="form-select" name="codTem" id="codTem" required>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-500">Cancha</label>
                                <select class="form-select" name="codCan" id="codCan" required>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-500">Equipo Local</label>
                                <select class="form-select" name="codequilocMat" id="codequilocMat" required>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-500">Equipo Visitante</label>
                                <select class="form-select" name="codequivisMat" id="codequivisMat" required>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-500">Fecha y Hora</label>
                                <input type="datetime-local" class="form-control" name="fecMat" id="fecMat">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-500">Estado</label>
                                <select class="form-select" name="estMat" id="estMat">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="jugado">Jugado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-500">Puntos Local</label>
                                <input type="number" class="form-control" name="punlocMat" id="punlocMat" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-500">Puntos Visitante</label>
                                <input type="number" class="form-control" name="punvisMat" id="punvisMat" min="0">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardar">Guardar Partido</button>
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
        const urlBase = 'matches.php';

        // Inicializar
        $(document).ready(function() {
            cargarDatos();
            cargarPartidos();
            inicializarEventos();
        });

        // Cargar datos iniciales
        function cargarDatos() {
            Promise.all([
                $.get(urlBase, { accion: 'obtenerTemporadas' }, null, 'json'),
                $.get(urlBase, { accion: 'obtenerCanchas' }, null, 'json'),
                $.get(urlBase, { accion: 'obtenerEquipos' }, null, 'json')
            ]).then(function(results) {
                const temporadas = results[0];
                const canchas = results[1];
                const equipos = results[2];

                // Cargar temporadas
                let htmlTemp = '';
                temporadas.forEach(t => {
                    htmlTemp += `<option value="${t.codTem}">${t.nomTem}</option>`;
                });
                $('#codTem').html(htmlTemp);

                // Cargar canchas
                let htmlCan = '';
                canchas.forEach(c => {
                    htmlCan += `<option value="${c.codCan}">${c.nomCan}</option>`;
                });
                $('#codCan').html(htmlCan);

                // Cargar equipos
                let htmlEq = '<option value="">Seleccione</option>';
                equipos.forEach(e => {
                    htmlEq += `<option value="${e.codEqui}">${e.nomEqui}</option>`;
                });
                $('#codequilocMat').html(htmlEq);
                $('#codequivisMat').html(htmlEq);
            });
        }

        // Cargar tabla
        function cargarPartidos() {
            $.ajax({
                url: urlBase,
                method: 'GET',
                data: { accion: 'obtener' },
                dataType: 'json',
                success: function(data) {
                    let html = '';
                    data.forEach(partido => {
                        const fecha = new Date(partido.fecMat).toLocaleDateString('es-ES', { 
                            year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
                        });
                        
                        let estadoBadge = 'bg-warning';
                        if (partido.estMat === 'jugado') estadoBadge = 'bg-success';
                        if (partido.estMat === 'cancelado') estadoBadge = 'bg-danger';
                        
                        const resultado = (partido.punlocMat !== null && partido.punvisMat !== null) 
                            ? `<span class="score-badge">${partido.punlocMat} - ${partido.punvisMat}</span>`
                            : '-';
                        
                        html += `
                            <tr>
                                <td>
                                    <small class="text-muted"><i class="fas fa-clock me-1"></i>${fecha}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">${partido.nomTem}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <strong>${partido.local}</strong>
                                        <span class="match-vs">VS</span>
                                        <strong>${partido.visitante}</strong>
                                    </div>
                                </td>
                                <td>
                                    <small><i class="fas fa-building me-1"></i>${partido.cancha}</small>
                                </td>
                                <td>
                                    ${resultado}
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-icon btn-outline-primary me-2" onclick="editarPartido(${partido.codMat})" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-icon btn-outline-danger" onclick="eliminarPartido(${partido.codMat})" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    let tabla = document.querySelector('#partidosTable tbody');
                    tabla.innerHTML = html || '<tr><td colspan="6" class="text-center text-muted py-4">No hay partidos registrados</td></tr>';
                    
                    if (dataTable) {
                        dataTable.destroy();
                    }
                    dataTable = $('#partidosTable').DataTable({
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
            $('#modalPartido').on('show.bs.modal', function() {
                if ($(this).data('editar')) {
                    $('#accion').val('editar');
                    $('#modalTitle').text('Editar Partido');
                } else {
                    limpiarFormulario();
                    $('#accion').val('crear');
                    $('#modalTitle').text('Nuevo Partido');
                }
            });

            $('#btnGuardar').click(function() {
                guardarPartido();
            });

            $('#formPartido').keypress(function(e) {
                if (e.which == 13) {
                    guardarPartido();
                    return false;
                }
            });
        }

        // Guardar
        function guardarPartido() {
            const codequilocMat = parseInt($('#codequilocMat').val());
            const codequivisMat = parseInt($('#codequivisMat').val());
            
            if (!codequilocMat || !codequivisMat) {
                mostrarAlerta('error', 'Debes seleccionar ambos equipos');
                return;
            }

            if (codequilocMat === codequivisMat) {
                mostrarAlerta('error', 'Los equipos no pueden ser iguales');
                return;
            }

            const datos = {
                accion: $('#accion').val(),
                codMat: $('#codMat').val(),
                codTem: $('#codTem').val(),
                codequilocMat: codequilocMat,
                codequivisMat: codequivisMat,
                codCan: $('#codCan').val(),
                fecMat: $('#fecMat').val(),
                estMat: $('#estMat').val(),
                punlocMat: $('#punlocMat').val(),
                punvisMat: $('#punvisMat').val()
            };

            $.ajax({
                url: urlBase,
                method: 'POST',
                data: datos,
                dataType: 'json',
                success: function(respuesta) {
                    if (respuesta.exito) {
                        mostrarAlerta('success', respuesta.mensaje);
                        bootstrap.Modal.getInstance(document.getElementById('modalPartido')).hide();
                        cargarPartidos();
                    } else {
                        mostrarAlerta('error', respuesta.mensaje);
                    }
                }
            });
        }

        // Editar
        function editarPartido(codMat) {
            $.ajax({
                url: urlBase,
                method: 'GET',
                data: { accion: 'obtenerUno', codMat: codMat },
                dataType: 'json',
                success: function(partido) {
                    $('#codMat').val(partido.codMat);
                    $('#codTem').val(partido.codTem);
                    $('#codCan').val(partido.codCan);
                    $('#codequilocMat').val(partido.codequilocMat);
                    $('#codequivisMat').val(partido.codequivisMat);
                    $('#fecMat').val(partido.fecMat);
                    $('#estMat').val(partido.estMat);
                    $('#punlocMat').val(partido.punlocMat || '');
                    $('#punvisMat').val(partido.punvisMat || '');
                    
                    let modal = document.getElementById('modalPartido');
                    modal.setAttribute('data-editar', 'true');
                    
                    new bootstrap.Modal(modal).show();
                }
            });
        }

        // Eliminar
        function eliminarPartido(codMat) {
            Swal.fire({
                title: '¿Eliminar partido?',
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
                        data: { accion: 'eliminar', codMat: codMat },
                        dataType: 'json',
                        success: function(respuesta) {
                            if (respuesta.exito) {
                                mostrarAlerta('success', respuesta.mensaje);
                                cargarPartidos();
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
            $('#formPartido')[0].reset();
            $('#codMat').val('');
            document.getElementById('modalPartido').removeAttribute('data-editar');
        }

        // Mostrar alerta
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