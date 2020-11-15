<?php $_this = $_ci_CI; ?>
<?php $_this->load->helper('form'); ?>
<section class="content-notifications">
    <div class="row">
        <div class="col-md-12">
            <?php $notificationMessages = $_this->session->flashdata('notification'); ?>
            <?php if (isset($notificationMessages['error'])): ?>
                <?php if (!is_array($notificationMessages['error'])) $notificationMessages['error'] = [$notificationMessages['error']]; ?>
                <?php if (count($notificationMessages['error'])): ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php foreach ($notificationMessages['error'] as $error): ?>
                            <?= $error ?><br>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (isset($notificationMessages['success'])): ?>
                <?php if (!is_array($notificationMessages['success'])) $notificationMessages['success'] = [$notificationMessages['success']]; ?>
                <?php if (count($notificationMessages['success'])): ?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php foreach ($notificationMessages['success'] as $success): ?>
                            <?= $success ?><br>
                        <?php endforeach; ?>
                    </div>
                <?php endif ?>
            <?php endif; ?>
        </div>
    </div>
</section>
