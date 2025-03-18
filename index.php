<?php
$page_title = 'Home - Surgical Equipment E-commerce';
$current_page = 'home';
require_once 'includes/header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            background: #f1f5f9;
            color: #333;
        }

        .hero-section {

            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/bg.png');
            background-size: cover;
            background-position: center;
            height: 565px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .hero-content {
            max-width: 800px;
            padding: 20px;
        }

        .hero-content h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 25px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section {
            background: #fff;
            margin-bottom: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 25px;
            position: relative;
            overflow: hidden;
        }

        .section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #3b82f6;
        }

        h2 {
            color: #1e293b;
            font-size: 24px;
            margin-bottom: 25px;
            padding-left: 15px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .card {
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            min-height: 380px;
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .card img {
            width: 100%;
            height: 220px;
            object-fit: contain;
            background: #f8fafc;
            padding: 15px;
            transition: transform 0.3s ease;
        }

        .card:hover img {
            transform: scale(1.05);
        }

        .hot-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ef4444;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .card-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
        }

        .card-content h3 {
            font-size: 16px;
            color: #334155;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .price {
            color: #3b82f6;
            font-size: 20px;
            font-weight: 600;
            margin: 12px 0;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .price::before {
            content: '';
            width: 20px;
            height: 2px;
            background: #3b82f6;
        }

        .add-to-cart-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            margin-top: auto;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            gap: 8px;
        }

        .add-to-cart-btn:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .add-to-cart-btn::after {
            content: '→';
            transition: transform 0.3s ease;
        }

        .add-to-cart-btn:hover::after {
            transform: translateX(5px);
        }

        .upcoming-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            font-weight: 500;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .card:hover .upcoming-overlay {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .hero-section {
                height: 300px;
            }

            .hero-content h1 {
                font-size: 2rem;
            }

            .container {
                padding: 15px;
            }
            
            .section {
                padding: 20px;
            }

            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    
    <div class="hero-section">
        <div class="hero-content">
            <h1>Find Your Perfect Surgical Equipment</h1>
            <p>Discover powerful machines for work, gaming, and creativity</p>
        </div>
    </div>
    <div class="news-section">
    <div class="container">
        <h2>Latest News & Updates</h2>
        <div class="news-grid">
            <div class="news-card">
                <img src="assets/icons/bag2-new.svg" alt="New Arrival"  width="10">
                <div class="news-content">
                    <span class="date">June 15, 2024</span>
                    <h3>New Gaming Surgical Equipments Arrival</h3>
                    <p>Latest gaming Surgical Equipments with RTX 4000 series now available.</p>
                    
                </div>
            </div>
            <div class="news-card">
                <img src="assets/icons/news.svg" alt="News"  height="10px">
                <div class="news-content">
                    <span class="date">June 10, 2024</span>
                    <h3>Student Special Offer</h3>
                    <p>Special discounts for students with valid ID cards.</p>
                </div>
            </div>
        </div>
    </div>
</div>

        <div class="brands">
    <div class="container">
        <h2>Our Trusted Brands</h2>
        <div class="brands-slider"> 
                <img src="assets/icons/Dell_logo_2016.svg" alt="Dell" width="100">

                <!-- HP Logo -->
                <img src="assets/icons/HP_logo_2012.svg" alt="HP" width="100">

                <!-- Lenovo Logo -->
                <img src="assets/icons/Lenovo_logo_2015.svg" alt="Lenovo" width="100">

                <!-- Asus Logo -->
                <img src="assets/icons/AsusTek_logo.svg" alt="Asus" width="100">

                <!-- Acer Logo -->
                <img src="assets/icons/Acer_2011.svg" alt="Acer" width="100">
        </div>
    </div>
</div>

<!-- Testimonials Section -->
<div class="testimonials">
    <div class="container">
        <h2>What Our Customers Say</h2>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="quote">"</div>
                <p>Best Surgical Equipment store in town. Great prices and excellent customer service. Really happy with my purchase!</p>
                <div class="testimonial-author">
                <img src="assets/icons/user-icon.svg" alt="User" width="100">
                    <div>
                        <h4>Ram Sharma</h4>
                        <span>Gaming Enthusiast</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="quote">"</div>
                <p>Their technical knowledge is impressive. They helped me choose the perfect Surgical Equipment for my needs.</p>
                <div class="testimonial-author">
                    <img src="assets/icons/user-icon.svg" alt="User" width="100">
                    <div>
                        <h4>Sita Rai</h4>
                        <span>Graphic Designer</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="quote">"</div>
                <p>Quick delivery and genuine products. Will definitely recommend to others!</p>
                <div class="testimonial-author">
                <img src="assets/icons/user-icon.svg" alt="User" width="100">
                    <div>
                        <h4>Hari KC</h4>
                        <span>IT Professional</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Features Section -->
<div class="features">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card">
                <img src="assets/icons//free-delivery-free.svg" alt="free-delievery" width="100">
                <h4>Free Shipping</h4>
                <p>On orders above Rs. 50,000</p>
            </div>
            <div class="feature-card">
            <img src="assets/icons/warranty.svg" alt="Warrenty" width="100">
                <h4>2 Year Warranty</h4>
                <p>Official warranty support</p>
            </div>
            <div class="feature-card">
            <img src="assets/icons/24-hours.svg" alt="24/7 Support" width="100">
                <h4>24/7 Support</h4>
                <p>Dedicated customer service</p>
            </div>
            <div class="feature-card">
                <img src="assets/icons/secure-payment.svg" alt="Secure Payment"  width="100">
                <h4>Secure Payment</h4>
                <p>100% secure checkout</p>
            </div>
        </div>
    </div>
</div>



<!-- News & Updates Section -->



<style>
/* Features Section */
.features {
    padding: 50px 0;
    background: #fff;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    text-align: center;
}

.feature-card {
    padding: 20px;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-card img {
    width: 60px;
    height: 60px;
    margin-bottom: 15px;
}

.feature-card h4 {
    color: #1e293b;
    margin-bottom: 10px;
}

.feature-card p {
    color: #64748b;
    font-size: 0.9rem;
}

/* Testimonials Section */
.testimonials {
    background: #f8fafc;
    padding: 70px 0;
}

.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 40px;
}

.testimonial-card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    position: relative;
}

.quote {
    font-size: 4rem;
    position: absolute;
    top: -10px;
    left: 20px;
    color: #3b82f6;
    opacity: 0.1;
}

.testimonial-card p {
    margin-bottom: 20px;
    color: #475569;
    font-style: italic;
}

.testimonial-author {
    display: flex;
    align-items: center;
    gap: 15px;
}

.testimonial-author img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.testimonial-author h4 {
    color: #1e293b;
    margin: 0;
}

.testimonial-author span {
    color: #64748b;
    font-size: 0.9rem;
}

/* News Section */
.news-section {
    padding: 70px 0;
    background: #fff;
}

.news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 40px;
}

.news-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
}

.news-card:hover {
    transform: translateY(-5px);
}

.news-card img {
    width: 60px;
    height: 60px;
    margin-bottom: 15px;
}

.news-content {
    padding: 20px;
}

.date {
    color: #64748b;
    font-size: 0.9rem;
}

.news-content h3 {
    color: #1e293b;
    margin: 10px 0;
    font-size: 1.2rem;
}

.news-content p {
    color: #475569;
    margin-bottom: 15px;
}

.read-more {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.read-more:hover {
    color: #2563eb;
}

.read-more::after {
    content: '→';
    transition: transform 0.3s ease;
}

.read-more:hover::after {
    transform: translateX(5px);
}

/* Brands Section */
.brands {
    padding: 50px 0;
    background: #f8fafc;
}

.brands-slider {
    display: flex;
    justify-content: space-around;
    align-items: center;
    flex-wrap: wrap;
    gap: 40px;
    margin-top: 30px;
}

.brands-slider img {
    height: 40px;
    object-fit: contain;
    filter: grayscale(100%);
    opacity: 0.7;
    transition: all 0.3s ease;
}

.brands-slider img:hover {
    filter: grayscale(0);
    opacity: 1;
}

@media (max-width: 768px) {
    .testimonials-grid,
    .news-grid {
        grid-template-columns: 1fr;
    }
    
    .brands-slider {
        gap: 20px;
    }
    
    .brands-slider img {
        height: 30px;
    }
}
</style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/add-to-cart.js"></script>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>