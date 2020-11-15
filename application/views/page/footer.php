<?php /** @var BaseController $_this */ ?>
<?php $_this = $_ci_CI; ?>
<footer class="<?php if ($_this->uri->rsegment(2) != NOT_FOUND_ACTION): ?>main-<?php endif; ?>footer">
    <div class="footer-copyright text-<?php if ($_this->uri->rsegment(2) != NOT_FOUND_ACTION): ?>right<?php else: ?>center<?php endif; ?>">
        <strong>Copyright &copy; 2020 <a href="<?= base_url(); ?>"></strong> <?= lang('footer_copyright') ?>
    </div>
</footer>
