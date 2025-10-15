<!-- Footer -->
<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-brand">
                    <i class="fas fa-leaf"></i>
                    <span>NutriTrack Pro</span>
                </div>
                <p class="footer-description">
                    Advanced nutrition tracking and meal planning system for better health management.
                </p>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="../index.php">Dashboard</a></li>
                    <li><a href="../views/food/search_foods.php">Food Search</a></li>
                    <li><a href="../controllers/meal/planner.php">Meal Planner</a></li>
                    <li><a href="../views/goals/index.php">Goals</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Support</h4>
                <ul class="footer-links">
                    <li><a href="../views/user/profile.php">Profile</a></li>
                    <li><a href="../views/user/settings.php">Settings</a></li>
                    <li><a href="../help.php">Help & FAQ</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Connect</h4>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2024 NutriTrack Pro. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Footer Styles -->
<style>
.main-footer {
    background: var(--bg-tertiary);
    border-top: 1px solid var(--border-color);
    margin-top: auto;
    padding: 3rem 0 1rem;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer-section h4 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.footer-brand {
    display: flex;
    align-items: center;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.footer-brand i {
    margin-right: 0.5rem;
}

.footer-description {
    color: var(--text-secondary);
    line-height: 1.6;
}

.footer-links {
    list-style: none;
}

.footer-links li {
    margin-bottom: 0.5rem;
}

.footer-links a {
    color: var(--text-secondary);
    text-decoration: none;
    transition: color 0.2s ease;
}

.footer-links a:hover {
    color: var(--primary-color);
}

.social-links {
    display: flex;
    gap: 1rem;
}

.social-link {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--bg-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 0.2s ease;
}

.social-link:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
}

.footer-bottom {
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
    color: var(--text-secondary);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .footer-container {
        padding: 0 1rem;
    }

    .footer-content {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}
</style>

</body>
</html>
