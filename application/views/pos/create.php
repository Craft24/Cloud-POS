<?php
require_once(__DIR__ . '/../../models/Ticket_model.php');
?>

<div class="container">
    <div class="row">

        <div class="col-xs-3">

            <?php $error_validmsg = validation_errors(); ?>
            <?php if (!empty($error_validmsg)) : ?>
                <div class="alert alert-warning" role="alert"><?= $error_validmsg ?></div>
            <?php endif; ?>
            <?= form_open("pos/create/$new_ticket->_table_id") ?>
            <fieldset>

                <div id="legend" class="">
                    <legend class="">开台 <?= $new_ticket->_table_name ?></legend>
                    <span class="help-block">当前时间：<?= $new_ticket->_create_time ?></span>
                </div>

                <div class="control-group">
                    <label class="sr-only"  for="menu_count">点菜</label>
                    <input  class="sr-only"  type="input" name="menu_count" id="menu_count" value=""/>

                    <input  class="sr-only"  type="input" name="total_price" id="total_price" value="<?= $new_ticket->_total_price ?>"/>
                    <input  class="sr-only"  type="input" name="discount" id="discount" value="<?= $new_ticket->_discount ?>"/>

                    <label class="control-label" for="menu-list" id="menu-label"><h4>菜单 <small>总单价：0.00 优惠金额：0.00</small></h4></label>
                    <div class="controls">
                        <div class="list-group" name="menu-list" id="menu-list">
                            <?php $item_colos = ['list-group-item-success', 'list-group-item-info', 'list-group-item-warning', 'list-group-item-danger']; ?>
                            <?php foreach ($new_ticket->_items as $item) : ?>
                                <div class="list-group-item <?= $item_colos[($item['menu_id']) % 4] ?>" value="<?= $item['menu_id'] ?>">
                                    <span class="badge"><?= $item['menu_count'] ?></span>
                                    <h4 class="list-group-item-heading"><?= $item['menu_name'] ?></h4>
                                    <p class="list-group-item-text">￥<?= number_format($item['total_price'], 2) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>

                <div class="control-group">

                    <label class="sr-only control-label">Check In</label>
                    <!-- Button -->
                    <div class="controls">
                        <button class="btn btn-success btn-block" id="order-btn" disabled="disabled">下单</button>
                    </div>
                </div>

            </fieldset>
            </form>
        </div>

        <div class="col-xs-9">
            <div id="tabMenuCategory">
                <ul class="nav navbar-default">
                    <?php $id_categories = array(); ?>
                    <?php foreach ($menu_categories as $category) : ?>
                        <li><a href="#tabMenuCategory-<?= $category['id'] ?>"><?= $category['name'] ?></a></li>
                        <?php $id_categories[] = $category['id']; ?>
                    <?php endforeach; ?>
                </ul>
                <?php foreach ($id_categories as $id_category) : ?>
                    <div id="tabMenuCategory-<?= $id_category ?>">
                        <ul class="nav nav-tabs">
                            <?php $id_groups = array(); ?>
                            <?php foreach ($menu_categories as $category) : ?>
                                <?php if ($category['id'] == $id_category) : ?>
                                    <?php foreach ($category['menu_groups'] as $menuGroup) : ?>
                                        <li>
                                            <a href="#tabMenuGroup-<?= $menuGroup['id'] ?>"><?= $menuGroup['name'] ?></a>
                                        </li>
                                        <?php $id_groups[] = $menuGroup['id']; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>

                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?php foreach ($id_groups as $id_group) : ?>
                                    <div id="tabMenuGroup-<?= $id_group ?>">
                                        <div class="row">
                                            <?php foreach ($menu_categories as $category) : ?>
                                                <?php foreach ($category['menu_groups'] as $menuGroup) : ?>
                                                    <?php if ($menuGroup['id'] == $id_group) : ?>
                                                        <?php foreach ($menuGroup['menus'] as $menu) : ?>

                                                            <div class="col-xs-6 col-md-3">
                                                                <a href=javascript:addMenu('<?= $menu['id'] ?>','<?= $menu['name'] ?>','<?= number_format($menu['price'], 2) ?>')>
<!--                                                                <a href=javascript:addMenu('1','2','3')>-->
                                                                    <div class="thumbnail">
                                                                        <img
                                                                            src="<?= base_url('/uploads/default.png') ?>"/>
                                                                        <h4 class="text-center"><?= $menu['name'] ?>
                                                                            <small>
                                                                                ￥<?= number_format($menu['price'], 2) ?></small>
                                                                        </h4>
                                                                    </div>
                                                                </a>
                                                            </div>

                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
            <!-- end of tabMenuCategory -->
        </div>
    </div>
</div>


