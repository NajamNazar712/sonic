$.fn.dataTable.ext.buttons.reset = {
    text: '<i class="la la-refresh"></i> Reset',
    className: 'btn btn-primary mb-1',
    action : function(e, dt) {
        $(dt.table().header()).find('input').val('');
        $(dt.table().header()).find('select').val('').trigger('change.select2');

        dt.columns().search('').draw();
    }
};