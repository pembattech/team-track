<?php include 'access_denied.php'; ?>

<title>Create Project - TeamTrack</title>
<?php include 'partial/navbar.php'; ?>
<div class="container">
    <?php include 'partial/sidebar.php'; ?>
    <div class="main-content">
        <div class="heading-content">
            <div class="heading-style">
                <p>Create a New Project</p>
            </div>
        </div>

        <form action="partial/create_project.php" method="POST">

            <div class="text-input">
                <input type="text" class="input-style" name="project_name" id="projectname" placeholder="Name your Project">
                <label for="projectname">Project Name</label>
            </div>
            <div class="text-input">
                <input type="text" class="input-style" name="description" id="projectdesc" placeholder="Describe here">
                <label for="projectdesc">Description</label>
            </div>
            <div class="text-input">
                <input type="text" class="input-style" name="start_date" id="datepicker" class="date" placeholder="Start Date">
                <label for="datepicker">Start Date</label>
            </div>
            <div class="text-input">
                <input type="text" class="input-style" name="end_date" id="datepickerEnd" class="date" placeholder="End Date">
                <label for="datepickerEnd">End Date</label>
            </div>
            <!-- Add jQuery and jQuery UI script links -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>

            <!-- <script>
                $(document).ready(function () {
                    $("#datepicker, #datepickerEnd").datepicker({
                        firstDay: 1,
                        showOtherMonths: true,
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: "yy-mm-dd",
                        beforeShowDay: function (date) {
                            const today = new Date();
                            return [today <= date, ""];
                        }
                    });

                    $(".date").mousedown(function () {
                        $(".ui-datepicker").addClass("active");
                    });
                });
            </script> -->

            <div class="text-input">
                <input type="submit" class="button-style" value="Submit">
            </div>
        </form>
    </div>
</div>
</div>