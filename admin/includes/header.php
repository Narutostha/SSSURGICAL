<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SS SURGICAL - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #60a5fa;
            --secondary: #64748b;
            --accent: #06b6d4;
            --success: #059669;
            --warning: #d97706;
            --danger: #dc2626;
            --surface: #ffffff;
            --text: #0f172a;
            --text-light: #64748b;
            
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 2px 4px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 8px 16px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 12px 24px rgba(0, 0, 0, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, system-ui, sans-serif;
            background-color: #f8fafc;
            color: var(--text);
            line-height: 1.5;
        }

        .header {
            background: var(--surface);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: all 0.3s ease;
        }

        .header.scrolled {
            box-shadow: var(--shadow-lg);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Enhanced Logo Styles */
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text);
            position: relative;
            padding: 0.5rem;
        }

        .logo::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: 0;
            left: 0;
            background: linear-gradient(to right, var(--primary), var(--accent));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .logo:hover::after {
            transform: scaleX(1);
        }

        .logo-icon {
            position: relative;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .logo-icon::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2));
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .logo:hover .logo-icon::before {
            transform: translateX(100%);
        }

        .logo:hover .logo-icon {
            transform: translateY(-2px) rotate(5deg);
            box-shadow: var(--shadow-md);
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
        }

        /* Enhanced Navigation */
        .nav-container {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-list {
            display: flex;
            gap: 0.25rem;
            list-style: none;
            background: rgba(37, 99, 235, 0.05);
            padding: 0.25rem;
            border-radius: 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(37, 99, 235, 0.1), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .nav-link:hover::before {
            transform: translateX(100%);
        }

        .nav-link.active {
            color: var(--primary);
            background: var(--surface);
            box-shadow: var(--shadow-sm);
        }

        .nav-link i {
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }

        .nav-link:hover i {
            transform: translateY(-2px);
        }

        .nav-link span {
            position: relative;
        }

        .nav-link.active span::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(to right, var(--primary), var(--primary-light));
            transform: scaleX(1);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        /* Enhanced Profile Menu */
        .profile-menu {
            position: relative;
        }

        .profile-button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: transparent;
            border: 1px solid rgba(37, 99, 235, 0.1);
            color: var(--text);
            font-size: 0.9375rem;
            font-weight: 500;
            cursor: pointer;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .profile-button:hover {
            background: rgba(37, 99, 235, 0.05);
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        .profile-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            position: relative;
            overflow: hidden;
        }

        .profile-avatar::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2));
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .profile-button:hover .profile-avatar::before {
            transform: translateX(100%);
        }

        .profile-name {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .profile-role {
            font-size: 0.75rem;
            color: var(--text-light);
            display: block;
        }

        .profile-name i {
            font-size: 0.875rem;
            transition: transform 0.3s ease;
        }

        .profile-menu:hover .profile-name i {
            transform: rotate(180deg);
        }

        .profile-dropdown {
            position: absolute;
            top: calc(100% + 0.75rem);
            right: 0;
            width: 240px;
            background: var(--surface);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            border: 1px solid rgba(37, 99, 235, 0.1);
        }

        .profile-menu:hover .profile-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            color: var(--text);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .dropdown-item::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(37, 99, 235, 0.05), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .dropdown-item:hover::before {
            transform: translateX(100%);
        }

        .dropdown-item i {
            font-size: 1rem;
            color: var(--text-light);
            transition: transform 0.3s ease;
        }

        .dropdown-item:hover {
            background: rgba(37, 99, 235, 0.05);
            color: var(--primary);
        }

        .dropdown-item:hover i {
            color: var(--primary);
            transform: translateX(2px);
        }

        .dropdown-item.danger {
            color: var(--danger);
        }

        .dropdown-item.danger i {
            color: var(--danger);
        }

        .dropdown-item.danger:hover {
            background: rgba(220, 38, 38, 0.05);
        }

        .page-indicator {
            position: fixed;
            top: 1rem;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(37, 99, 235, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            color: var(--primary);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .page-indicator.visible {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .header-container {
                padding: 0.75rem 1rem;
            }

            .logo-text {
                display: none;
            }

            .nav-link {
                padding: 0.75rem;
            }

            .nav-link span {
                display: none;
            }

            .profile-name span {
                display: none;
            }

            .profile-button {
                padding: 0.5rem;
            }

            .profile-dropdown {
                right: -1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-laptop"></i>
                </div>
                <span class="logo-text">SS Surgical</span>
            </a>

            <div class="nav-container">
                <nav>
                    <ul class="nav-list">
                        <li>
                            <a href="index" class="nav-link">
                                <i class="fas fa-chart-line"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="products" class="nav-link">
                                <i class="fas fa-box"></i>
                                <span>Products</span>
                            </a>
                        </li>
                        <li>
                            <a href="orders" class="nav-link">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Orders</span>
                            </a>
                        </li>
                        <li>
    <a href="social_media" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'site_settings.php' ? 'active' : '' ?>">
        <i class="fas fa-cog"></i>
        <span>Site Settings</span>
    </a>
</li>
                    
                        <li class="nav-item">
    <a href="categories" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">
        <i class="nav-icon fas fa-tags"></i>
        <p>Brands</p>
    </a>
</li>
<li class="nav-item">
    <a href="contact-management" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'enquiry.php' ? 'active' : '' ?>">
        <i class="nav-icon fas fa-question-circle"></i>
        <p>Enquiry</p>
    </a>
</li>

                    </ul>
                </nav>

                <div class="profile-menu">
                    <button class="profile-button">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="profile-name">
                            <div>
                                <span>Admin User</span>
                                <small class="profile-role">Administrator</small>
                            </div>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </button>

                    <div class="profile-dropdown">
                      
                        <a href="logout.php" class="dropdown-item danger">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="page-indicator">You are here: <strong>Dashboard</strong></div>
    <script>
        // Enhanced Header scroll effect
        const header = document.querySelector('.header');
        const pageIndicator = document.querySelector('.page-indicator');
        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            header.classList.toggle('scrolled', currentScroll > 0);

            // Show/hide page indicator
            if (currentScroll > 100) {
                pageIndicator.classList.add('visible');
            } else {
                pageIndicator.classList.remove('visible');
            }

            if (currentScroll <= 0) {
                header.style.transform = 'translateY(0)';
            } else if (currentScroll > lastScroll && currentScroll > 100) {
                header.style.transform = 'translateY(-100%)';
            } else {
                header.style.transform = 'translateY(0)';
            }
            
            lastScroll = currentScroll;
        });

        // Enhanced Active link handler
        const navLinks = document.querySelectorAll('.nav-link');
        const currentPath = window.location.pathname;
        const pageName = document.querySelector('.page-indicator strong');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPath) {
                link.classList.add('active');
                // Update page indicator text
                if (pageName) {
                    pageName.textContent = link.querySelector('span').textContent;
                }
            } else {
                link.classList.remove('active');
            }

            // Add hover effect
            link.addEventListener('mouseenter', () => {
                if (!link.classList.contains('active')) {
                    link.style.transform = 'translateY(-2px)';
                }
            });

            link.addEventListener('mouseleave', () => {
                link.style.transform = 'translateY(0)';
            });
        });

        // Profile dropdown animation
        const profileButton = document.querySelector('.profile-button');
        const profileDropdown = document.querySelector('.profile-dropdown');

        let timeoutId;

        profileButton.addEventListener('mouseenter', () => {
            clearTimeout(timeoutId);
            profileDropdown.style.opacity = '1';
            profileDropdown.style.visibility = 'visible';
            profileDropdown.style.transform = 'translateY(0)';
        });

        const profileMenu = document.querySelector('.profile-menu');
        profileMenu.addEventListener('mouseleave', () => {
            timeoutId = setTimeout(() => {
                profileDropdown.style.opacity = '0';
                profileDropdown.style.visibility = 'hidden';
                profileDropdown.style.transform = 'translateY(-10px)';
            }, 200);
        });

        // Add ripple effect to buttons
        const buttons = document.querySelectorAll('button, .nav-link, .dropdown-item');
        
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const ripple = document.createElement('span');
                ripple.style.position = 'absolute';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.style.transform = 'translate(-50%, -50%)';
                ripple.style.width = '0';
                ripple.style.height = '0';
                ripple.style.borderRadius = '50%';
                ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.3)';
                ripple.style.pointerEvents = 'none';

                this.appendChild(ripple);

                // Animate the ripple
                requestAnimationFrame(() => {
                    ripple.style.transition = 'all 0.6s ease-out';
                    ripple.style.width = '200px';
                    ripple.style.height = '200px';
                    ripple.style.opacity = '0';
                });

                // Remove the ripple after animation
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Initialize page title in header
        function updatePageTitle() {
            const pathSegments = currentPath.split('/');
            let currentPage = pathSegments[pathSegments.length - 1].replace('.php', '');
            if (currentPage === '' || currentPage === 'index') {
                currentPage = 'Dashboard';
            } else {
                currentPage = currentPage.charAt(0).toUpperCase() + currentPage.slice(1);
            }
            document.title = `Laptop Station - ${currentPage}`;
        }

        updatePageTitle();
    </script>
</body>
</html>