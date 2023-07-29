<title>Inbox - TeamTrack</title>

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
                  `  <img src="./static/image/sms.svg" alt="">
                    <p>Send message</p>
                </div>
            </div>
        </div>

        <div class="tab-content" id="tab2">
            <h3>Tab 2 Content</h3>
            <p>This is the content of Tab 2.</p>
        </div>
    </div>
</div>

