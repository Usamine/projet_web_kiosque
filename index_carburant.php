<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index_sign_in.php");
    exit;
}

require_once 'config.php';

// Récupérer les produits de la catégorie 'Carburants' (category_id = 3)
$sql = "SELECT p.* 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE c.name = 'Carburants' AND p.status = 1 
        ORDER BY p.price ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$carburant_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EnergyFuel - Carburants</title>
    <link rel="stylesheet" href="style_carburant.css">
    <script src="script_carburant.js" defer></script>
    <script src="script_time.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
        <div class="header-top">
            <div class="current-time" id="currentTime"></div>
        </div>
            <div class="logo">
                <a href="index.html"><span>Energy</span><span>Fuel</span></a>
            </div>
            <nav>
                <ul>
                    <li><a href="index_acceuil.php">Accueil</a></li>
                    <li><a href="index_service.php">Services</a></li>
                    <li><a href="index_about_us.php">À propos</a></li>
                    <li>
                        <div class="user-dropdown">
                            <div class="user-icon <?php echo isset($_SESSION['email']) ? 'connected' : 'disconnected'; ?>">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="dropdown-content">
                                <?php if (isset($_SESSION['email'])): ?>
                                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a>
                                <?php else: ?>
                                    <a href="index_sign_in.php"><i class="fas fa-sign-in-alt"></i> Connecter</a>
                                    <a href="index_sign_up.php"><i class="fas fa-user-plus"></i> Inscrivez-vous</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="panier.php" class="cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-badge" id="cart-badge">0</span>
                        </a>
                    </li>
                    <li>
                        <a href="panier.php" class="cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-badge" id="cart-badge">0</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="container">
                <h1>Nos Carburants de Qualité</h1>
                <p>Des carburants performants pour votre véhicule</p>
                <a href="#carburants" class="btn">Voir nos carburants</a>
            </div>
        </div>
    </section>

    <!-- Carburants Section -->
    <section class="carburants" id="carburants">
        <div class="container">
            <h2>Nos carburants</h2>
            <div class="produits-grid">
                <?php foreach ($carburant_products as $p): ?>
                    <div class="produit-card">
                        <?php if ($p['discount'] > 0): ?>
                            <div class="produit-badge">Promo</div>
                        <?php endif; ?>
                        <img src="images/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                        <div class="produit-info">
                            <h3><?= htmlspecialchars($p['name']) ?></h3>
                            <p class="produit-desc"><?= htmlspecialchars($p['description']) ?></p>
                            <div class="produit-footer">
                                <?php if ($p['discount'] > 0): ?>
                                    <?php $prixPromo = $p['price'] * (1 - $p['discount'] / 100); ?>
                                    <span class="prix-promo"><?= number_format($prixPromo, 2, ',', ' ') ?> €</span>
                                    <span class="prix-ancien"><?= number_format($p['price'], 2, ',', ' ') ?> €</span>
                                <?php else: ?>
                                    <span class="prix"><?= number_format($p['price'], 2, ',', ' ') ?> €</span>
                                <?php endif; ?>
                                <a href="#" class="btn-ajouter add-to-cart"
                                   data-product-id="<?= $p['id'] ?>"
                                   data-product-name="<?= htmlspecialchars($p['name']) ?>"
                                   data-product-price="<?= number_format($p['discount'] > 0 ? $prixPromo : $p['price'], 2) ?>">
                                    <i class="fas fa-cart-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Avantages carburants -->
    <section class="avantages-carburant">
        <div class="container">
            <h2>Les avantages EnergyFuel</h2>
            
            <div class="avantages-list">
                <div class="avantage-item">
                    <div class="avantage-number">1</div>
                    <div class="avantage-content">
                        <h3>Additifs haute performance</h3>
                        <p>Nos carburants contiennent des additifs spéciaux qui nettoient le moteur, réduisent la consommation et améliorent les performances.</p>
                    </div>
                </div>
                
                <div class="avantage-item">
                    <div class="avantage-number">2</div>
                    <div class="avantage-content">
                        <h3>Programme de fidélité</h3>
                        <p>Avec notre carte fidélité, cumulez des points à chaque plein et bénéficiez de réductions et avantages exclusifs.</p>
                    </div>
                </div>
                
                <div class="avantage-item">
                    <div class="avantage-number">3</div>
                    <div class="avantage-content">
                        <h3>Qualité certifiée</h3>
                        <p>Tous nos carburants répondent aux normes les plus strictes et sont régulièrement contrôlés pour garantir leur qualité.</p>
                    </div>
                </div>
                
                <div class="avantage-item">
                    <div class="avantage-number">4</div>
                    <div class="avantage-content">
                        <h3>Service rapide</h3>
                        <p>Nos stations sont conçues pour un service efficace, avec des pistes larges et un personnel disponible pour vous aider.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- FAQ -->
    <section class="faq">
        <div class="container">
            <h2>Questions fréquentes</h2>
            
            <div class="faq-item">
                <div class="faq-question">Quelle est la différence entre l'essence 95 et 98 ?</div>
                <div class="faq-answer">
                    <p>L'essence 98 a un indice d'octane plus élevé que le 95, ce qui la rend plus résistante à l'auto-allumage. Elle est recommandée pour les moteurs hautes performances ou turbocompressés. Notre essence 98 contient également des additifs nettoyants plus concentrés pour une meilleure protection du moteur.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">Puis-je utiliser votre diesel pour mon véhicule ancien ?</div>
                <div class="faq-answer">
                    <p>Oui, notre diesel haute performance est compatible avec tous les véhicules diesel, y compris les modèles anciens. Il contient des additifs qui protègent le système d'injection et nettoient les dépôts, ce qui est particulièrement bénéfique pour les moteurs plus âgés.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">Comment fonctionne la recharge électrique ?</div>
                <div class="faq-answer">
                    <p>Nos bornes de recharge rapide permettent de recharger la plupart des véhicules électriques à 80% en 30 minutes environ. Il vous suffit de brancher votre véhicule, de scanner votre carte EnergyFuel ou de payer par carte bancaire, et la recharge démarre automatiquement.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">Comment obtenir la carte fidélité EnergyFuel ?</div>
                <div class="faq-answer">
                    <p>Vous pouvez demander votre carte fidélité gratuitement dans n'importe quelle station EnergyFuel en présentant une pièce d'identité. Vous pouvez également vous inscrire en ligne et recevoir votre carte par courrier sous 7 jours ou l'ajouter directement à votre application mobile.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <!-- Column 1 -->
                <div class="footer-column">
                    <h3>À propos d'EnergyFuel</h3>
                    <p>EnergyFuel est votre partenaire de confiance pour tous vos besoins automobiles, offrant des services de qualité supérieure depuis plus de 15 ans.</p>
                </div>
                
                <!-- Column 2 -->
                <div class="footer-column">
                    <h3>Nos services</h3>
                    <ul class="footer-links">
                        <li><a href="index_lavage.php">Lavage Auto</a></li>
                        <li><a href="index_produit.php">Produits</a></li>
                        <li><a href="index_carburant.php">Carburant</a></li>
                    </ul>
                </div>
                
                <!-- Column 3 -->
                <div class="footer-column">
                    <h3>Contact</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Avenue des Énergies, 75001 Paris</li>
                        <li><i class="fas fa-phone"></i> +33 1 23 45 67 89</li>
                        <li><i class="fas fa-envelope"></i> contact@energyfuel.com</li>
                        <li><i class="fas fa-clock"></i> Lun-Sam: 8h-20h | Dim: 9h-18h</li>
                    </ul>
                </div>
                
                <!-- Column 4 -->
                <div class="footer-column">
                    <h3>Suivez-nous</h3>
                    <ul class="social-links">
                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                    </ul>
                    <div style="margin-top: 20px;">
                        <a href="#" class="btn">Contactez-nous</a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 EnergyFuel. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>