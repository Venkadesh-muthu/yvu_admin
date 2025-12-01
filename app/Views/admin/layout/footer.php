<!-- include/footer.php -->
<!-- <footer class="main-footer text-center py-3 bg-light mt-auto">
    <p class="mb-0">&copy; 2025 Hospital Health System. All rights reserved.</p>
</footer> -->

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ Then DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggler = document.getElementById('customNavbarToggler');
        const nav = document.getElementById('customNavbarNav');

        if (toggler && nav) {
            toggler.addEventListener('click', () => {
                nav.classList.toggle('d-none');
            });
        }
    });
</script>
<script>
$(document).ready(function() {
    $("#articlesTable").DataTable({
        pageLength: 10,
        ordering: true,
        searching: true,
        lengthMenu: [[10, 25, 50, -1], ["10", "25", "50", "Show All"]],
    });
});
</script>



<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.getElementById('sidebar');
        const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(sidebar);

        // Close offcanvas when a nav-link is clicked (only on mobile)
        document.querySelectorAll('#sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992 && bsOffcanvas) {
                    bsOffcanvas.hide();
                }
            });
        });
    });
</script>




</body>

</html>