<title>Inbox - TeamTrack</title>

<?php include 'access_denied.php'; ?>

<?php include 'partial/navbar.php'; ?>
<div class="container">
    <?php include 'partial/sidebar.php'; ?>

    <div class="main-content">
        <div class="heading-content">
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

            <style>
                .inbox-container {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    padding: 20px;
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
                    background-color: #f2f2f2;
                    cursor: pointer;
                }

                .inbox-right-side {
                    position: fixed;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    overflow: auto;
                    width: 40%;
                    /* display: relative; */
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
            </style>

            <div class="inbox-container">
                <div class="inbox-left-side">
                    <h2>All Messages</h2>
                    <table border="1" class="message-list">
                        <thead>
                            <tr>
                                <th>Message ID</th>
                                <th>Project ID</th>
                                <th>Recipient ID</th>
                                <th>Message</th>
                                <th>Read</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Message rows will be populated here dynamically -->
                        </tbody>
                    </table>
                </div>
                <div class="inbox-right-side" id="messageInbox-Container">
                    <!-- Selected message content will be displayed here -->
                </div>
            </div>
            <!-- <h2>Number as Power Example</h2>
            <p>
                The number 2 raised to the power of 3 is written as 2<sup>3</sup>.
            </p> -->


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
                                        const row = `
                            <tr data-message-id="${message.message_id}">
                                <td>${message.message_id}</td>
                                <td>${message.task_id}</td>
                                <td>${message.recipient_id}</td>
                                <td>${message.message}</td>
                                <td>${message.is_read}</td>
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
                                $('#messageContainer').html(response);
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