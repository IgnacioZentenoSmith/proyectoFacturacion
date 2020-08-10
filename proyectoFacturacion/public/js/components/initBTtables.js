document.getElementsByClassName('btTable').forEach(table => {
    $('#' + table.id).bootstrapTable({
        pageSize: 50,
        exportDataType: 'all',
    });
});
