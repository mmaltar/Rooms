
<?php
require_once 'view/_header.php';

// require_once __DIR__.'/_header.php';
?>

<div id="contViewTypes">
    <div id="currentWeekButton"></div>
    <div id="prevButton"></div>
    <div id="nextButton"></div>
    <div id="rangeInfo">
        <span id="rangeInfoFrom"></span>
        <span> - </span>
        <span id="rangeInfoTo"></span>
    </div>

</div>

<div id="contViewControllers">
    <div id ="datepicker"></div>
    <div id="roomsList">
    </div>
</div>

<div id="contView">
    <div class="contViewHeader">
        <div class="contViewTable">
            <table id="mainTableHeader"></table>
        </div>
    </div>
    <div class="contData">
        <div class="contTable">
            <table class="selectable" id="mainTableData"></table>
        </div>
    </div>
</div>

<div id="newReservationPopup" class="modal">
    <div class="modal-content">
        <div style="height: 20px; border-bottom: 1px solid #f1f1f1">
            <p style="">New Reservation</p>
        </div>

        <div class="reservationData">
            <p id="newReservationRoom">Room: <span></span></p>
            <p id="newReservationDate">Date: <span></span></p>
            <p id="newReservationUser">Username: <span></span></p>
            <label for="newReservationFrom">Time From: </label>
            <select name="newReservationFromName" id="newReservationFrom">
                <option disabled value="-1" selected>Select...</option>
            </select>
            <br />
            <br />
            <label id="newReservationToLabel" for="newReservationTo">Time To: </label>
            <select name="newReservationToName" id="newReservationTo">
                <option disabled value="-1" selected>Select...</option>
            </select>
            <br />
            <br />
            <label id="newReservationInfoLabel" for="newReservationInfo">Info: </label>
            <input id="newReservationInfo" type="text">
        </div>
        <div class="newReservationButtons">
            <div id="newReservationSave">Save/Close</div>
            <div id="newReservationClose">Close</div>
        </div>
    </div>
</div>

<div id="editReservationPopup" class="modal">
    <div class="modal-content">
        <div style="height: 20px; border-bottom: 1px solid #f1f1f1">
            <p style="">Reservation Info</p>
        </div>
        <div class="reservationData">
            <p id="editReservationRoom"><b>Room:</b> <span></span></p>
            <p id="editReservationUser"><b>Username:</b> <span></span></p>
            <p id="editReservationFrom"><b>From:</b> <span></span></p>
            <p id="editReservationTo"><b>To:</b> <span></span></p>
            <p id="editReservationInfo"><b>Info:</b> <span></span></p>
        </div>
        <div class="editReservationButtons">
            <div id="editReservationDelete">Delete</div>
            <div id="editReservationClose">Close</div>
        </div>
    </div>
</div>

<div id="selectRoomInfo" class="modal">
    <div class="modal-content">
        <div style="height: 20px; border-bottom: 1px solid #f1f1f1">
            <p style="">Info</p>
        </div>
        <p>Please select room</p>
    </div>
</div>
<!-- <script src="/~mmaltar/view/rooms_index.js" type="text/javascript"></script> !-->

<script src="../view/rooms_index.js" type="text/javascript"></script>
<?php require_once 'view/_footer.html'; ?>
