    </main>
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Servicio Técnico </p>

        <div class="social-links">
            <a href="https://www.instagram.com/ac.serviciotecnico.mardelplata" target="_blank" aria-label="Instagram">
                <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/instagram.svg" alt="Instagram">
            </a>
            <a href="#" target="_blank" aria-label="Facebook">
                <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/facebook.svg" alt="Facebook">
            </a>
        </div>
    </footer>
</body>
</html>

<style>
.footer {
    text-align: center;
    padding: 15px 10px;
    background-color: #1e1e2f;
    color: #fff;
    font-size: 14px;
    margin-top: 40px;
}

.footer .social-links {
    margin-top: 10px;
    display: flex;
    justify-content: center;
    gap: 20px;
}

.footer .social-links a img {
    width: 24px;
    height: 24px;
    filter: invert(1);
    transition: transform 0.2s ease;
}

.footer .social-links a:hover img {
    transform: scale(1.2);
}
</style>
