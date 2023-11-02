/*localStorage.open_tab = Date.now()
localStorage.tabs_opened = 1

onLocalStorageEvent = function(e) {
    if (e.key == 'open_tab') {
        localStorage.more_tabs_open = Date.now()
        localStorage.tabs_opened = 1
    }

    if (e.key == 'more_tabs_open') {
        localStorage.tabs_opened++
    }
}

window.addEventListener('beforeunload', function (e) { 
    e.preventDefault();

    localStorage.test = e.path[0].closed
    e.returnValue = ''

    /*if (localStorage.tabs_opened == 1) {
        e.returnValue = ''
    }*/
//});

//window.addEventListener('storage', onLocalStorageEvent, false);

/*window.addEventListener('pagehide', (event) => {
    if (event.persisted) {
      // If the event's persisted property is `true` the page is about
      // to enter the Back-Forward Cache, which is also in the frozen state.
      console.log('Freeze');
    } else {
      // If the event's persisted property is not `true` the page is
      // about to be unloaded.
      confirm("Press a button!");
      console.log('Terminated');
    }
  }, {capture: true});*/