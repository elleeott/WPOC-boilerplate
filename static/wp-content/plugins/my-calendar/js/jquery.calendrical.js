(function($) { 
    var monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
        
    function getToday()
    {
        var date = new Date();
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }
    
    function areDatesEqual(date1, date2)
    {
        return String(date1) == String(date2);
    }
    
    function daysInMonth(year, month)
    {
        if (year instanceof Date) return daysInMonth(year.getFullYear(), year.getMonth());
        if (month == 1) {
            var leapYear = (year % 4 == 0) &&
                (!(year % 100 == 0) || (year % 400 == 0));
            return leapYear ? 29 : 28;
        } else if (month == 3 || month == 5 || month == 8 || month == 10) {
            return 30;
        } else {
            return 31;
        }
    }
    
    function dayAfter(date)
    {
        var year = date.getFullYear();
        var month = date.getMonth();
        var day = date.getDate();
        var lastDay = daysInMonth(date);
        return (day == lastDay) ?
            ((month == 11) ?
                new Date(year + 1, 0, 1) :
                new Date(year, month + 1, 1)
            ) :
            new Date(year, month, day + 1);
    }
    
    function dayBefore(date)
    {
        var year = date.getFullYear();
        var month = date.getMonth();
        var day = date.getDate();
        return (day == 1) ?
            ((month == 0) ?
                new Date(year - 1, 11, daysInMonth(year - 1, 11)) :
                new Date(year, month - 1, daysInMonth(year, month - 1))
            ) :
            new Date(year, month, day - 1);
    }
    
    function monthAfter(year, month)
    {
        return (month == 11) ?
            new Date(year + 1, 0, 1) :
            new Date(year, month + 1, 1);
    }
    
    function formatDate(date)
    {
		var d = date.getDate();
		var m = date.getMonth() + 1;
		var dlen = d.toString();
		var mlen = m.toString();
		var day = ( dlen.length == 2 ) ? d : '0' + d;
		var month = ( mlen.length == 2 ) ? ( m ) : '0' + m;
		return (date.getFullYear() + '-' + month + '-' + day );
    }
    
    function parseDate(date)
    {
        a = date.split(/[\.\-\/]/);
        var year = a.shift();
        var month = a.shift()-1;
		var day = a.shift();
        return new Date( year, month, day );
    }
    
    function formatTime(hour, minute)
    {
        var printHour = hour % 12;
        if (printHour == 0) printHour = 12;
        var printMinute = minute;
        if (minute < 10) printMinute = '0' + minute;
        var half = (hour < 12) ? 'am' : 'pm';
        
        return printHour + ':' + printMinute + half;
    }
    
    function parseTime(text)
    {
        var match = match = /(\d+)\s*[:\-\.,]\s*(\d+)\s*(am|pm)?/i.exec(text);
        if (match && match.length >= 3) {
            var hour = Number(match[1]);
            var minute = Number(match[2])
            if (hour == 12 && match[3]) hour -= 12;
            if (match[3] && match[3].toLowerCase() == 'pm') hour += 12;
            return {
                hour:   hour,
                minute: minute
            };
        } else {
            return null;
        }
    }
    
  /**
     * Generates calendar header, with month name, << and >> controls, and
     * initials for days of the week.
     */
    function renderCalendarHeader(element, year, month, options)
    {
        //Prepare thead element
        var thead = $('<thead />');
        var titleRow = $('<tr />').appendTo(thead);
        
        //Generate << (back a month) link
        $('<th />').addClass('monthCell').append(
          $('<a href="javascript:;">&laquo;</a>')
                  .addClass('prevMonth')
                  .mousedown(function(e) {
                      renderCalendarPage(element,
                          month == 0 ? (year - 1) : year,
                          month == 0 ? 11 : (month - 1), options
                      );
                      e.preventDefault();
                  })
        ).appendTo(titleRow);
        
        //Generate month title
        $('<th />').addClass('monthCell').attr('colSpan', 5).append(
            $('<a href="javascript:;">' + monthNames[month] + ' ' +
                year + '</a>').addClass('monthName')
        ).appendTo(titleRow);
        
        //Generate >> (forward a month) link
        $('<th />').addClass('monthCell').append(
            $('<a href="javascript:;">&raquo;</a>')
                .addClass('nextMonth')
                .mousedown(function() {
                    renderCalendarPage(element,
                        month == 11 ? (year + 1) : year,
                        month == 11 ? 0 : (month + 1), options
                    );
                })
        ).appendTo(titleRow);
        
        //Generate weekday initials row
        var dayNames = $('<tr />').appendTo(thead);
        $.each(String('SMTWTFS').split(''), function(k, v) {
            $('<th />').addClass('dayName').append(v).appendTo(dayNames);
        });
        
        return thead;
    }
    
    function renderCalendarPage(element, year, month, options)
    {
        options = options || {};
        
        var today = getToday();
        
        var date = new Date(year, month, 1);
        
        //Wind end date forward to saturday week after month
        var endDate = monthAfter(year, month);
        var ff = 6 - endDate.getDay();
        if (ff < 6) ff += 7;
        for (var i = 0; i < ff; i++) endDate = dayAfter(endDate);
        
        var table = $('<table />');
        renderCalendarHeader(element, year, month, options).appendTo(table);
        
        var tbody = $('<tbody />').appendTo(table);
        var row = $('<tr />');

        //Rewind date to monday week before month
        var rewind = date.getDay() + 7;
        for (var i = 0; i < rewind; i++) date = dayBefore(date);
        
        while (date <= endDate) {
            var td = $('<td />')
                .addClass('day')
                .append(
                    $('<a href="javascript:;">' +
                        date.getDate() + '</a>'
                    ).click((function() {
                        var thisDate = date;
                        
                        return function() {
                            if (options && options.selectDate) {
                                options.selectDate(thisDate);
                            }
                        }
                    }()))
                )
                .appendTo(row);
            
            var isToday     = areDatesEqual(date, today);
            var isSelected  = options.selected &&
                                areDatesEqual(options.selected, date);
            
            if (isToday)                    td.addClass('today');
            if (isSelected)                 td.addClass('selected');
            if (isToday && isSelected)      td.addClass('today_selected');
            if (date.getMonth() != month)   td.addClass('nonMonth');
            
            dow = date.getDay();
            if (dow == 6) {
                tbody.append(row);
                row = $('<tr />');
            }
            date = dayAfter(date);
        }
        if (row.children().length) {
            tbody.append(row);
        } else {
            row.remove();
        }
        
        element.empty().append(table);
    }
    
	function roundNumber( num, dec ) {
		var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
		return result;	
	}
	
    function renderTimeSelect(element, options)
    {
        var selection = options.selection && parseTime(options.selection);
        if (selection) {
            selection.minute = Math.floor(selection.minute / 15.0) * 15;
        }
        var startTime = options.startTime &&
            (options.startTime.hour * 60 + options.startTime.minute);
        
        var scrollTo;   //Element to scroll the dropdown box to when shown
        var ul = $('<ul />');
		var first = $('<li />').append(
                        $('<a href="javascript:;">All day</a>')
                        .click(function() {
                            if (options && options.selectTime) {
                                options.selectTime('');
                            }
                        }).mousemove(function() {
                            $('li.selected', ul).removeClass('selected');
                        })
                    ).appendTo(ul);
        for (var hour = 0; hour < 24; hour++) {
            for (var minute = 0; minute < 60; minute += 15) {
                //if (startTime && startTime > (hour * 60 + minute)) continue;

                (function() {
                    var timeText = formatTime(hour, minute);
                    var fullText = timeText;
                    if (startTime != null) {
                        var duration = roundNumber( ( (hour * 60 + minute) - startTime ), 2 );
                        if (duration < 60 && duration >= 0) {
                            fullText += ' (' + duration + ' mins)';
                        } else if (duration == 60) {
                            fullText += ' (1 hr)';
                        } else {
							var long_duration = roundNumber( ( duration / 60.0 ), 2 );
                            fullText += ' (' + long_duration + ' hrs)';
                        }
                    }
                    var li = $('<li />').append(
                        $('<a href="javascript:;">' + fullText + '</a>')
                        .click(function() {
                            if (options && options.selectTime) {
                                options.selectTime(timeText);
                            }
                        }).mousemove(function() {
                            $('li.selected', ul).removeClass('selected');
                        })
                    ).appendTo(ul);
                    
                    //Set to scroll to the default hour, unless already set
                    if (!scrollTo && hour == options.defaultHour) {
                        scrollTo = li;
                    }
                    
                    if (selection &&
                        selection.hour == hour &&
                        selection.minute == minute)
                    {
                        //Highlight selected item
                        li.addClass('selected');
                        //Set to scroll to the selected hour
                        //
                        //This is set even if scrollTo is already set, since scrolling to selected hour is more important than
                        //scrolling to default hour
                        scrollTo = li;
                    }
                })();
            }
        }
		var last = $('<li />').append(
                        $('<a href="javascript:;">12:00am</a>')
                        .click(function() {
                            if (options && options.selectTime) {
                                options.selectTime('12:00am');
                            }
                        }).mousemove(function() {
                            $('li.selected', ul).removeClass('selected');
                        })
                    ).appendTo(ul);		
        if (scrollTo) {
            //Set timeout of zero so code runs immediately after any calling
            //functions are finished (this is needed, since box hasn't been
            //added to the DOM yet)
            setTimeout(function() {
                //Scroll the dropdown box so that scrollTo item is in the middle
                element[0].scrollTop =
                    scrollTo[0].offsetTop - scrollTo.height() * 2;
            }, 0);
        }
        element.empty().append(ul);
    }
    
    $.fn.calendricalDate = function( options )
    {
        options = options || {};
        options.padding = options.padding || 4;
        
        return this.each(function() {
            var element = $(this);
            var div;
            var within = false;
            
            element.bind('focus click', function() {
                if (div) return;
                var offset = element.position();
                var padding = element.css('padding-left');
                div = $('<div />')
					.addClass('calendricalDatePopup')
                    .mouseenter(function() { within = true; })
                    .mouseleave(function() { within = false; })
                    .mousedown(function(e) {
                        e.preventDefault();
                    })
                    .css({
                        position: 'absolute',
                        left: offset.left,
                        top: offset.top + element.height
                    });
                element.after(div); 
                
                var selected = parseDate(element.val());
                if (!selected.getFullYear()) selected = getToday();
                
                renderCalendarPage(
                    div,
                    selected.getFullYear(),
                    selected.getMonth(), {
                        selected: selected,
                        selectDate: function(date) {
							within = false;
                            element.val(formatDate(date));
                            div.remove();
                            div = null;
                            if (options.endDate) {
                                var endDate = parseDate(
                                    options.endDate.val()
                                );
                                if (endDate >= selected) {
                                    options.endDate.val(formatDate(
                                        new Date(
                                            date.getTime() +
                                            endDate.getTime() -
                                            selected.getTime()
                                        )
                                    ));
                                }
                            }
                        }
                    }
                );
            }).blur(function() {
                if (within){
                    if (div) element.focus();
                    return;
                }
                if (!div) return;
                div.remove();
                div = null;
            });
        });
    };
    
    $.fn.calendricalDateRange = function(options)
    {
        if (this.length >= 2) {
            $(this[0]).calendricalDate($.extend({
                endDate:   $(this[1])
            }, options));
            $(this[1]).calendricalDate(options);
        }
        return this;
    };
    
    $.fn.calendricalTime = function(options)
    {
        options = options || {};
        options.padding = options.padding || 4;
        
        return this.each(function() {
            var element = $(this);
            var div;
            var within = false;
            
			element.attr( "autocomplete", "off" );
            element.bind('focus click', function() {
                if (div) return;

                var useStartTime = options.startTime;
                if (useStartTime) {
                    if (options.startDate && options.endDate &&
                        !areDatesEqual(parseDate(options.startDate.val()),
                            parseDate(options.endDate.val())))
                        useStartTime = true;
                }

                var offset = element.position();
                div = $('<div />')
                    .addClass('calendricalTimePopup')
                    .mouseenter(function() { within = true; })
                    .mouseleave(function() { within = false; })
                    .mousedown(function(e) {
                        e.preventDefault();
                    })
                    .css({
                        position: 'absolute',
                        left: offset.left,
                        top: offset.top + element.height
                    });
                if (useStartTime) {
                    div.addClass('calendricalEndTimePopup');
                }

                element.after(div); 
                
                var opts = {
                    selection: element.val(),
                    selectTime: function(time) {
                        within = false;
                        element.val(time);
                        div.remove();
                        div = null;
                    },
                    defaultHour: (options.defaultHour != null) ?
                                    options.defaultHour : 8
                };
                
                if (useStartTime) {
                    opts.startTime = parseTime(options.startTime.val());
                }
                
                renderTimeSelect(div, opts);
            }).blur(function() {
				if (within){
                    if (div) element.focus();
                    return;
                }
                if (!div) return;
                div.remove();
                div = null;
            });
        });
    },
    
    $.fn.calendricalTimeRange = function(options)
    {
        if (this.length >= 2) {
            $(this[0]).calendricalTime(options);
            $(this[1]).calendricalTime($.extend({
                startTime: $(this[0])
            }, options));
        }
        return this;
    };

    $.fn.calendricalDateTimeRange = function(options)
    {
        if (this.length >= 4) {
            $(this[0]).calendricalDate($.extend({
                endDate:   $(this[2])
            }, options));
            $(this[1]).calendricalTime(options);
            $(this[2]).calendricalDate(options);
            $(this[3]).calendricalTime($.extend({
                startTime: $(this[1]),
                startDate: $(this[0]),
                endDate:   $(this[2])
            }, options));
        }
        return this;
    };
})( jQuery );