$(document).ready(function () {
    // Hide the popup when the Escape key is pressed
    $(document).keydown(function (event) {
        if (event.keyCode === 27) { // 27 is the key code for the Escape key
            $(".popup-style").hide();
            $("#popup-btn").blur(); // Remove focus from the button
        }
    });
});

function capitalizeFirstLetter(inputString) {
    return inputString.charAt(0).toUpperCase() + inputString.slice(1);
}

// Function to validate an email address
function isValidEmail(email) {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// JavaScript function to add ellipsis
function addEllipsis(text, maxLength) {
    if (text.length > maxLength) {
        text = text.substring(0, maxLength) + '...';
    }
    return text;
}

function initializeDateRangePicker(startDateField, endDateField) {
    // Get references to the date input fields
    const startDateInput = $(startDateField);
    const endDateInput = $(endDateField);

    // Function to format a date as "yyyy-mm-dd"
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Add leading zero if needed
        const day = String(date.getDate()).padStart(2, '0'); // Add leading zero if needed
        return `${year}-${month}-${day}`;
    }

    // Function to disable past dates in the date input fields
    function disablePastDates() {
        const today = new Date();
        const formattedToday = formatDate(today); // Format today's date
        $(startDateInput).attr('min', formattedToday);
        $(endDateInput).attr('min', formattedToday);
    }

    // Function to update the minimum date for the end date input based on the start date
    function updateEndDateMin() {
        const startDateValue = $(startDateInput).val();
        if (startDateValue) {
            const startDate = new Date(startDateValue);
            const formattedStartDate = formatDate(startDate); // Format start date
            $(endDateInput).attr('min', formattedStartDate);
            if ($(endDateInput).val() && new Date($(endDateInput).val()) < startDate) {
                $(endDateInput).val(formattedStartDate);
            }
        }
    }

    // Function to update the maximum date for the start date input based on the end date
    function updateStartDateMax() {
        const endDate = new Date($(endDateInput).val());
        const formattedEndDate = formatDate(endDate); // Format end date
        $(startDateInput).attr('max', formattedEndDate);
        if ($(startDateInput).val() && new Date($(startDateInput).val()) > endDate) {
            $(startDateInput).val(formattedEndDate);
        }
    }

    // Initialize the calendar and set the end date min attribute based on start date
    disablePastDates();
    updateEndDateMin();
    updateStartDateMax();

    // Listen for changes in the start date and end date and update attributes accordingly
    $(startDateInput).on('change', function () {
        updateEndDateMin();
        updateStartDateMax();
    });

    $(endDateInput).on('change', function () {
        updateStartDateMax();
        updateEndDateMin();
    });

}

function openTab(event, tabId) {
    // Get all tab contents and hide them
    const tabContents = document.getElementsByClassName('tab-content');
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = 'none';
    }

    // Get all tab buttons and remove the 'active' class
    const tabButtons = document.getElementsByClassName('tab-btn');
    for (let i = 0; i < tabButtons.length; i++) {
        tabButtons[i].classList.remove('active');
    }

    // Show the clicked tab content and mark the button as active
    document.getElementById(tabId).style.display = 'block';
    event.currentTarget.classList.add('active');

    // Store the active tab in localStorage
    localStorage.setItem('activeTab', tabId);
}

document.addEventListener("DOMContentLoaded", function () {
    // Retrieve the active tab from localStorage
    const activeTab = localStorage.getItem('activeTab');

    // If there is an active tab stored, open it
    if (activeTab) {
        const tabButton = document.querySelector(`[onclick="openTab(event, '${activeTab}')"]`);
        if (tabButton) {
            tabButton.click();
        }
    }
});

function openTab_inbox(event, tabId) {
    // Get all tab contents and hide them
    const tabContents = document.getElementsByClassName('tab-content');
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = 'none';
    }

    // Get all tab buttons and remove the 'active' class
    const tabButtons = document.getElementsByClassName('tab-btn');
    for (let i = 0; i < tabButtons.length; i++) {
        tabButtons[i].classList.remove('active');
    }

    // Show the clicked tab content and mark the button as active
    document.getElementById(tabId).style.display = 'block';
    event.currentTarget.classList.add('active');

    // Store the active tab in localStorage
    localStorage.setItem('activeTab_inbox', tabId);
}

document.addEventListener("DOMContentLoaded", function () {
    // Retrieve the active tab from localStorage
    const activeTab = localStorage.getItem('activeTab_inbox');

    // If there is an active tab stored, open it
    if (activeTab) {
        const tabButton = document.querySelector(`[onclick="openTab_inbox(event, '${activeTab}')"]`);
        if (tabButton) {
            tabButton.click();
        }
    }
});

// Function to show the popup menu
function showPopup() {
    var popup = document.getElementById("myPopup");
    popup.style.display = "block";
}

// Function to hide the popup menu
function hidePopup() {
    var popup = document.getElementById("myPopup");
    popup.style.display = "none";
}

// Function for logout action
function logout() {
    // Add your logout code here
    // For example, redirecting the user to the logout page
    window.location.href = "logout.php";
    hidePopup();
}

// Function for profile edit action
function editProfile() {
    // Add your profile edit code here
    // For example, redirecting the user to the profile edit page
    window.location.href = "profile.php";
    hidePopup();
}

// Event listener to show/hide the popup menu when the button is clicked
document.getElementById("popup-btn").addEventListener("click", function () {
    var popup = document.getElementById("myPopup");
    if (popup.style.display === "block") {
        hidePopup();
    } else {
        showPopup();
    }
});

// Event listener to hide the popup menu when clicking outside of it
window.addEventListener("click", function (event) {
    var popup = document.getElementById("myPopup");
    if (event.target !== popup && event.target !== document.getElementById("popup-btn")) {
        hidePopup();
    }
});


function toggleCollapse(className) {
    var rows = document.getElementsByClassName(className);
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        if (row.style.display === "none") {
            row.style.display = "table-row";
        } else {
            row.style.display = "none";
        }
    }
}

var initialCollapsedRows = document.getElementsByClassName('collapsed');
for (var i = 0; i < initialCollapsedRows.length; i++) {
    initialCollapsedRows[i].style.display = "table-row";
}