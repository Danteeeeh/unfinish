<?php $pageTitle = 'Future Features'; ?>
<?php include __DIR__ . '/../views/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-rocket"></i> Future Features</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php?route=admin" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Back to Admin
                    </a>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <!-- Future Features Hero -->
            <div class="jumbotron bg-gradient-primary text-white mb-4">
                <div class="container">
                    <h1 class="display-4">🚀 Exciting Features Coming Soon!</h1>
                    <p class="lead">Discover the innovative features we're developing to enhance your laboratory experience</p>
                </div>
            </div>

            <!-- Feature Roadmap -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Mobile App</h5>
                            <p class="card-text">Native mobile applications for iOS and Android with offline sample tracking and real-time notifications.</p>
                            <span class="badge bg-info">In Development</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-qrcode fa-3x text-success mb-3"></i>
                            <h5 class="card-title">QR Code Integration</h5>
                            <p class="card-text">Scan QR codes for instant sample identification, automated data entry, and streamlined workflow management.</p>
                            <span class="badge bg-warning">Planned</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-robot fa-3x text-info mb-3"></i>
                            <h5 class="card-title">AI-Powered Analysis</h5>
                            <p class="card-text">Machine learning algorithms for automated result interpretation, anomaly detection, and quality assurance.</p>
                            <span class="badge bg-success">In Testing</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-network fa-3x text-warning mb-3"></i>
                            <h5 class="card-title">Advanced Analytics</h5>
                            <p class="card-text">Comprehensive dashboards with predictive analytics, trend analysis, and performance metrics for lab optimization.</p>
                            <span class="badge bg-secondary">Planned</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-video fa-3x text-danger mb-3"></i>
                            <h5 class="card-title">Telemedicine Integration</h5>
                            <p class="card-text">Video consultation features for remote patient care coordination and real-time result discussion.</p>
                            <span class="badge bg-primary">Planned</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-satellite-dish fa-3x text-dark mb-3"></i>
                            <h5 class="card-title">IoT Device Integration</h5>
                            <p class="card-text">Connect laboratory equipment and sensors for automated data collection and real-time monitoring.</p>
                            <span class="badge bg-light text-dark">Planned</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Development Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-calendar-alt"></i> Development Roadmap</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Q4 2024</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Mobile App Beta</li>
                                <li><i class="fas fa-clock text-warning"></i> QR Code Integration</li>
                                <li><i class="fas fa-clock text-warning"></i> Enhanced Security</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6>Q1 2025</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-clock text-warning"></i> AI Analysis Engine</li>
                                <li><i class="fas fa-clock text-warning"></i> Advanced Analytics</li>
                                <li><i class="fas fa-clock text-warning"></i> Multi-language Support</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6>Q2 2025</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-clock text-warning"></i> Telemedicine Features</li>
                                <li><i class="fas fa-clock text-warning"></i> IoT Integration</li>
                                <li><i class="fas fa-clock text-warning"></i> Cloud Sync</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature Request -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-lightbulb"></i> Suggest New Features</h5>
                </div>
                <div class="card-body">
                    <p>Have ideas for new features? We'd love to hear from you!</p>
                    <button class="btn btn-primary" onclick="showFeatureRequestModal()">
                        <i class="fas fa-plus"></i> Submit Feature Request
                    </button>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Feature Request Modal -->
<div id="featureRequestModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Feature Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="featureRequestForm">
                    <div class="mb-3">
                        <label for="featureTitle" class="form-label">Feature Title</label>
                        <input type="text" class="form-control" id="featureTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="featureDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="featureDescription" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="featurePriority" class="form-label">Priority</label>
                        <select class="form-select" id="featurePriority">
                            <option>Low</option>
                            <option selected>Medium</option>
                            <option>High</option>
                            <option>Critical</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitFeatureRequest()">Submit Request</button>
            </div>
        </div>
    </div>
</div>

<script>
function showFeatureRequestModal() {
    const modal = new bootstrap.Modal(document.getElementById('featureRequestModal'));
    modal.show();
}

function submitFeatureRequest() {
    const title = document.getElementById('featureTitle').value;
    const description = document.getElementById('featureDescription').value;
    const priority = document.getElementById('featurePriority').value;

    if (title && description) {
        // In a real application, this would send data to the server
        alert(`Feature Request Submitted!\n\nTitle: ${title}\nPriority: ${priority}\n\nThank you for your suggestion!`);

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('featureRequestModal'));
        modal.hide();

        // Reset form
        document.getElementById('featureRequestForm').reset();
    } else {
        alert('Please fill in all required fields.');
    }
}

// Add smooth animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate feature cards on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    document.querySelectorAll('.card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});
</script>

<?php include __DIR__ . '/../views/layouts/footer.php'; ?>
