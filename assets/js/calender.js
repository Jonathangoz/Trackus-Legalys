
var inst = mobiscroll.eventcalendar('#demo-desktop-month-view', {
    locale: mobiscroll.localeEs,
    theme: 'windows',
    themeVariant: 'light',
    clickToCreate: true,
    dragToCreate: true,
    dragToMove: true,
    dragToResize: true,
    eventDelete: true,
    view: {
      calendar: { labels: true },
    },
    onEventClick: function (args) {
      mobiscroll.toast({
        message: args.event.title,
      });
    },
  });
  
  mobiscroll.getJson(
    'https://trial.mobiscroll.com/events/?vers=5',
    function (events) {
      inst.setEvents(events);
    },
    'jsonp',
  );
    