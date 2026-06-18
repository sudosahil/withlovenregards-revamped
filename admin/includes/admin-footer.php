<?php
/**
 * Admin shell footer. Closes the layout and loads scripts.
 * If $adminCharts (array) is set, it is exposed to admin.js as window.WLNR_CHARTS
 * and Chart.js is loaded.
 */
?>
        </div><!-- /.admin-content -->
    </div><!-- /.admin-main -->
</div><!-- /.admin-shell -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<?php if (!empty($adminCharts)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>window.WLNR_CHARTS = <?= json_encode($adminCharts, JSON_UNESCAPED_SLASHES) ?>;</script>
<?php endif; ?>
<script src="<?= e(asset('js/admin.js')) ?>"></script>
</body>
</html>
