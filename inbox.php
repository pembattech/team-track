<title>Inbox - TeamTrack</title>

<?php include 'access_denied.php'; ?>


<style>
    .inbox-wrapper {
        max-height: 60vh;
    }

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
        max-height: 68vh;
        overflow-y: auto;
        background-color: var(--bg-color);
        border-radius: 5px;
    }

    .inbox-left-side::-webkit-scrollbar, .inbox-right-side::-webkit-scrollbar {
        width: 5px;
    }

    .inbox-right-side {
        height: 62vh;
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
        font-weight: 900;
        background-color: var(--color-background-weak-hover-deprecated);
        transition: background-color 0.3s ease;
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
            <div class="send-msg-content div-space-top">
                <div class="send-message-btn related-btn-img overlay-border">
                    <img src="./static/image/sms.svg" alt="">
                    <p>Send message</p>
                </div>
            </div>

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

    // Function to fetch the list of messages and display them in the left-side container
    function fetchMessages() {
        $.ajax({
            url: 'partial/inbox_partial/get_messages.php',
            method: 'GET',
            success: function (response) {
                const messageList = $('.message-list tbody');
                messageList.empty();

                if (response.length > 0) {
                    response.forEach(message => {
                        const messageStyleClass = message.is_read === 0 ? 'message-text unread' : 'message-text';
                        const messageText = message.text;
                        
                        // Get the limited message text using the JavaScript function
                        const messageTextLimited = addEllipsis(messageText, 120);
                        const row = `
                        <tr data-message-id="${message.message_id}">
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
            },
            error: function (xhr, status, error) {
                console.error('Error marking message as read:', error);
            }
        });
    }

    $(document).ready(function () {
        // Fetch messages on page load
        fetchMessages();

        // Event listener for clicking on a message row
        $(document).on('click', '.message-list tbody tr', function () {
            event.preventDefault();

            const messageId = $(this).data('message-id');

            // // First, mark the message as read
            // markAsRead(messageId);

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
    });
</script>