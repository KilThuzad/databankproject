<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Blog</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --text-color: #333;
            --text-light: #777;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            background-color: #f9f9f9;
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background-color: white;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }

        .logo span {
            color: var(--secondary-color);
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        nav ul li {
            margin-left: 25px;
        }

        nav ul li a {
            text-decoration: none;
            color: var(--dark-color);
            font-weight: 500;
            transition: var(--transition);
        }

        nav ul li a:hover {
            color: var(--primary-color);
        }

        .search-bar {
            display: flex;
            align-items: center;
            background: var(--light-color);
            border-radius: 30px;
            padding: 8px 15px;
            width: 250px;
        }

        .search-bar input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            padding: 5px;
        }

        .search-bar button {
            background: transparent;
            border: none;
            color: var(--text-light);
            cursor: pointer;
        }

        .mobile-menu {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(44, 62, 80, 0.8), rgba(44, 62, 80, 0.8)), url('https://source.unsplash.com/random/1200x600') no-repeat center center/cover;
            color: white;
            padding: 100px 0;
            text-align: center;
            margin-bottom: 40px;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
        }

        .btn {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #2980b9;
            transform: translateY(-3px);
        }

        /* Main Content */
        .main-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            margin-bottom: 50px;
        }

        /* Blog Posts */
        .blog-post {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            transition: var(--transition);
        }

        .blog-post:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .post-image {
            height: 250px;
            width: 100%;
            object-fit: cover;
        }

        .post-content {
            padding: 25px;
        }

        .post-category {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 0.8rem;
            margin-bottom: 15px;
        }

        .post-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .post-title a {
            text-decoration: none;
            color: var(--dark-color);
            transition: var(--transition);
        }

        .post-title a:hover {
            color: var(--primary-color);
        }

        .post-meta {
            display: flex;
            margin-bottom: 15px;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .post-meta span {
            margin-right: 15px;
        }

        .post-meta i {
            margin-right: 5px;
        }

        .post-excerpt {
            margin-bottom: 20px;
            color: var(--text-light);
        }

        .read-more {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }

        .read-more i {
            margin-left: 5px;
            transition: var(--transition);
        }

        .read-more:hover i {
            transform: translateX(5px);
        }

        /* Sidebar */
        .sidebar-widget {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .widget-title {
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light-color);
            position: relative;
        }

        .widget-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 50px;
            height: 2px;
            background: var(--primary-color);
        }

        .categories-list {
            list-style: none;
        }

        .categories-list li {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--light-color);
        }

        .categories-list li:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .categories-list a {
            text-decoration: none;
            color: var(--text-color);
            display: flex;
            justify-content: space-between;
            transition: var(--transition);
        }

        .categories-list a:hover {
            color: var(--primary-color);
        }

        .categories-list span {
            background: var(--light-color);
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.8rem;
        }

        .recent-posts .post-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-color);
        }

        .recent-posts .post-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .recent-posts .post-image {
            width: 80px;
            height: 80px;
            border-radius: 5px;
            margin-right: 15px;
        }

        .recent-posts .post-content h4 {
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .recent-posts .post-content h4 a {
            text-decoration: none;
            color: var(--dark-color);
            transition: var(--transition);
        }

        .recent-posts .post-content h4 a:hover {
            color: var(--primary-color);
        }

        .recent-posts .post-date {
            font-size: 0.8rem;
            color: var(--text-light);
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tag {
            background: var(--light-color);
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 0.8rem;
            text-decoration: none;
            color: var(--text-color);
            transition: var(--transition);
        }

        .tag:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Newsletter */
        .newsletter {
            background: var(--secondary-color);
            color: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
        }

        .newsletter h3 {
            margin-bottom: 15px;
        }

        .newsletter p {
            margin-bottom: 20px;
            opacity: 0.8;
        }

        .newsletter-form {
            display: flex;
            max-width: 400px;
            margin: 0 auto;
        }

        .newsletter-form input {
            flex: 1;
            padding: 12px 15px;
            border: none;
            border-radius: 30px 0 0 30px;
            outline: none;
        }

        .newsletter-form button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0 20px;
            border-radius: 0 30px 30px 0;
            cursor: pointer;
            transition: var(--transition);
        }

        .newsletter-form button:hover {
            background: #2980b9;
        }

        /* Footer */
        footer {
            background: var(--dark-color);
            color: white;
            padding: 60px 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            margin-bottom: 40px;
        }

        .footer-widget h3 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-widget h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--primary-color);
        }

        .footer-widget p {
            margin-bottom: 20px;
            opacity: 0.8;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            text-decoration: none;
            color: white;
            opacity: 0.8;
            transition: var(--transition);
        }

        .footer-links a:hover {
            opacity: 1;
            padding-left: 5px;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            text-decoration: none;
            transition: var(--transition);
        }

        .social-links a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.9rem;
            opacity: 0.7;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .footer-content {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                align-items: flex-start;
            }
            
            nav {
                width: 100%;
                margin: 20px 0;
            }
            
            nav ul {
                flex-direction: column;
            }
            
            nav ul li {
                margin: 10px 0;
            }
            
            .search-bar {
                width: 100%;
                margin-top: 10px;
            }
            
            .mobile-menu {
                display: block;
                position: absolute;
                top: 25px;
                right: 20px;
            }
            
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .footer-content {
                grid-template-columns: 1fr;
            }
            
            .newsletter-form {
                flex-direction: column;
            }
            
            .newsletter-form input {
                border-radius: 30px;
                margin-bottom: 10px;
            }
            
            .newsletter-form button {
                border-radius: 30px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-container">
            <a href="#" class="logo">Blog<span>Space</span></a>
            
            <div class="mobile-menu">
                <i class="fas fa-bars"></i>
            </div>
            
            <nav>
                <ul>
                    <li><a href="#" class="active">Home</a></li>
                    <li><a href="#">Categories</a></li>
                    <li><a href="#">Features</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </nav>
            
            <div class="search-bar">
                <input type="text" placeholder="Search...">
                <button><i class="fas fa-search"></i></button>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h1>Welcome to BlogSpace</h1>
            <p>Discover amazing articles, tutorials, and stories from our community of writers. Explore topics that interest you and join the conversation.</p>
            <a href="#" class="btn">Start Reading</a>
        </div>
    </section>

    <div class="container main-content">
        <div class="blog-posts">
            <article class="blog-post">
                <img src="https://source.unsplash.com/random/800x400?technology" alt="Blog Post" class="post-image">
                <div class="post-content">
                    <span class="post-category">Technology</span>
                    <h2 class="post-title"><a href="#">The Future of Artificial Intelligence in Everyday Life</a></h2>
                    <div class="post-meta">
                        <span><i class="far fa-user"></i> John Doe</span>
                        <span><i class="far fa-calendar"></i> June 15, 2023</span>
                        <span><i class="far fa-comment"></i> 12 Comments</span>
                    </div>
                    <p class="post-excerpt">Explore how artificial intelligence is transforming our daily lives, from smart assistants to personalized recommendations. Discover the potential and challenges of this rapidly evolving technology.</p>
                    <a href="#" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>

            <article class="blog-post">
                <img src="https://source.unsplash.com/random/800x400?travel" alt="Blog Post" class="post-image">
                <div class="post-content">
                    <span class="post-category">Travel</span>
                    <h2 class="post-title"><a href="#">10 Hidden Gems in Europe You Need to Visit</a></h2>
                    <div class="post-meta">
                        <span><i class="far fa-user"></i> Sarah Johnson</span>
                        <span><i class="far fa-calendar"></i> June 10, 2023</span>
                        <span><i class="far fa-comment"></i> 8 Comments</span>
                    </div>
                    <p class="post-excerpt">Discover breathtaking destinations off the beaten path in Europe. From charming villages to stunning natural wonders, these hidden gems offer unique experiences away from tourist crowds.</p>
                    <a href="#" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>

            <article class="blog-post">
                <img src="https://source.unsplash.com/random/800x400?food" alt="Blog Post" class="post-image">
                <div class="post-content">
                    <span class="post-category">Food</span>
                    <h2 class="post-title"><a href="#">Healthy Recipes for Busy Weeknights</a></h2>
                    <div class="post-meta">
                        <span><i class="far fa-user"></i> Michael Chen</span>
                        <span><i class="far fa-calendar"></i> June 5, 2023</span>
                        <span><i class="far fa-comment"></i> 15 Comments</span>
                    </div>
                    <p class="post-excerpt">Quick, nutritious, and delicious meals you can prepare in 30 minutes or less. These recipes will help you maintain a healthy diet even on your busiest days.</p>
                    <a href="#" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>
        </div>
        <aside class="sidebar">
            <div class="sidebar-widget">
                <h3 class="widget-title">About Me</h3>
                <p>Hello! I'm a passionate blogger sharing insights on technology, travel, food, and lifestyle. Join me on this journey of discovery and learning.</p>
                <a href="#" class="btn">Read More</a>
            </div>

            <div class="sidebar-widget">
                <h3 class="widget-title">Categories</h3>
                <ul class="categories-list">
                    <li><a href="#">Technology <span>12</span></a></li>
                    <li><a href="#">Travel <span>8</span></a></li>
                    <li><a href="#">Food <span>15</span></a></li>
                    <li><a href="#">Lifestyle <span>9</span></a></li>
                    <li><a href="#">Health <span>7</span></a></li>
                    <li><a href="#">Business <span>5</span></a></li>
                </ul>
            </div>

            <div class="sidebar-widget recent-posts">
                <h3 class="widget-title">Recent Posts</h3>
                <div class="post-item">
                    <img src="https://source.unsplash.com/random/100x100?technology" alt="Post" class="post-image">
                    <div class="post-content">
                        <h4><a href="#">The Future of AI in Everyday Life</a></h4>
                        <span class="post-date">June 15, 2023</span>
                    </div>
                </div>
                <div class="post-item">
                    <img src="https://source.unsplash.com/random/100x100?travel" alt="Post" class="post-image">
                    <div class="post-content">
                        <h4><a href="#">10 Hidden Gems in Europe</a></h4>
                        <span class="post-date">June 10, 2023</span>
                    </div>
                </div>
                <div class="post-item">
                    <img src="https://source.unsplash.com/random/100x100?food" alt="Post" class="post-image">
                    <div class="post-content">
                        <h4><a href="#">Healthy Recipes for Busy Weeknights</a></h4>
                        <span class="post-date">June 5, 2023</span>
                    </div>
                </div>
            </div>

            <div class="sidebar-widget">
                <h3 class="widget-title">Tags</h3>
                <div class="tags">
                    <a href="#" class="tag">Technology</a>
                    <a href="#" class="tag">Travel</a>
                    <a href="#" class="tag">Recipes</a>
                    <a href="#" class="tag">Lifestyle</a>
                    <a href="#" class="tag">Health</a>
                    <a href="#" class="tag">Business</a>
                    <a href="#" class="tag">AI</a>
                    <a href="#" class="tag">Europe</a>
                </div>
            </div>
        </aside>
    </div>

    <!-- Newsletter -->
    <section class="container newsletter">
        <h3>Subscribe to Our Newsletter</h3>
        <p>Stay updated with our latest articles and news. No spam, just valuable content.</p>
        <form class="newsletter-form">
            <input type="email" placeholder="Your email address">
            <button type="submit">Subscribe</button>
        </form>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-widget">
                    <h3>About BlogSpace</h3>
                    <p>BlogSpace is a modern blogging platform where writers share their knowledge, experiences, and stories with a global audience.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="footer-widget">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Categories</a></li>
                        <li><a href="#">Features</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-widget">
                    <h3>Categories</h3>
                    <ul class="footer-links">
                        <li><a href="#">Technology</a></li>
                        <li><a href="#">Travel</a></li>
                        <li><a href="#">Food</a></li>
                        <li><a href="#">Lifestyle</a></li>
                        <li><a href="#">Health</a></li>
                    </ul>
                </div>

                <div class="footer-widget">
                    <h3>Contact Us</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Blog Street, City</li>
                        <li><i class="fas fa-phone"></i> +1 234 567 8900</li>
                        <li><i class="fas fa-envelope"></i> info@blogspace.com</li>
                    </ul>
                </div>
            </div>

            <div class="copyright">
                <p>&copy; 2023 BlogSpace. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu').addEventListener('click', function() {
            document.querySelector('nav').classList.toggle('active');
        });
    </script>
</body>
</html>