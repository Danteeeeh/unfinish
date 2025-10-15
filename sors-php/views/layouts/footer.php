    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4><?php echo APP_NAME; ?></h4>
                <p>Advanced surgery scheduling and operating room management system.</p>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php?route=dashboard">Dashboard</a></li>
                    <li><a href="index.php?route=surgeries/list">Surgeries</a></li>
                    <li><a href="index.php?route=rooms/list">Operating Rooms</a></li>
                    <li><a href="index.php?route=staff/list">Staff</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">Help & Documentation</a></li>
                    <li><a href="#">Contact Support</a></li>
                    <li><a href="#">System Status</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
        </div>
    </footer>

    <style>
        /* Footer Styles */
        .main-footer {
            margin-top: auto;
            background: var(--bg-secondary);
            border-top: 1px solid var(--border-color);
            padding: 2rem 0 1rem;
            margin-left: 280px;
            transition: margin-left 0.3s ease;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-section h4 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .footer-section p {
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section ul li a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: var(--primary-color);
        }

        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .footer-bottom p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        @media (max-width: 1024px) {
            .main-footer {
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .footer-section {
                text-align: center;
            }
        }
    </style>
</body>
</html>
