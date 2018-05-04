$(document).ready(function ()
{
    var DateTo;
    var DateFrom;
    var DateRange;
    var Dates;
    var SelectedRoomId;
    var Reservations;
    var url = "index.php?";
   
    function resizeTables() {
        var mainTableDataWidth = $("#mainTableData").width();
        var tdWidth = (mainTableDataWidth - 50)/DateRange;
        var hh = window.innerHeight;
        hh -= $(".headerDiv").height()
        hh -= $("#contViewTypes").height()
        hh -= 55;
        $("#mainTableHeader").width(mainTableDataWidth + 1);
        $("#mainTableHeader td").not('#mainTableHeader td:first-child').width(tdWidth);
        $("#mainTableData td").not('#mainTableData td:first-child').width(tdWidth);
        $("#contViewControllers").height(hh);
        $("#contView").height(hh);
    }
    
    $(window).on('resize', function () {
        resizeTables();
    });
    
    $( "#roomsList" ).controlgroup( {
      direction: "vertical"
    } );
    
    //funkcija koja seta prikaz datuma iznad tablice koja prikazuje rezervacije
    function setTableHeader(){
        var dateIt = new Date(DateFrom);
        for(var k = 1; k <= DateRange; k++){
            $(".col_"+k+".header").text($.datepicker.formatDate('D d/m', dateIt));
            dateIt.setDate(dateIt.getDate()+1);
        }
        
    }
    //funkcija koja dodaje <table> koja služi kao header za rezervacije
    //u tablici se nalazi prikaz datuma po kolonama i row za dodavanje 
    //nove rezervacije
    function insertTableHeader(){
        var trh = $("<tr></tr>");
        var trhh = $("<tr></tr>");
        trh.append("<td></td>");
        for(var k = 1; k <= DateRange; k++){
            var tdH = $("<td></td>").addClass("col_"+k+" header");
            trh.append(tdH);
        }    
        $("#mainTableHeader").append(trh);
        
        trhh.append('<td class="all_day"></td>');
        for(var k = 1; k <= DateRange; k++){
            var tdH = $("<td></td>").addClass("col_"+k+" all_day");
            var tdD = $("<div></div>").addClass("addNew");
            tdH.append(tdD);
            trhh.append(tdH);
        }    
        $("#mainTableHeader").append(trhh);
        if(localStorage.login === "true"){
            $(".addNew").text("+");
            $(".addNew").css({"color":"white", "background-color":"#c6322a", "cursor": "pointer"});
            initNewReservationButtons();
        }
    }
    
    //funkcija vraća slobodne termine za parametar col koji je kolona u tablici
    function getAvailableTime(col){
        var ava = $(".col_"+col).not(".td_reserved, .header, .all_day");
        return ava;
    }
    
    //funkcija koja inicijalizira popup za dodavanje nove rezervacije s obzirom 
    //na parametar col koji označava redni broj kolone
    function initNewReservationData(col){
        $("#newReservationRoom span").text(SelectedRoomId);
        $("#newReservationDate span").text(Dates[parseInt(col) - 1]);
        $("#newReservationUser span").text(localStorage.username);
       
        var available = getAvailableTime(col);
        var setClass = false;
        $(".optionFromClass").remove();
        $("#newReservationToLabel").hide();
        $("#newReservationTo").hide();
        $("#newReservationSave").hide();
        $("#newReservationInfo").val("");
        
        $.each(available, function(){
            //array kalase definirane na this elementu
            var classList = $(this).attr("class").split(/\s+/);
            $.each(classList, function(index, item) {
                //klasa koja počine s row_
                if(item.substring(0,4) == 'row_'){
                    var row = item.substring(4);
                    row = parseInt(row);
                    var h = row/2;
                    var text ="";
                    if(row%2 == 0){
                        text = h < 10 ? "0"+h+":00" : h+":00";
                    }
                    else{
                        h = Math.floor(h);
                        text = h < 10 ? "0"+h+":30" : h+":30";
                    }
                   var op = $("<option></option>");
                   op.addClass("optionFromClass");
                   op.val(row);
                   op.text(text);
                   $("#newReservationFrom").append(op);
                   setClass = true;
                }
            });
        });
        
        $("#newReservationFrom").removeClass();
        if(setClass){
            $("#newReservationFrom").addClass("ncol_"+col);
        }
    }
    
    //funkcija koja popunjava opcije za odabir vremena do kojeg se želi 
    //rezervirati neka prostorija. Moguće je odabrati vrijeme "do" tako da je
    //to vrijeme strogo veće od vremena "od" i nije veće od vremena početka neke
    //druge rezervacije za taj dan i za tu prostoriju
    function setNewReservationTo(){
        $(".optionToClass").remove();
        var cls = $("#newReservationFrom").attr("class").split(/\s+/);
        var col; 
        $.each(cls, function(index, item) {
                if(item.substring(0,5) == 'ncol_'){
                    col = item.substring(5);
                }
            });
        var row = $("#newReservationFrom").val();
        row = parseInt(row);
        for(var k = 0; k < 48; k++){
            if(k <= row){
                continue;
            }
            var h = k/2;
            var text ="";
            if(k%2 == 0){
                text = h < 10 ? "0"+h+":00" : h+":00";
            }
            else{
                h = Math.floor(h);
                text = h < 10 ? "0"+h+":30" : h+":30";
            }
           var op = $("<option></option>");
           op.addClass("optionToClass");
           op.val(k);
           op.text(text);
           $("#newReservationTo").append(op);
           if($(".col_"+col+".row_"+k).hasClass("td_reserved")){
                break;
            }
        }
        $("#newReservationTo").show();
        $("#newReservationToLabel").show();
    }
    
    //zatvara popup za novu rezervaciju
    function closeNewReservationPopup() {
       $('#newReservationPopup').css("display","none");
    }
    
    //inicijalizira buttone za popup koji dodaje novu organizaciju
    //koriste se jquery-ui za prikaz buttona
    //button koji sprema(kreira) novu rezervaciju se ne prikazuje dok nisu
    //odabrana sva obavezna polja
    function initNewReservationButtons(){
        $("#newReservationSave").button();
        $("#newReservationSave").hide();
        $("#newReservationClose").button();
        $("#newReservationClose").click(function(){
            closeNewReservationPopup();
        });
    }
     
    //funkcija otvara popup odabrane rezervacije 
    //funkciji se prosljeđuje reservation objekt koji sadrži podatke o odabranoj rezervaciji. 
    //funkcija provjerava da li je korisnik logiran i koji mu je level, te da li je on 
    //vlasnik rezervacije, pa na temelju toga prikazuje ili sakriva button za brisanje rezervacije
    //podaci se spremaju u localStorage za provjeru na frontendu
    function showEditReservation(reservation){
        $("#editReservationRoom span").text(reservation.room_number);
        $("#editReservationUser span").text(reservation.username);
        $("#editReservationFrom span").text(reservation.res_start);
        $("#editReservationTo span").text(reservation.res_end);
        $("#editReservationInfo span").text(reservation.info);
        $("#editReservationDelete").button();
        $("#editReservationDelete").hide();
        $("#editReservationClose").button();
        if(localStorage.login == "true"){
            if(localStorage.username == reservation.username){
                 $("#editReservationDelete").show();
            }else if(localStorage.level == "2" ){
                 $("#editReservationDelete").show();
            }
        }
        $('#editReservationPopup').css("display","block");
        
    }
   
    //funkcija koja dodaje <tr> i <td> u tablicu za prikaz rezervacije
    function insertTableMain(){
        for(var i= 0; i < 48; i++){
            var tr = $("<tr></tr>");
            var no = i%2 == 0 ? "trEven":"trOdd";
            var tdFirst = $('<td></td>').addClass("timeClass");
            var h = i / 2;
//            tr.attr("id", "row_"+i);
            tr.css({"font-size": "12px", "text-align":"right"});
            tr.addClass(no);
            
            if(no == "trEven"){
                var text = h < 10 ? "0"+h+":00" : h+":00";
                tdFirst.text(text);
            }
            tr.append(tdFirst);
            for(var k = 1; k <= DateRange; k++){
                var tdd = $("<td></td>").addClass("col_"+k+" row_"+i);
                tr.append(tdd);
            }            
            $("#mainTableData").append(tr);
        }
        
        $(".trEven td:first-child").each(function(){
            $(this).css({"border-bottom-color":"white"});
        });
        var time = new Date();
        var kk = time.getHours() *2;
        var contD = $(".contData");
            contD.animate({
            scrollTop: $(".row_"+kk).offset().top - contD.offset().top + contD.scrollTop() -4
        });
    }
    
    //funkcija koja na temelju globalnih varijabli DateFrom i DateTo
    //postavlja prikaz odabranog perioda za prikaz rezervacije
    function setDateRangeInfo(){
        if(DateFrom.getFullYear() != DateTo.getFullYear()){
            $("#rangeInfoFrom").text($.datepicker.formatDate('M d,  yy', DateFrom));        
            $("#rangeInfoTo").text($.datepicker.formatDate('M d,  yy', DateTo));        
        }
        else{
            $("#rangeInfoFrom").text($.datepicker.formatDate('M d', DateFrom));        
            $("#rangeInfoTo").text($.datepicker.formatDate('M d,yy', DateTo));        
        }
    }
    
    //funkcija koja zove ostale funkcije za kreiranje tablica za prikaz rezervacija
    function drawTables(){
        $("#mainTableHeader").empty();
        $("#mainTableData").empty();
        insertTableHeader();
        insertTableMain()
        setTableHeader(); 
        resizeTables();
    }
    
    //pomoćna funkcija koja iz objekta tipa Date vraća string u formatu "YYYY-MM-DD"
    function dateToString(date){
        var monthPart = date.getMonth()+1;
        monthPart = monthPart < 10 ? "0"+monthPart : monthPart;
        var datePart = date.getDate();
        datePart = datePart < 10 ? "0"+datePart : datePart;
        return ""+date.getFullYear()+"-"+monthPart+"-"+datePart;
    }
    
    //funkcija popunjava globalnu varijablu Dates (array) s datumima koji se nalaze 
    //između datuma DateFrom i DateTo
    function fillDates(){
        Dates  = [];
        var dateFrom = new Date(DateFrom); 
        var dateTo = new Date(DateTo);
        var timeDiff = Math.abs(dateTo.getTime() - dateFrom.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
        Dates.push(dateToString(DateFrom));
        for(var k = 0; k < diffDays -1; k++){
            dateFrom = new Date(dateFrom.setTime( dateFrom.getTime() +  86400000 ));
            Dates.push(dateToString(dateFrom));
        }
        setDateRangeInfo();
        setTableHeader();
        getRoomReservations();
        $( "#datepicker" ).datepicker("setDate", DateFrom);
        $( "#datepicker" ).datepicker( "refresh" );
    }
    
    //funkcija postavlja DateFrom i DateTo globalne varijable
    //DateFrom je prvi, a DateTo zadnji dan u tjednu s datumom date
    function setDates(date){
        DateFrom = new Date(date);
        var day =  DateFrom.getDay() == 0 ? 7: DateFrom.getDay();
        DateFrom.setDate(DateFrom.getDate()- day +1);
        DateTo = new Date(DateFrom);
        DateTo.setDate(DateFrom.getDate() + DateRange);
        fillDates();
    }
     
    //inicijalizacije jquery-ui widgeta za odabir datuma
    function initDateTimePicker(){        
        $( "#datepicker" ).datepicker({
            firstDay:1,
            inline:true,
            showOtherMonths: true,
            selectOtherMonths: true,
            beforeShowDay: function (date) {
                var theday =  dateToString(date);          
                return [true,$.inArray(theday, Dates) >=0?"specialDate":''];
            },
            onSelect: function(value, date) {
                setDates(value);
            }
        });
    } 
    
    //prikazuje prostorije koje su definirane u arrayu data
    function setRoomList(data){
        $("#roomsList").empty();
        for(var k = 0; k < data.length; k++){
            $("#roomsList").append($('<label for="roomId'+data[k].room_number+'">'+data[k].room_number+'</label>'));
            $("#roomsList").append($('<input class="inputRadioRoom" type="radio" name="selected_room" id="roomId'+data[k].room_number+'">'));
        }
        $( "#roomsList" ).controlgroup( {
            direction: "vertical"
          });
    } 
    
    //funkcija u tablici za prikaz rezervacija mijenja pozadinu elemenata za rezervirane termine 
    function markReservations(){
        $(".td_reserved").removeClass("td_reserved td_reserved_first td_reserved_last").text("").attr("title", "");
        if(Reservations === undefined){
            return;
        }
        for(var k= 0; k < Reservations.length; k++){
            var dateTimeBegin  = new Date(Reservations[k].res_start);
            var dateTimeEnd  = new Date(Reservations[k].res_end);
            var index = Dates.indexOf(dateToString(dateTimeBegin));
            var timeDiff = Math.abs(dateTimeEnd.getTime() - dateTimeBegin.getTime());
            var diffMin = Math.ceil(timeDiff / (1000*60*30));
            var us = Math.floor(diffMin/2);
            if(us > 0) us--;
            var uss = diffMin > 1 ? us +1 : us;
            for(var i= 0; i < diffMin; i++){
                var r = dateTimeBegin.getHours();
                r *=2;
                r += dateTimeBegin.getMinutes() == 0 ? 0 : 1;
                var tdRef =  $(".col_"+(index+1)+".row_"+r);
                $(tdRef).addClass("td_reserved");
                $(tdRef).addClass("resid_"+k);
                if(i == 0){
                    $(tdRef).addClass("td_reserved_first");
                }
                if(i== diffMin -1){
                    $(tdRef).addClass("td_reserved_last");
                }
                if(us == uss){
                    if(i == us){
                        $(tdRef).text(Reservations[k].username+"; "+Reservations[k].info);
                        $(tdRef).css({"text-align": "center"});
                    }
                }
                else{
                    if(i == us){
                        $(tdRef).text(Reservations[k].username);
                        $(tdRef).css({"text-align": "center"});
                    }
                    if(i == uss){
                        $(tdRef).text(Reservations[k].info);
                        $(tdRef).css({"text-align": "center"});
                    }
                    
                }

                
                if(Reservations[k].info != null && Reservations[k].info != "null"){
                    $(tdRef).attr("title", Reservations[k].info);
                }
                
                dateTimeBegin.setMinutes(dateTimeBegin.getMinutes() + 30);
            }
        }
    }
    
    //dohvaćanje rezervacije za odabranu prostoriju i odabrani tjedan
    function getRoomReservations(){
        if(SelectedRoomId != undefined){            
            var par1 = dateToString(DateFrom);
            var par2 = dateToString(DateTo);
            $.get(url+"rt=rooms/reservationsfromto/"+par1+"/"+par2+"/"+SelectedRoomId, function(data){
                Reservations = data;
                markReservations();
            },  "json")
            .fail(function() {
                console.log("Greška prilikom Ajax poziva: ");
            }); 
        }
        else{
            markReservations();
        }
    }
    
    //obrada za klik na dodavanje nove rezervacije
    //provjerava se da li je odabrana prostorija ako nije upozorava korisnika
    //inače zove funkcije za prikaz popup-a za nove rezervacije
    $("#mainTableHeader").on("click", ".addNew", function(){
        if(SelectedRoomId==undefined){
            $('#selectRoomInfo').css("display", "block");
            setTimeout(function(){
                $('#selectRoomInfo').css("display", "none");
            }, 1000);
        }
        else{
            var col;
            var classList = $(this).parent().attr("class").split(/\s+/);
            $.each(classList, function(index, item) {
                if(item.substring(0,4) == 'col_'){
                   col = item.substring(4);
                }
            });
            initNewReservationData(col);
            $('#newReservationPopup').css("display", "block");
            
        }
    });
  
    //inicijalizacija jquery-ui widgeta 
    $("#currentWeekButton").button({
        label:"Current Week"
    });
    
    //inicijalizacija jquery-ui widgeta 
    $("#prevButton").button({
        icon: "ui-icon-caret-1-w"
    });
    
    //inicijalizacija jquery-ui widgeta 
    $("#nextButton").button({
        icon: "ui-icon-caret-1-e"
    });
    
    //klikom na gumb currentWeekButton prikaže se rezervacije za trenutni tjedan
    $("#currentWeekButton").on('click', function(){
        setDates(dateToString(new Date()));
        setTableHeader(); 
        resizeTables();
    });
    
    //klikom na gumb prevButton prikaže se rezervacije za prethodni tjedan
    $("#prevButton").on('click', function(){
        var newDate = new Date(DateFrom);
        newDate.setDate(DateFrom.getDate() -1);
        setDates(dateToString(newDate));
        setTableHeader(); 
        resizeTables();
    });
    
    //klikom na gumb nextButton prikaže se rezervacije za sljedeći tjedan
    $("#nextButton").on('click', function(){
        var newDate = new Date(DateTo);
        newDate.setDate(DateTo.getDate()+1);
        setDates(dateToString(newDate));
        setTableHeader(); 
        resizeTables();
    });
    
    //odabirom prostorije pozivaju se funkcije koje prikazuju rezervacije za odabranu prostoriju
    $("#roomsList").on("change", 'input[type=radio][name=selected_room]', function() {
       SelectedRoomId = $(this).attr("id").slice(6);
       getRoomReservations();
    });
    
    //zatvaranje popupa za edit rezervacije klikom na Close dugme
    $("#editReservationClose").click(function(){
        $('#editReservationPopup').css("display","none");
    });
    
    //poziv serveru za brisanje rezervacije klikom na Delete dugme
    $("#editReservationDelete").click(function(){
         var postData = {};
        postData.room_number = $("#editReservationRoom span").text();
        postData.res_start = $("#editReservationFrom span").text();
        postData.res_end = $("#editReservationTo span").text();
        $.post(url+'rt=rooms/removereservation', postData).always(function() {
            $('#editReservationPopup').css("display","none");
            getRoomReservations();
        });
    });
    
    //kod promjene vremena početka rezervacije mijena se mogućnost odabira vremena 
    //za kraj rezervacije
    $("#newReservationFrom").on("change", function(){
        var val = $(this).val();
        val = parseInt(val);
        
        if(val != -1){
            setNewReservationTo();
        }
        else{
            $("#newReservationTo").val(-1);
            $("#newReservationTo").trigger("change");
            $("#newReservationTo").hide();
            $("#newReservationToLabel").hide();
        }
    });
    
    //kod odabira vremena kraja rezervacije zadovoljeni su svi uvjeti za prikaz Save buttona
    $("#newReservationTo").on("change", function(){
        var val = $(this).val();
        val = parseInt(val);
        if(val != -1){
            $("#newReservationSave").show();
        }
        else{
            $("#newReservationSave").hide();
        }
    });
     
    //Spremanje nove rezervacije
    $("#newReservationSave").click(function(){
        var postData = {};
        postData.room_number = $("#newReservationRoom span").text();
        postData.res_start = $("#newReservationDate span").text()+" "+$("#newReservationFrom option:selected").text();
        postData.res_end = $("#newReservationDate span").text()+" "+$("#newReservationTo option:selected").text();
        postData.username = $("#newReservationUser span").text();
        postData.info = $("#newReservationInfo").val();
        $.post(url+'rt=rooms/addReservation', postData).always(function() {
                closeNewReservationPopup();
                getRoomReservations();
        });
        
    });
    
    //klikom na rezervaciju u tablici koja prikazuje rezervacije pozivaju se funkcije 
    //za prikaz i mogućnost brisanja rezervacije
    $("#mainTableData").on("click", ".td_reserved", function(){
        console.log($(this));
        var ind;
        var classList = $(this).attr("class").split(/\s+/);
            $.each(classList, function(index, item) {
                if(item.substring(0,6) == 'resid_'){
                    ind = item.substring(6);
                    ind = parseInt(ind);
                }
            });
        showEditReservation(Reservations[ind]);
    });
    
    //dohvačanje prostorija sa servera
    function getRooms(){
        $.get(url+"rt=rooms/allrooms", function(data){
            setRoomList(data);
        },  "json")
        .fail(function(e) {
            console.log("Greška prilikom Ajax poziva: ", e);
        }); 
    }
    
    (function init(){
        DateRange = 7;
        setDates(dateToString(new Date()));
        initDateTimePicker();
        drawTables();
        resizeTables();
        getRooms();
        
    })();
});

