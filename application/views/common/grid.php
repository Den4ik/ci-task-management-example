<?php /** @var BaseController $_this */ ?>
<?php $_this = $_ci_CI; ?>
<?php if ($_this->getContentData('title')): ?>
    <section class="content-header">
        <h1>
            <i class="<?= $_this->getContentData('header_icon_class') ?>"></i>
            &nbsp;<?= $_this->getContentData('title') ?>
        </h1>
    </section>
<?php endif; ?>
<section class="content">
    <div class="row">
        <div class="col-xs-12 text-right">
            <div class="form-group">
                <?php if ($_this->getContentData('allow_send_email')): ?>
                    <a class="btn btn-primary" href="<?= $_this->getContentData('new_email_url') ?>"><i
                                class="fas fa-envelope"></i>&nbsp;<?= lang('email_active') ?></a>
                <?php endif; ?>
                <?php if ($_this->getContentData('allow_add')): ?>
                    <a class="btn btn-primary" href="<?= $_this->getContentData('add_new_url') ?>"><i
                                class="fas fa-plus"></i>&nbsp;<?= lang('add_new') ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?= $_this->getContentData('grid_title') ?></h3>
                    <?php if (false !== $_this->getContentData('allow_search')): ?>
                        <div class="box-tools">
                            <form action="<?= $_this->getContentData('grid_search_url') ?>" method="POST"
                                  id="searchList">
                                <input type="hidden" name="<?= $_this->getData('form_token_name') ?>"
                                       value="<?= $_this->getData('form_token_value') ?>"/>
                                <div class="input-group">
                                    <input type="text" name="search_text"
                                           value="<?= $_this->getContentData('search_text'); ?>"
                                           class="form-control input-sm pull-right" style="width: 150px;"
                                           placeholder="<?= lang('grid_search_placeholder') ?>"/>
                                    <div class="input-group-btn">
                                        <button class="btn btn-sm btn-default searchList"><i
                                                    class="fas fa-search-plus"></i></button>
                                        <?php if ($_this->getContentData('search_text')): ?>
                                            <a href="<?= $_this->getContentData('grid_search_url') ?>"
                                               class="btn btn-sm btn-default"><i class="fas fa-search-minus"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tr>
                            <?php foreach ($_this->getContentData('columns') as $column): ?>
                                <?php if (isset($column['hidden']) && $column['hidden']): ?>
                                    <?php continue; ?>
                                <?php endif; ?>
                                <th<?php if (isset($column['class'])): ?> class="<?= $column['class'] ?>"<?php endif; ?>><?= $column['title'] ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <?php $gridCollection = $_this->getContentData('collection'); ?>
                        <?php if ($gridCollection && $gridCollection->count()): ?>
                            <?php foreach ($gridCollection as $item): ?>
                                <tr <?= ($item->getData('id') && !($item instanceof AdminLoginHistory)) ? 'class="active"' : ''; ?>>
                                    <?php foreach ($_this->getContentData('columns') as $column): ?>
                                        <?php if (isset($column['hidden']) && $column['hidden']): ?>
                                            <?php continue; ?>
                                        <?php endif; ?>
                                        <td<?php if (isset($column['class'])): ?> class="<?= $column['class'] ?>"<?php endif; ?>>
                                            <?php if (!isset($column['links'])): ?>
                                                <?= isset($column['getter']) ? $item->{$column['getter']}() : (isset($column['index']) ? $item->getData($column['index']) : '') ?>
                                            <?php else: ?>
                                                <?php foreach ($column['links'] as $link): ?>
                                                    <?php $url = $link['link'] ?>
                                                    <?php preg_match_all('/{(\w+)}/', $url, $matches); ?>
                                                    <?php if (count($matches) && isset($matches[1])): ?>
                                                        <?php foreach ($matches[1] as $match): ?>
                                                            <?php $url = str_replace('{' . $match . '}', $item->getData($match), $url); ?>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>

                                                    <?php if (isset($link['confirm'])): ?>
                                                        <a class="btn btn-sm <?= $link['btn-class'] ?>"
                                                           href="#"
                                                           onclick="var isConfirm = confirm('<?= $link['confirm'] ?>'); if (isConfirm) window.location.href='<?= $url ?>'; return false;"
                                                           title="<?= $link['btn-title'] ?>"><i
                                                                    class="<?= $link['icon-class'] ?>"></i></a>
                                                    <?php elseif (isset($link['text-button'])): ?>
                                                        <a class="btn btn-sm <?= $link['btn-class'] ?>"
                                                           href="<?= $url ?>"
                                                           title="<?= $link['btn-title'] ?>"><?= isset($link['btn-caption-getter']) ? $item->{$link['btn-caption-getter']}() :
                                                                (isset($link['btn-caption']) ? $item->getData($link['btn-caption']) : '') ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <a class="btn btn-sm <?= $link['btn-class'] ?>"
                                                           href="<?= $url ?>"
                                                           title="<?= $link['btn-title'] ?>"><i
                                                                    class="<?= $link['icon-class'] ?>"></i></a>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= count($_this->getContentData('columns')) ?>"
                                    class="text-center"><?= lang('no_records_found') ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>

                </div>
                <div class="box-footer clearfix">
                    <?= $this->pagination->create_links(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
