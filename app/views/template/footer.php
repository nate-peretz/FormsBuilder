        <!-- App files -->
        <script src="/app/views/js/jquery.min.js"></script>
        <script src="/app/views/js/classes/forms.js"></script>
        <script src="/app/views/js/general.js"></script>

        <?php if( isset( $_SESSION['user_ip'])) { ?>
            <script>var user_ip = '<?= $_SESSION['user_ip'] ?>';</script>
        <?php } ?>
    </body>
</html>
