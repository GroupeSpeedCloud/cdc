
<div id="sidebar-overlay"></div>

<script>
(function () {
  var toggle  = document.getElementById('menu-toggle');
  var sidebar = document.getElementById('sidebar');
  var overlay = document.getElementById('sidebar-overlay');
  var close   = document.getElementById('sidebar-close');

  function openSidebar() {
    sidebar.classList.add('open');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  if (toggle)  toggle.addEventListener('click', openSidebar);
  if (overlay) overlay.addEventListener('click', closeSidebar);
  if (close)   close.addEventListener('click', closeSidebar);

  /* Close sidebar when navigating (touch UX) */
  document.querySelectorAll('#sidebar a').forEach(function (a) {
    a.addEventListener('click', closeSidebar);
  });
}());
</script>
</body>
</html>
