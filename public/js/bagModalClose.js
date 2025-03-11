function triggerModalCloseInAllTabs() {
    localStorage.setItem('refreshTriggered', JSON.stringify({
        timestamp: new Date().getTime(),
        pageUrl: window.location.pathname
    }));
}

$(window).on('storage', function(event) {
    if (event.originalEvent.key === 'refreshTriggered') {
        const data = JSON.parse(event.originalEvent.newValue);
        if (data.pageUrl === window.location.pathname) {
            $('#cargo_consignment').modal('hide');
        }
    }
});
