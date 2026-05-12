<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GetafeGRAB | Modern Booking System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="glass-bg">
        <nav>
            <div class="logo">Getafe<span>GRAB</span></div>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#about">About</a>
                <a href="login.php" class="btn-primary">Get Started</a>
            </div>
        </nav>

        <main class="hero">
            <div class="hero-content">
                <h1>Your Premium <span>Booking</span> Experience</h1>
                <p>Fast, reliable, and premium ride-hailing at your fingertips. Join the future of urban mobility with GetafeGRAB.</p>
                <div class="hero-cta">
                    <a href="register.php?role=rider" class="btn-primary">Book a Ride</a>
                    <a href="register.php?role=driver" class="btn-outline">Drive with Us</a>
                </div>
            </div>
            <div class="hero-image">
                 <div class="car-card">
                    <img src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Premium Ride">
                    <div class="card-info">
                        <h3>Premium Class</h3>
                        <p>Starting at ₱10.00</p>
                    </div>
                 </div>
            </div>
        </main>

        <section id="features" class="features">
            <h2 class="section-title">Why Choose <span>GetafeGRAB</span>?</h2>
            <div class="features-grid">
                <div class="feature-card glass-card">
                    <i class="fas fa-bolt"></i>
                    <h3>Instant Booking</h3>
                    <p>Get a ride in minutes with our high-speed driver matching system.</p>
                </div>
                <div class="feature-card glass-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Safe & Secure</h3>
                    <p>All drivers are verified and trips are tracked in real-time.</p>
                </div>
                <div class="feature-card glass-card">
                    <i class="fas fa-gem"></i>
                    <h3>Premium Quality</h3>
                    <p>Top-rated drivers and comfortable vehicles for the best experience.</p>
                </div>
            </div>
        </section>

        <section id="about" class="about">
            <div class="about-content glass-card">
                <h2>About Our Mission</h2>
                <p>GetafeGRAB is dedicated to revolutionizing urban mobility by providing a seamless, premium booking experience. We connect riders with professional drivers through cutting-edge technology, ensuring safety and comfort on every journey.</p>
            </div>
        </section>

        <footer>
            <p>&copy; 2024 GetafeGRAB. All rights reserved. <span>Elevating your journey.</span></p>
        </footer>
    </div>

    <style>
        :root {
            --primary: #f9d423;
            --secondary: #ff4e50;
            --dark: #1a1a1a;
            --light: #ffffff;
            --glass: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a1a1a, #2d3436);
            color: var(--light);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .glass-bg {
            background: radial-gradient(circle at top right, rgba(249, 212, 35, 0.15), transparent),
                        radial-gradient(circle at bottom left, rgba(255, 78, 80, 0.15), transparent);
            min-height: 100vh;
            padding: 2rem 5%;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5rem;
        }

        .logo {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .logo span {
            color: var(--primary);
        }

        .nav-links a {
            color: var(--light);
            text-decoration: none;
            margin-left: 2rem;
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--primary), #fbc531);
            color: var(--dark) !important;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 10px 20px rgba(249, 212, 35, 0.3);
        }

        .btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary) !important;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            margin-left: 1rem;
            font-weight: 600;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-content h1 {
            font-size: 4.5rem;
            line-height: 1.1;
            margin-bottom: 2rem;
            font-weight: 800;
        }

        .hero-content h1 span {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-content p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 3rem;
            max-width: 500px;
        }

        .hero-cta {
            display: flex;
        }

        .hero-image {
            position: relative;
        }

        .car-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 30px;
            transform: rotate(-5deg);
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
        }

        .car-card img {
            width: 100%;
            border-radius: 20px;
            margin-bottom: 1.5rem;
        }

        .card-info h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .card-info p {
            color: var(--primary);
            font-weight: 600;
        }

        @media (max-width: 968px) {
            .hero {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .hero-content h1 {
                font-size: 3rem;
            }
            .hero-cta {
                justify-content: center;
            }
            .hero-image {
                display: none;
            }
        }

        /* New Sections */
        .features { padding: 8rem 0; }
        .section-title { font-size: 3rem; text-align: center; margin-bottom: 4rem; }
        .section-title span { color: var(--primary); }
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; }
        .feature-card { padding: 3rem; text-align: center; transition: 0.3s; }
        .feature-card:hover { transform: translateY(-10px); border-color: var(--primary); }
        .feature-card i { font-size: 3rem; color: var(--primary); margin-bottom: 1.5rem; }
        .feature-card h3 { margin-bottom: 1rem; }
        .feature-card p { color: rgba(255,255,255,0.6); line-height: 1.6; }

        .about { padding: 4rem 0; }
        .about-content { padding: 4rem; max-width: 900px; margin: 0 auto; text-align: center; }
        .about-content h2 { font-size: 2.5rem; margin-bottom: 1.5rem; color: var(--primary); }
        .about-content p { font-size: 1.2rem; color: rgba(255,255,255,0.8); line-height: 1.8; }

        footer { padding: 4rem 0; text-align: center; border-top: 1px solid rgba(255,255,255,0.05); margin-top: 4rem; }
        footer p { color: rgba(255,255,255,0.4); font-size: 0.9rem; }
        footer span { color: var(--primary); font-weight: 600; margin-left: 0.5rem; }

        @media (max-width: 768px) {
            .features-grid { grid-template-columns: 1fr; }
        }
    </style>
</body>
</html>
