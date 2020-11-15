<?php /** @var BaseController $_this */ ?>
<?php $_this = $_ci_CI; ?>
<head>
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <title><?= $_this->getData('page_title') ? $_this->getHeaderData('page_title') : lang('task_list'); ?></title>
    <link href="<?= base_url(); ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= base_url(); ?>assets/fontawesome/css/fontawesome-all.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= base_url(); ?>assets/adminlte/css/adminlte.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= base_url(); ?>assets/adminlte/css/skins/skin-blue.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= base_url(); ?>assets/common/css/styles.css" rel="stylesheet" type="text/css">
    <script src="<?= base_url(); ?>assets/jquery/jquery-3.3.1.min.js"></script>
    <script src="<?= base_url(); ?>assets/jquery/jquery.validate.min.js"></script>
    <script src="<?= base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?= base_url(); ?>assets/adminlte/js/adminlte.min.js"></script>
    <?php foreach ($_this->getJs() as $js): ?>
        <script src="<?= base_url() . 'assets/' . $js; ?>"></script>
    <?php endforeach; ?>
</head>
