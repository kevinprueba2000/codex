<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Category.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/auth/login.php');
}

$product = new Product();
$category = new Category();

// Obtener productos
$products = $product->getAllProducts();
$categories = $category->getAllCategories();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Productos - Admin <?php echo SITE_NAME; ?></title>
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
                <li class="active">
                    <a href="products.php">
                        <i class="fas fa-box"></i>
                        Productos
                    </a>
                </li>
                <li>
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
                            <h1 class="h3 mb-0">Gestionar Productos</h1>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                <i class="fas fa-plus me-2"></i>Nuevo Producto
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-box me-2"></i>
                            Lista de Productos
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
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $prod): ?>
                                        <tr>
                                            <td><?php echo $prod['id']; ?></td>
                                            <td>
                                                <?php 
                                                $imageUrl = 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=50&q=80';
                                                if (!empty($prod['images'])) {
                                                    $images = json_decode($prod['images'], true);
                                                    if ($images && is_array($images) && !empty($images[0])) {
                                                        $imageUrl = $images[0];
                                                    }
                                                }
                                                ?>
                                                <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                                                     alt="<?php echo htmlspecialchars($prod['name']); ?>" 
                                                     class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($prod['name']); ?></strong>
                                                <?php if (isset($prod['is_featured']) && $prod['is_featured']): ?>
                                                    <span class="badge bg-warning ms-2">Destacado</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $cat = $category->getCategoryById($prod['category_id']);
                                                echo $cat ? $cat['name'] : 'Sin categoría';
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (isset($prod['discount_percentage']) && $prod['discount_percentage'] > 0): ?>
                                                    <span class="text-decoration-line-through text-muted">
                                                        $<?php echo number_format($prod['price'], 0, ',', '.'); ?>
                                                    </span>
                                                    <br>
                                                    <span class="text-success fw-bold">
                                                        $<?php echo number_format($prod['price'] * (1 - $prod['discount_percentage'] / 100), 0, ',', '.'); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="fw-bold">$<?php echo number_format($prod['price'], 0, ',', '.'); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Disponible</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">Activo</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-outline-primary btn-sm" onclick="editProduct(<?php echo $prod['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteProduct(<?php echo $prod['id']; ?>)">
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

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Producto</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Categoría</label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="">Seleccionar categoría</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Precio</label>
                                    <input type="number" class="form-control" name="price" min="0" step="0.01" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Stock</label>
                                    <input type="number" class="form-control" name="stock" min="0" value="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Descuento (%)</label>
                                    <input type="number" class="form-control" name="discount_percentage" min="0" max="100" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" name="description" rows="4" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Imágenes del Producto</label>
                                    <div class="upload-area" id="uploadArea">
                                        <div class="upload-placeholder">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Arrastra imágenes aquí o haz clic para seleccionar</p>
                                            <p class="text-muted small">Formatos: JPG, PNG, GIF, WEBP (máx. 5MB cada una)</p>
                                        </div>
                                        <input type="file" id="imageUpload" name="images[]" multiple accept="image/*" style="display: none;">
                                    </div>
                                    <div id="imagePreview" class="mt-3"></div>
                                    <input type="hidden" name="images_json" id="imagesJson">
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="featured" id="featured">
                                        <label class="form-check-label" for="featured">
                                            Producto Destacado
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()">Guardar Producto</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        <input type="hidden" name="id" id="editProductId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Producto</label>
                                    <input type="text" class="form-control" name="name" id="editProductName" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Categoría</label>
                                    <select class="form-select" name="category_id" id="editProductCategory" required>
                                        <option value="">Seleccionar categoría</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Precio</label>
                                    <input type="number" class="form-control" name="price" id="editProductPrice" min="0" step="0.01" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Stock</label>
                                    <input type="number" class="form-control" name="stock" id="editProductStock" min="0" value="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Descuento (%)</label>
                                    <input type="number" class="form-control" name="discount_percentage" id="editProductDiscount" min="0" max="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" name="description" id="editProductDescription" rows="4" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Imágenes del Producto</label>
                                    <div class="upload-area" id="editUploadArea">
                                        <div class="upload-placeholder">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Arrastra imágenes aquí o haz clic para seleccionar</p>
                                            <p class="text-muted small">Formatos: JPG, PNG, GIF, WEBP (máx. 5MB cada una)</p>
                                        </div>
                                        <input type="file" id="editImageUpload" name="images[]" multiple accept="image/*" style="display: none;">
                                    </div>
                                    <div id="editImagePreview" class="mt-3"></div>
                                    <input type="hidden" name="images_json" id="editImagesJson">
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="featured" id="editProductFeatured">
                                        <label class="form-check-label" for="editProductFeatured">
                                            Producto Destacado
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateProduct()">Actualizar Producto</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    
    <script>
        // CSRF Token
        const csrfToken = '<?php echo generateCSRFToken(); ?>';
        
        // Product management functions
        function editProduct(productId) {
            // Load product data via AJAX
            const formData = new FormData();
            formData.append('action', 'get_product');
            formData.append('id', productId);
            formData.append('csrf_token', csrfToken);
            
            fetch('process_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const product = data.product;
                    document.getElementById('editProductId').value = product.id;
                    document.getElementById('editProductName').value = product.name;
                    document.getElementById('editProductCategory').value = product.category_id;
                    document.getElementById('editProductPrice').value = product.price;
                    document.getElementById('editProductStock').value = product.stock_quantity || 0;
                    document.getElementById('editProductDiscount').value = product.discount_percentage || 0;
                    document.getElementById('editProductDescription').value = product.description;
                    document.getElementById('editProductFeatured').checked = product.is_featured == 1;
                    
                    // Cargar imágenes si existen
                    if (product.images) {
                        try {
                            const images = JSON.parse(product.images);
                            const preview = document.getElementById('editImagePreview');
                            preview.innerHTML = '';
                            
                            images.forEach(imageUrl => {
                                const item = document.createElement('div');
                                item.className = 'image-preview-item';
                                
                                const img = document.createElement('img');
                                img.src = imageUrl;
                                img.alt = 'Product image';
                                
                                const removeBtn = document.createElement('button');
                                removeBtn.className = 'remove-btn';
                                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                                removeBtn.onclick = () => {
                                    item.remove();
                                    updateImagesJson();
                                };
                                
                                item.appendChild(img);
                                item.appendChild(removeBtn);
                                preview.appendChild(item);
                            });
                            
                            document.getElementById('editImagesJson').value = product.images;
                        } catch (e) {
                            console.error('Error parsing images:', e);
                        }
                    }
                    
                    const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
                    modal.show();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos del producto');
            });
        }
        
        function saveProduct() {
            const form = document.getElementById('addProductForm');
            const formData = new FormData(form);
            formData.append('action', 'create');
            formData.append('csrf_token', csrfToken);
            
            // Obtener imágenes JSON
            const imagesJson = document.getElementById('imagesJson').value || '[]';
            formData.append('images_json', imagesJson);
            
            // Obtener stock (si no existe, usar 0)
            if (!formData.get('stock')) {
                formData.append('stock', '0');
            }
            
            // Obtener featured
            formData.append('featured', form.querySelector('input[name="featured"]').checked ? 1 : 0);
            
            fetch('process_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                    modal.hide();
                    form.reset();
                    document.getElementById('imagePreview').innerHTML = '';
                    document.getElementById('imagesJson').value = '';
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar el producto');
            });
        }
        
        function updateProduct() {
            const form = document.getElementById('editProductForm');
            const formData = new FormData(form);
            formData.append('action', 'update');
            formData.append('csrf_token', csrfToken);
            
            // Obtener imágenes JSON
            const imagesJson = document.getElementById('editImagesJson').value || '[]';
            formData.append('images_json', imagesJson);
            
            // Obtener stock (si no existe, usar 0)
            if (!formData.get('stock')) {
                formData.append('stock', '0');
            }
            
            // Obtener featured
            formData.append('featured', form.querySelector('input[name="featured"]').checked ? 1 : 0);
            
            fetch('process_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                    modal.hide();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el producto');
            });
        }
        
        function deleteProduct(productId) {
            if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', productId);
                formData.append('csrf_token', csrfToken);
                
                fetch('process_product.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el producto');
                });
            }
        }
        
        function updateImagesJson() {
            const previews = document.querySelectorAll('.image-preview');
            previews.forEach(preview => {
                const images = Array.from(preview.querySelectorAll('img')).map(img => img.src);
                const hiddenInput = preview.parentElement.querySelector('[id$="ImagesJson"]');
                if (hiddenInput) {
                    hiddenInput.value = JSON.stringify(images);
                }
            });
        }
    </script>
</body>
</html> 