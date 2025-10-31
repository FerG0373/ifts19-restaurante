<?php
use App\DTOs\PersonalVistaDTO;
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">        
        <div class="d-flex align-items-center">            
            <h2 class="text-primary mb-0 me-3"><?php echo htmlspecialchars($titulo); ?></h2>                        
            <button type="button" class="btn btn-link fa-lg mt-2" title="Editar Personal" onclick="editarPersonal(<?= htmlspecialchars($personal->id) ?>)">
                <i class="fas fa-edit"></i>
            </button>
        </div>
        <a href="<?=APP_BASE_URL?>personal" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a la lista
        </a>
    </div>

    <?php
    /** @var PersonalVistaDTO $personal */
    if (empty($personal)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($mensaje ?? 'No se encontró el personal.'); ?></div>
    <?php else: ?>
        <div class="card shadow-sm mx-auto mt-5" style="max-width: 600px;">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Información Personal y Laboral</h4>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    
                    <dt class="col-sm-4 text-nowrap">ID:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->id); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">DNI:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->dni); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Nombre:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->nombre); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Apellido:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->apellido); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Fecha de Nacimiento:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->fechaNacimiento); ?></dd> 
                    
                    <dt class="col-sm-4 text-nowrap">Email:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->email); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Teléfono:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->telefono); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Sexo:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->sexo); ?></dd> 
                    
                    <dt class="col-sm-4 text-nowrap">Puesto:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->puesto); ?></dd> 
                    
                    <dt class="col-sm-4 text-nowrap">Fecha de Contratación:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->fechaContratacion); ?></dd> 
                    
                    <dt class="col-sm-4 text-nowrap">Activo:</dt>
                    <dd class="col-sm-8">
                        <?php if ($personal->activo): ?>
                            <span class="text-success fw-bold">Sí</span>
                        <?php else: ?>
                            <span class="text-danger fw-bold">No</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>
    <?php endif; ?>
</div>


<script>
    async function editarPersonal(id) {
        const formData = new FormData();
        formData.append('id', id);

        try {
            const response = await fetch("<?= APP_BASE_URL ?>personal/formulario/editar", {
            method: "POST",
            body: formData
            });

            if (response.redirected) {
            // Si el servidor redirige, seguimos normalmente
            window.location.href = response.url;
            } else {
            const html = await response.text();

            // Reemplazamos el contenido actual
            document.body.innerHTML = html;

            // 🔥 Actualizamos la URL mostrada en la barra del navegador
            // sin recargar la página
            window.history.pushState({}, "", "<?= APP_BASE_URL ?>personal/formulario");
            }
        } catch (err) {
            console.error("Error al abrir el formulario de edición:", err);
        }
    }
</script>