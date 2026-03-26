<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ondjangobay - Site Under Maintenance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #1a365d;
            --secondary: #2d3748;
            --accent: #3182ce;
            --light: #f7fafc;
            --text: #2d3748;
        }

        body {
            background-color: var(--light);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1000px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .logo-section {
            margin-bottom: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo-icon {
            font-size: 2.5rem;
            color: var(--accent);
            margin-right: 15px;
        }

        .logo-text {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: 1px;
        }

        .tagline {
            font-size: 1.2rem;
            color: var(--secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        .main-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            margin: 40px 0;
            width: 100%;
        }

        .maintenance-info {
            flex: 1;
            min-width: 300px;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-top: 4px solid var(--accent);
        }

        .status-section {
            flex: 1;
            min-width: 300px;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-top: 4px solid var(--primary);
        }

        h1 {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        h2 {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }

        h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: var(--accent);
        }

        .maintenance-icon {
            font-size: 4rem;
            color: var(--accent);
            margin: 20px 0;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .description {
            margin-bottom: 25px;
            font-size: 1.1rem;
            color: var(--secondary);
        }

        .progress-container {
            background: #e2e8f0;
            border-radius: 10px;
            height: 12px;
            margin: 30px 0;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            width: 65%;
            background: linear-gradient(90deg, var(--accent), #63b3ed);
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }

        .progress-bar:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background-image: linear-gradient(
                -45deg, 
                rgba(255, 255, 255, 0.2) 25%, 
                transparent 25%, 
                transparent 50%, 
                rgba(255, 255, 255, 0.2) 50%, 
                rgba(255, 255, 255, 0.2) 75%, 
                transparent 75%, 
                transparent
            );
            z-index: 1;
            background-size: 50px 50px;
            animation: move 2s linear infinite;
        }

        @keyframes move {
            0% { background-position: 0 0; }
            100% { background-position: 50px 50px; }
        }

        .progress-text {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-weight: 600;
            color: var(--secondary);
        }

        .status-items {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .status-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f7fafc;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .status-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.05);
        }

        .status-icon {
            font-size: 1.5rem;
            color: var(--accent);
            margin-right: 15px;
            width: 40px;
            text-align: center;
        }

        .status-details {
            flex: 1;
            text-align: left;
        }

        .status-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .status-desc {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .countdown-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 600px;
        }

        .countdown {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .countdown-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: var(--primary);
            color: white;
            padding: 15px;
            border-radius: 8px;
            min-width: 80px;
        }

        .countdown-value {
            font-size: 2rem;
            font-weight: 700;
        }

        .countdown-label {
            font-size: 0.8rem;
            margin-top: 5px;
            opacity: 0.8;
        }

        .contact-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
            width: 100%;
            max-width: 600px;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
            margin: 25px 0;
        }

        .contact-item {
            display: flex;
            align-items: center;
        }

        .contact-icon {
            font-size: 1.2rem;
            color: var(--accent);
            margin-right: 10px;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: var(--accent);
            transform: translateY(-3px);
        }

        @media (max-width: 768px) {
            .logo-text {
                font-size: 2.2rem;
            }
            
            h1 {
                font-size: 1.8rem;
            }
            
            .main-content {
                flex-direction: column;
            }
            
            .countdown {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-section">
            <div class="logo">
                <i class="logo-icon fas fa-cube"></i>
                <div class="logo-text">ONDJANGOBAY</div>
            </div>
            <p class="tagline">Innovative Solutions for Modern Businesses</p>
        </div>

        <div class="main-content">
            <div class="maintenance-info">
                <div class="maintenance-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <h1>We're Under Maintenance</h1>
                <p class="description">We're working hard to improve our website and bring you an enhanced experience. Thank you for your patience.</p>
                
                <div class="progress-container">
                    <div class="progress-bar"></div>
                </div>
                <div class="progress-text">
                    <span>Progress</span>
                    <span>65% Complete</span>
                </div>
                
                <p>We expect to be back online shortly. In the meantime, feel free to contact us with any urgent inquiries.</p>
            </div>

            <div class="status-section">
                <h2>Current Status</h2>
                <div class="status-items">
                    <div class="status-item">
                        <div class="status-icon">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="status-details">
                            <div class="status-title">Server Infrastructure</div>
                            <div class="status-desc">Upgrade in progress</div>
                        </div>
                    </div>
                    
                    <div class="status-item">
                        <div class="status-icon">
                            <i class="fas fa-paint-brush"></i>
                        </div>
                        <div class="status-details">
                            <div class="status-title">UI/UX Design</div>
                            <div class="status-desc">Completed</div>
                        </div>
                    </div>
                    
                    <div class="status-item">
                        <div class="status-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="status-details">
                            <div class="status-title">Development</div>
                            <div class="status-desc">65% complete</div>
                        </div>
                    </div>
                    
                    <div class="status-item">
                        <div class="status-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="status-details">
                            <div class="status-title">Database Migration</div>
                            <div class="status-desc">Scheduled</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--<div class="countdown-section">
            <h2>Expected Completion</h2>
            <div class="countdown">
                <div class="countdown-item">
                    <div class="countdown-value" id="days">05</div>
                    <div class="countdown-label">Days</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-value" id="hours">12</div>
                    <div class="countdown-label">Hours</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-value" id="minutes">45</div>
                    <div class="countdown-label">Minutes</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-value" id="seconds">30</div>
                    <div class="countdown-label">Seconds</div>
                </div>
            </div>
        </div>-->

        <div class="contact-section">
            <h2>Contact Us</h2>
            <div class="contact-info">
                <div class="contact-item">
                    <i class="contact-icon fas fa-envelope"></i>
                    <span>support@ondjangobay.com</span>
                </div>
                <div class="contact-item">
                    <i class="contact-icon fas fa-phone"></i>
                    <span>+1 (555) 123-4567</span>
                </div>
            </div>
            
            <div class="social-links">
                <a href="#" class="social-link">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-link">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="social-link">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="#" class="social-link">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Countdown Timer
        function updateCountdown() {
            // Set your target date here
            const targetDate = new Date();
            targetDate.setDate(targetDate.getDate() + 5); // 5 days from now
            targetDate.setHours(18, 0, 0, 0); // Set to 6 PM
            
            const now = new Date();
            const difference = targetDate - now;
            
            if (difference > 0) {
                const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((difference % (1000 * 60)) / 1000);
                
                document.getElementById('days').textContent = days.toString().padStart(2, '0');
                document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
                document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
            } else {
                document.querySelector('.countdown').innerHTML = '<div style="padding: 20px; font-size: 1.2rem;">We\'re almost done! Final touches in progress.</div>';
            }
        }
        
        // Update countdown every second
        setInterval(updateCountdown, 1000);
        updateCountdown(); // Initial call
        
        // Animate progress bar
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.querySelector('.progress-bar');
            let width = 0;
            const targetWidth = 65;
            const interval = setInterval(() => {
                if (width >= targetWidth) {
                    clearInterval(interval);
                } else {
                    width++;
                    progressBar.style.width = width + '%';
                }
            }, 30);
        });
    </script>
</body>
</html>