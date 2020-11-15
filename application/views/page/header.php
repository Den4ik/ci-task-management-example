<?php /** @var BaseController $_this */ ?>
<?php $_this = $_ci_CI; ?>
<?php if (!$_this->getHeaderData('ignore_header')): ?>
    <header class="main-header">
        <a href="<?= $_this->getUrl('dashboard'); ?>" class="logo">
            <span class="logo-mini"><i class="fas fa-tasks"></i></span>
            <span class="logo-lg"><i class="fas fa-tasks"></i>&nbsp;<?= lang('system_name') ?></span>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </nav>
    </header>
    <?php $_this->loadSidebar(); ?>
<?php endif; ?>
