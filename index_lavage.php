<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index_sign_in.php");
    exit;
}

require_once 'config.php';

// Récupérer les produits de la catégorie 'Lavage auto' (category_id = 2)
$sql = "SELECT p.* 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE c.name = 'Lavage auto' AND p.status = 1 
        ORDER BY p.price ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$lavage_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EnergyFuel - Service de Lavage Auto</title>
    <link rel="stylesheet" href="style_lavage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="script_lavage.js" defer></script>
    <script src="script_time.js" defer></script>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <div class="header-top">
                <div class="current-time" id="currentTime"></div>
            </div>
            <div class="logo">
                <a href="index_acceuil.php"><span>Energy</span><span>Fuel</span></a>
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
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="container">
                <h1>Service de Lavage Auto Premium</h1>
                <p>Redonnez à votre véhicule son éclat d'origine avec nos formules de lavage professionnel</p>
                <a href="#formules" class="btn">Découvrir nos formules</a>
            </div>
        </div>
    </section>

    <!-- Service Details Section -->
    <section class="service-details">
        <div class="container">
            <div class="service-intro">
                <h2>Pourquoi choisir notre service de lavage auto ?</h2>
                <p>Chez EnergyFuel, nous utilisons des techniques de lavage avancées et des produits de haute qualité pour garantir un résultat impeccable. Notre équipe de professionnels prend soin de votre véhicule comme s'il s'agissait du leur, en accordant une attention particulière à chaque détail.</p>
            </div>
        </div>
    </section>

    <!-- Service Details Section (inchangé) -->
    <section class="service-details">
        <div class="container">
            <div class="service-intro">
                <h2>Pourquoi choisir notre service de lavage auto ?</h2>
                <p>Chez EnergyFuel, nous utilisons des techniques de lavage avancées et des produits de haute qualité pour garantir un résultat impeccable.</p>
            </div>
        </div>
    </section>

    <!-- Formules Section (dynamique) -->
    <section class="formules" id="formules">
        <div class="container">
            <h2>Nos formules de lavage</h2>
            <div class="produits-grid">
                <?php foreach ($lavage_products as $p): ?>
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
    <!-- Process Section -->
    <section class="process">
        <div class="container">
            <h2>Notre processus de lavage</h2>
            <div class="process-steps">
                <!-- Step 1 -->
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Pré-lavage</h3>
                    <p>Application d'un produit dégraissant pour éliminer les saletés tenaces et préserver la peinture lors du lavage principal.</p>
                </div>
                
                <!-- Step 2 -->
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Lavage principal</h3>
                    <p>Lavage en profondeur avec des produits de qualité et des techniques douces pour protéger la carrosserie.</p>
                </div>
                
                <!-- Step 3 -->
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Traitement spécifique</h3>
                    <p>Nettoyage des jantes, traitement des plastiques et des vitres pour un résultat impeccable.</p>
                </div>
                
                <!-- Step 4 -->
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Finition</h3>
                    <p>Séchage minutieux, polissage et application de produits de protection pour un éclat durable.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <h2>Ce que disent nos clients</h2>
            <div class="testimonial-cards">
                <!-- Testimonial 1 -->
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        <p>Service impeccable ! Ma voiture n'a jamais été aussi propre. Je recommande la formule Premium qui offre un excellent rapport qualité-prix.</p>
                    </div>
                    <div class="client-info">
                        <img src="images/user.jpg" alt="Client">
                        <div class="client-name">
                            <h4>Sophie Martin</h4>
                            <p>Cliente fidèle</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        <p>J'ai opté pour la formule Deluxe pour mon SUV qui avait grand besoin d'un rafraîchissement. Résultat bluffant, comme neuf !</p>
                    </div>
                    <div class="client-info">
                        <img src="images/user.jpg" alt="Client">
                        <div class="client-name">
                            <h4>Thomas Dubois</h4>
                            <p>Client régulier</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        <p>Personnel très professionnel et attentif aux détails. Le service est rapide mais sans compromis sur la qualité. Je reviendrai !</p>
                    </div>
                    <div class="client-info">
                        <img src="images/user.jpg" alt="Client">
                        <div class="client-name">
                            <h4>Émilie Rousseau</h4>
                            <p>Nouvelle cliente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq">
        <div class="container">
            <h2>Questions fréquentes</h2>
            
            <div class="faq-item">
                <div class="faq-question">Combien de temps dure un lavage complet ?</div>
                <div class="faq-answer">
                    <p>La durée varie selon la formule choisie. Comptez environ 30 minutes pour la formule Basique, 1 heure pour la formule Premium et 1h30 à 2h pour la formule Deluxe. Nous vous conseillons de réserver à l'avance pour garantir votre créneau.</p>
                </div>
            </div> 
            
            <div class="faq-item">
                <div class="faq-question">Quels types de véhicules acceptez-vous ?</div>
                <div class="faq-answer">
                    <p>Nous traitons tous les types de véhicules : citadines, berlines, SUV, 4x4, utilitaires et même camping-cars (avec supplément pour les grands véhicules). N'hésitez pas à nous contacter pour connaître les tarifs spécifiques.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">Utilisez-vous des produits écologiques ?</div>
                <div class="faq-answer">
                    <p>Oui, nous utilisons principalement des produits biodégradables et respectueux de l'environnement. Notre station de lavage est également équipée d'un système de récupération et de filtration de l'eau pour limiter notre impact environnemental.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">Puis-je attendre pendant que ma voiture est lavée ?</div>
                <div class="faq-answer">
                    <p>Absolument ! Nous disposons d'un espace d'attente confortable avec WiFi gratuit, boissons chaudes et magazines. Vous pouvez également profiter de notre espace boutique pour découvrir nos produits d'entretien automobile.</p>
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