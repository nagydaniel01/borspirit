import $ from 'jquery';
import moment from 'moment';
import 'moment/locale/hu';  // Import Hungarian locale

$(document).ready(function($) {
    let currentWeekStart = moment().startOf('week');

    function initializeMomentLocale() {
        moment.locale('hu'); // Set locale to Hungarian
        moment.updateLocale('hu', { week: { dow: 1 } }); // Set the first day of the week to Monday
    }

    function isCurrentMonth(startDate) {
        return moment(startDate).isSame(moment(), 'month');
    }
    
    function isCurrentWeek(startDate) {
        return moment(startDate).isSame(moment().startOf('week'), 'week');
    }

    function generatePostsForMonthlyCalendar(posts) {
        const postsContainer = $('<div class="posts-container"></div>');
        posts.forEach(post => {
            /*
            let firstCategory = null;
            for (let i = 0; i < post.post_category.length; i++) {
                firstCategory = post.post_category[i];
                break;
            }
            // Add: 'firstCategory.slug' into append().
            */

            //let postTime = moment(post.post_date, "YYYY.MM.DD.").format('HH:mm');
            let postTime = post.post_time;
    
            postsContainer.append('<div id="' + post.post_id + '" class="post"><a href="' + post.post_url + '" class="post__link" target="_blank"><span class="post__date">' + postTime + '</span><strong class="post__title">' + post.post_title + '</strong></a></div>');
        });
        return postsContainer;
    }

    function generatePostsForWeeklyCalendar(posts) {
        const postsContainer = $('<div class="posts-container"></div>');
        posts.forEach(post => {
            /*
            let firstCategory = null;
            for (let i = 0; i < post.post_category.length; i++) {
                firstCategory = post.post_category[i];
                break;
            }
            */
    
            postsContainer.append('<div id="' + post.post_id + '" class="post"><a href="' + post.post_url + '" class="post__link" target="_blank"><span class="post__title">' + post.post_title + '</span></a></div>');
        });
        return postsContainer;
    }

    function renderMonthlyCalendar(posts, startDate = moment()) {
        initializeMomentLocale();
    
        const startOfMonth = moment(startDate).startOf('month');
        const endOfMonth = moment(startOfMonth).endOf('month');
        const today = moment();
    
        $('#monthly-calendar').empty();
    
        // Add header for days of the week
        const headerRow = $('<div class="calendar__row calendar__row--header"></div>');
        for (let day = 0; day < 7; day++) {
            const dayHeader = $('<div class="calendar__cell"></div>');
            dayHeader.append('<span class="day-name">' + moment().day(day + 1).format('dd') + '</span>');
            headerRow.append(dayHeader);
        }
        $('#monthly-calendar').append(headerRow);
    
        // Create rows for each week of the month
        let currentDay = startOfMonth.clone().startOf('week');
    
        while (currentDay.isBefore(endOfMonth) || currentDay.isSame(endOfMonth, 'day')) {
            const calendarRow = $('<div class="calendar__row"></div>');
    
            for (let day = 0; day < 7; day++) {
                const dayCell = $('<div class="calendar__cell"></div>');
    
                if (currentDay.month() === startOfMonth.month()) {
                    dayCell.append('<span class="day-number">' + currentDay.date() + '</span>');
    
                    const dayPosts = posts.filter(post => moment(post.post_date, "YYYY.MM.DD.").isSame(currentDay, 'day'));
    
                    if (dayPosts.length > 0) {
                        dayCell.addClass('calendar__cell--has-posts');
                        //dayCell.append(generatePostsForMonthlyCalendar(dayPosts));

                        // Add post count badge
                        const postCountBadge = $('<span class="post-count">' + dayPosts.length + '</span>');
                        dayCell.append(postCountBadge);

                        // Toggle logic
                        const clickedDate = currentDay.clone();

                        // Function to show details
                        function showDetails() {
                            const postContent = generatePostsForMonthlyCalendar(dayPosts);
                            const clickedDateFormatted = clickedDate.format('YYYY. MMMM D.');

                            $('.calendar__details')
                                .html('')
                                .append(postContent)
                                .stop(true, true)
                                .slideDown(200);
                        }

                        // Show on click
                        dayCell.css('cursor', 'pointer');
                        dayCell.on('click', function (e) {
                            e.stopPropagation();
                            showDetails();
                        });

                        // Show on hover
                        dayCell.on('mouseenter', function () {
                            showDetails();
                        });
                    }
    
                    if (currentDay.isSame(today, 'day')) {
                        dayCell.addClass('calendar__cell--today');
                    }
                } else {
                    dayCell.addClass('calendar__cell--empty');
                }
    
                calendarRow.append(dayCell);
                currentDay.add(1, 'days');
            }
    
            $('#monthly-calendar').append(calendarRow);
        }
    
        $('#current-month').text(startOfMonth.format('YYYY. MMMM'));
    
        if (isCurrentMonth(startDate)) {
            $('#jump-to-today-monthly').prop('disabled', true);
            $('#prev-month').prop('disabled', true);
        } else {
            $('#jump-to-today-monthly').prop('disabled', false);
            $('#prev-month').prop('disabled', false);
        }

        // Hide the .calendar__details box when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.calendar__cell').length && !$(e.target).closest('.calendar__details').length) {
                $('.calendar__details').slideUp(200);
            }
        });
    }
    
    function renderWeeklyCalendar(posts, startDate = moment()) {
        initializeMomentLocale();
    
        const startOfWeek = moment(startDate).startOf('week');
        const endOfWeek = moment(startOfWeek).endOf('week');
        const today = moment();
    
        $('#weekly-calendar').empty();
    
        const headerRow = $('<div class="calendar__row calendar__row--header"></div>');
        headerRow.append('<div class="calendar__cell"></div>');
        for (let day = 0; day < 7; day++) {
            const dayDate = moment(startOfWeek).add(day, 'days');
            const dayHeader = $('<div class="calendar__cell day-header"></div>');
    
            if (dayDate.isSame(today, 'day')) {
                dayHeader.addClass('calendar__cell--today');
            }
    
            dayHeader.append('<div class="day-name">' + dayDate.format('dd') + '<span>' + dayDate.format('D') + '</span></div>');
            headerRow.append(dayHeader);
        }
        $('#weekly-calendar').append(headerRow);
    
        for (let hour = 8; hour < 22; hour++) {
            const calendarRow = $('<div class="calendar__row"></div>');
            const hourHeader = $('<div class="calendar__cell time-header"></div>');
            hourHeader.append('<div class="hour-header">' + (hour < 10 ? '0' + hour : hour) + ':00</div>');
            calendarRow.append(hourHeader);
    
            for (let day = 0; day < 7; day++) {
                const dayDate = moment(startOfWeek).add(day, 'days');
                const hourCell = $('<div class="calendar__cell"></div>');
    
                const dayHourPosts = posts.filter(post => 
                    moment(post.post_date, "YYYY.MM.DD.").isSame(dayDate, 'day') &&
                    moment(post.post_date, "YYYY.MM.DD.").hour() === hour
                );
    
                if (dayHourPosts.length > 0) {
                    hourCell.addClass('calendar__cell--has-posts');
                    hourCell.append(generatePostsForWeeklyCalendar(dayHourPosts));
                }
    
                if (dayDate.isSame(today, 'day')) {
                    hourCell.addClass('calendar__cell--today');
                }
    
                calendarRow.append(hourCell);
            }
    
            $('#weekly-calendar').append(calendarRow);
        }
    
        $('#current-week').text(startOfWeek.format('YYYY. MMMM D.') + ' - ' + endOfWeek.format('YYYY. MMMM D.'));
    
        if (isCurrentWeek(startDate)) {
            $('#jump-to-today-weekly').prop('disabled', true);
            $('#prev-week').prop('disabled', true);
        } else {
            $('#jump-to-today-weekly').prop('disabled', false);
            $('#prev-week').prop('disabled', false);
        }
    }

    function renderListView(posts) {
        // Clear previous list view
        $('#list-view').empty();
    
        // Create a container for the list
        const listContainer = $('<div class="list-container"></div>');
    
        // Group posts by date
        const postsByDate = {};
    
        posts.forEach(post => {
            const postDate = moment(post.post_date, "YYYY.MM.DD.").format('YYYY. MMMM D.'); // Display date format
            const postDateId = moment(post.post_date, "YYYY.MM.DD.").format('YYYY-MM-DD'); // ID-friendly date format
    
            if (!postsByDate[postDate]) {
                postsByDate[postDate] = { dateId: postDateId, posts: [] };
            }
    
            postsByDate[postDate].posts.push(post);
        });
    
        // Iterate over the grouped dates and create list sections with <ul> tags
        Object.keys(postsByDate).forEach(date => {
            const dateId = postsByDate[date].dateId;
            
            // Add date header with id
            listContainer.append('<h2 id="' + dateId + '" class="date-header">' + date + '</h2>');
    
            // Create a <ul> for the posts under this date
            const ulElement = $('<ul class="posts-container"></ul>');
    
            postsByDate[date].posts.forEach(post => {
                const listItem = $('<li class="post"></li>');
                //const postTime = moment(post.post_date, "YYYY.MM.DD.").format('HH:mm'); // Format the time
                let postTime = post.post_time;
                
                // Create the HTML structure for each post in the list view
                listItem.append('<div class="post-time">' + postTime + '</div>');
                listItem.append('<div class="post-title"><a href="' + post.post_url + '" target="_blank">' + post.post_title + '</a></div>');
                
                // Optionally, include the first category as a tag
                /*
                if (post.post_category.length > 0) {
                    const firstCategory = post.post_category[0];
                    listItem.append('<div class="post-category">' + firstCategory.name + '</div>');
                }
                */
    
                // Append the list item to the <ul>
                ulElement.append(listItem);
            });
    
            // Append the <ul> to the container
            listContainer.append(ulElement);
        });
    
        // Append the list container to the list-view div
        $('#list-view').append(listContainer);
    
        // Optionally, update the view title or other UI elements
        $('#current-view').text('List View');
    }

    /*
    // Example posts data (replace this with your actual posts data)
    const MomentData = [
        { post_title: "Bejegyzés 1", post_date: "2024-08-09 10:00:00" },
        { post_title: "Bejegyzés 2", post_date: "2024-08-19 12:00:00" },
        { post_title: "Bejegyzés 3", post_date: "2024-08-19 13:00:00" },
        // Add more post data as needed
    ];
    */

    if (typeof MomentData !== 'undefined' && Array.isArray(MomentData)) {
        //console.log('Moment JS Loaded');

        // Buttons for switching views
        $('#switch-to-monthly').on('click', function() {
            renderMonthlyCalendar(MomentData);
        });

        $('#switch-to-weekly').on('click', function() {
            renderWeeklyCalendar(MomentData);
        });

        $('#switch-to-list').on('click', function() {
            renderListView(MomentData);
        });

        // Mini calendar controls for monthly view
        $('#prev-month').on('click', function() {
            const currentMonth = moment($('#current-month').text(), 'YYYY MMMM').subtract(1, 'month');
            renderMonthlyCalendar(MomentData, currentMonth);
        });

        $('#next-month').on('click', function() {
            const currentMonth = moment($('#current-month').text(), 'YYYY MMMM').add(1, 'month');
            renderMonthlyCalendar(MomentData, currentMonth);
        });

        // Mini calendar controls for weekly view
        $('#prev-week').on('click', function() {
            currentWeekStart = currentWeekStart.clone().subtract(1, 'week');
            renderWeeklyCalendar(MomentData, currentWeekStart);
        });

        $('#next-week').on('click', function() {
            currentWeekStart = currentWeekStart.clone().add(1, 'week');
            renderWeeklyCalendar(MomentData, currentWeekStart);
        });

        $('#jump-to-today-monthly').on('click', function() {
            renderMonthlyCalendar(MomentData, moment());
        });

        $('#jump-to-today-weekly').on('click', function() {
            currentWeekStart = moment().startOf('week');
            renderWeeklyCalendar(MomentData, currentWeekStart);
        });

        // Initial render in monthly view
        renderMonthlyCalendar(MomentData);

        // Initial render in weekly view (optional if you want to start with the weekly view)
        // renderWeeklyCalendar(MomentData, currentWeekStart);
        
    }
});
