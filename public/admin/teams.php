<?php
session_start();
include __DIR__ . '/../config/conexion.php';
include __DIR__ . '/../config/funciones.php';

if (!esAdmin()) {
    header('Location: ../login.php');
    exit;
}

$accion = $_POST['accion'] ?? ($_GET['accion'] ?? '');

// Si es una llamada AJAX, devuelve JSON
if ($accion) {
    header('Content-Type: application/json');
    $respuesta = ['exito' => false, 'mensaje' => ''];

    if ($accion === 'crear' || $accion === 'editar' || $accion === 'eliminar') {
        $nomEqui = trim($_POST['nomEqui'] ?? '');
        $ciuEqui = trim($_POST['ciuEqui'] ?? '');
        $codEqui = (int) ($_POST['codEqui'] ?? 0);

        if ($accion === 'crear' && $nomEqui !== '') {
            $stmt = $conector->prepare('INSERT INTO Equipo (nomEqui, ciuEqui) VALUES (?, ?)');
            $stmt->bind_param('ss', $nomEqui, $ciuEqui);
            if ($stmt->execute()) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Equipo creado correctamente.';
                $respuesta['codEqui'] = $conector->insert_id;
            } else {
                $respuesta['mensaje'] = 'No se pudo crear el equipo.';
            }
        }

        if ($accion === 'editar' && $codEqui > 0) {
            $stmt = $conector->prepare('UPDATE Equipo SET nomEqui = ?, ciuEqui = ? WHERE codEqui = ?');
            $stmt->bind_param('ssi', $nomEqui, $ciuEqui, $codEqui);
            if ($stmt->execute()) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Equipo actualizado correctamente.';
            } else {
                $respuesta['mensaje'] = 'No se pudo actualizar el equipo.';
            }
        }

        if ($accion === 'eliminar' && $codEqui > 0) {
            $stmt = $conector->prepare('DELETE FROM Equipo WHERE codEqui = ?');
            $stmt->bind_param('i', $codEqui);
            if ($stmt->execute()) {
                $respuesta['exito'] = true;
                $respuesta['mensaje'] = 'Equipo eliminado correctamente.';
            } else {
                $respuesta['mensaje'] = 'No se pudo eliminar el equipo. Comprueba si tiene relaciones activas.';
            }
        }

        echo json_encode($respuesta);
        exit;
    }

    if ($accion === 'obtener') {
        $equipos = [];
        $result = $conector->query('SELECT codEqui, nomEqui, ciuEqui FROM Equipo ORDER BY nomEqui');
        if ($result) {
            $equipos = $result->fetch_all(MYSQLI_ASSOC);
        }
        echo json_encode($equipos);
        exit;
    }

    if ($accion === 'obtenerUno') {
        $codEqui = (int) $_GET['codEqui'];
        $stmt = $conector->prepare('SELECT codEqui, nomEqui, ciuEqui FROM Equipo WHERE codEqui = ?');
        $stmt->bind_param('i', $codEqui);
        $stmt->execute();
        $equipo = $stmt->get_result()->fetch_assoc();
        echo json_encode($equipo ?: []);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Equipos - Admin | LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/themes/datatables/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
            transition: all 0.3s ease;
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
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
        .badge-custom {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
        }
    </style>
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>
    
    <main class="container-fluid px-4 py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="h2 fw-bold mb-1"><i class="fas fa-basketball me-2"></i>Gestión de Equipos</h1>
                <p class="text-muted">Administra todos los equipos de la liga</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalEquipo">
                    <i class="fas fa-plus me-2"></i>Nuevo Equipo
                </button>
            </div>
        </div>

        <!-- Alert -->
        <div id="alertContainer"></div>

        <!-- Tabla -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="equiposTable" class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="30%"><i class="fas fa-shield me-2"></i>Equipo</th>
                                <th width="30%"><i class="fas fa-map-marker me-2"></i>Ciudad</th>
                                <th width="40%" class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Crear/Editar -->
    <div class="modal fade" id="modalEquipo" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Nuevo Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEquipo">
                        <input type="hidden" name="accion" id="accion" value="crear">
                        <input type="hidden" name="codEqui" id="codEqui" value="">
                        
                        <div class="mb-3">
                            <label class="form-label fw-500">Nombre del Equipo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                                <input type="text" class="form-control" name="nomEqui" id="nomEqui" placeholder="Ej: Lakers, Warriors..." required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-500">Ciudad</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-city"></i></span>
                                <input type="text" class="form-control" name="ciuEqui" id="ciuEqui" placeholder="Ej: Los Angeles, Golden State...">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardar">Guardar Equipo</button>
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
        const urlBase = 'teams.php';

        // Inicializar
        $(document).ready(function() {
            cargarEquipos();
            inicializarEventos();
        });

        // Cargar tabla
        function cargarEquipos() {
            $.ajax({
                url: urlBase,
                method: 'GET',
                cache: false,
                data: { accion: 'obtener' },
                dataType: 'json',
                success: function(data) {
                    if (!dataTable) {
                        dataTable = $('#equiposTable').DataTable({
                            data: data,
                            columns: [
                                {
                                    data: 'nomEqui',
                                    render: function(data) {
                                        return `
                                            <div class="d-flex align-items-center">
                                                <div class="badge bg-primary me-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                                    <i class="fas fa-basketball"></i>
                                                </div>
                                                <strong>${data}</strong>
                                            </div>
                                        `;
                                    }
                                },
                                {
                                    data: 'ciuEqui',
                                    render: function(data) {
                                        return `<i class="fas fa-map-pin text-secondary me-1"></i>${data || 'No especificada'}`;
                                    }
                                },
                                {
                                    data: 'codEqui',
                                    orderable: false,
                                    searchable: false,
                                    className: 'text-end',
                                    render: function(data) {
                                        return `
                                            <button class="btn btn-icon btn-outline-primary me-2" onclick="editarEquipo(${data})" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-icon btn-outline-danger" onclick="eliminarEquipo(${data})" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        `;
                                    }
                                }
                            ],
                            language: { url: '../assets/js/spanish.json' },
                            paging: true,
                            pageLength: 10,
                            responsive: true,
                            destroy: true
                        });
                    } else {
                        dataTable.clear();
                        dataTable.rows.add(data);
                        dataTable.draw();
                    }
                }
            });
        }

        // Eventos
        function inicializarEventos() {
            $('#modalEquipo').on('show.bs.modal', function() {
                if ($(this).data('editar')) {
                    $('#accion').val('editar');
                    $('#modalTitle').text('Editar Equipo');
                } else {
                    limpiarFormulario();
                    $('#accion').val('crear');
                    $('#modalTitle').text('Nuevo Equipo');
                }
            });

            $('#modalEquipo').on('hidden.bs.modal', function() {
                limpiarFormulario();
            });

            $('#btnGuardar').click(function() {
                guardarEquipo();
            });

            $('#formEquipo').keypress(function(e) {
                if (e.which == 13) {
                    guardarEquipo();
                    return false;
                }
            });
        }

        // Guardar
        function guardarEquipo() {
            const accion = $('#accion').val();
            const nomEqui = $('#nomEqui').val();
            
            if (!nomEqui) {
                mostrarAlerta('error', 'El nombre del equipo es obligatorio');
                return;
            }

            const datos = {
                accion: accion,
                nomEqui: nomEqui,
                ciuEqui: $('#ciuEqui').val(),
                codEqui: $('#codEqui').val()
            };

            $.ajax({
                url: urlBase,
                method: 'POST',
                data: datos,
                dataType: 'json',
                success: function(respuesta) {
                    if (respuesta.exito) {
                        mostrarAlerta('success', respuesta.mensaje);
                        bootstrap.Modal.getInstance(document.getElementById('modalEquipo')).hide();
                        cargarEquipos();
                    } else {
                        mostrarAlerta('error', respuesta.mensaje);
                    }
                }
            });
        }

        // Editar
        function editarEquipo(codEqui) {
            $.ajax({
                url: urlBase,
                method: 'GET',
                data: { accion: 'obtenerUno', codEqui: codEqui },
                dataType: 'json',
                success: function(equipo) {
                    $('#codEqui').val(equipo.codEqui);
                    $('#nomEqui').val(equipo.nomEqui);
                    $('#ciuEqui').val(equipo.ciuEqui);
                    
                    let modal = document.getElementById('modalEquipo');
                    modal.setAttribute('data-editar', 'true');
                    
                    new bootstrap.Modal(modal).show();
                }
            });
        }

        // Eliminar
        function eliminarEquipo(codEqui) {
            Swal.fire({
                title: '¿Eliminar equipo?',
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
                        data: { accion: 'eliminar', codEqui: codEqui },
                        dataType: 'json',
                        success: function(respuesta) {
                            if (respuesta.exito) {
                                mostrarAlerta('success', respuesta.mensaje);
                                cargarEquipos();
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
            $('#formEquipo')[0].reset();
            $('#codEqui').val('');
            document.getElementById('modalEquipo').removeAttribute('data-editar');
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
