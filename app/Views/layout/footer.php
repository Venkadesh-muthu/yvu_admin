<!-- Footer -->
<footer class="bg-dark text-white pt-5 mt-5">
  <div class="container text-center small">
  <form class="mb-4">
  <label for="subscribe" class="form-label">Subscribe to Email Alerts</label>
  <div class="input-group justify-content-center">
    <input type="email" class="form-control" id="subscribe" placeholder="Enter your email" style="max-width: 300px;">
    <button class="btn btn-warning" type="submit">Subscribe</button>
  </div>
</form>


    <div class="mb-3">
      <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
      <a href="#" class="text-white me-3"><i class="bi bi-twitter"></i></a>
      <a href="#" class="text-white me-3"><i class="bi bi-linkedin"></i></a>
      <a href="#" class="text-white"><i class="bi bi-envelope"></i></a>
    </div>

    <p>
      <strong>Indian Journal of Advances in Chemical Science</strong> is licensed under a 
      <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank" class="text-warning text-decoration-underline">
        Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
      </a>.<br />
      Based on a work at 
      <a href="http://www.ijacskros.com/" target="_blank" class="text-info text-decoration-underline">ijacskros.com</a>.<br />
      Permissions beyond the scope of this license may be available at 
      <a href="http://www.ijacskros.com/" target="_blank" class="text-info text-decoration-underline">ijacskros.com</a>.
    </p>
    <hr class="bg-light">
    <p class="mb-0">&copy; 2025 <a href="https://www.krospub.com" target="_blank" class="text-light text-decoration-none">KROS Publications</a>. All Rights Reserved.</p>
  </div>

  <a href="#" class="btn btn-warning position-fixed bottom-0 end-0 m-3 shadow rounded-circle" style="width: 45px; height: 45px; z-index: 1050;">
    <i class="bi bi-arrow-up" style="font-size: 1.2rem;"></i>
  </a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init();
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
      const updateCount = () => {
        const target = +counter.getAttribute('data-target');
        const count = +counter.innerText;
        const increment = target / 200;
        if (count < target) {
          counter.innerText = Math.ceil(count + increment);
          setTimeout(updateCount, 10);
        } else {
          counter.innerText = target;
        }
      };
      updateCount();
    });
  </script>
</footer>