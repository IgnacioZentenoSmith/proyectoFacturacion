document.getElementsByClassName('btTable').forEach(table => {
    $('#' + table.id).bootstrapTable({
        pageSize: 100,
        exportDataType: 'all',
    });
});
