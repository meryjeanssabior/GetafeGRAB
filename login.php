<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GetafeGRAB</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="logo">Getafe<span>GRAB</span></div>
            <h2>Welcome Back</h2>
            <p>Login to your account</p>
            
            <form id="loginForm">
                <div class="input-group">
                    <label>Email or Phone</label>
                    <input type="text" name="identifier" required placeholder="Enter email or phone">
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn-primary w-100">Login</button>
            </form>
            <p class="auth-footer">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>

    <style>
        :root {
            --primary: #f9d423;
            --secondary: #ff4e50;
            --dark: #1a1a1a;
            --light: #ffffff;
            --glass: rgba(255, 255, 255, 0.05);
        }

        body {
            background: linear-gradient(135deg, #1a1a1a, #2d3436);
            color: var(--light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            padding: 2rem 0;
        }

        .auth-container {
            width: 100%;
            max-width: 400px;
            padding: 1.5rem;
            position: relative;
            z-index: 5;
        }

        .auth-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2.5rem;
            border-radius: 30px;
            box-shadow: 0 40px 80px rgba(0,0,0,0.5);
            position: relative;
            z-index: 10;
        }

        .logo { font-size: 1.8rem; font-weight: 800; margin-bottom: 1.5rem; text-align: center; }
        .logo span { color: var(--primary); }

        h2 { font-size: 1.8rem; margin-bottom: 0.5rem; text-align: center; }
        p { color: rgba(255,255,255,0.6); margin-bottom: 2rem; text-align: center; }

        .input-group { margin-bottom: 1.2rem; }
        .input-group label { display: block; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 500; }
        .input-group input {
            width: 100%;
            padding: 0.8rem 1.2rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: var(--light);
            outline: none;
            transition: 0.3s;
        }
        .input-group input:focus { border-color: var(--primary); background: rgba(255,255,255,0.1); }

        .btn-primary {
            position: relative;
            z-index: 10;
            background: linear-gradient(90deg, var(--primary), #fbc531);
            color: var(--dark);
            border: none;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 1rem;
        }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(249, 212, 35, 0.3); }

        .w-100 { width: 100%; }

        .auth-footer { margin-top: 1.5rem; font-size: 0.9rem; text-align: center; }
        .auth-footer a { color: var(--primary); text-decoration: none; font-weight: 600; }
    </style>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.innerText = 'Logging in...';
            submitBtn.disabled = true;

            const formData = new FormData(e.target);
            try {
                const response = await fetch('rider/api/auth/login.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) throw new Error('Server error');
                
                const result = await response.json();
                if(result.success) {
                    window.location.href = result.role === 'driver' ? 'driver/dashboard.php' : 'rider/dashboard.php';
                } else {
                    alert(result.error || 'Login failed');
                    submitBtn.innerText = 'Login';
                    submitBtn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                alert('Connection error. Please try again.');
                submitBtn.innerText = 'Login';
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>
