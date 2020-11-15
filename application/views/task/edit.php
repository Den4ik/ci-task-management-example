<?php /** @var BaseController $_this */ ?>
<?php $_this = $_ci_CI; ?>
<?php $task = $_this->getContentData('task'); ?>
<section class="content-header">
    <h1>
        <i class="fas fa-tasks"></i>
        &nbsp;<?= $_this->getContentData('title') ?>
    </h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary">
                <form action="<?= $_this->getUrl('taskSave') ?>" method="post" id="edit_form">
                    <div class="box-body">
                        <input type="hidden" name="<?= $_this->getData('form_token_name') ?>" value="<?= $_this->getData('form_token_value') ?>"/>
                        <?php if ($task->getId()): ?>
                            <input type="hidden" value="<?= $task->getId() ?>" name="task_id" id="task_id"/>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title"><?= lang('title') ?></label>
                                    <input type="text" class="form-control" id="title" placeholder="<?= lang('title') ?>" name="title" value="<?= $task->getData('title') ?>" maxlength="255">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description"><?= lang('description') ?></label>
                                    <textarea class="form-control form-description" id="description" placeholder="<?= lang('description') ?>" name="description"><?= $task->getData('description') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="<?= lang('btn-save') ?>"/>
                        <a href="<?= $_this->getUrl('taskList') ?>" class="btn btn-default"><?= lang('btn-back') ?></a>
                        <input type="reset" class="btn btn-default" value="<?= lang('btn-reset') ?>"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
