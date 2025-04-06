</main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0"></script>
    <script>
    function processApproval(id, action) {
        $('#loading-overlay').show();
        
        $.ajax({
            url: '<?= ADMIN_URL ?>api/process_approval.php',
            type: 'POST',
            data: {
                id: id,
                action: action,
                csrf_token: '<?= $_SESSION['csrf_token'] ?? '' ?>'
            },
            success: function(response) {
                if(response.success) {
                    Toastify({
                        text: "Action completed successfully",
                        duration: 3000,
                        backgroundColor: "#28a745"
                    }).showToast();
                    
                    setTimeout(() => location.reload(), 1000);
                } else {
                    $('#loading-overlay').hide();
                    Toastify({
                        text: response.message || "Error processing request",
                        duration: 3000,
                        backgroundColor: "#dc3545"
                    }).showToast();
                }
            },
            error: function() {
                $('#loading-overlay').hide();
                Toastify({
                    text: "Network error occurred",
                    duration: 3000,
                    backgroundColor: "#dc3545"
                }).showToast();
            }
        });
    }
    </script>
</body>
</html>