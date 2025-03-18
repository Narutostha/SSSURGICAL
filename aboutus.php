<?php
$page_title = 'About Us';
$current_page = 'about';
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Laptop Station</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            background-color: #f4f5f7;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d3436 100%);
            color: white;
            padding: 100px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('assets/images/circuit-pattern.png') repeat;
            opacity: 0.1;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #3498db, #2ecc71);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: #e5e7eb;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Stats Section */
        .stats-section {
            background: white;
            padding: 40px 20px;
            margin-top: -50px;
            position: relative;
            z-index: 2;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 2.5rem;
            color: #2563eb;
            margin-bottom: 8px;
        }

        .stat-item p {
            color: #6b7280;
            font-size: 0.9rem;
        }

        /* Mission Section */
        .mission-section {
            padding: 80px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .mission-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .mission-card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .mission-card:hover {
            transform: translateY(-5px);
        }

        .mission-icon {
            width: 60px;
            height: 60px;
            background: #f0f9ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #2563eb;
        }

        .mission-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #1a1a1a;
        }

        .mission-card p {
            color: #6b7280;
            font-size: 0.95rem;
        }

        /* Team Section */
        .team-section {
            padding: 80px 20px;
            background: white;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .section-title p {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .team-member {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .team-member:hover {
            transform: translateY(-5px);
        }

        .member-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .member-info {
            padding: 20px;
            text-align: center;
        }

        .member-name {
            font-size: 1.25rem;
            color: #1a1a1a;
            margin-bottom: 5px;
        }

        .member-role {
            color: #2563eb;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-link {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #f4f5f7;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a1a1a;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: #2563eb;
            color: white;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .mission-grid {
                grid-template-columns: 1fr;
            }

            .team-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Laptop Station</h1>
            <p class="hero-subtitle">
                Your trusted destination for premium laptops. We bring you the latest technology 
                with unmatched service and expertise since 2020.
            </p>
        </div>
    </div>

    <div class="stats-section">
        <div class="stats-grid">
            <div class="stat-item">
                <h3>3K+</h3>
                <p>Happy Customers</p>
            </div>
            <div class="stat-item">
                <h3>500+</h3>
                <p>Laptops Sold</p>
            </div>
            <div class="stat-item">
                <h3>50+</h3>
                <p>Brand Partners</p>
            </div>
            <div class="stat-item">
                <h3>98%</h3>
                <p>Customer Satisfaction</p>
            </div>
        </div>
    </div>

    <div class="mission-section">
        <div class="mission-grid">
            <div class="mission-card">
                <div class="mission-icon">
                    <i class="fas fa-rocket fa-2x"></i>
                </div>
                <h3>Our Mission</h3>
                <p>To provide cutting-edge laptop solutions that empower individuals and businesses 
                   to achieve their technological aspirations.</p>
            </div>
            <div class="mission-card">
                <div class="mission-icon">
                    <i class="fas fa-eye fa-2x"></i>
                </div>
                <h3>Our Vision</h3>
                <p>To become Nepal's most trusted laptop retailer, known for our expert guidance 
                   and exceptional customer service.</p>
            </div>
            <div class="mission-card">
                <div class="mission-icon">
                    <i class="fas fa-heart fa-2x"></i>
                </div>
                <h3>Our Values</h3>
                <p>Integrity, excellence, and customer satisfaction are at the heart of everything 
                   we do at Laptop Station.</p>
            </div>
        </div>
    </div>

    <div class="team-section">
        <div class="section-title">
            <h2>Meet Our Team</h2>
            <p>The passionate experts behind Laptop Station</p>
        </div>
        <div class="team-grid">
            <div class="team-member">
                <div class="member-info">
                    <h3 class="member-name">Reyaham Shrestha</h3>
                    <p class="member-role">Founder & CEO</p>
                    
                </div>
            </div>
            <div class="team-member">
                <div class="member-info">
                    <h3 class="member-name">Dinesh Bista</h3>
                    <p class="member-role">Technical Director</p>
                    
                </div>
            </div>
            <div class="team-member">
                <div class="member-info">
                    <h3 class="member-name">Saroj Gamal</h3>
                    <p class="member-role">Customer Experience Manager</p>
                    
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>