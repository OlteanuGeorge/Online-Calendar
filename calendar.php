<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar Online</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(45deg, #8594e4, #e4c585);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .header {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            width: 100%;
            padding: 10px;
            background-color: #f1f1f1;
        }

        .header a {
            margin: 0 10px;
            color: black;
            text-decoration: none;
            font-size: 18px; 
            line-height: 25px;
            border-radius: 4px;
        }

        .header a.logo {
            font-size: 25px;
            font-weight: bold;
        }

        .header a:hover {
            background-color: #ddd;
            color: black;
        }

        .header a.active {
            background-color: #4d367b;
            color: white;
        }

        .calendar {
            width: 80%;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            text-align: center;
            margin-top: 20px;
        }

        .month {
            background-color: #4d367b;
            color: #fff;
            padding: 10px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .month select, .year select {
            background-color: #4d367b;
            color: #fff;
            border: none;
            padding: 5px;
            border-radius: 10px;
        }

        .table {
            table-layout: fixed;
            border-collapse: collapse;
            width: 100%;
            border-radius: 10px;
        }

        .table th, .table td {
            border: 1px solid #4d367b;
            padding: 15px;
            text-align: center;
            background-color: #fff;
            transition: background-color 0.3s;
        }

        .table th {
            background-color: #4d367b;
            color: #fff;
        }

        .table td:hover {
            background-color: #a0aae5;
            cursor: pointer;
        }

        /* Popup styles */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background-color: #fff;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .header-right {
            float: right;
        }

        @media screen and (max-width: 500px) {
            .header a {
                float: none;
                display: block;
                text-align: left;
            }

            .header-right {
                float: none;
            }
        }

        .header-left {
            margin-right: auto;
            padding-left: 10px;
            font-family: Arial, sans-serif;
        }

        .welcome-message {
            margin-right: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-left">
            <?php
            if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
                $first_name = $_SESSION['first_name'];
                $last_name = $_SESSION['last_name'];
                echo '<span>Welcome, ' . $first_name . ' ' . $last_name . '!</span>';
            }
            ?>
        </div>
    </div>

    <div class="calendar">
        <h1>Calendar Online</h1>
        <div class="month">
            <label for="month">Choose the month and year:</label>
            <select id="month">
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
            <select id="year"></select>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                    <th>Sunday</th>
                </tr>
            </thead>
            <tbody>
                <!-- Here you will add the calendar days -->
            </tbody>
        </table>
    </div>

    <div class="popup" id="chooseActionPopup">
        <div class="popup-content">
            <button onclick="openAddEventPopup()">Add Event</button>
            <button onclick="openSeeEventsPopup()">See Events</button>
            <button onclick="openAddMeetPopup()">Add Meet</button>
            <button onclick="openSeeMeetsPopup()">See Meets</button>
            <button onclick="closeChooseActionPopup()">Close</button>
        </div>
    </div>

    <div class="popup" id="addEventPopup">
        <div class="popup-content">
            <form action="event_process.php" method="POST">
                <label for="eventTitle">Title:</label>
                <input type="text" id="eventTitle" name="title">
                <br>
                <label for="eventDescription">Description:</label>
                <textarea placeholder="Enter event description..." id="eventDescription" name="description"></textarea>
                <br>
                <label for="eventHour">Hour:</label>
                <input type="time" id="eventHour" name="hour">
                <br>
                <input type="hidden" name="date" id="dateInput">
                <input type="hidden" name="month" id="monthInput">
                <input type="hidden" name="year" id="yearInput">

                <button type="submit" onclick="saveEvent()">Save Event</button>
                <button onclick="closeChooseActionPopup()">Close</button>
            </form>
        </div>
    </div>

    <div class="popup" id="seeEventsPopup">
        <div class="popup-content">
            <h3>Saved Events</h3>
            <div id="seeEventsPopupContent"></div>
            <button onclick="closeSeeEventsPopup()">Close</button>
        </div>
    </div>

    <div class="popup" id="addMeetPopup">
        <div class="popup-content">
            <form action="meet_process.php" method="POST">
                <label for="meetTitle">Title:</label>
                <input type="text" id="meettitle" name="meettitle">
                <br>
                <label for="meetDescription">Description:</label>
                <textarea placeholder="Enter meet description..." id="meetdescription" name="meetdescription"></textarea>
                <br>
                <label for="meetHourStart">Starting Hour:</label>
                <input type="time" id="starting_hour" name="starting_hour">
                <label for="meetHourFinish">Finishing Hour:</label>
                <input type="time" id="finishing_hour" name="finishing_hour">
                <br>
                <input type="hidden" name="meetdate" id="meetdateInput">
                <input type="hidden" name="meetmonth" id="meetmonthInput">
                <input type="hidden" name="meetyear" id="meetyearInput">

                <label for="meetParticipant">Participants to the meet:</label>
                <textarea placeholder="Enter meet participant..." id="participants" name="participants"></textarea>
                <br>

                <button type="submit" onclick="saveMeet()">Save Meet</button>
                <button onclick="closeChooseActionPopup()">Close</button>
            </form>
        </div>
    </div>

    <div class="popup" id="seeMeetsPopup">
        <div class="popup-content">
            <h3>Saved Meets</h3>
            <div id="seeMeetsPopupContent"></div>
            <button onclick="closeSeeMeetsPopup()">Close</button>
        </div>
    </div>

    <script>
        function generateYearOptions() {
            const yearSelect = document.getElementById("year");
            const currentYear = new Date().getFullYear();
            for (let i = currentYear - 23; i <= currentYear + 30; i++) {
                const option = document.createElement("option");
                option.value = i;
                option.textContent = i;
                yearSelect.appendChild(option);
            }
        }

        generateYearOptions();

        const events = {};

        function generateCalendar(year, month) {
            const calendarTable = document.querySelector(".table tbody");
            calendarTable.innerHTML = "";

            const firstDay = new Date(year, month - 1, 1).getDay();
            const lastDay = new Date(year, month, 0).getDate();

            let dayOfWeek = (firstDay + 6) % 7; // Adjust the starting day

            let row = document.createElement("tr");

            for (let i = 0; i < dayOfWeek; i++) {
                const cell = document.createElement("td");
                cell.textContent = "";
                row.appendChild(cell);
            }

            for (let day = 1; day <= lastDay; day++) {
                const cell = document.createElement("td");
                cell.textContent = day;

                // Add event listener to open the chooseActionPopup when the cell is clicked
                cell.addEventListener("click", function () {
                    openChooseActionPopup(day);
                });

                row.appendChild(cell);
                dayOfWeek++;

                if (dayOfWeek === 7) {
                    calendarTable.appendChild(row);
                    row = document.createElement("tr");
                    dayOfWeek = 0;
                }
            }

            if (dayOfWeek > 0) {
                for (let i = dayOfWeek; i < 7; i++) {
                    const cell = document.createElement("td");
                    cell.textContent = "";
                    row.appendChild(cell);
                }
                calendarTable.appendChild(row);
            }
        }

        document.getElementById("month").addEventListener("change", function () {
            const selectedMonth = parseInt(this.value);
            const selectedYear = parseInt(document.getElementById("year").value);
            generateCalendar(selectedYear, selectedMonth);
        });

        document.getElementById("year").addEventListener("change", function () {
            const selectedYear = parseInt(this.value);
            const selectedMonth = parseInt(document.getElementById("month").value);
            generateCalendar(selectedYear, selectedMonth);
        });

        const currentMonth = new Date().getMonth() + 1;
        const currentYear = new Date().getFullYear();
        document.getElementById("month").value = currentMonth;
        document.getElementById("year").value = currentYear;
        generateCalendar(currentYear, currentMonth);

        function openChooseActionPopup(day) {
            document.getElementById("chooseActionPopup").style.display = "flex";
            document.getElementById("chooseActionPopup").dataset.day = day; // Store the selected day in the dataset
        }

        function closeChooseActionPopup() {
            document.getElementById("chooseActionPopup").style.display = "none";
        }

        function openAddEventPopup() {
            closeChooseActionPopup();
            document.getElementById("addEventPopup").style.display = "flex";
        }

        function closeAddEventPopup() {
            document.getElementById("addEventPopup").style.display = "none";
        }

        function openAddMeetPopup() {
            closeChooseActionPopup();
            document.getElementById("addMeetPopup").style.display = "flex";
        }

        function closeAddMeetPopup() {
            document.getElementById("addMeetPopup").style.display = "none";
        }

        function openSeeEventsPopup() {
    closeChooseActionPopup();
    const day = document.getElementById("chooseActionPopup").dataset.day;
    const month = document.getElementById("month").value;
    const year = document.getElementById("year").value;

    // Make an AJAX request to fetch events
    const xhr = new XMLHttpRequest();
    const url = "fetch_events.php"; // Update with your server-side script
    const params = `day=${day}&month=${month}&year=${year}`;

    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            const events = JSON.parse(xhr.responseText);
            displayEvents(events);
        }
    };

    xhr.send(params);
}

function displayEvents(events) {
    const seeEventsPopupContent = document.getElementById("seeEventsPopupContent");
    seeEventsPopupContent.innerHTML = "";

    if (events.length > 0) {
        for (const event of events) {
            seeEventsPopupContent.innerHTML += `<p>Title: ${event.title} - Description: ${event.description} - Hour: ${event.hour}</p>`;
        }
    } else {
        seeEventsPopupContent.innerHTML = "<p>No events for this day.</p>";
    }

    document.getElementById("seeEventsPopup").style.display = "flex";
}


        function closeSeeEventsPopup() {
            document.getElementById("seeEventsPopup").style.display = "none";
        }

function openSeeMeetsPopup() {
    closeChooseActionPopup();
    const day = document.getElementById("chooseActionPopup").dataset.day;
    const month = document.getElementById("month").value;
    const year = document.getElementById("year").value;

    const xhr = new XMLHttpRequest();
    const url = "fetch_meets.php"; // Update with your server-side script
    const params = `day=${day}&month=${month}&year=${year}`;

    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            const meets = JSON.parse(xhr.responseText);
            displayMeets(meets);
        }
    };

    xhr.send(params);
}

function displayMeets(meets) {
    const seeMeetsPopupContent = document.getElementById("seeMeetsPopupContent");
    seeMeetsPopupContent.innerHTML = "";

    if (meets.length > 0) {
        for (const meet of meets) {
            seeMeetsPopupContent.innerHTML += `<p>Title: ${meet.title} - Description: ${meet.description} - Starting Hour: ${meet.starting_hour} - Finishing Hour: ${meet.finishing_hour} - Participants: ${meet.participants}</p>`;
        }
    } else {
        seeMeetsPopupContent.innerHTML = "<p>No meets for this day.</p>";
    }

    document.getElementById("seeMeetsPopup").style.display = "flex";
}


        function closeSeeMeetsPopup() {
            document.getElementById("seeMeetsPopup").style.display = "none";
        }


      function saveEvent() {
    const title = document.getElementById("eventTitle").value;
    const description = document.getElementById("eventDescription").value;
    const hour = document.getElementById("eventHour").value;
    const day = document.getElementById("chooseActionPopup").dataset.day;
    const month = document.getElementById("month").value;
    const year = document.getElementById("year").value;

    // Set the selected date in the hidden input
    document.getElementById("dateInput").value = day;

    // Add additional hidden inputs for month and year
    document.getElementById("monthInput").value = month;
    document.getElementById("yearInput").value = year;

    // Submit the form
    document.querySelector("form").submit();

    // Optionally, you can close the popup here if needed
    closeAddEventPopup();
}

      function saveMeet() {
    const title = document.getElementById("meettitle").value;
    const description = document.getElementById("meetdescription").value;
    const starting_hour = document.getElementById("starting_hour").value;
    const finishing_hour = document.getElementById("finishing_hour").value;
    const day = document.getElementById("chooseActionPopup").dataset.day;
    const month = document.getElementById("month").value;
    const year = document.getElementById("year").value;
    const participants = document.getElementById("participants").value;

    // Set the selected date in the hidden input
    document.getElementById("meetdateInput").value = day;

    // Add additional hidden inputs for month and year
    document.getElementById("meetmonthInput").value = month;
    document.getElementById("meetyearInput").value = year;

    // Submit the form
    document.querySelector("form").submit();

    // Optionally, you can close the popup here if needed
    closeAddMeetPopup();
}




function sendReminderEmail() {
    const title = document.getElementById("eventTitle").value;
    const reminderEmail = document.getElementById("reminderEmail").value;

    if (title && reminderEmail) {
        const xhr = new XMLHttpRequest();
        const url = "sendmail.php";

        const params = `to=${reminderEmail}`;
        xhr.open("POST", url, true);

        // Setează tipul de conținut și trimite datele
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert("Email reminder sent successfully!");
            }
        };

        xhr.send(params);
    } else {
        alert("Please enter both the event title and your email address.");
    }
}
    </script>
</body>
</html>


