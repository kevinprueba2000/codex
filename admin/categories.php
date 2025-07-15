<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Category.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/auth/login.php');
}

$category = new Category();

// Obtener categorías
$categories = $category->getAllCategories();
$showAddModal = isset($_GET['action']) && $_GET['action'] === 'add';
$editCategoryId = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Categorías - Admin <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3>
                    <i class="fas fa-flask"></i>
                    Admin Panel
                </h3>
            </div>
            
            <ul class="list-unstyled components">
                <li>
                    <a href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="products.php">
                        <i class="fas fa-box"></i>
                        Productos
                    </a>
                </li>
                <li class="active">
                    <a href="categories.php">
                        <i class="fas fa-tags"></i>
                        Categorías
                    </a>
                </li>
                <li>
                    <a href="orders.php">
                        <i class="fas fa-shopping-cart"></i>
                        Pedidos
                    </a>
                </li>
                <li>
                    <a href="users.php">
                        <i class="fas fa-users"></i>
                        Usuarios
                    </a>
                </li>
                <li>
                    <a href="messages.php">
                        <i class="fas fa-envelope"></i>
                        Mensajes
                    </a>
                </li>
                <li>
                    <a href="team.php">
                        <i class="fas fa-users-cog"></i>
                        Equipo
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <i class="fas fa-cog"></i>
                        Configuración
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <a href="../index.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-eye me-2"></i>Ver Sitio
                </a>
                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt me-2"></i>Salir
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>
                                <?php echo $_SESSION['user_name']; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php">Mi Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../auth/logout.php">Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="h3 mb-0">Gestionar Categorías</h1>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                <i class="fas fa-plus me-2"></i>Nueva Categoría
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Categories Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tags me-2"></i>
                            Lista de Categorías
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Imagen</th>
                                        <th>Nombre</th>
                                        <th>Slug</th>
                                        <th>Productos</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $cat): ?>
                                        <tr>
                                            <td><?php echo $cat['id']; ?></td>
                                            <td>
                                                <?php if ($cat['image']): ?>
                                                    <img src="<?php echo $cat['image']; ?>" 
                                                         alt="<?php echo htmlspecialchars($cat['name']); ?>" 
                                                         class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                        <i class="fas fa-tag"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($cat['name']); ?></strong>
                                                <?php if ($cat['description']): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($cat['description']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><code><?php echo $cat['slug']; ?></code></td>
                                            <td>
                                                <span class="badge bg-primary">5</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Activo</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-outline-primary btn-sm" onclick="editCategory(<?php echo $cat['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteCategory(<?php echo $cat['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Categoría</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control" name="slug" required>
                            <small class="text-muted">URL amigable (ej: software-personalizado)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagen</label>
                            <div class="upload-area" data-folder="categories">
                                <div class="upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="text-muted small">Selecciona una imagen</p>
                                </div>
                                <input type="file" id="categoryImageUpload" name="images[]" accept="image/*" class="form-control mt-2">
                            </div>
                            <div id="categoryImagePreview" class="image-preview mt-2"></div>
                            <input type="hidden" name="image_json" id="categoryImagesJson">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveCategory()">Guardar Categoría</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <input type="hidden" name="id" id="editCategoryId">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Categoría</label>
                            <input type="text" class="form-control" name="name" id="editCategoryName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="description" id="editCategoryDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control" name="slug" id="editCategorySlug" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagen</label>
                            <div class="upload-area" data-folder="categories">
                                <div class="upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="text-muted small">Selecciona una imagen</p>
                                </div>
                                <input type="file" id="editCategoryImageUpload" name="images[]" accept="image/*" class="form-control mt-2">
                            </div>
                            <div id="editCategoryImagePreview" class="image-preview mt-2"></div>
                            <input type="hidden" name="image_json" id="editCategoryImagesJson">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateCategory()">Actualizar Categoría</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    
    <script>
        // Category management functions
        const csrfToken = '<?php echo generateCSRFToken(); ?>';

        function editCategory(categoryId) {
            const formData = new FormData();
            formData.append('action', 'get_category');
            formData.append('id', categoryId);
            formData.append('csrf_token', csrfToken);

            fetch('process_category.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const cat = data.category;
                    document.getElementById('editCategoryId').value = cat.id;
                    document.getElementById('editCategoryName').value = cat.name;
                    document.getElementById('editCategoryDescription').value = cat.description || '';
                    document.getElementById('editCategorySlug').value = cat.slug;

                    if (cat.image) {
                        const preview = document.getElementById('editCategoryImagePreview');
                        preview.innerHTML = '';
                        const item = document.createElement('div');
                        item.className = 'image-preview-item';
                        const img = document.createElement('img');
                        img.src = cat.image;
                        item.appendChild(img);
                        preview.appendChild(item);
                        document.getElementById('editCategoryImagesJson').value = JSON.stringify([cat.image]);
                    }

                    const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                    modal.show();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(() => alert('Error al cargar la categoría'));
        }

        function saveCategory() {
            const form = document.getElementById('addCategoryForm');
            const formData = new FormData(form);
            formData.append('action', 'create');
            formData.append('csrf_token', csrfToken);

            fetch('process_category.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
                    modal.hide();
                    form.reset();
                    document.getElementById('categoryImagePreview').innerHTML = '';
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(() => alert('Error al guardar la categoría'));
        }

        function updateCategory() {
            const form = document.getElementById('editCategoryForm');
            const formData = new FormData(form);
            formData.append('action', 'update');
            formData.append('csrf_token', csrfToken);

            fetch('process_category.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
                    modal.hide();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(() => alert('Error al actualizar la categoría'));
        }

        function deleteCategory(categoryId) {
            if (confirm('¿Estás seguro de que quieres eliminar esta categoría?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', categoryId);
                formData.append('csrf_token', csrfToken);

                fetch('process_category.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(() => alert('Error al eliminar la categoría'));
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if ($showAddModal) {
                const addModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
                addModal.show();
            }
            if ($editCategoryId > 0) {
                // Function editCategory might exist if implemented via AJAX
                if (typeof editCategory === 'function') {
                    editCategory($editCategoryId);
                }
                const editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                editModal.show();
            }
        });
    </script>
</body>
</html>