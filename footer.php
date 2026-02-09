<footer>
        <div class="container">
            <div class="footer-content">
                <p>&copy; <?php echo date("Y"); ?> Sports Field Booking. All rights reserved.</p>
                <ul class="footer-links">
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <style>
        footer {
            background-color: #222;
            color: #fff;
            padding: 20px 0;
            margin-top: 50px;
            text-align: center;
        }
        .footer-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .footer-links {
            list-style: none;
            display: flex;
            gap: 15px;
            padding: 0;
        }
        .footer-links a {
            color: #aaa;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }
        .footer-links a:hover {
            color: #2ecc71;
        }
    </style>