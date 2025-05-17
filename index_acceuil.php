<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php 
        // Affiche l'email de l'utilisateur connecté comme titre
        echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) . " - EnergyFuel" : "EnergyFuel - Station Service"; 
        ?>
    </title>
    <script src="script_time.js" defer></script>
    <link rel="stylesheet" href="style_acceuil.css">
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
<!-- acceuil -->
<!--entete -->
    <section class="hero-section">
        <div class="overlay"></div>
        <div class="content">
            <h1>Bienvenue chez EnergyFuel</h1>
            <p>Connectez-vous pour accéder à nos services</p>
        </div>
    </section>
    <div id="authSection" class="container">
        <section id="service">
            <h2>Explorer nos Services pour vous</h2>
            <div class="services">
                <div class="service-item">
                    <h3>lavage professionel pour les vehicules</h3>
                    <p>produits de qualité pour une vehicule propre</p>
                </div>
                <div class="service-item">
                    <h3>produits et accessoires</h3>
                    <p>produits de qualité avec des prix raisonnable</p>
                </div>
                <div class="service-item">
                    <h3>carburant de qualité</h3>
                    <p>carburant de qualité qui offre à votre vehicule le meilleur rendement</p>
                </div>
            </div>
        </section>
        <section>
            <h2>Acceuil vocal</h2>
            <audio controls>
                <source src="images/27 févr., 16.36_.m4a" type="audio/mpeg">
                Votre navigateur ne supporte pas l'élément audio.
            </audio>
        </section>
<!-- we are the best in gas-->
        <div class="best-features">
            <div class="image-container">
                <img src="images/gas-station-8626683_1920.jpg" alt="Station-service">
            </div>
            <div class="features">
                <div class="feature">
                    <h2>Service Rapide</h2>
                    <p>Faites le plein en quelques minutes avec notre service optimisé.</p>
                </div>
                <div class="feature">
                    <h2>Carburants de Qualité</h2>
                    <p>Nous offrons des carburants premium pour améliorer la performance de votre véhicule.</p>
                </div>
                <div class="feature">
                    <h2>Ouvert 24/7</h2>
                    <p>Profitez d'un accès à nos stations à toute heure du jour et de la nuit.</p>
                </div>
            </div>
        </div>
<!-- we are the best in lavage-->
        <div class="best-features">
            <div class="features">
                <div class="feature">
                    <h2>Nettoyage Professionnel</h2>
                    <p>Confiez le lavage de votre véhicule à nos experts pour un résultat impeccable.</p>
                </div>
                <div class="feature">
                    <h2>Produits de Qualité</h2>
                    <p>Nous utilisons des produits de nettoyage de qualité pour protéger votre véhicule.</p>
                </div>
                <div class="feature">
                    <h2>Service Personnalisé</h2>
                    <p>Nos équipes sont à votre écoute pour répondre à vos besoins spécifiques.</p>
                </div>
            </div>
            <div class="image-container">
                <img src="images/car-wash-1619823_1920.jpg" alt="Lavage-auto">
            </div>
        </div>
<!-- plus d'infos -->
        <section id="more-info">
            <h2>Rejoignez-nous pour des avantages exclusifs</h2>
        </section>
    </div>
<!--our team  -->
<section class="testimonials">
    <h1>voici notre équipe</h1>
    <p>cette équipe fait tous pour vous offrir les meilleurs services 24/7</p>
        <div class="testimonials-container">
            <div class="testimonial-card">
                <img src="images/1000167349.jpg" alt="Author 1" class="author-image">
                <h2>Amine Harrabi</h2>
                <p>Gérant de la Station</p>
                <p><strong>Passionné </strong>par le secteur de l’énergie, Amine supervise les opérations quotidiennes de la station. Avec plus de 10 ans d'expérience, il s'assure que chaque service — du ravitaillement au lavage auto — fonctionne efficacement et offre une expérience client irréprochable.</p>
            </div>
            <div class="testimonial-card">
                <img src="images/1000167348.jpg" alt="Author 2" class="author-image">
                <h2>Ilyess Saddi</h2>
                <p>Responsable des Services Clients</p>
                <p><strong>Expert </strong> en relation client, Ilyess est le visage accueillant de la station. il gère les réservations, les paiements et le suivi des clients fidèles. Son objectif ? Offrir un service personnalisé et rapide à chaque visite.</p>
            </div>
            <div class="testimonial-card">
                <img src="images/1000167346.jpg" alt="Author 3" class="author-image">
                <h2>Haythem Idi</h2>
                <p>Chef des Opérations Techniques</p>
                <p><strong>Spécialiste </strong> des infrastructures énergétiques, Haythem s'assure que tous les équipements — pompes à carburant, bornes électriques, et installations de lavage — fonctionnent de manière optimale. Il joue un rôle clé dans la modernisation de la station.</p>
            </div>
        </div>
    </section>
<!--contact-->
<section class="contact-us">
    <h3>contacter nous aujourd'hui!</h3>
    <h1>Contacter nous</h1>
    <p>Vous avez des questions ou des commentaires ? N'hésitez
 pas à nous contacter pour obtenir plus d'informations sur nos services ou pour partager vos suggestions.</p>
    <div class="contact-details">
      <div class="contact-card">
        <i class="icon">📧</i>
        <h2>Email</h2>
        <a href="mailto:haythem.idi@ensi-uma.tn">haythem.idi@ensi-uma.tn</a>
      </div>
  
      <div class="contact-card">
        <i class="icon">📞</i>
        <h2>Phone</h2>
        <p>(+216) 27 312 507</p>
      </div>
  
      <div class="contact-card">
        <i class="icon">📍</i>
        <h2>Office</h2>
        <p>5051 moknine , monastir</p>
      </div>
    </div>
  
    <div class="newsletter">
    <h3>Abonnez-vous à notre newsletter pour les dernières mises à jour sur les nouvelles fonctionnalités et les sorties de produits.</h3>
      <form action="#" class="newsletter-form">
        <input type="email" placeholder="Enter your email" required />
        <button type="submit">Subscribe</button>
      </form>
    <p>En vous abonnant, vous acceptez notre politique de confidentialité et donnez votre consentement pour recevoir des mises à jour de notre entreprise.</p>
    </div>
  
    <footer>
      <div class="footer-links">
        <div class="social-media">
          <h4>Suivez-nous</h4>
          <ul>
            <li><img src="images/facebook.png" width="20px" height="20px"><a href="#">Facebook</a></li>
            <li><img src="images/instagram.png" width="20px" height="20px"><a href="#">Instagram</a></li>
            <li><img src="images/twitter.png" width="20px" height="20px"><a href="#">X</a></li>
            <li><img src="images/linkedin.png" width="20px" height="20px"><a href="#">LinkedIn</a></li>
            <li><img src="images/youtube.png" width="20px" height="20px"><a href="#">Youtube</a></li>
          </ul>
        </div>
      </div>
  
      <div class="footer-bottom">
        <p>&copy; 2025 EnergyFuel</p>
        <ul>
          <li><a href="https://policies.google.com/privacy"  >Privacy Policy</a></li>
          <li><a href="https://policies.google.com/terms">Terms of Service</a></li>
          <li><a href="https://www.youronlinechoices.com/">Cookies Settings</a></li>
        </ul>
      </div>
    </footer>
  </section>
</body>

</html>