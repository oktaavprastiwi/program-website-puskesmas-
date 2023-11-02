require('./bootstrap');
require('./noty');

Echo.channel('queue-notifier').listen('FileProcessDone', (res) => {
    noty({
        type: 'success',
        layout: 'bottomRight',
        text: res.message,
        timeout: false
    })
})