</div><!-- #layout -->

<script>
(function () {
  var sidebar  = document.getElementById('sidebar');
  var overlay  = document.getElementById('sidebar-overlay');
  var toggle   = document.getElementById('menu-toggle');
  var closeBtn = document.getElementById('sidebar-close');
  function open()  { sidebar.classList.add('open');  overlay.classList.add('active'); }
  function close() { sidebar.classList.remove('open'); overlay.classList.remove('active'); }
  if (toggle)   toggle.addEventListener('click', open);
  if (closeBtn) closeBtn.addEventListener('click', close);
  if (overlay)  overlay.addEventListener('click', close);
}());

// Chart.js global defaults
if (typeof Chart !== 'undefined') {
  Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
  Chart.defaults.font.size   = 12;
  Chart.defaults.color       = '#64748b';
  Chart.defaults.borderColor = '#e2e8f0';
  Chart.defaults.plugins.legend.labels.boxWidth  = 10;
  Chart.defaults.plugins.legend.labels.padding   = 14;
  Chart.defaults.plugins.tooltip.backgroundColor = '#1e293b';
  Chart.defaults.plugins.tooltip.titleColor      = '#f1f5f9';
  Chart.defaults.plugins.tooltip.bodyColor       = '#cbd5e1';
  Chart.defaults.plugins.tooltip.cornerRadius    = 8;
  Chart.defaults.plugins.tooltip.padding         = 10;
  Chart.defaults.plugins.tooltip.callbacks = Chart.defaults.plugins.tooltip.callbacks || {};
}

const CHART_COLORS = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4','#84cc16'];
</script>
</body>
</html>
