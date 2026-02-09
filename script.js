document.addEventListener('DOMContentLoaded', function() {
    // --- 1. SELEKSI ELEMEN ---
    const fieldSelect = document.getElementById('field_id');
    const durationInput = document.getElementById('duration');
    const timeInput = document.getElementById('time'); // ✅ TAMBAHAN
    const priceDisplay = document.getElementById('price_display');
    const totalDisplay = document.getElementById('total_price');
    const bookingForm = document.getElementById('booking-form');

    // --- 2. FUNGSI UPDATE HARGA ---
    function updatePrice() {
        if (!fieldSelect || !priceDisplay) return;

        const selectedOption = fieldSelect.options[fieldSelect.selectedIndex];
        const pricePerHour = parseInt(selectedOption.getAttribute('data-price')) || 0;
        const duration = parseInt(durationInput.value) || 0;

        priceDisplay.value = pricePerHour > 0
            ? "Rp " + pricePerHour.toLocaleString('id-ID')
            : "Rp 0";

        const totalPrice = pricePerHour * duration;
        if (totalDisplay) {
            totalDisplay.value = totalPrice > 0
                ? "Rp " + totalPrice.toLocaleString('id-ID')
                : "Rp 0";
        }
    }

    if (fieldSelect) fieldSelect.addEventListener('change', updatePrice);
    if (durationInput) durationInput.addEventListener('input', updatePrice);

    // --- 3. FORM SUBMISSION & STORAGE ---
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const duration = parseInt(durationInput.value);
            const dateValue = document.getElementById('date').value;
            const timeValue = timeInput.value; // ✅ FIX JAM

            // VALIDASI JAM
            if (!timeValue) {
                alert('Jam booking wajib diisi');
                return;
            }

            if (duration < 1 || duration > 4) {
                alert('Duration must be between 1 and 4 hours');
                return;
            }

            const selectedDate = new Date(dateValue);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                alert('Please select a valid date');
                return;
            }

            // ✅ DATA LENGKAP & KONSISTEN
            const bookingData = {
                name: document.getElementById('name').value,
                sport: fieldSelect.options[fieldSelect.selectedIndex].text,
                date: dateValue,
                time: timeValue, // ✅ SEKARANG PASTI MASUK
                duration: duration,
                total: totalDisplay.value
            };

            localStorage.setItem('dataBookingUser', JSON.stringify(bookingData));
            window.location.href = 'submit.html';
        });
    }

    // --- 4. SMOOTH SCROLLING ---
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const target = document.getElementById(this.getAttribute('href').substring(1));
            if (target) {
                e.preventDefault();
                window.scrollTo({
                    top: target.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
});
