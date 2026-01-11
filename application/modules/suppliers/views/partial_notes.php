<?php foreach ($supplier_notes as $supplier_note) : ?>
    <div class="panel panel-default small">
        <div class="panel-body">
            <?php echo nl2br(htmlsc($supplier_note->supplier_note)); ?>
        </div>
        <div class="panel-footer text-muted">
            <?php echo date_from_mysql($supplier_note->supplier_note_date, true); ?>
        </div>
    </div>
<?php endforeach; ?>
