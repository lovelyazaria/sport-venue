// Smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function() {
    // Get all anchor links that start with #
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Only apply smooth scroll if it's a hash link (not just #)
            if (href !== '#' && href.startsWith('#')) {
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    
                    // Calculate offset for fixed header
                    const headerOffset = 80;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
    
        // Form validation feedback & DATA HANDLING
    const bookingForm = document.getElementById('booking-form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            // 1. Ambil nilai input
            const duration = parseInt(document.getElementById('duration').value);
            const dateValue = document.getElementById('date').value;
            // Asumsi kamu punya input lain (sesuaikan ID-nya dengan HTML kamu)
            // const sport = document.getElementById('sport').value; 
            // const name = document.getElementById('name').value; 
            
            // 2. Validasi Durasi
            if (duration < 1 || duration > 4) {
                e.preventDefault();
                alert('Duration must be between 1 and 4 hours');
                return false;
            }
            
            // 3. Validasi Tanggal
            const selectedDate = new Date(dateValue);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                e.preventDefault();
                alert('Please select a date that is today or in the future');
                return false;
            }

            // ============================================================
            // PERBAIKAN DI SINI:
            // Jika semua validasi lolos, kita Simpan Data & Pindah Halaman
            // ============================================================
            
            e.preventDefault(); // Cegah submit bawaan browser agar kita bisa handle manual

            // Simpan data ke LocalStorage
            const bookingData = {
                date: dateValue,
                duration: duration,
                // sport: sport, // Hapus tanda // jika ada input sport
                // name: name    // Hapus tanda // jika ada input nama
            };

            localStorage.setItem('dataBookingUser', JSON.stringify(bookingData));

            // Pindah ke halaman submit.html
            window.location.href = 'submit.html';
        });
    
    if (error && window.location.pathname.includes('booking.html')) {
        const errorMessages = {
            'missing_fields': 'Please fill in all required fields.',
            'invalid_email': 'Please enter a valid email address.',
            'past_date': 'Booking date cannot be in the past.',
            'invalid_sport': 'Please select a valid sport.',
            'slot_unavailable': 'Sorry, this time slot is not available. Please choose another time.',
            'database_error': 'An error occurred. Please try again later.'
        };
        
        const message = errorMessages[error] || 'An error occurred. Please try again.';
        
        // Create and display error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.innerHTML = `
            <p>⚠️ ${message}</p>
        `;
        errorDiv.style.cssText = 'background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 20px auto; max-width: 600px; text-align: center; border: 1px solid #f5c6cb;';
        
        const container = document.querySelector('.booking .container');
        if (container) {
            container.insertBefore(errorDiv, container.firstChild.nextSibling);
        }
    }
    
    // Contact form validation
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            const message = document.getElementById('message').value;
            
            if (message.length < 10) {
                e.preventDefault();
                alert('Please enter a message with at least 10 characters');
                return false;
            }
        });
    }
    
    // Active navigation highlight
    const currentLocation = window.location.pathname.split('/').pop() || 'index.html';
    const navLinks = document.querySelectorAll('.nav-links a');
    
    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href').split('#')[0];
        if (linkPath === currentLocation || (currentLocation === '' && linkPath === 'index.html')) {
            link.style.opacity = '1';
            link.style.fontWeight = 'bold';
        }
    });
});

// Add scroll effect to header
let lastScroll = 0;
const header = document.querySelector('header');

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 100) {
        header.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
    } else {
        header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
    }
    
    lastScroll = currentScroll;
});