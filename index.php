<?php
// Démarrer la session
session_start();

// Configuration de la base de données
$config = [
    'db_host' => 'localhost',
    'db_name' => 'kiosque_db',
    'db_user' => 'root',
    'db_pass' => '',
    'site_url' => 'index_acceuil.php' // URL du site public
];

// Connexion à la base de données
try {
    $pdo = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
        $config['db_user'],
        $config['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Vérification de l'authentification
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && $_SESSION['admin_id'] > 0;
}

// Redirection si non connecté
if (!isLoggedIn() && !isset($_POST['login_submit'])) {
    if ($_SERVER['PHP_SELF'] !== '/admin/login.php') {
        // Si l'utilisateur n'est pas sur la page de connexion, le rediriger
        if (!isset($_GET['page']) || $_GET['page'] !== 'login') {
            header('Location: ?page=login');
            exit;
        }
    }
}

// Traitement du formulaire de connexion
if (isset($_POST['login_submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            
            if (isset($_GET['page']) && $_GET['page'] === 'login') {
                header('Location: ?page=dashboard');
                exit;
            }
        } else {
            $login_error = "Identifiants incorrects";
        }
    } else {
        $login_error = "Veuillez remplir tous les champs";
    }
}

// Traitement de la déconnexion
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: ?page=login');
    exit;
}

// Fonctions CRUD pour les produits
function getAllProducts() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT p.*, c.name as category_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.name ASC
    ");
    return $stmt->fetchAll();
}
function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
function saveProduct($data) {
    global $pdo;
    
    // Gérer l'upload de l'image
    $imagePath = $data['current_image'] ?? null;
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'images/';
        
        // Créer le dossier s'il n'existe pas
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Supprimer l'ancienne image si elle existe
        if ($imagePath && file_exists($uploadDir . $imagePath)) {
            unlink($uploadDir . $imagePath);
        }
        
        // Générer un nom de fichier unique
        $ext = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('prod_') . '.' . $ext;
        $targetPath = $uploadDir . $imageName;
        
        // Déplacer le fichier uploadé
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath)) {
            $imagePath = $imageName;
        }
    }
    
    if (isset($data['id']) && $data['id'] > 0) {
        // Mise à jour
        $stmt = $pdo->prepare("
            UPDATE products 
            SET name = ?, category_id = ?, price = ?, stock = ?, 
                description = ?, status = ?, image = ?, discount = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['name'],
            $data['category_id'] ?: null,
            $data['price'],
            $data['stock'],
            $data['description'] ?? '',
            $data['status'] ?? 1,
            $imagePath,
            $data['discount'] ?? 0,
            $data['id']
        ]);
        return $data['id'];
    } else {
        // Création
        $stmt = $pdo->prepare("
            INSERT INTO products 
            (name, category_id, price, stock, description, status, image, discount) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['category_id'] ?: null,
            $data['price'],
            $data['stock'],
            $data['description'] ?? '',
            $data['status'] ?? 1,
            $imagePath,
            $data['discount'] ?? 0
        ]);
        return $pdo->lastInsertId();
    }
}
function deleteProduct($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    return $stmt->execute([$id]);
}

// Fonctions pour les catégories
function getAllCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategoryById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function saveCategory($data) {
    global $pdo;
    
    if (isset($data['id']) && $data['id'] > 0) {
        // Mise à jour
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['id']]);
        return $data['id'];
    } else {
        // Création
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$data['name']]);
        return $pdo->lastInsertId();
    }
}

function deleteCategory($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    return $stmt->execute([$id]);
}

// Fonctions pour les commandes
function getAllOrders($limit = 50) {
    global $pdo;
    
    // Version avec paramètre nommé et typage strict
    $stmt = $pdo->prepare("SELECT * FROM orders ORDER BY date_created DESC LIMIT :limit");
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne un tableau associatif
}

function getOrderById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getOrderItems($order_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name as product_name 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll();
}

// Fonctions pour les statistiques
function getDailySales($days = 7) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 
            DATE(date_created) as sale_date,
            SUM(total_amount) as daily_total
        FROM orders
        WHERE date_created >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY DATE(date_created)
        ORDER BY sale_date ASC
    ");
    $stmt->execute([$days]);
    return $stmt->fetchAll();
}

function getProductCategorySales() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT 
            c.name as category_name,
            SUM(oi.quantity * oi.price) as total_sales,
            COUNT(DISTINCT o.id) as order_count
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN categories c ON p.category_id = c.id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.date_created >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY c.id
        ORDER BY total_sales DESC
    ");
    return $stmt->fetchAll();
}

function getTopSellingProducts($limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.name,
            c.name as category_name,
            p.price,
            SUM(oi.quantity) as total_quantity,
            SUM(oi.quantity * oi.price) as total_sales
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN categories c ON p.category_id = c.id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.date_created >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY p.id
        ORDER BY total_quantity DESC
        LIMIT :limit
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Fonctions pour les paramètres
function getSettings() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

function updateSetting($key, $value) {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO settings (setting_key, setting_value) 
        VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE setting_value = ?
    ");
    return $stmt->execute([$key, $value, $value]);
}

// Traitement des actions
$notification = '';

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Traitement du formulaire de produit
    if (isset($_POST['product_submit'])) {
        $productData = [
            'name' => $_POST['product_name'] ?? '',
            'category_id' => $_POST['product_category'] ?? 0,
            'price' => $_POST['product_price'] ?? 0,
            'stock' => $_POST['product_stock'] ?? 0,
            'description' => $_POST['product_description'] ?? '',
            'status' => isset($_POST['product_status']) ? 1 : 0
        ];
        
        if (isset($_POST['product_id']) && $_POST['product_id'] > 0) {
            $productData['id'] = $_POST['product_id'];
        }
        
        if (!empty($productData['name']) && $productData['price'] > 0) {
            $productId = saveProduct($productData);
            $notification = "Produit enregistré avec succès!";
        } else {
            $notification = "Erreur: Nom et prix requis";
        }
    }
    
    // Traitement du formulaire de catégorie
    if (isset($_POST['category_submit'])) {
        $categoryData = [
            'name' => $_POST['category_name'] ?? ''
        ];
        
        if (isset($_POST['category_id']) && $_POST['category_id'] > 0) {
            $categoryData['id'] = $_POST['category_id'];
        }
        
        if (!empty($categoryData['name'])) {
            $categoryId = saveCategory($categoryData);
            $notification = "Catégorie enregistrée avec succès!";
        } else {
            $notification = "Erreur: Nom requis";
        }
    }
    
    // Traitement des paramètres
    if (isset($_POST['settings_submit'])) {
        // Heures d'ouverture
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days as $day) {
            $open = $_POST[$day.'_open'] ?? '08:00';
            $close = $_POST[$day.'_close'] ?? '19:00';
            updateSetting('hours_'.$day, $open.'-'.$close);
        }
        
        // Informations de la boutique
        updateSetting('shop_name', $_POST['shop_name'] ?? '');
        updateSetting('shop_address', $_POST['shop_address'] ?? '');
        updateSetting('shop_postal', $_POST['shop_postal'] ?? '');
        updateSetting('shop_city', $_POST['shop_city'] ?? '');
        updateSetting('shop_phone', $_POST['shop_phone'] ?? '');
        updateSetting('shop_email', $_POST['shop_email'] ?? '');
        
        // Méthodes de paiement
        $payment_methods = [];
        if (isset($_POST['payment_cash'])) $payment_methods[] = 'cash';
        if (isset($_POST['payment_card'])) $payment_methods[] = 'card';
        if (isset($_POST['payment_contactless'])) $payment_methods[] = 'contactless';
        if (isset($_POST['payment_tickets'])) $payment_methods[] = 'tickets';
        updateSetting('payment_methods', implode(',', $payment_methods));
        
        $notification = "Paramètres enregistrés avec succès!";
    }
}

// Suppression d'un produit
if (isset($_GET['action']) && $_GET['action'] === 'delete_product' && isset($_GET['id'])) {
    $productId = (int)$_GET['id'];
    if (deleteProduct($productId)) {
        $notification = "Produit supprimé avec succès!";
    } else {
        $notification = "Erreur lors de la suppression du produit";
    }
}

// Suppression d'une catégorie
if (isset($_GET['action']) && $_GET['action'] === 'delete_category' && isset($_GET['id'])) {
    $categoryId = (int)$_GET['id'];
    if (deleteCategory($categoryId)) {
        $notification = "Catégorie supprimée avec succès!";
    } else {
        $notification = "Erreur lors de la suppression de la catégorie";
    }
}

// Récupération de la page courante
$current_page = $_GET['page'] ?? 'dashboard';

// Chargement des données selon la page
switch ($current_page) {
    case 'dashboard':
        $daily_sales = getDailySales(7);
        $top_products = getTopSellingProducts(5);
        $category_sales = getProductCategorySales();
        $recent_orders = getAllOrders(10);
        break;
        
    case 'products':
        $products = getAllProducts();
        $categories = getAllCategories();
        // Si on modifie un produit, on récupère ses données
        if (isset($_GET['edit_product'])) {
            $edit_product = getProductById($_GET['edit_product']);
        }
        break;
        
    case 'orders':
        $orders = getAllOrders();
        // Si on visualise une commande en détail
        if (isset($_GET['view_order'])) {
            $order_details = getOrderById($_GET['view_order']);
            $order_items = getOrderItems($_GET['view_order']);
        }
        break;
        
    case 'stats':
        $daily_sales = getDailySales(30);
        $category_sales = getProductCategorySales();
        $top_products = getTopSellingProducts(10);
        break;
        
    case 'settings':
        $settings = getSettings();
        $categories = getAllCategories();
        break;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panneau d'Administration - Kiosque</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
    <sccript src="script_time.js" defer></script>
</head>
<body>
    <?php if ($current_page === 'login' || !isLoggedIn()): ?>
    <!-- Page de connexion -->
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">Fuel<span>Admin</span></div>
                <p>Connectez-vous pour accéder au panneau d'administration</p>
            </div>
            
            <?php if (isset($login_error)): ?>
            <div class="error-message"><?php echo $login_error; ?></div>
            <?php endif; ?>
            
            <form class="login-form" method="post" action="?page=login">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login_submit" class="login-btn">Connexion</button>
            </form>
        </div>
    </div>
    <?php else: ?>
    <!-- Interface d'administration -->
    <header>
        <div class="logo">Fuel<span>Admin</span></div>
        <nav>
            <ul>
                <li><a href="?page=dashboard" class="<?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">Tableau de bord</a></li>
                <li><a href="<?php echo $config['site_url']; ?>" target="_blank">Site Public</a></li>
            </ul>
        </nav>
        <div class="user-dropdown">
            <div class="user-icon">👤 <?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
            <div class="dropdown-content">
                <a href="?page=profile">Mon Profil</a>
                <a href="?page=settings">Paramètres</a>
                <a href="?action=logout">Déconnexion</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="admin-header">
            <h1>Tableau de bord d'administration</h1>
            <p>Gérez votre kiosque et suivez les performances en temps réel</p>
        </div>

        <!-- Statistiques rapides -->
        <div class="dashboard">
            <div class="stat-card revenue">
                <h3>Ventes du jour</h3>
                <?php 
                $today_sales = 0;
                if (isset($daily_sales) && !empty($daily_sales)) {
                    foreach ($daily_sales as $sale) {
                        if ($sale['sale_date'] === date('Y-m-d')) {
                            $today_sales = $sale['daily_total'];
                            break;
                        }
                    }
                }
                ?>
                <div class="value"><?php echo number_format($today_sales, 2, ',', ' '); ?> €</div>
                <p>+ 15% vs hier</p>
            </div>

            <div class="stat-card users">
                <h3>Visiteurs</h3>
                <?php
                // Exemple: vous pourriez récupérer ce nombre depuis une table de statistiques
                $visitors = mt_rand(200, 500); // Juste pour l'exemple
                ?>
                <div class="value"><?php echo $visitors; ?></div>
                <p>Aujourd'hui</p>
            </div>
        </div>          

        <!-- Navigation par onglets -->
        <div class="admin-tabs">
            <div class="tab-header">
                <a href="?page=dashboard"><button class="tab-btn <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">Tableau de Bord</button></a>
                <a href="?page=products"><button class="tab-btn <?php echo $current_page === 'products' ? 'active' : ''; ?>">Produits</button></a>
                <a href="?page=orders"><button class="tab-btn <?php echo $current_page === 'orders' ? 'active' : ''; ?>">Commandes</button></a>
                <a href="?page=stats"><button class="tab-btn <?php echo $current_page === 'stats' ? 'active' : ''; ?>">Statistiques</button></a>
                <a href="?page=settings"><button class="tab-btn <?php echo $current_page === 'settings' ? 'active' : ''; ?>">Paramètres</button></a>
            </div>

            <div class="tab-content">
                <!-- Contenu de l'onglet actif -->
                <div class="tab-pane active">
                    <?php if ($current_page === 'dashboard'): ?>
                        <!-- TABLEAU DE BORD -->
                        <div class="section-title">
                            <h2>Aperçu Rapide</h2>
                        </div>

                        <div class="dashboard">
                            <div class="stat-card revenue">
                                <h3>Chiffre d'affaires</h3>
                                <div class="value">2,845 €</div>
                                <div class="time-format">Aujourd'hui</div>
                            </div>
                            <div class="stat-card fuel">
                                <h3>Carburant vendu</h3>
                                <div class="value">1,785 L</div>
                                <div class="time-format">Dernières 24h</div>
                            </div>
                            <div class="stat-card wash">
                                <h3>Lavages effectués</h3>
                                <div class="value">42</div>
                                <div class="time-format">Ce mois</div>
                            </div>
                            <div class="stat-card products">
                                <h3>Produits vendus</h3>
                                <div class="value">87</div>
                                <div class="time-format">Cette semaine</div>
                            </div>
                        </div>

                        <div class="chart-container">
                            <div class="chart-header">
                                <div class="chart-title">Ventes des 7 derniers jours</div>
                                <div class="time-period-selector">
                                    <button class="time-btn">Jour</button>
                                    <button class="time-btn active">Semaine</button>
                                    <button class="time-btn">Mois</button>
                                    <button class="time-btn">Année</button>
                                </div>
                            </div>
                            <div class="bar-chart">
                                <div class="chart-y-axis">
                                    <div>3000€</div>
                                    <div>2250€</div>
                                    <div>1500€</div>
                                    <div>750€</div>
                                    <div>0€</div>
                                </div>
                                <div class="chart-grid">
                                    <div class="grid-line"></div>
                                    <div class="grid-line"></div>
                                    <div class="grid-line"></div>
                                    <div class="grid-line"></div>
                                    <div class="grid-line"></div>
                                </div>
                                
                                <?php
                                // Créer un tableau avec tous les jours de la semaine
                                $days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
                                $height_values = [180, 220, 250, 280, 320, 240, 200]; // Hauteurs en pixels
                                
                                // Pour chaque jour, afficher une barre
                                foreach ($days as $index => $day): 
                                    $height = $height_values[$index];
                                ?>
                                <div class="bar-container">
                                    <div class="bar" style="height: <?php echo $height; ?>px;"></div>
                                    <div class="bar-label"><?php echo $day; ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="pie-chart-container">
                            <div class="pie-chart"></div>
                            <div class="pie-legend">
                                <div class="legend-item">
                                    <div class="color-indicator color-primary"></div>
                                    <div class="legend-text">
                                        <span class="legend-label">Carburants</span>
                                        <span class="legend-value">62%</span>
                                    </div>
                                </div>
                                <div class="legend-item">
                                    <div class="color-indicator color-success"></div>
                                    <div class="legend-text">
                                        <span class="legend-label">Lavages</span>
                                        <span class="legend-value">25%</span>
                                    </div>
                                </div>
                                <div class="legend-item">
                                    <div class="color-indicator color-warning"></div>
                                    <div class="legend-text">
                                        <span class="legend-label">Produits</span>
                                        <span class="legend-value">10%</span>
                                    </div>
                                </div>
                                <div class="legend-item">
                                    <div class="color-indicator color-info"></div>
                                    <div class="legend-text">
                                        <span class="legend-label">Recharges</span>
                                        <span class="legend-value">3%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-title">
                            <h2>Produits & Services populaires</h2>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Produit/Service</th>
                                    <th>Catégorie</th>
                                    <th>Prix</th>
                                    <th>Vendus</th>
                                    <th>Stock</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($top_products) && !empty($top_products)): ?>
                                    <?php foreach ($top_products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                        <td><?php echo number_format($product['price'], 2, ',', ' '); ?> €</td>
                                        <td><?php echo $product['total_quantity']; ?></td>
                                        <td><?php echo ($product['stock'] === null) ? '∞' : $product['stock']; ?></td>
                                        <td>
                                            <?php if ($product['stock'] > 0 || $product['stock'] === null): ?>
                                                <span class="status status-active">Actif</span>
                                            <?php else: ?>
                                                <span class="status status-inactive">Rupture</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Données d'exemple -->
                                    <tr>
                                        <td>Essence sans plomb 95</td>
                                        <td>Carburants</td>
                                        <td>1,589 TND/L</td>
                                        <td>1,245 L</td>
                                        <td>∞</td>
                                        <td><span class="status status-active">Actif</span></td>
                                    </tr>
                                    <tr>
                                        <td>Formule Premium</td>
                                        <td>Lavages</td>
                                        <td>34,90 TND</td>
                                        <td>28</td>
                                        <td>∞</td>
                                        <td><span class="status status-active">Actif</span></td>
                                    </tr>
                                    <tr>
                                        <td>Huile moteur EnergyFuel</td>
                                        <td>produits</td>
                                        <td>29,99 TND</td>
                                        <td>15</td>
                                        <td>42</td>
                                        <td><span class="status status-active">Actif</span></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <div class="section-title">
                            <h2>Transactions récentes</h2>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Montant</th>
                                    <th>Détails</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($recent_orders) && !empty($recent_orders)): ?>
                                    <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo date('d/m/Y - H:i', strtotime($order['date_created'])); ?></td>
                                        <td><?php echo htmlspecialchars($order['order_type']); ?></td>
                                        <td><?php echo number_format($order['total_amount'], 2, ',', ' '); ?> €</td>
                                        <td><?php echo htmlspecialchars($order['notes'] ?? '-'); ?></td>
                                        <td>
                                            <?php if ($order['status'] === 'completed'): ?>
                                                <span class="status status-active">Complété</span>
                                            <?php elseif ($order['status'] === 'pending'): ?>
                                                <span class="status status-pending">En cours</span>
                                            <?php else: ?>
                                                <span class="status status-inactive">Annulé</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="?page=orders&view_order=<?php echo $order['id']; ?>">
                                                <button class="btn btn-sm">Détails</button>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Données d'exemple -->
                                    <tr>
                                        <td>#4587</td>
                                        <td>19/06/2023 - 14:28</td>
                                        <td>Lavage</td>
                                        <td>34,90 €</td>
                                        <td>Formule Premium</td>
                                        <td><span class="status status-active">Complété</span></td>
                                        <td>
                                            <button class="btn btn-sm">Détails</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#4586</td>
                                        <td>19/06/2023 - 14:15</td>
                                        <td>Carburant</td>
                                        <td>45,20 €</td>
                                        <td>SP95 - 28.45L</td>
                                        <td><span class="status status-active">Complété</span></td>
                                        <td>
                                            <button class="btn btn-sm">Détails</button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    
                    <?php elseif ($current_page === 'products'): ?>
                        <!-- PRODUITS -->
                        <div class="section-title">
                            <h2>Gestion des Produits</h2>
                            <a href="?page=products&action=add">
                                <button class="btn btn-success">+ Ajouter un produit</button>
                            </a>
                        </div>
                    
                        <div class="stat-card products">
                            <h3>Produits</h3>
                            <?php
                            $product_count = 0;
                            $out_of_stock = 0;
                            if (isset($products)) {
                                $product_count = count($products);
                                foreach ($products as $product) {
                                    if ($product['stock'] <= 0 && $product['stock'] !== null) $out_of_stock++;
                                }
                            }
                            ?>
                            <div class="value"><?php echo $product_count; ?></div>
                            <p><?php echo $out_of_stock; ?> en rupture</p>
                        </div>
                    
                        <div class="filters">
                            <form method="get" action="" id="filter-form">
                                <input type="hidden" name="page" value="products">
                                <?php if (isset($_GET['search'])): ?>
                                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
                                <?php endif; ?>
                                
                                <select name="category" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                                    <option value="">Toutes les catégories</option>
                                    <?php if (isset($categories) && !empty($categories)): ?>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                
                                <select name="status" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                                    <option value="">Tous les statuts</option>
                                    <option value="active" <?php echo (isset($_GET['status']) && $_GET['status'] == 'active') ? 'selected' : ''; ?>>Actif</option>
                                    <option value="out_of_stock" <?php echo (isset($_GET['status']) && $_GET['status'] == 'out_of_stock') ? 'selected' : ''; ?>>Rupture de stock</option>
                                    <option value="inactive" <?php echo (isset($_GET['status']) && $_GET['status'] == 'inactive') ? 'selected' : ''; ?>>Inactif</option>
                                </select>
                                
                                <select name="sort" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                                    <option value="name_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : ''; ?>>Trier par nom (A-Z)</option>
                                    <option value="name_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : ''; ?>>Trier par nom (Z-A)</option>
                                    <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>Prix croissant</option>
                                    <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Prix décroissant</option>
                                    <option value="stock_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'stock_asc') ? 'selected' : ''; ?>>Stock croissant</option>
                                    <option value="stock_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'stock_desc') ? 'selected' : ''; ?>>Stock décroissant</option>
                                </select>
                            </form>
                        </div>
                        
                    
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Prix</th>
                                    <th>Stock</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $products = getAllProducts();
                                if (!empty($products)): 
                                    foreach ($products as $product): 
                                        $imagePath = !empty($product['image']) ? 'images/' . htmlspecialchars($product['image']) : 'images/default-product.jpg';
                                ?>
                                    <tr>
                                        <td>P<?php echo sprintf('%03d', $product['id']); ?></td>
                                        <td>
                                            <div class="product-img">
                                                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 3px;">
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'Non catégorisé'); ?></td>
                                        <td><?php echo number_format($product['price'], 2, ',', ' '); ?> €</td>
                                        <td>
                                            <?php 
                                            if ($product['stock'] === null) {
                                                echo '∞';
                                            } else {
                                                echo (int)$product['stock']; 
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($product['status'] == 1): ?>
                                                <?php if ($product['stock'] > 0 || $product['stock'] === null): ?>
                                                    <span class="status status-active">Actif</span>
                                                <?php else: ?>
                                                    <span class="status status-inactive">Rupture</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="status status-inactive">Inactif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="?page=products&edit_product=<?php echo $product['id']; ?>">
                                                <button class="btn btn-sm btn-warning">Modifier</button>
                                            </a>
                                            <a href="?page=products&action=delete_product&id=<?php echo $product['id']; ?>" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit?');">
                                                <button class="btn btn-sm btn-danger">Supprimer</button>
                                            </a>
                                        </td>
                                    </tr>
                                <?php 
                                    endforeach; 
                                else: 
                                ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center;">Aucun produit trouvé</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    
                        <div class="pagination">
                            <div class="pagination-item">«</div>
                            <div class="pagination-item active">1</div>
                            <div class="pagination-item">2</div>
                            <div class="pagination-item">3</div>
                            <div class="pagination-item">»</div>
                        </div>
                    
                        <?php if (isset($_GET['edit_product']) || isset($_GET['action']) && $_GET['action'] === 'add'): ?>
                            <?php 
                            // Initialiser les données du formulaire
                            $form_data = [
                                'id' => '',
                                'name' => '',
                                'category_id' => '',
                                'price' => '',
                                'stock' => '',
                                'description' => '',
                                'status' => 1
                            ];
                            
                            // Si modification, charger les données
                            if (isset($_GET['edit_product']) && isset($edit_product)) {
                                $form_data = $edit_product;
                            }
                            ?>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><?php echo isset($_GET['edit_product']) ? 'Modifier' : 'Ajouter'; ?> un produit</h3>
                                </div>
                                <form method="post" action="?page=products" enctype="multipart/form-data">
                                    <?php if (isset($_GET['edit_product'])): ?>
                                        <input type="hidden" name="product_id" value="<?php echo $form_data['id']; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="form-row">
                                        <div class="form-col">
                                            <div class="form-group">
                                                <label for="product-name">Nom du produit</label>
                                                <input type="text" id="product-name" name="product_name" class="form-control" 
                                                       placeholder="Entrez le nom du produit" value="<?php echo htmlspecialchars($form_data['name']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-col">
                                            <div class="form-group">
                                                <label for="product-category">Catégorie</label>
                                                <select id="product-category" name="product_category" class="form-control">
                                                    <option value="">Sélectionnez une catégorie</option>
                                                    <?php if (isset($categories) && !empty($categories)): ?>
                                                        <?php foreach ($categories as $category): ?>
                                                            <option value="<?php echo $category['id']; ?>" 
                                                                    <?php echo ($form_data['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($category['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                            
                                    <div class="form-row">
                                        <div class="form-col">
                                            <div class="form-group">
                                                <label for="product-price">Prix (€)</label>
                                                <input type="number" id="product-price" name="product_price" class="form-control" 
                                                       placeholder="0.00" step="0.01" value="<?php echo $form_data['price']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-col">
                                            <div class="form-group">
                                                <label for="product-stock">Stock (laisser vide pour stock illimité)</label>
                                                <input type="number" id="product-stock" name="product_stock" class="form-control" 
                                                       placeholder="Quantité en stock" value="<?php echo $form_data['stock'] !== null ? $form_data['stock'] : ''; ?>">
                                            </div>
                                        </div>
                                    </div>
                            
                                    <div class="form-group">
                                        <label for="product-description">Description</label>
                                        <textarea id="product-description" name="product_description" class="form-control" rows="4" 
                                                  placeholder="Entrez une description du produit"><?php echo htmlspecialchars($form_data['description'] ?? ''); ?></textarea>
                                    </div>
                            
                                    <div class="form-row">
                                        <div class="form-col">
                                        <div class="form-group">
                                        <label>Image du produit</label>
                                    <div class="image-upload">
                                        <div class="upload-preview">
                                            <?php if (isset($form_data['image']) && !empty($form_data['image'])): ?>
                                                <img src="<?php echo htmlspecialchars($form_data['image']); ?>" alt="Image du produit">
                                            <?php else: ?>
                                                Cliquez ou glissez une image ici
                                            <?php endif; ?>
                                        </div>
                                        <input type="file" id="product-image" name="product_image" accept="image/*">
                                    </div>
</div>
                                        </div>
                                        <div class="form-col">
                                            <div class="form-group">
                                                <label>Statut du produit</label>
                                                <div class="form-control">
                                                    <label class="toggle-switch">
                                                        <input type="checkbox" name="product_status" <?php echo ($form_data['status'] == 1) ? 'checked' : ''; ?>>
                                                        <span class="toggle-slider"></span>
                                                    </label>
                                                    <span style="margin-left: 1rem;">Actif</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="product-discount">Remise (%)</label>
                                                <input type="number" id="product-discount" name="product_discount" class="form-control" 
                                                       placeholder="0" min="0" max="100" value="<?php echo $form_data['discount'] ?? 0; ?>">
                                            </div>
                                        </div>
                                    </div>
                            
                                    <div class="form-actions">
                                        <a href="?page=products">
                                            <button type="button" class="btn btn-danger">Annuler</button>
                                        </a>
                                        <button type="submit" name="product_submit" class="btn btn-success">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    
                    <?php elseif ($current_page === 'orders'): ?>
                        <!-- COMMANDES -->
                        <div class="section-title">
                            <h2>Historique des Commandes</h2>
                        </div>

                        <div class="stat-card orders">
                            <h3>Commandes</h3>
                            <?php
                            $order_count = 0;
                            $pending_orders = 0;
                            if (isset($orders)) {
                                $order_count = count($orders);
                                foreach ($orders as $order) {
                                    if ($order['status'] === 'pending') $pending_orders++;
                                }
                            }
                            ?>
                            <div class="value"><?php echo $order_count; ?></div>
                            <p><?php echo $pending_orders; ?> en cours</p>
                        </div>

                        <div class="filters">
                            <form method="get" action="" id="order-filter-form">
                                <input type="hidden" name="page" value="orders">
                                <?php if (isset($_GET['search'])): ?>
                                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
                                <?php endif; ?>
                                
                                <select name="status" class="filter-select" onchange="document.getElementById('order-filter-form').submit()">
                                    <option value="">Tous les statuts</option>
                                    <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Complété</option>
                                    <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>En cours</option>
                                    <option value="canceled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'canceled') ? 'selected' : ''; ?>>Annulé</option>
                                </select>
                                
                                <select name="period" class="filter-select" onchange="document.getElementById('order-filter-form').submit()">
                                    <option value="today" <?php echo (isset($_GET['period']) && $_GET['period'] == 'today') ? 'selected' : ''; ?>>Aujourd'hui</option>
                                    <option value="yesterday" <?php echo (isset($_GET['period']) && $_GET['period'] == 'yesterday') ? 'selected' : ''; ?>>Hier</option>
                                    <option value="week" <?php echo (isset($_GET['period']) && $_GET['period'] == 'week') ? 'selected' : ''; ?>>Cette semaine</option>
                                    <option value="month" <?php echo (isset($_GET['period']) && $_GET['period'] == 'month') ? 'selected' : ''; ?>>Ce mois</option>
                                    <option value="custom" <?php echo (isset($_GET['period']) && $_GET['period'] == 'custom') ? 'selected' : ''; ?>>Personnalisé</option>
                                </select>
                                
                                <select name="sort" class="filter-select" onchange="document.getElementById('order-filter-form').submit()">
                                    <option value="date_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date_desc') ? 'selected' : ''; ?>>Trier par date (récent)</option>
                                    <option value="date_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date_asc') ? 'selected' : ''; ?>>Trier par date (ancien)</option>
                                    <option value="amount_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'amount_desc') ? 'selected' : ''; ?>>Trier par montant (élevé)</option>
                                    <option value="amount_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'amount_asc') ? 'selected' : ''; ?>>Trier par montant (faible)</option>
                                </select>
                            </form>
                        </div>

                        <?php if (isset($_GET['view_order']) && isset($order_details)): ?>
                            <!-- Affichage du détail d'une commande -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Détails de la commande #<?php echo $order_details['id']; ?></h3>
                                </div>
                                <div class="card-content">
                                    <div class="form-row">
                                        <div class="form-col">
                                            <h4>Informations</h4>
                                            <p><strong>Date:</strong> <?php echo date('d/m/Y - H:i', strtotime($order_details['date_created'])); ?></p>
                                            <p><strong>Statut:</strong> 
                                                <?php if ($order_details['status'] === 'completed'): ?>
                                                    <span class="status status-active">Complété</span>
                                                <?php elseif ($order_details['status'] === 'pending'): ?>
                                                    <span class="status status-pending">En cours</span>
                                                <?php else: ?>
                                                    <span class="status status-inactive">Annulé</span>
                                                <?php endif; ?>
                                            </p>
                                            <p><strong>Type:</strong> <?php echo htmlspecialchars($order_details['order_type']); ?></p>
                                            <p><strong>Client:</strong> <?php echo $order_details['customer_id'] ? 'ID: '.$order_details['customer_id'] : 'Non enregistré'; ?></p>
                                            <p><strong>Méthode de paiement:</strong> <?php echo htmlspecialchars($order_details['payment_method'] ?? 'Non spécifié'); ?></p>
                                        </div>
                                        <div class="form-col">
                                            <h4>Récapitulatif</h4>
                                            <?php if (isset($order_items) && !empty($order_items)): ?>
                                                <?php foreach ($order_items as $item): ?>
                                                    <div class="summary-item">
                                                        <span><?php echo $item['quantity'].'x '.htmlspecialchars($item['product_name']); ?></span>
                                                        <span><?php echo number_format($item['price'] * $item['quantity'], 2, ',', ' '); ?> €</span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p>Aucun article trouvé pour cette commande.</p>
                                            <?php endif; ?>
                                            <div class="summary-item total">
                                                <span>Total</span>
                                                <span><?php echo number_format($order_details['total_amount'], 2, ',', ' '); ?> €</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <a href="?page=orders">
                                            <button type="button" class="btn btn-primary">Retour à la liste</button>
                                        </a>
                                        <button class="btn btn-warning">Imprimer Reçu</button>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Liste des commandes -->
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date et Heure</th>
                                        <th>Type</th>
                                        <th>Client</th>
                                        <th>Articles</th>
                                        <th>Total</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($orders) && !empty($orders)): ?>
                                        <?php foreach ($orders as $order): ?>
                                            <?php
                                            // Récupérer les articles de la commande
                                            $order_items = getOrderItems($order['id']);
                                            ?>
                                            <tr>
                                                <td>#<?php echo $order['id']; ?></td>
                                                <td>
                                                    <?php 
                                                    echo date('d/m/Y - H:i', strtotime($order['date_created'])); 
                                                    $time_diff = time() - strtotime($order['date_created']);
                                                    if ($time_diff < 86400) { // Moins de 24h
                                                        $hours = round($time_diff / 3600);
                                                        echo ' <span class="time-format">(il y a '.$hours.'h)</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($order['order_type']); ?></td>
                                                <td><?php echo $order['customer_id'] ? 'ID: '.$order['customer_id'] : '-'; ?></td>
                                                <td>
                                                    <?php if (!empty($order_items)): ?>
                                                        <?php foreach (array_slice($order_items, 0, 3) as $item): ?>
                                                            <div><?php echo $item['quantity'].'x '.htmlspecialchars($item['product_name']); ?></div>
                                                        <?php endforeach; ?>
                                                        <?php if (count($order_items) > 3): ?>
                                                            <div>...</div>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <div>-</div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo number_format($order['total_amount'], 2, ',', ' '); ?> €</td>
                                                <td>
                                                    <?php if ($order['status'] === 'completed'): ?>
                                                        <span class="status status-active">Complété</span>
                                                    <?php elseif ($order['status'] === 'pending'): ?>
                                                        <span class="status status-pending">En cours</span>
                                                    <?php else: ?>
                                                        <span class="status status-inactive">Annulé</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="?page=orders&view_order=<?php echo $order['id']; ?>">
                                                        <button class="btn btn-sm">Détails</button>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" style="text-align: center;">Aucune commande trouvée</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <div class="pagination">
                                <div class="pagination-item">«</div>
                                <div class="pagination-item active">1</div>
                                <div class="pagination-item">2</div>
                                <div class="pagination-item">3</div>
                                <div class="pagination-item">»</div>
                            </div>
                        <?php endif; ?>
                    
                    <?php elseif ($current_page === 'stats'): ?> 
                        <!-- STATISTIQUES -->
                        <div class="section-title">
                            <h2>Statistiques de Ventes</h2>
                        </div>

                        <div class="time-period-selector">
                            <form method="get" action="" id="stats-period-form">
                                <input type="hidden" name="page" value="stats">
                                <button type="submit" name="period" value="day" class="time-btn <?php echo (!isset($_GET['period']) || $_GET['period'] === 'day') ? 'active' : ''; ?>">Aujourd'hui</button>
                                <button type="submit" name="period" value="week" class="time-btn <?php echo (isset($_GET['period']) && $_GET['period'] === 'week') ? 'active' : ''; ?>">Cette semaine</button>
                                <button type="submit" name="period" value="month" class="time-btn <?php echo (isset($_GET['period']) && $_GET['period'] === 'month') ? 'active' : ''; ?>">Ce mois</button>
                                <button type="submit" name="period" value="year" class="time-btn <?php echo (isset($_GET['period']) && $_GET['period'] === 'year') ? 'active' : ''; ?>">Cette année</button>
                                <button type="button" class="time-btn <?php echo (isset($_GET['period']) && $_GET['period'] === 'custom') ? 'active' : ''; ?>">Personnalisé</button>
                            </form>
                        </div>

                        <div class="dashboard">
                            <div class="stat-card revenue">
                                <h3>Revenus totaux</h3>
                                <?php
                                $total_revenue = 0;
                                if (isset($daily_sales) && !empty($daily_sales)) {
                                    foreach ($daily_sales as $sale) {
                                        $total_revenue += $sale['daily_total'];
                                    }
                                }
                                ?>
                                <div class="value"><?php echo number_format($total_revenue, 2, ',', ' '); ?> €</div>
                                <p>+ 12% vs période précédente</p>
                            </div>
                            <div class="stat-card orders">
                                <h3>Commandes</h3>
                                <?php
                                $order_count = 0;
                                if (isset($orders)) {
                                    $order_count = count($orders);
                                }
                                ?>
                                <div class="value"><?php echo $order_count; ?></div>
                                <p>+ 8% vs période précédente</p>
                            </div>
                            <div class="stat-card">
                                <h3>Panier moyen</h3>
                                <?php
                                $avg_basket = 0;
                                if ($order_count > 0) {
                                    $avg_basket = $total_revenue / $order_count;
                                }
                                ?>
                                <div class="value"><?php echo number_format($avg_basket, 2, ',', ' '); ?> €</div>
                                <p>+ 3% vs période précédente</p>
                            </div>
                            <div class="stat-card">
                                <h3>Articles vendus</h3>
                                <?php
                                $total_items = 0;
                                if (isset($top_products) && !empty($top_products)) {
                                    foreach ($top_products as $product) {
                                        $total_items += $product['total_quantity'];
                                    }
                                }
                                ?>
                                <div class="value"><?php echo $total_items; ?></div>
                                <p>+ 15% vs période précédente</p>
                            </div>
                        </div>

                        <div class="chart-container">
                            <div class="chart-header">
                                <div class="chart-title">Évolution des ventes</div>
                            </div>
                            <div class="bar-chart">
                                <div class="chart-y-axis">
                                    <div>1500€</div>
                                    <div>1125€</div>
                                    <div>750€</div>
                                    <div>375€</div>
                                    <div>0€</div>
                                </div>
                                <div class="chart-grid">
                                    <div class="grid-line"></div>
                                    <div class="grid-line"></div>
                                    <div class="grid-line"></div>
                                    <div class="grid-line"></div>
                                    <div class="grid-line"></div>
                                </div>
                                
                                <?php
                                // Exemple de valeurs pour le graphique
                                $day_labels = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
                                $day_values = [170, 220, 180, 250, 290, 200, 110]; // Hauteurs en pixels
                                
                                // Pour chaque jour, afficher une barre
                                foreach ($day_labels as $index => $day): 
                                    $height = $day_values[$index];
                                ?>
                                <div class="bar-container">
                                    <div class="bar" style="height: <?php echo $height; ?>px;"></div>
                                    <div class="bar-label"><?php echo $day; ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-col">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <div class="chart-title">Répartition des ventes par catégorie</div>
                                    </div>
                                    <div class="pie-chart-container">
                                        <div class="pie-chart"></div>
                                        <div class="pie-legend">
                                            <?php
                                            $categories_data = [
                                                'Carburants' => 25,
                                                'Lavages' => 30,
                                                'Produits' => 20,
                                                'Recharges' => 25
                                            ];
                                            
                                            $colors = ['primary', 'success', 'warning', 'info'];
                                            $i = 0;
                                            
                                            foreach ($categories_data as $cat_name => $percentage):
                                                $color = $colors[$i % count($colors)];
                                                $i++;
                                            ?>
                                            <div class="legend-item">
                                                <div class="color-indicator color-<?php echo $color; ?>"></div>
                                                <div class="legend-text">
                                                    <span class="legend-label"><?php echo htmlspecialchars($cat_name); ?></span>
                                                    <span class="legend-value"><?php echo $percentage; ?>%</span>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <div class="chart-title">Heures d'affluence</div>
                                    </div>
                                    <div class="bar-chart">
                                        <div class="chart-y-axis">
                                            <div>30</div>
                                            <div>20</div>
                                            <div>10</div>
                                            <div>0</div>
                                        </div>
                                        <div class="chart-grid">
                                            <div class="grid-line"></div>
                                            <div class="grid-line"></div>
                                            <div class="grid-line"></div>
                                            <div class="grid-line"></div>
                                        </div>
                                        
                                        <?php
                                        // Exemple de valeurs pour les heures d'affluence
                                        $hour_values = [30, 60, 50, 40, 90, 85, 45, 40, 55, 70, 30, 20]; // Hauteurs en pixels
                                        $hour_labels = ['8h', '9h', '10h', '11h', '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h'];
                                        
                                        foreach ($hour_labels as $index => $hour): 
                                            $height = $hour_values[$index];
                                        ?>
                                        <div class="bar-container">
                                            <div class="bar" style="height: <?php echo $height; ?>px;"></div>
                                            <div class="bar-label"><?php echo $hour; ?></div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-title">
                            <h2>Produits les plus vendus</h2>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Catégorie</th>
                                    <th>Prix unitaire</th>
                                    <th>Quantité vendue</th>
                                    <th>Chiffre d'affaires</th>
                                    <th>% des ventes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($top_products) && !empty($top_products)): ?>
                                    <?php 
                                    $total_sales_amount = 0;
                                    foreach ($top_products as $product) {
                                        $total_sales_amount += $product['total_sales'];
                                    }
                                    
                                    foreach ($top_products as $product): 
                                        $percentage = ($total_sales_amount > 0) ? round(($product['total_sales'] / $total_sales_amount) * 100) : 0;
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                        <td><?php echo number_format($product['price'], 2, ',', ' '); ?> €</td>
                                        <td><?php echo $product['total_quantity']; ?></td>
                                        <td><?php echo number_format($product['total_sales'], 2, ',', ' '); ?> €</td>
                                        <td><?php echo $percentage; ?>%</td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Données d'exemple -->
                                    <tr>
                                        <td>Essence sans plomb 95</td>
                                        <td>carburants</td>
                                        <td>2,50 TND</td>
                                        <td>142</td>
                                        <td>355,00 TND</td>
                                        <td>18%</td>
                                    </tr>
                                    <tr>
                                        <td>Formule Premium</td>
                                        <td>Lavahes</td>
                                        <td>3,90 TND</td>
                                        <td>87</td>
                                        <td>339,30 TND</td>
                                        <td>15%</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    

                        
                    <?php elseif ($current_page === 'settings'): ?>
                        <!-- PARAMÈTRES -->
                        <div class="section-title">
                            <h2>Paramètres Généraux</h2>
                        </div>

                        <div class="settings-grid">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Heures d'ouverture</h3>
                                </div>
                                <form method="post" action="?page=settings">
                                    <?php
                                    $days = [
                                        'monday' => 'Lundi',
                                        'tuesday' => 'Mardi',
                                        'wednesday' => 'Mercredi',
                                        'thursday' => 'Jeudi',
                                        'friday' => 'Vendredi',
                                        'saturday' => 'Samedi',
                                        'sunday' => 'Dimanche'
                                    ];
                                    
                                    foreach ($days as $day_key => $day_name):
                                        $hours = '08:00-19:00'; // Valeurs par défaut
                                        
                                        if (isset($settings['hours_'.$day_key])) {
                                            $hours = $settings['hours_'.$day_key];
                                        }
                                        
                                        list($open, $close) = explode('-', $hours);
                                    ?>
                                    <div class="form-group">
                                        <label><?php echo $day_name; ?></label>
                                        <div class="form-row">
                                            <div class="form-col">
                                                <input type="time" name="<?php echo $day_key; ?>_open" class="form-control" value="<?php echo $open; ?>">
                                            </div>
                                            <div class="form-col">
                                                <input type="time" name="<?php echo $day_key; ?>_close" class="form-control" value="<?php echo $close; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <div class="form-actions">
                                        <button type="submit" name="settings_submit" class="btn btn-success">Enregistrer</button>
                                    </div>
                                </form>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Informations du kiosque</h3>
                                </div>
                                <form method="post" action="?page=settings">
                                    <div class="form-group">
                                        <label for="shop-name">Nom du kiosque</label>
                                        <input type="text" id="shop-name" name="shop_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['shop_name'] ?? 'Mon Kiosque Parisien'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="shop-address">Adresse</label>
                                        <input type="text" id="shop-address" name="shop_address" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['shop_address'] ?? '123 Avenue des Champs-Élysées'); ?>">
                                    </div>
                                    <div class="form-row">
                                        <div class="form-col">
                                            <div class="form-group">
                                                <label for="shop-postal">Code Postal</label>
                                                <input type="text" id="shop-postal" name="shop_postal" class="form-control" 
                                                       value="<?php echo htmlspecialchars($settings['shop_postal'] ?? '75008'); ?>">
                                            </div>
                                        </div>
                                        <div class="form-col">
                                            <div class="form-group">
                                                <label for="shop-city">Ville</label>
                                                <input type="text" id="shop-city" name="shop_city" class="form-control" 
                                                       value="<?php echo htmlspecialchars($settings['shop_city'] ?? 'Paris'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="shop-phone">Téléphone</label>
                                        <input type="tel" id="shop-phone" name="shop_phone" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['shop_phone'] ?? '01 23 45 67 89'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="shop-email">Email</label>
                                        <input type="email" id="shop-email" name="shop_email" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['shop_email'] ?? 'contact@monkiosque.fr'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="shop-logo">Logo</label>
                                        <div class="image-upload">
                                            <div class="upload-preview" style="background-color: var(--secondary); color: white; display: flex; justify-content: center; align-items: center; font-weight: bold; font-size: 1.5rem;">
                                                Fuel<span style="color: var(--primary);">Admin</span>
                                            </div>
                                            <input type="file" id="shop-logo" name="shop_logo" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" name="settings_submit" class="btn btn-success">Enregistrer</button>
                                    </div>
                                </form>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Options de paiement</h3>
                                </div>
                                <form method="post" action="?page=settings">
                                    <?php
                                    $payment_methods = [];
                                    if (isset($settings['payment_methods'])) {
                                        $payment_methods = explode(',', $settings['payment_methods']);
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label>Méthodes de paiement acceptées</label>
                                        <div class="form-control" style="display: flex; align-items: center;">
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="payment_cash" <?php echo in_array('cash', $payment_methods) ? 'checked' : ''; ?>>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span style="margin-left: 1rem;">Espèces</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-control" style="display: flex; align-items: center;">
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="payment_card" <?php echo in_array('card', $payment_methods) ? 'checked' : ''; ?>>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span style="margin-left: 1rem;">Carte Bancaire</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-control" style="display: flex; align-items: center;">
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="payment_contactless" <?php echo in_array('contactless', $payment_methods) ? 'checked' : ''; ?>>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span style="margin-left: 1rem;">Sans contact</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-control" style="display: flex; align-items: center;">
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="payment_tickets" <?php echo in_array('tickets', $payment_methods) ? 'checked' : ''; ?>>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span style="margin-left: 1rem;">Tickets Restaurant</span>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" name="settings_submit" class="btn btn-success">Enregistrer</button>
                                    </div>
                                </form>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Gestion des catégories</h3>
                                </div>
                                <div class="form-group">
                                    <label>Catégories actuelles</label>
                                    <div>
                                        <?php if (isset($categories) && !empty($categories)): ?>
                                            <?php foreach ($categories as $category): ?>
                                                <span class="category-tag"><?php echo htmlspecialchars($category['name']); ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="category-tag">Aucune catégorie trouvée</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <form method="post" action="?page=settings">
                                    <div class="form-group">
                                        <label for="new-category">Ajouter une catégorie</label>
                                        <div class="form-row">
                                            <div class="form-col" style="flex: 3;">
                                                <input type="text" id="new-category" name="category_name" class="form-control" placeholder="Nom de la catégorie">
                                            </div>
                                            <div class="form-col" style="flex: 1;">
                                                <button type="submit" name="category_submit" class="btn btn-success" style="width: 100%;">Ajouter</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Catégorie</th>
                                            <th>Nb produits</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($categories) && !empty($categories)): ?>
                                            <?php foreach ($categories as $category): ?>
                                                <?php
                                                // Compter le nombre de produits dans cette catégorie
                                                $product_count = 0;
                                                if (isset($products)) {
                                                    foreach ($products as $product) {
                                                        if ($product['category_id'] == $category['id']) {
                                                            $product_count++;
                                                        }
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                                    <td><?php echo $product_count; ?></td>
                                                    <td>
                                                        <a href="?page=settings&edit_category=<?php echo $category['id']; ?>">
                                                            <button class="btn btn-sm btn-warning">Modifier</button>
                                                        </a>
                                                        <a href="?page=settings&action=delete_category&id=<?php echo $category['id']; ?>" 
                                                          onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie?');">
                                                            <button class="btn btn-sm btn-danger">Supprimer</button>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" style="text-align: center;">Aucune catégorie trouvée</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($notification)): ?>
    <div class="notification show" id="notification"><?php echo $notification; ?></div>
    <?php endif; ?>
    <?php endif; ?>
</body>
</html>