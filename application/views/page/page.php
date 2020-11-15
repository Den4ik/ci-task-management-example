<?php /** @var BaseController $_this */ ?>
<?php $_this = $_ci_CI; ?>
<!DOCTYPE html>
<html>
<?php $_this->load->view('page/head') ?>
<body class="<?= $_this->getData('body_class') ?> <?= $_this->uri->rsegment(2) ?>-page">
<div class="wrapper">
    <header>
        <?php $_this->load->view('page/header') ?>
    </header>
    <main class="<?php if ($_this->getData('content_class')): ?><?= $_this->getData('content_class') ?><?php else: ?>content-wrapper<?php endif; ?>">
        <?php $_this->load->view('page/notification') ?>
        <?php $_this->renderContent() ?>
    </main>
    <?php $_this->load->view('page/footer') ?>
</div>
</body>
</html>
