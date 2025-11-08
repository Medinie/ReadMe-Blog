// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    
    console.log('ReadMe Blog App loaded successfully! ✨');
    
    // Image Preview
    const imageInput = document.querySelector('input[type="file"]');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (5MB max)
                if (file.size > 5000000) {
                    alert('Image size should be less than 5MB!');
                    this.value = '';
                    return;
                }
                
                // Check file type
                if (!file.type.match('image.*')) {
                    alert('Please select an image file!');
                    this.value = '';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    let preview = document.getElementById('imagePreview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.id = 'imagePreview';
                        preview.className = 'image-preview';
                        imageInput.parentNode.appendChild(preview);
                    }
                    preview.innerHTML = '<img src="' + event.target.result + '" alt="Preview">';
                    
                    // Add animation
                    preview.style.opacity = '0';
                    setTimeout(() => {
                        preview.style.transition = 'opacity 0.5s ease';
                        preview.style.opacity = '1';
                    }, 10);
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Delete Confirmation with Animation
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;
            
            // Create custom confirmation dialog
            const confirmBox = document.createElement('div');
            confirmBox.className = 'confirm-dialog';
            confirmBox.innerHTML = `
                <div class="confirm-content">
                    <h3>🗑️ Delete Blog Post?</h3>
                    <p>This action cannot be undone!</p>
                    <div class="confirm-buttons">
                        <button class="btn-danger confirm-yes">Yes, Delete</button>
                        <button class="btn-secondary confirm-no">Cancel</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(confirmBox);
            
            // Add styles dynamically
            const style = document.createElement('style');
            style.textContent = `
                .confirm-dialog {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.7);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                    animation: fadeIn 0.3s ease;
                }
                .confirm-content {
                    background: white;
                    padding: 30px;
                    border-radius: 15px;
                    text-align: center;
                    max-width: 400px;
                    animation: scaleIn 0.3s ease;
                }
                .confirm-content h3 {
                    margin-bottom: 15px;
                    color: #dc3545;
                }
                .confirm-content p {
                    margin-bottom: 25px;
                    color: #666;
                }
                .confirm-buttons {
                    display: flex;
                    gap: 10px;
                    justify-content: center;
                }
                @keyframes scaleIn {
                    from {
                        transform: scale(0.7);
                        opacity: 0;
                    }
                    to {
                        transform: scale(1);
                        opacity: 1;
                    }
                }
            `;
            document.head.appendChild(style);
            
            // Handle button clicks
            confirmBox.querySelector('.confirm-yes').onclick = () => {
                window.location.href = url;
            };
            
            confirmBox.querySelector('.confirm-no').onclick = () => {
                confirmBox.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => confirmBox.remove(), 300);
            };
        });
    });
    
    //Smooth Scroll Animation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    //Form Validation with Animation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#dc3545';
                    field.style.animation = 'shake 0.5s ease';
                    
                    setTimeout(() => {
                        field.style.animation = '';
                    }, 500);
                } else {
                    field.style.borderColor = '#28a745';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showToast('❌ Please fill all required fields!', 'error');
            }
        });
        
        // Remove error styling on input
        const inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.style.borderColor = '#28a745';
                } else {
                    this.style.borderColor = '#e0e0e0';
                }
            });
        });
    });
    
    //Toast Notification System
    window.showToast = function(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast-notification toast-' + type;
        toast.textContent = message;
        
        const style = document.createElement('style');
        style.textContent = `
            .toast-notification {
                position: fixed;
                bottom: 30px;
                right: 30px;
                padding: 15px 25px;
                background: #28a745;
                color: white;
                border-radius: 10px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
                z-index: 9999;
                animation: slideInRight 0.5s ease, slideOutRight 0.5s ease 2.5s;
                font-weight: 500;
            }
            .toast-error {
                background: #dc3545;
            }
            @keyframes slideInRight {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-10px); }
                75% { transform: translateX(10px); }
            }
        `;
        
        if (!document.querySelector('style[data-toast]')) {
            style.setAttribute('data-toast', 'true');
            document.head.appendChild(style);
        }
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    };
    
    
    // Card Hover Effect
    const blogCards = document.querySelectorAll('.blog-card');
    blogCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    // Success message from URL 
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success')) {
        showToast('✅ ' + urlParams.get('success'), 'success');
    }
    
    console.log('🎉 All animations and features loaded!');
});