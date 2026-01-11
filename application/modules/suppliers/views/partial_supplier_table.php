
<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th><?php _trans('active'); ?></th>
            <th><?php _trans('supplier_name'); ?></th>
            <th><?php _trans('email_address'); ?></th>
            <th><?php _trans('phone_number'); ?></th>
            <th class="amount"><?php _trans('balance'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $supplier) : ?>
            <tr>
				<td>
					<?php echo ($supplier->supplier_active) ? '<span class="label active">' . trans('yes') . '</span>' : '<span class="label inactive">' . trans('no') . '</span>'; ?>
				</td>
                <td><?php echo anchor('suppliers/view/' . $supplier->supplier_id, htmlsc(format_supplier($supplier))); ?></td>
                <td><?php _htmlsc($supplier->supplier_email); ?></td>
                <td><?php _htmlsc($supplier->supplier_phone ? $supplier->supplier_phone : ($supplier->supplier_mobile ? $supplier->supplier_mobile : '')); ?></td>
                <td class="amount"><?php echo format_currency($supplier->supplier_invoice_balance); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('suppliers/view/' . $supplier->supplier_id); ?>">
                                    <i class="fa fa-eye fa-margin"></i> <?php _trans('view'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('suppliers/form/' . $supplier->supplier_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="supplier-create-quote"
                                   data-supplier-id="<?php echo $supplier->supplier_id; ?>">
                                    <i class="fa fa-file fa-margin"></i> <?php _trans('create_quote'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="supplier-create-invoice"
                                   data-supplier-id="<?php echo $supplier->supplier_id; ?>">
                                    <i class="fa fa-file-text fa-margin"></i> <?php _trans('create_invoice'); ?>
                                </a>
                            </li>
                            <li>
                                <form action="<?php echo site_url('suppliers/delete/' . $supplier->supplier_id); ?>"
                                      method="POST">
                                    <?php _csrf_field(); ?>
                                    <button type="submit" class="dropdown-button"
                                            onclick="return confirm('<?php _trans('delete_supplier_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
