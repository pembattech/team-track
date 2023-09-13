
// JavaScript function to add ellipsis
function addEllipsis(text, maxLength) {
    if (text.length > maxLength) {
        text = text.substring(0, maxLength) + '...';
    }
    return text;
}

function initializeDateRangePicker(startDateField, endDateField) {
    // Get reference to the date input fields
    const startDateInput = document.getElementById(startDateField);
    const endDateInput = document.getElementById(endDateField);

    // Function to disable past dates in the date input fields
    function disablePastDates() {
        const today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
        startDateInput.setAttribute('min', today);
        endDateInput.setAttribute('min', today);
    }

    // Function to update the minimum date for the end date input based on the start date
    function updateEndDateMin() {
        const startDate = new Date(startDateInput.value);
        endDateInput.setAttribute('min', startDateInput.value);
        if (endDateInput.value && new Date(endDateInput.value) < startDate) {
            endDateInput.value = startDateInput.value;
        }
    }

    // Function to update the maximum date for the start date input based on the end date
    function updateStartDateMax() {
        const endDate = new Date(endDateInput.value);
        startDateInput.setAttribute('max', endDateInput.value);
        if (startDateInput.value && new Date(startDateInput.value) > endDate) {
            startDateInput.value = endDateInput.value;
        }
    }

    // Initialize the calendar and set the end date min attribute based on start date
    disablePastDates();
    updateEndDateMin();
    updateStartDateMax();

    // Listen for changes in the start date and end date and update attributes accordingly
    startDateInput.addEventListener('change', () => {
        updateEndDateMin();
        updateStartDateMax();
    });

    endDateInput.addEventListener('change', () => {
        updateEndDateMin();
        updateStartDateMax();
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