document.addEventListener('DOMContentLoaded', function () {

    // === 1. SELEKSI ELEMEN (AMAN) ===
    const fieldSelect   = document.getElementById('field_id');
    const durationInput = document.getElementById('duration');
    const timeInput     = document.getElementById('time');
    const dateInput     = document.getElementById('date');
    const nameInput     = document.getElementById('name');
    const priceDisplay  = document.getElementById('price_display');
    const totalDisplay  = document.getElementById('total_price');
    const bookingForm   = document.getElementById('booking-form');

    // === 2. UPDATE HARGA ===
    function updatePrice() {
        if (!fieldSelect || !durationInput || !priceDisplay || !totalDisplay) return;

        const selectedOption = fieldSelect.options[fieldSelect.selectedIndex];
        const pricePerHour  = parseInt(selectedOption?.dataset.price || 0);
        const duration      = parseInt(durationInput.value || 0);

        priceDisplay.value = pricePerHour
            ? "Rp " + pricePerHour.toLocaleString('id-ID')
            : "Rp 0";

        const totalPrice = pricePerHour * duration;
        totalDisplay.value = totalPrice
            ? "Rp " + totalPrice.toLocaleString('id-ID')
            : "Rp 0";
    }

    if (fieldSelect)   fieldSelect.addEventListener('change', updatePrice);
    if (durationInput) durationInput.addEventListener('input', updatePrice);

    // auto hitung saat load
    updatePrice();

    // === 3. SUBMIT FORM ===
    if (bookingForm) {
        bookingForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // VALIDASI FIELD WAJIB
            if (!nameInput?.value.trim()) {
                alert('Nama wajib diisi');
                return;
            }

            if (!fieldSelect?.value) {
                alert('Pilih lapangan terlebih dahulu');
                return;
            }

            if (!dateInput?.value) {
                alert('Tanggal wajib diisi');
                return;
            }

            if (!timeInput?.value) {
                alert('Jam booking wajib diisi');
                return;
            }

            const duration = parseInt(durationInput.value);
            if (isNaN(duration) || duration < 1 || duration > 4) {
                alert('Durasi harus antara 1–4 jam');
                return;
            }

            // VALIDASI TANGGAL
            const selectedDate = new Date(dateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                alert('Tanggal tidak boleh di masa lalu');
                return;
            }

            // === DATA FINAL ===
            const bookingData = {
                name: nameInput.value.trim(),
                sport: fieldSelect.options[fieldSelect.selectedIndex].text,
                date: dateInput.value,
                time: timeInput.value,
                duration: duration,
                pricePerHour: priceDisplay.value,
                total: totalDisplay.value
            };

            localStorage.setItem('dataBookingUser', JSON.stringify(bookingData));
            window.location.href = 'submit.html';
        });
    }

    // === 4. SMOOTH SCROLL ===
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function (e) {
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
