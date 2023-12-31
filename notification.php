<title>Notification - TeamTrack</title>

<?php include 'access_denied.php'; ?>


<?php include 'partial/navbar.php'; ?>
<div class="container">
    <?php include 'partial/sidebar.php'; ?>

    <div class="main-content inbox-wrapper">
        <div class="heading-content sticky-heading">
            <div class="heading-style">
                <p>Notification</p>
            </div>

            <div class="tab-btns">
                <!-- Tab Buttons -->
                <div class="heading-nav between-verticle-line tab-btn active" onclick="openTab_inbox(event, 'tab1')">
                    Activity
                </div>
            </div>
        </div>
        <div class="bottom-line"></div>

        <div class="tab-content active" id="tab1">
            <div class="div-space-top"></div>
            <div class="options-btn-inbox">
                <div class="filter-btns">
                    <button class="filter-button button-style active" data-filter="all">All</button>
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

        <!-- <div class="tab-content" id="tab2">
            <h3>Tab 2 Content</h3>
            <p>This is the content of Tab 2.</p>
        </div> -->
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>

    $(document).ready(function () {
        // Add click event to filter buttons
        $('.filter-button').on('click', function () {
            // Remove "active" class from all buttons
            $('.filter-button').removeClass('active');

            // Add "active" class to the clicked button
            $(this).addClass('active');
        });
    });

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