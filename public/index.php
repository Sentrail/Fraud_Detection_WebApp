

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SmartBank - Secure Savings</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <header class="landing-header">
    <div class="logo">SmartBank</div>
    <nav>
      <a href="#index.php">Home</a>
      <a href="#features">Features</a>
      <a href="../views/auth/admin_login.php">Admin Login</a>
      <a href="../views/auth/register_admin.php">Admin Registration</a>
      <a href="#contact">Contact</a>
    </nav>
  </header>

  <section class="hero">
    <h1>Protect Your Savings, Smartly</h1>
    <p>AI-Powered Fraud Detection System for Safe Banking</p>
    <a href="../views/auth/register.php" class="btn">Get Started</a>
  </section>

  <section id="features" class="features">
    <h2>System Features</h2>
    <ul>
      <li>✔️ Real-time fraud alerts</li>
      <li>✔️ Intelligent pattern detection</li>
      <li>✔️ Admin-controlled dashboard</li>
      <li>✔️ User account safety assurance</li>
    </ul>
  </section>

  <section id="contact" class="contact">
    <h2>Contact Us</h2>
    <p>Email: support@smartbank.com | Phone: +234-xxx-xxx-xxxx</p>
  </section>

  <footer>
    <p>&copy; <?= date('Y') ?> SmartBank. All rights reserved.</p>
  </footer>
</body>
</html>
