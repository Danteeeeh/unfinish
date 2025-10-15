    <footer style="background: var(--bg-secondary); border-top: 1px solid var(--border-color); padding: 2rem 0; margin-top: auto;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <span style="color: var(--text-muted);">&copy; <?php echo date('Y'); ?> <?php echo APP_NAME ?? 'NutriTrack Pro'; ?>. All rights reserved.</span>
                </div>
                <div style="text-align: right;">
                    <span style="color: var(--text-muted);">Version <?php echo APP_VERSION ?? '1.0'; ?></span>
                </div>
            </div>
        </div>
    </footer>

    <style>
        /* Enhanced Footer Styling */
        .footer .text-muted {
            color: var(--text-muted) !important;
            font-size: 0.875rem;
        }
    </style>
