<?php /** @var BaseController $_this */ ?>
<?php $_this = $_ci_CI; ?>
<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="header">
                <i class="fas fa-bars"></i>
                <strong><?= lang('main_menu') ?></strong>
            </li>
            <li class="treeview">
                <a href="<?= $_this->getUrl('taskList'); ?>">
                    <i class="fas fa-tasks"></i>
                    <span><?= lang('task_list') ?></span>
                </a>
            </li>
        </ul>
    </section>
</aside>
