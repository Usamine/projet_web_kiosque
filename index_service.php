<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index_sign_in.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EnergyFuel - Nos services</title>
    <script src="script_service.js" defer></script>
    <script src="script_time.js" defer></script>
    <link rel="stylesheet" href="style_service.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="header-top">
            <div class="current-time" id="currentTime"></div>
        </div>
        <div class="logo">Energy<span>Fuel</span></div>
        <nav>
            <ul>
                <li><a href="index_acceuil.php" id="navHome">Accueil</a></li>
                <li><a href="index_service.php" id="navServices">Services</a></li>
                <li><a href="index_about_us.php" id="navAccount">about us</a></li>
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
            </ul>
        </nav>
    </header>
    
    <!-- Page Title -->
    <div class="page-title">
        <div class="container">
            <h1>Nos Services</h1>
            <p>Sélectionnez le service dont vous avez besoin</p>
        </div>
    </div>
    <!-- Service Details -->
    <section class="service-details container">
        <h2 class="section-title">Détails des services</h2>
        
        <!-- Lavage Auto Detail -->
        <div id="lavage-details" class="detail-card">
            <div class="detail-image" style="background-image: url(/projet_web_kiosque/images/Cleaning.jpg)"></div>
            <div class="detail-content">
                <h3>Lavage Auto</h3>
                <p>Notre service de lavage auto propose plusieurs formules adaptées à vos besoins. Notre équipe professionnelle utilise des produits de haute qualité pour garantir un résultat impeccable.</p>
                <ul class="features-list">
                    <li>Lavage extérieur complet</li>
                    <li>Nettoyage intérieur approfondi</li>
                    <li>Traitement des jantes et pneus</li>
                    <li>Polissage et cirage</li>
                    <li>Désodorisation de l'habitacle</li>
                </ul>
                <p class="price-tag">À partir de 25€</p>
                <a href="index_lavage.php" class="btn">Prendre rendez-vous</a>
            </div>
        </div>
        
        <!-- Produits Detail -->
        <div id="produits-details" class="detail-card">
            <div class="detail-image" style="background-image: url(/projet_web_kiosque/images/magasin.jpg)"></div>
            <div class="detail-content">
                <h3>Produits</h3>
                <p>Nous proposons une large gamme de produits de qualité pour l'entretien et l'amélioration des performances de votre véhicule.</p>
                <ul class="features-list">
                    <li>Huiles et lubrifiants</li>
                    <li>Produits de nettoyage</li>
                    <li>Accessoires et pièces détachées</li>
                    <li>Produits d'entretien spécialisés</li>
                    <li>Additifs pour carburant</li>
                </ul>
                <p class="price-tag">Prix variés</p>
                <a href="index_produit.php" class="btn">Voir notre catalogue</a>
            </div>
        </div>
        
        <!-- Carburant Detail -->
        <div id="carburant-details" class="detail-card">
            <div class="detail-image" style="background-image: url(/projet_web_kiosque/images/carbur.jpg)"></div>
            <div class="detail-content">
                <h3>Carburant</h3>
                <p>Nos carburants de qualité supérieure sont conçus pour optimiser les performances de votre moteur tout en réduisant la consommation.</p>
                <ul class="features-list">
                    <li>Essence sans plomb 95 et 98</li>
                    <li>Diesel haute performance</li>
                    <li>Carburants additifs spéciaux</li>
                    <li>Stations de recharge électrique</li>
                    <li>Système de fidélité avec points cumulables</li>
                </ul>
                <p class="price-tag">Prix du marché</p>
                <a href="index_carburant.php" class="btn">Remplir le reservoir</a>
            </div>
        </div>
    </section>
    
    <!-- FAQ Section -->
    <section class="faq-section container">
        <h2 class="section-title">Questions fréquentes</h2>
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">Comment prendre rendez-vous pour un lavage auto ?</div>
                <div class="faq-answer">
                    <p>Vous pouvez prendre rendez-vous pour un lavage auto en ligne via notre site web, par téléphone au +216 27 312 507 ou directement à notre station.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">Quels moyens de paiement acceptez-vous ?</div>
                <div class="faq-answer">
                    <p>Nous acceptons les paiements par carte bancaire, espèces et via notre application mobile. Les clients réguliers peuvent également bénéficier d'un système de facturation mensuelle.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">Vos stations sont-elles ouvertes 24h/24 ?</div>
                <div class="faq-answer">
                    <p>Oui, nos stations-service sont ouvertes 24h/24 et 7j/7. Cependant, certains services comme le lavage auto et la maintenance ont des horaires spécifiques que vous pouvez consulter sur chaque station.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">Comment fonctionne votre programme de fidélité ?</div>
                <div class="faq-answer">
                    <p>Notre programme de fidélité vous permet de cumuler des points à chaque achat. Ces points peuvent ensuite être échangés contre des réductions, des lavages gratuits ou des produits de notre catalogue.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Energy<span>Fuel</span></h3>
                    <p>Votre partenaire pour tous les services automobiles. Nous sommes là pour vous offrir des services de qualité 24h/24 et 7j/7.</p>
                </div>
                
                <div class="footer-column">
                    <h3>Liens rapides</h3>
                    <a href="index_acceuil.php">Accueil</a>
                    <a href="index_service.php">Services</a>
                    <a href="index_classement.php">Classement</a>
                    <a href="index_about_us.php">À propos de nous</a>
                    <a href="#">Contact</a>
                </div>
                
                <div class="footer-column">
                    <h3>Services</h3>
                    <a href="#lavage-details">Lavage Auto</a>
                    <a href="#produits-details">Produits</a>
                    <a href="#carburant-details">Carburant</a>
                </div>
                
                <div class="footer-column">
                    <h3>Contact</h3>
                    <p>5051 Moknine, Monastir</p>
                    <p>Téléphone: (+216) 27 312 507</p>
                    <p>Email: haythem.idi@ensi-uma.tn</p>
                    <div class="social-links">
                        <a href="#"><img src="images/facebook.png" alt="Facebook"></a>
                        <a href="#"><img src="images/instagram.png" alt="Instagram"></a>
                        <a href="#"><img src="images/twitter.png" alt="Twitter"></a>
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