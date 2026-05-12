<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | GetafeGRAB</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="logo">Getafe<span>GRAB</span></div>
            <h2>Create an Account</h2>
            <p>Join the premium booking network</p>
            
            <form id="registerForm">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required placeholder="Enter your name">
                </div>
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="Enter your email">
                </div>
                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" required placeholder="Enter your phone number">
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Create a password">
                </div>
                <div class="input-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="rider">Rider (Passenger)</option>
                        <option value="driver">Driver (Partner)</option>
                    </select>
                </div>
                
                <div id="driverFields" style="display:none;">
                    <h3>Vehicle Information</h3>
                    <div class="input-group">
                        <label>Vehicle Model</label>
                        <input type="text" name="model" placeholder="e.g. Toyota Vios">
                    </div>
                    <div class="input-group">
                        <label>Plate Number</label>
                        <input type="text" name="plate_number" placeholder="e.g. ABC 1234">
                    </div>
                    <div class="input-group">
                        <label>Vehicle Type</label>
                        <select name="type">
                            <option value="car">Car</option>
                            <option value="motorcycle">Motorcycle</option>
                            <option value="taxi">Taxi</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-100">Sign Up</button>
            </form>
            <p class="auth-footer">Already have an account? <a href="login.php">Login here</a></p>
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
            padding: 2rem 0; /* Add vertical padding for small screens */
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
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
        .input-group input, .input-group select {
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

        .auth-footer { margin-top: 1.5rem; font-size: 0.9rem; }
        .auth-footer a { color: var(--primary); text-decoration: none; font-weight: 600; }

        #driverFields h3 { font-size: 1.1rem; margin: 1.5rem 0 1rem; color: var(--primary); }
    </style>

    <script>
        // Pre-select role from URL if present
        const urlParams = new URLSearchParams(window.location.search);
        const roleParam = urlParams.get('role');
        if (roleParam) {
            const roleSelect = document.querySelector('select[name="role"]');
            roleSelect.value = roleParam;
            if (roleParam === 'driver') {
                document.getElementById('driverFields').style.display = 'block';
            }
        }

        document.querySelector('select[name="role"]').addEventListener('change', function() {
            document.getElementById('driverFields').style.display = this.value === 'driver' ? 'block' : 'none';
        });

        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.innerText = 'Signing up...';
            submitBtn.disabled = true;

            const formData = new FormData(e.target);
            try {
                const response = await fetch('rider/api/auth/register.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) throw new Error('Network response was not ok');
                
                const result = await response.json();
                if(result.success) {
                    alert('Registration successful! Please login.');
                    window.location.href = 'login.php';
                } else {
                    alert(result.error || 'Registration failed');
                    submitBtn.innerText = 'Sign Up';
                    submitBtn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred. Please check your connection and database.');
                submitBtn.innerText = 'Sign Up';
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>
