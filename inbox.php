<title>Inbox - TeamTrack</title>

<?php include 'access_denied.php'; ?>

<?php include 'partial/navbar.php'; ?>
<div class="container">
    <?php include 'partial/sidebar.php'; ?>

    <div class="main-content">
        <div class="heading-content sticky-heading">
            <div class="heading-style">
                <p>Inbox</p>
            </div>

            <div class="tab-btns">
                <!-- Tab Buttons -->
                <div class="heading-nav between-verticle-line tab-btn active" onclick="openTab(event, 'tab1')">Activity
                </div>
                <div class="heading-nav between-verticle-line tab-btn" onclick="openTab(event, 'tab2')">
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

            <style>
                .inbox-container {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                }

                .inbox-left-side {
                    overflow-y: hidden;
                }

                .message-list {
                    width: 100%;
                }

                .message-list th,
                .message-list td {
                    padding: 10px;
                }

                .message-list th {
                    background-color: #f2f2f2;
                }

                .message-list tbody tr:hover {
                    background-color: var(--sidebar-bgcolor);
                    cursor: pointer;
                }

                .inbox-right-side {
                    top: 0;
                    color: red;
                    right: 0;
                    bottom: 0;
                    overflow: auto;
                    border: 1px solid #ccc;
                    padding: 20px;
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
                    background-color: var(--sidebar-bgcolor);
                }
            </style>

            <div class="inbox-container div-space-top">
                <div class="inbox-left-side">
                    <div class="message-list">

                    </div>

                    <table border="1" class="message-list">

                        <tbody>
                            <!-- Message rows will be populated here dynamically -->
                        </tbody>
                    </table>
                </div>
                <div class="inbox-right-side" id="messageInbox-Container">
                    <!-- Selected message content will be displayed here -->
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function () {
                    // Function to fetch the list of messages and display them in the left-side container
                    function fetchMessages() {
                        $.ajax({
                            url: 'partial/inbox_partial/get_messages.php',
                            method: 'GET',
                            success: function (response) {
                                $('.message-list tbody').empty();

                                if (response.length > 0) {
                                    $.each(response, function (index, message) {
                                        const messageStyleClass = message.is_read == 0 ? "message-text unread" : "message-text";
                                        console.log(messageStyleClass);
                                        const row = `
                                        <tr data-message-id="${message.message_id}">
                                            <td>
                                            <p class="messsage-project-name">${message.project_name}</p>
                                            <p class="message-timestamp">${message.timestamp}</p>
                                            <p class="${messageStyleClass}">${message.text}</p>
                                            </td>
                                        </tr>`;
                                        $('.message-list tbody').append(row);
                                    });
                                } else {
                                    const row = '<tr><td colspan="4">No messages found.</td></tr>';
                                    $('.message-list tbody').append(row);
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
                                $('#messageInbox-Container').html(response);

                                // Remove the 'open-message' class from all message rows
                                $('.message-list tbody tr').removeClass('open-message');

                                // Add the 'open-message' class to the clicked message row
                                $('.message-list tbody tr[data-message-id="' + messageId + '"]').addClass('open-message');
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
                                fetchMessages(); // Update the message list
                            },
                            error: function (xhr, status, error) {
                                console.error('Error marking message as read:', error);
                            }
                        });
                    }

                    // Fetch messages on page load
                    fetchMessages();

                    // Event listener for clicking on a message row
                    $(document).on('click', '.message-list tbody tr', function () {
                        const messageId = $(this).data('message-id');
                        showMessageContent(messageId);
                        markAsRead(messageId);
                    });
                });
            </script>
        </div>

        <div class="tab-content" id="tab2">
            <h3>Tab 2 Content</h3>
            <p>This is the content of Tab 2.</p>
        </div>
    </div>
</div>