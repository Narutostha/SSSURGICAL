<?php
require_once 'config/database.php';

// Fetch active social media platforms
function getSocialPlatforms($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM social_media_platforms WHERE is_active = 1 ORDER BY display_order ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching social platforms: " . $e->getMessage());
        return [];
    }
}

// Get social platforms
$socialPlatforms = getSocialPlatforms($pdo);
?>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Company Info -->
            <div class="footer-col">
                <p>Your trusted destination for premium Medical Equipment Experience quality and innovation.</p>
                <div class="social">
                    <?php foreach ($socialPlatforms as $platform): ?>
                        <a href="<?php echo htmlspecialchars($platform['url']); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo $platform['icon_svg']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="aboutus">About Us</a></li>
                    <li><a href="contact_us">Contact</a></li>
                    <li><a href="blog">Blog</a></li>
                    <li><a href="connectus">Support</a></li>
                </ul>
            </div>

            <!-- Categories -->
            <div class="footer-col">
                <h3>Categories</h3>
                <ul>
                    <li><a href="products?category=1&search=&sort=default">Spine</a></li>
                    <li><a href="products?category=2&search=&sort=default">Neuro</a></li>
                    <li><a href="products?category=3&search=&sort=default">ERCP</a></li>
                    <li><a href="products?category=4&search=&sort=default">Hospital Furniture</a></li>
                    <li><a href="products?category=5&search=&sort=default">3D Cranioplasty Bone</a></li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="footer-col">
                <h3>Newsletter</h3>
                <p>Subscribe for updates and exclusive offers</p>
                <form class="newsletter">
                    <button href="contact_us" type="submit">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</footer>

<style>
.footer {
    background: #111827;
    color: #e5e7eb;
    padding: 4rem 1rem 1rem;
}

.container {
    max-width: 100%;
    margin: 0 auto;
}

.footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.footer-col p {
    color: #9ca3af;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.footer-col h3 {
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
    position: relative;
}

.footer-col h3:after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -0.5rem;
    width: 2rem;
    height: 2px;
    background: #3b82f6;
}

.social {
    display: flex;
    gap: 1rem;
}

.social a {
    color: #9ca3af;
    transition: 0.3s;
}

.social a:hover {
    color: #3b82f6;
}

.footer-col ul {
    list-style: none;
    padding: 0;
}

.footer-col ul li {
    margin-bottom: 0.75rem;
}

.footer-col ul a {
    color: #9ca3af;
    text-decoration: none;
    transition: 0.3s;
}

.footer-col ul a:hover {
    color: #3b82f6;
}

.newsletter {
    display: flex;
    gap: 0.5rem;
}

.newsletter button {
    padding: 0.75rem;
    border: none;
    border-radius: 0.25rem;
    background: #3b82f6;
    color: #fff;
    cursor: pointer;
    transition: 0.3s;
}

.newsletter button:hover {
    background: #2563eb;
}

@media (max-width: 768px) {
    .footer-grid {
        grid-template-columns: 1fr;
    }
}
</style>