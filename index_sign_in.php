<?php
session_start();
if (isset($_SESSION['email'])) {
    header("Location: index_acceuil.php");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kiosque_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Échec de la connexion : " . $e->getMessage());
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loginEmail'], $_POST['loginPassword'])) {
    $email = filter_var($_POST['loginEmail'], FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['loginPassword']);

    $stmt = $pdo->prepare("SELECT email, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = "Email non trouvé.";
    } elseif (!password_verify($password, $user['password'])) {
        $error = "Mot de passe incorrect.";
    } else {
        session_regenerate_id(true);
        $_SESSION['email'] = $user['email'];
        header("Location: index_acceuil.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EnergyFuel - Connexion</title>
    <link rel="stylesheet" href="style_sign.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="script_sign_in.js" defer></script>
    <script src="script_time.js" defer></script>
</head>
<body>
    <header>
        <div class="header-top">
            <div class="current-time" id="currentTime"></div>
        </div>
        <div class="logo">Energy<span>Fuel</span></div>
        <nav>
            <ul>
                <li><a href="index_acceuil.php">Accueil</a></li>
                <li><a href="index_service.php">Services</a></li>
                <li><a href="index_about_us.php">about us</a></li>
            </ul>
        </nav>
    </header>
    <div id="authSection" class="container">
        <?php if ($error): ?>
            <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form class="auth-form active" id="loginForm" method="POST">
            <div class="form-group">
                <label for="loginEmail">Email <span class="required">*</span></label>
                <input type="email" id="loginEmail" name="loginEmail" placeholder="Votre email" value="<?php echo isset($_POST['loginEmail']) ? htmlspecialchars($_POST['loginEmail']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="loginPassword">Mot de passe <span class="required">*</span></label>
                <div class="password-wrapper">
                    <input type="password" id="loginPassword" name="loginPassword" placeholder="Votre mot de passe" required>
                    <label class="toggle-password">
                        <input type="checkbox" class="toggle-checkbox">
                        <i class="fas fa-eye"></i>
                    </label>
                </div>
            </div>
            <section>
                <button type="submit" class="btn btn-block" id="loginButton">Se connecter</button>
            </section>
            <p><a href="index_sign_up.php">S'inscrire</a></p>
        </form>
    </div>
    <footer class="minimised">
        <p>&copy; 2025 EnergyFuel. Tous droits réservés.</p>
        <p>Adresse : 5051 Moknine, Monastir</p>
        <p>Téléphone : (+216) 27 312 507 | Email : haythem.idi@ensi-uma.tn</p>
    </footer>
</body>
</html>