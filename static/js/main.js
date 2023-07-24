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
    window.location.href = "profile_edit.php";
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