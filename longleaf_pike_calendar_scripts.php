<script>
//longleaf_pike_calendar_scripts.js
/////
//supporting and rendering scripts for Longleaf Digital + Pike13 integration calendar app
/////

/* make events a dynamic js array */
var event_list = <?php echo $occurences_json;  ?>;
console.log(event_list);
var current_time = (new Date()).getTime();

//initialize the Calendar
var calendarEl = document.getElementById('calendar');
var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    contentHeight: 'auto',
    events: event_list,
    views: {
        timeGridThreeDay: {
            type: 'timeGrid',
            duration: { days: 3 },
            buttonText: '3 day',
            slotMinTime: "05:00:00",
            slotMaxTime: "20:00:00"
        }
    },
    //functions for responsivness
    windowResize: function() {
        console.log('resize');
        getCalendarView();
    },
    //functions for event actions
    eventClick: function(info) {
        console.log(info.event.id + info.event.title + info.event.extendedProps.description + info.event.extendedProps.enroll_url + info.event.extendedProps.capacity + info.event.extendedProps.staff_member_name + info.event.start);
        jQuery('#longleaf_fc_modal').addClass('fc-eventmodal-' + info.event.id);
        jQuery('#longleaf_fc_modal .dog_image_holder').css("background-image", "url('https://placedog.net/500?random&" + info.event.id + "')");
        jQuery('#longleaf_fc_modal h3.title').text(info.event.title);
        jQuery('#longleaf_fc_modal div.longleaf-modal-content').html(info.event.extendedProps.description);
        jQuery('#longleaf_fc_modal a.button').prop("href", info.event.extendedProps.enroll_url);
        let formatted_occurence_date = moment(info.event.start).format('dddd, MMM Do [at] ha');
        if(info.event.extendedProps.capacity_remaining != null) {
            var occurence_capacity = '<br><span class="capacity">' + info.event.extendedProps.capacity_remaining + ' spots remaining</span>';
        } else {
            var occurence_capacity = '';
        }
        let occurence_details = '<div class="service-details">' + formatted_occurence_date + ' with '+ info.event.extendedProps.staff_member_name + occurence_capacity + '</div><a class="button" href="#">Enroll</a>';
        jQuery('#longleaf_fc_modal .service-info').html(occurence_details);

        //open modal
        jQuery('#longleaf_fc_modal').modal();
    },
	eventDidMount: function(e) {
		weekday = moment(e.event.start).day();
		console.log(e.event.id);

		jQuery(e.el).attr("data-event-id", e.event.id);
		jQuery(e.el).attr("data-event-staff", e.event.extendedProps.staff_member);
		jQuery(e.el).attr("data-event-day", weekday);

		console.log(e.el);
		jQuery(e.el).addClass("fc-filter-active");
	}
});

//inital Calendar render...
document.addEventListener('DOMContentLoaded', function() {
    //render the Calendar after DOM loads the content
    calendar.render();
    //getCalendarView();
});

//function to ease view selection
function getCalendarView() {
	let isMobile = window.matchMedia("screen and (max-width: 767px)").matches;

	if (isMobile) {
		console.log('mobile');
		calendar.changeView('timeGridThreeDay');
	} else {
		console.log('not mobile');
		calendar.changeView('dayGridMonth');
	}
}


///////////////////////////////////
////***** Filtering Logic *****////
///////////////////////////////////

var filterActive;
function filterCategory(topic, staff, weekday) {

    // reset results list
    jQuery('.fc-event').removeClass('fc-filter-active');
    
    // the filtering in action for all criteria
    var selector = ".fc-event";
    if (topic !== 'all') {
         selector = '[data-event-id=' + topic + "]";
    }
    if (staff !== 'all') {
        selector = selector + '[data-event-staff=' + staff + "]";
    }
    if (weekday !== 'all') {
        selector = selector + '[data-event-day=' + weekday + "]";
    }
    
    // show all results
    jQuery(selector).addClass('fc-filter-active');

    // reset active filter
    filterActive = topic;
}

jQuery('#options select').on('change', function() {
    //let activeEvents = calendar.getEvents();
    //let newEvents = filterEvents(activeEvents);
	//console.log(newEvents);
    //console.log(calendar.getEvents());
	filterCategory(jQuery('select.topic').val(), jQuery('select.staff').val(), jQuery('select.weekday').val());
});

</script>