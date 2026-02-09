<header>
    <nav>
        <div class="logo">
            <h1>Sports Field Booking</h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="index.html#sports">Sports</a></li>
            <li><a href="booking.html">Booking</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="contact.html">Contact</a></li>
        </ul>
    </nav>
</header>

<style>
    /* Tambahan CSS dasar agar header terlihat rapi jika belum ada di style.css */
    header {
        background-color: #333;
        color: white;
        padding: 1rem 0;
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    nav {
        display: flex;
        justify-content: space-around;
        align-items: center;
        max-width: 1200px;
        margin: auto;
    }
    .nav-links {
        display: flex;
        list-style: none;
        gap: 20px;
    }
    .nav-links a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
    }
    .nav-links a:hover {
        color: #2ecc71; /* Warna hijau sukses */
    }
    .logo h1 {
        font-size: 1.5rem;
        margin: 0;
    }
</style>