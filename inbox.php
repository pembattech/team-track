<title>Inbox - TeamTrack</title>

<?php include 'access_denied.php'; ?>


<style>
    .inbox-container {
        display: flex;
        gap: 10px;
        border-radius: 5px;

    }

    .message-list {
        width: 100%;
        border: 0;
        border-radius: 5px;
    }

    .message-list td {
        padding: 10px;
        border: 0;
        border-radius: 5px;
    }

    .message-list tbody tr:hover {
        background-color: var(--sidebar-bgcolor);
        cursor: pointer;
        border-radius: 5px;
    }

    .inbox-left-side {
        max-height: 65vh;
        overflow-y: auto;
        background-color: var(--bg-color);
        border-radius: 5px;
        width: 100%;
    }

    .inbox-left-side::-webkit-scrollbar,
    .inbox-right-side::-webkit-scrollbar {
        width: 5px;
    }

    .inbox-right-side {
        height: 59vh;
        width: 650px;
        position: sticky;
        top: 139;
        right: 0;
        bottom: 0;
        overflow-y: auto;
        border-radius: 5px;
        padding: 20px;
        background-color: var(--color-background-weak-hover-deprecated);
        transition: background-color 0.3s ease;
    }

    .inbox-right-side h3 {
        margin-top: 0;
        margin-bottom: 10px;
    }

    .inbox-right-side p {
        margin: 0;
    }

    .inbox-right-side p:last-child {
        margin-bottom: 0;
    }

    .unread {
        font-weight: bold;
    }

    .open-message {
        font-style: italic;
        font-weight: 100;
        background-color: var(--color-background-weak-hover-deprecated);
        transition: background-color 0.3s ease, font-style 0.3 ease;
    }

    .unread_message {
        font-weight: 900;
    }

    .options-btn-inbox {
        display: flex;
    }
</style>

<?php include 'partial/navbar.php'; ?>
<div class="container">
    <?php include 'partial/sidebar.php'; ?>

    <div class="main-content inbox-wrapper">
        <div class="heading-content sticky-heading">
            <div class="heading-style">
                <p>Inbox</p>
            </div>

            <div class="tab-btns">
                <!-- Tab Buttons -->
                <div class="heading-nav between-verticle-line tab-btn active" onclick="openTab_inbox(event, 'tab1')">
                    Activity
                </div>
                <div class="heading-nav between-verticle-line tab-btn" onclick="openTab_inbox(event, 'tab2')">
                    Message I've sent</div>
            </div>
        </div>
        <div class="bottom-line"></div>

        <div class="tab-content active" id="tab1">
            <div class="div-space-top"></div>
            <div class="options-btn-inbox">
                <div class="send-msg-content">
                    <div class="send-message-btn button-style related-btn-img overlay-border">
                        <img src="./static/image/sms.svg" alt="">
                        Send message
                    </div>
                </div>
                <div class="filter-btns">
                    <button class="filter-button button-style" data-filter="all">All</button>
                    <button class="filter-button button-style" data-filter="unread">Unread</button>
                </div>
            </div>
            <div class="div-space-top"></div>
            <div class="bottom-line"></div>
            <div class="inbox-container div-space-top">
                <div class="inbox-left-side">
                    <div class="message-list">
                        <table border="1" class="message-list">
                            <tbody>
                                <!-- Message rows will be populated here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="inbox-right-side" id="messageInbox-Container">
                    <!-- Selected message content will be displayed here -->
                    <p>Click on a message to view its contents.</p>
                </div>
            </div>
        </div>

        <div class="tab-content" id="tab2">
            <h3>Tab 2 Content</h3>
            <p>This is the content of Tab 2.</p>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let currentOpenMessageId = null; // Initialize a variable to store the current open message ID
    // Initial filter setting
    let currentFilter = 'all'; // Default filter: All messages
    $(document).ready(function () {

        // Fetch messages on page load
        fetchMessages();

        // Event listener for clicking on a message row
        $(document).on('click', '.message-list tbody tr', function () {
            event.preventDefault();

            const messageId = $(this).data('message-id');

            // Then, display the selected message content
            showMessageContent(messageId);
        });

        // Event listener for clicking the "Close" button
        $(document).on('click', '.close-message-button', function () {
            // Hide the "Close" button
            $(this).hide();

            // Remove the 'open-message' class from all message rows
            $('.message-list tbody tr').removeClass('open-message');

            // Clear the selected message content
            $('#messageInbox-Container').html('<p>Click on a message to view its contents.</p>');

            // Reset the current open message ID
            currentOpenMessageId = null;
        });

        // Event listener for clicking the filter buttons
        $('.filter-button').click(function () {
            // Get the filter value from the data-filter attribute
            const filterValue = $(this).data('filter');

            // Update the current filter
            currentFilter = filterValue;
            console.log(currentFilter);

            // Fetch messages with the selected filter
            fetchMessages();
        });

    });

    // Function to fetch messages based on the current filter and display them in the left-side container
    function fetchMessages() {
        $.ajax({
            url: 'partial/inbox_partial/get_messages.php',
            method: 'GET',
            data: { filter: currentFilter }, // Send the filter value to the server
            success: function (response) {
                
                const messageList = $('.message-list tbody');
                messageList.empty();

                if (response.length > 0) {
                    // Sort the messages based on read status (unread messages first)
                    response.sort((a, b) => {
                        if (currentFilter === 'unread') {
                            return a.is_read - b.is_read;
                        } else {
                            return b.timestamp.localeCompare(a.timestamp);
                        }
                    });

                    response.forEach(message => {
                        // Check if the message is unread based on the 'is_read' property
                        const isUnread = message.is_read == 0;

                        // Set the appropriate class name based on the filter
                        let messageStyleClass = 'message-text';
                        if (isUnread) {
                            messageStyleClass += ' unread';
                        }

                        const messageText = message.text;

                        // Get the limited message text using the JavaScript function
                        const messageTextLimited = addEllipsis(messageText, 120);
                        const row = `
                    <tr data-message-id="${message.message_id}" class="${messageStyleClass}">
                        <td>
                            <p class="messsage-project-name">${message.project_name}</p>
                            <p class="message-timestamp">${message.timestamp}</p>
                            <p class="messageStyleClass">${messageTextLimited}</p>
                        </td>
                    </tr>
                    `;
                        messageList.append(row);
                    });
                } else {
                    const row = '<tr><td colspan="4">No messages found.</td></tr>';
                    // const row = '<p>No messages found.</p>';
                    messageList.append(row);
                }

                // Reapply the 'open-message' class if there was a current open message
                if (currentOpenMessageId !== null) {
                    $('.message-list tbody tr[data-message-id="' + currentOpenMessageId + '"]').addClass('open-message');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching messages:', error);
            }
        });
    }

    // Function to display the selected message content in the right-side container
    function showMessageContent(messageId) {
        $.ajax({
            url: 'partial/inbox_partial/get_message.php',
            method: 'POST',
            data: { message_id: messageId },
            success: function (response) {
                // const closeButton = '<button class="close-message-button button-style">Close</button>';
                if (response) {
                    // Add the response (message content) and the "Close" button to the container
                    // $('#messageInbox-Container').html(response + closeButton);
                    $('#messageInbox-Container').html(response);

                    // Remove the 'open-message' class from all message rows
                    $('.message-list tbody tr').removeClass('open-message');

                    // Add the 'open-message' class to the clicked message row
                    $('.message-list tbody tr[data-message-id="' + messageId + '"]').addClass('open-message');

                    // Mark the message as read
                    markAsRead(messageId);

                    // Save the current open message ID
                    currentOpenMessageId = messageId;

                    // Fetch messages again after displaying the content and marking as read
                    fetchMessages();

                } else {
                    // Clear the selected message content and hide the "Close" button
                    $('#messageInbox-Container').html('<p>Click on a message to view its contents.</p>');
                }

            },
            error: function (xhr, status, error) {
                console.error('Error fetching message content:', error);
            }
        });
    }

    // Function to mark the message as read and update the list
    function markAsRead(messageId) {
        $.ajax({
            url: 'partial/inbox_partial/mark_as_read.php',
            method: 'POST',
            data: { message_id: messageId },
            success: function (response) {
                
                console.log('Marked read!');
                console.log(response.unreadCount);

                updateSidebarUnreadCount(response.unreadCount);
            },
            error: function (xhr, status, error) {
                console.error('Error marking message as read:', error);
            }
        });
    }

    // Function to update the sidebar unread badge
    function updateSidebarUnreadCount(count) {
        const badge = document.querySelector('.unread-badge');
        console.log(count);
        if (badge) {
            if (count == 0) {
                badge.style.backgroundColor = "transparent";
                badge.textContent = '';
            } else {
                badge.textContent = count;
            }
        }
    }
</script>