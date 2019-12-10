<?php
    if($_POST["submit"] == "true" ) {
        if(!isset($_POST["isDaily"])) {
            $city = "";
            $lat = "";
            $lon = "";
            
            if(!isset($_POST["lat"])  ) {
                $geoURL = "https://maps.googleapis.com/maps/api/geocode/xml?address=".urlencode($_POST["street"]).",".urlencode($_POST["city"]). ",".urlencode($_POST["state"])."&key=API_ID";
                $resp = file_get_contents($geoURL);
                $parseXML = new SimpleXMLElement($resp);

                if($parseXML->status != "OK") {
                    echo '{"error": true}';
                    exit;
                }

                $lat = $parseXML->result->geometry->location->lat;
                $lon =  $parseXML->result->geometry->location->lng;
                
            } else {
                $lat = $_POST["lat"] ;
                $lon = $_POST["lon"];
            }
        
            $darkUrl = "https://api.forecast.io/forecast/API_ID/".$lat.",".$lon."?exclude=minutely,hourly,alerts,flags";
            $data = file_get_contents($darkUrl) or die("can't load data");
            echo $data;

        } else {
            $lat = $_POST["lat"];
            $lon = $_POST["lon"];
            $time = $_POST["time"];
            $url = "https://api.darksky.net/forecast/API_ID/".$lat.",".$lon.",".$time."?exclude=minutely";
            $data = file_get_contents($url) or die("can't load data");
            $dailyData = json_decode($data, true);
            echo $data;
        }
        exit;
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HW6 </title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
        }

        input[type=text] {
            border: 1px solid grey;
            padding: 5px;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .search-title {
            text-align: center;
            color: white;
            font-size: 40px;
            font-style: italic;
        }

        #chart_div{
            width: 900px;
        }

        .search-wrapper {
            width: 60%;
            background-color: green;
            border-radius: 10px;
            margin: 20px auto;
            padding: 20px 50px;
        }

        .label {
            width: 50px;
            display: inline-block;
            color: white;
        }

        .input-wrapper {
            margin: 20px 0;
        }

        .disable {
            background: #e1e1e1;
        }

        .search-left {
            width: 50%;
            float: left;
            border-right: 5px solid white;
        }

        .search-right {
            width: 50%;
            float: left;
            padding: 10px 50px;
        }

        .clearfix:before,
        .clearfix:after {
            display: block;
            clear: both;
            content: "";
        }

        .search-btns {
            text-align: center;
            margin: 20px 0;
        }

        #error {
            width: 600px;
            border: solid 3px #cdcdcd;
            margin: 30px auto;
            background-color: #f1f1f1;
            text-align: center;
            padding: 10px;
            display: none;
        }

        /******* CARD ******/
        .card {
            width: 500px;
            background: #4EB6F1;
            color: white;
            margin: 0 auto;
            border-radius: 10px;
            padding: 20px;
        }

        .card-title {
            margin: 0px;
        }

        .card-subtitle {
            margin: 0px;
            font-size: 15px;
        }

        .deg, .deg2 {
            display: inline;
            font-size: 100px;
        }

        .deg::after {
            display: inline-block;
            width: 15px;
            height: 15px;
            position: relative;
            content: "";
            background-image: url(https://cdn3.iconfinder.com/data/icons/virtual-notebook/16/button_shape_oval-512.png);
            background-size: 15px;
            color: white;
            top: -50px;
            left: -25px;
        }

        .deg2::after {
            display: inline-block;
            width: 15px;
            height: 15px;
            position: relative;
            content: "";
            background-image: url(https://cdn3.iconfinder.com/data/icons/virtual-notebook/16/button_shape_oval-512.png);
            background-size: 15px;
            color: white;
            top: -60px;
            left: 5px;
        }

        .deg-type {
            display: inline;
            font-size: 65px;
        }
        .matrixs{
            margin: 0 -15px;
        }

        .matrix_item {
            width: calc(100% / 6);
            float: left;
            padding: 10px;
            text-align: center;
        }

        .matrix_img {
            width: 30px;
            text-align: center;
        }

        .matrix_val {
            text-align: center;
            font-size: 20px;
        }

        .disapear {
            display: none;
        }

        table {
            width: 800px;
            margin: 30px auto;
            border-collapse: collapse;
        }

        table, th, td {
            background-color: #8FBDE9;
            border: 2px solid #3F8BB7;
            text-align: center;
            cursor: pointer;
            color: white;
        }

        .line-left {
            float: left;
            width: 60%;
            text-align: right;
            margin: 2px 0;
            font-size: 17px;
            font-weight: bold;
            margin-top: 6px;
        }
        .line-right {
            float: left;
            width: 40%;
            text-align: left;
            margin: 2px 0;
            padding-left: 5px;
            font-size: 22px;
            font-weight: bold;
        }

        .weather-logo {
            width: 50%; 
            float: left; 
            padding-left: 20px;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div class="search-wrapper">
        <h2 class="search-title">Weather Search</h2>
        <form method="POST">
            <div class="form-fields clearfix">
                <div class="search-left">
                    <div class="input-wrapper">
                        <span class="label">Street</span>
                        <input type="text" name="street" id="street" /> <br>
                    </div>
                    <div class="input-wrapper">
                        <span class="label">City</span>
                        <input type="text" name="city" id="city" />
                    </div>
                    <div class="input-wrapper">
                        <span class="label">State</span>
                        <select id="states" name="state">
                            <option value="null" selected>state</option>
                            <option value="null" >*****************************************</option>
                        </select>
                    </div>
                </div>
                <div class="search-right">
                    <input id="current_loc" type="checkbox" onclick="handleCheckboxClick()" name="current_loc" value="true">
                    <span class="label" style="width: 200px;">Curent Location</span>
                    <input type="hidden" name="lat"/>
                    <input type="hidden" name="lon"/>
                    <input type="hidden" name="ip_city"/>
                   
                </div>
            </div>
            <div class="search-btns">
                <input type="submit" onclick="handleSubmit(event, this.form)" name="submit" value="search" />
                <input type="reset" onclick="handleReset(event)" value="clear" />
            </div>
        </form>
    </div>

    <div id="error">
        Please check the input address.
    </div>

    <div id="container"> </div>
    <center><div id="chart_div"></div><center>
</body>

<script>
    var statesTag = document.getElementById("states");
    var city = document.getElementById("city");
    var street = document.getElementById("street");
    var current_loc = document.getElementById("current_loc");
    var error = document.getElementById("error");
    var time = document.getElementById("time");
    var myForm = document.getElementById("myForm");
    var chartDiv = document.getElementById('chart_div');
    var container = document.getElementById('container');
    var chartData = null;

    function handleReset() {
        // window.location.href = window.location.origin;
        enableAddress();
        clearForm();
        error.style.display = "none";
        container.innerHTML = "";
        chartDiv.innerHTML = "";
    }

    function loadStates () {

        pl =  [
            {
            "Abbreviation": "AL",
            "State": "Alabama"
            },
            {
            "Abbreviation": "AK",
            "State": "Alaska"
            },
            {
            "Abbreviation": "AZ",
            "State": "Arizona"
            },
            {
            "Abbreviation": "AR",
            "State": "Arkansas"
            },
            {
            "Abbreviation": "CA",
            "State": "California"
            },
            {
            "Abbreviation": "CO",
            "State": "Colorado"
            },
            {
            "Abbreviation": "CT",
            "State": "Connecticut"
            },
            {
            "Abbreviation": "DE",
            "State": "Delaware"
            },
            {
            "Abbreviation": "DC",
            "State": "District Of Columbia"
            },
            {
            "Abbreviation": "FL",
            "State": "Florida"
            },
            {
            "Abbreviation": "GA",
            "State": "Georgia"
            },
            {
            "Abbreviation": "HI",
            "State": "Hawaii"
            },
            {
            "Abbreviation": "ID",
            "State": "Idaho"
            },
            {
            "Abbreviation": "IL",
            "State": "Illinois"
            },
            {
            "Abbreviation": "IN",
            "State": "Indiana"
            },
            {
            "Abbreviation": "IA",
            "State": "Iowa"
            },
            {
            "Abbreviation": "KS",
            "State": "Kansas"
            },
            {
            "Abbreviation": "KY",
            "State": "Kentucky"
            },
            {
            "Abbreviation": "LA",
            "State": "Louisiana"
            },
            {
            "Abbreviation": "ME",
            "State": "Maine"
            },
            {
            "Abbreviation": "MD",
            "State": "Maryland"
            },
            {
            "Abbreviation": "MA",
            "State": "Massachusetts"
            },
            {
            "Abbreviation": "MI",
            "State": "Michigan"
            },
            {
            "Abbreviation": "MN",
            "State": "Minnesota"
            },
            {
            "Abbreviation": "MS",
            "State": "Mississippi"
            },
            {
            "Abbreviation": "MO",
            "State": "Missouri"
            },
            {
            "Abbreviation": "MT",
            "State": "Montana"
            },
            {
            "Abbreviation": "NE",
            "State": "Nebraska"
            },
            {
            "Abbreviation": "NV",
            "State": "Nevada"
            },
            {
            "Abbreviation": "NH",
            "State": "New Hampshire"
            },
            {
            "Abbreviation": "NJ",
            "State": "New Jersey"
            },
            {
            "Abbreviation": "NM",
            "State": "New Mexico"
            },
            {
            "Abbreviation": "NY",
            "State": "New York"
            },
            {
            "Abbreviation": "NC",
            "State": "North Carolina"
            },
            {
            "Abbreviation": "ND",
            "State": "North Dakota"
            },
            {
            "Abbreviation": "OH",
            "State": "Ohio"
            },
            {
            "Abbreviation": "OK",
            "State": "Oklahoma"
            },
            {
            "Abbreviation": "OR",
            "State": "Oregon"
            },
            {
            "Abbreviation": "PA",
            "State": "Pennsylvania"
            },
            {
            "Abbreviation": "RI",
            "State": "Rhode Island"
            },
            {
            "Abbreviation": "SC",
            "State": "South Carolina"
            },
            {
            "Abbreviation": "SD",
            "State": "South Dakota"
            },
            {
            "Abbreviation": "TN",
            "State": "Tennessee"
            },
            {
            "Abbreviation": "TX",
            "State": "Texas"
            },
            {
            "Abbreviation": "UT",
            "State": "Utah"
            },
            {
            "Abbreviation": "VT",
            "State": "Vermont"
            },
            {
            "Abbreviation": "VA",
            "State": "Virginia"
            },
            {
            "Abbreviation": "WA",
            "State": "Washington"
            },
            {
            "Abbreviation": "WV",
            "State": "West Virginia"
            },
            {
            "Abbreviation": "WI",
            "State": "Wisconsin"
            },
            {
            "Abbreviation": "WY",
            "State": "Wyoming"
            }
        ]

        for(let i=0; i < pl.length; i++) {
            var option = document.createElement("option");
            option.text = pl[i].State;
            option.value = pl[i].Abbreviation ;
            statesTag.add(option);
        }
    }

    loadStates();

    function clearForm() {
        city.value = "";
        street.value = "";
        statesTag[0].selected = true;
        

    }

    function disableAddress() {
        city.disabled = true;
        city.classList.add('disable');
        street.disabled = true;
        street.classList.add('disable');
        statesTag.disabled = true;
    }

    function enableAddress() {
        city.disabled = false;
        city.classList.remove('disable');
        street.disabled = false;
        street.classList.remove('disable');
        statesTag.disabled = false;
    }

    function handleCheckboxClick() {
        if(current_loc.checked) {
            clearForm()
            disableAddress();
        } else {
            enableAddress();
        }
    }

    function changeTimeZone(time, zone) {
        var d = new Date(time * 1000).toLocaleString("en-US", {timeZone: zone});
        m = new Date(d);
        return m;
    }

    function toggle(e) {
        var isVisible = event.target.dataset.visible;
        if( isVisible == "false") {
            drawChart(chartData);
            event.target.src = "https://cdn0.iconfinder.com/data/icons/navigation-set-arrows-part-one/32/ExpandLess-512.png";
            event.target.dataset.visible = "true";
            chartDiv.style.display = "block";
        } else {
            event.target.src = "https://cdn4.iconfinder.com/data/icons/geosm-e-commerce/18/point-down-512.png"
            event.target.dataset.visible = "false";
            chartDiv.style.display = "none";
        }
    }



    function handleTableClick(event) {
        var xmlhttp = new XMLHttpRequest();
        let latV = event.target.parentElement.dataset.lat;
        let lonV = event.target.parentElement.dataset.lon;
        let timeV = event.target.parentElement.dataset.time;
        xmlhttp.open("POST", "index.php", false);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send(`lat=${latV}&lon=${lonV}&time=${timeV}&isDaily=true&submit=true`);
        jsonDoc = xmlhttp.responseText;
        jsonDoc = JSON.parse(jsonDoc);
        renderSecondView(jsonDoc); 
        console.log(jsonDoc);
        arr = jsonDoc["hourly"]["data"]; 
        var payload = [["Time","T"]];
        for( i=0; i < arr.length; i++ ) {
            payload.push([ (i) , arr[i]["temperature"] ] );
        }  
        chartData = payload; 
        console.log(payload);
    }

    function handleSubmit(event, form) {
        event.preventDefault();
        chartDiv.style.display = "none";
        container.innerHTML = "";
        error.style.display="none";
        var isError = false;
        var xmlhttp = new XMLHttpRequest();
        var cityV;
        
        if( current_loc.checked ) {
            xmlhttp.open("GET", "http://ip-api.com/json", false); //open, send, responseText are
            xmlhttp.send();
            if (xmlhttp.status != 200) {
                alert("Error Status: " + xmlhttp.status + `, "${filename}" file not found.`);
            } else {
                jsonDoc = xmlhttp.responseText;
                jsonDoc = JSON.parse(jsonDoc);

                let latV = jsonDoc.lat;
                let lonV = jsonDoc.lon;
                cityV = jsonDoc.city;

                xmlhttp.open("POST", "index.php", false);
                xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlhttp.send(`lat=${latV}&lon=${lonV}&city=${cityV}&submit=true`);
                jsonDoc = xmlhttp.responseText;
                
                jsonDoc = JSON.parse(jsonDoc);
            }
        } else {
            
            if(!street.value || !city.value || statesTag.value == "null") {
                error.style.display = "block";
                return;
            } else {
                error.style.display="none";
                cityV =  city.value;
                let stateV = statesTag.value;
                let streetV = street.value;

                xmlhttp.open("POST", "index.php", false);
                xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlhttp.send(`city=${cityV}&state=${stateV}&street=${streetV}&submit=true`);
                jsonDoc = xmlhttp.responseText;
                jsonDoc = JSON.parse(jsonDoc);
            }
        }

        console.log(jsonDoc);

        if(jsonDoc.error == true) {
            error.style.display="block";
            error.innerHTML= "Invalid Adrress. Please check the address again.";
            return;
        }

        renderFirstView(jsonDoc, cityV);
    }

    function getIcon( name ) {
        url = "";
        switch( name ) {
            case "clear-day":
                url = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-12-512.png";
            break;
            case "clear-night":
                url = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-12-512.png";
            break;
            case "rain":
                url = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-04-512.png";
            break;
            case "snow":
                url = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-19-512.png";
            break;
            case "sleet":
                url = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-07-512.png";
            break;
            case "wind":
                url = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-27-512.png";
            break;
            case "fog":
                url = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-28-512.png";
            break;
            case "cloudy":
                url = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-01-512.png";
            break;
            case "partly-cloudy-day":
                url = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-02-512.png";
            case "partly-cloudy-night":
                url = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-02-512.png";
            break;
        }
        return url;
    }

    function renderFirstView(data, city) {
        var firstView = `<div class="card">
            <h1 class="card-title"> ${city}</h1>
            <h6 class="card-subtitle"> ${data["timezone"]} </h6>
            <div class="temperature">
                <h1 class="deg"> ${Math.round(data["currently"]["temperature"])} </h1>
                <h6 class="deg-type"> F </h6>
            </div>
            <h1 class="weather">${data["currently"]["summary"]}</h1>
            <div class="matrixs clearfix">
                <div class="matrix_item" style="padding-left: 0">
                    <img class="matrix_img" src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-16-512.png" title="humidity"/>
                    <h2 class="matrix_val">  ${data["currently"]["humidity"]} </h2>
                </div>
                <div class="matrix_item">
                    <img class="matrix_img" src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-25-512.png" title="Pressure" >
                    <h2 class="matrix_val"> ${data["currently"]["pressure"]} </h2>
                </div>
                <div class="matrix_item">
                    <img class="matrix_img" src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-27-512.png" title="Wind Speed">
                    <h2 class="matrix_val"> ${data["currently"]["windSpeed"]} </h2>
                </div>
                <div class="matrix_item">
                    <img class="matrix_img" src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-30-512.png" title="Visibility" />
                    <h2 class="matrix_val"> ${data["currently"]["visibility"]}  </h2>
                </div>
                <div class="matrix_item">
                    <img class="matrix_img" src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-28-512.png" title="CloudCover" />
                    <h2 class="matrix_val"> ${data["currently"]["cloudCover"]}  </h2>
                </div>
                <div class="matrix_item" style="padding-right: 0">
                    <img class="matrix_img" src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-24-512.png" title="Ozone" />
                    <h2 class="matrix_val"> ${data["currently"]["ozone"]}  </h2>
                </div>
            </div>
        </div>

        <div class="table-section">
        <Table>
            <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Summary</th>
                <th>TemperatureHigh</th>
                <th>TemperatureLow</th>
                <th>Wind Speed</th>
            <tr>`

        arr = data["daily"]["data"];
        for(let i=0; i < arr.length; i++) {
            var d =  changeTimeZone(arr[i]["time"], data["timezone"] );
            var month = d.getMonth() + 1 ; //months from 1-12
            var day = d.getDate();
            var year = d.getFullYear();

            firstView += `<tr onclick="handleTableClick(event)" data-lat="${data["latitude"]}" data-lon="${data["longitude"]}" data-time="${arr[i]["time"]}">
                <td style="width: 100px;"> ${ year }-${ month }-${day} </td>`
            firstView +='<td><img src="'+getIcon(arr[i]["icon"])+'" style=" width: 32px;" /></td>';
            firstView += `<td>${arr[i]["summary"]}</td>
             <td>${arr[i]["temperatureHigh"]}</td>
             <td>${arr[i]["temperatureLow"]}</td>
             <td>${arr[i]["windSpeed"]}</td>
             <tr>`
        }
            
        firstView += '</Table></div>';
        container.innerHTML = firstView;
    }

    function getFormatedTime(d) {
        let hour =  d.getHours();
        if( hour >= 12 ) {
           return (hour-12) + ' <span style="font-size: 14px;"> PM</span>';
        } else {
           return (hour) + ' <span style="font-size: 14px;"> AM</span>';
        }
    }

    function getIconCard2( name ) {
        url = "";
        switch( name ) {
            case "clear-day":
                url ="https://cdn3.iconfinder.com/data/icons/weather-344/142/sun-512.png"
            break;
            case "clear-night":
                url = "https://cdn3.iconfinder.com/data/icons/weather-344/142/sun-512.png";
            break;
            case "rain":
                url = "https://cdn3.iconfinder.com/data/icons/weather-344/142/rain-512.png";
            break;
            case "snow":
                url = "https://cdn3.iconfinder.com/data/icons/weather-344/142/snow-512.png";
            break;
            case "sleet":
                url = "https://cdn3.iconfinder.com/data/icons/weather-344/142/lightning-512.png";
            break;
            case "wind":
                url = "https://cdn4.iconfinder.com/data/icons/the-weather-is-nice-today/64/weather_10-512.png";
            break;
            case "fog":
                url = "https://cdn3.iconfinder.com/data/icons/weather-344/142/cloudy-512.png";
            break;
            case "cloudy":
                url = "https://cdn3.iconfinder.com/data/icons/weather-344/142/cloud-512.png";
            break;
            case "partly-cloudy-day":
                url = "https://cdn3.iconfinder.com/data/icons/weather-344/142/sunny-512.png";
            break;
            case "partly-cloudy-night":
                url = "https://cdn3.iconfinder.com/data/icons/weather-344/142/sunny-512.png";
            break;
        }
        return url;
    }

    function getPrecip(value) {
        if(value <=0.001) {
            return "None";
        } else if( value <=0.015 ) {
            return "Very Light";
        } else if( value <=0.05 ) {
            return "Light";
        } else if( value <=0.1 ) {
            return "Moderate";
        } else if( value > 1) {
            return "Heavy";
        }
    }

    function renderSecondView(data) {
        var sunrise = changeTimeZone(data["daily"]["data"][0]["sunriseTime"],  data["timezone"] );
        var sunset = changeTimeZone(data["daily"]["data"][0]["sunsetTime"], data["timezone"] );
        let secondView = `<div class="card" style="background-color: #98C6D0; width: 550px; display: block;"> 
            <div class="section1 clearfix">
                <div class="temperature" style="width: 45%; margin-top: 50px; padding-left: 20px; float: left">
                    <h1 class="weather"> ${data["currently"]["summary"]}</h1>
                    <h1 class="deg2"> ${Math.round(data.currently.temperature) }</h1>
                    <h6 class="deg-type"> F </h6>
                </div>
                <div class="weather-logo" >
                    <img src="${  getIconCard2(data['currently']['icon'])}"  width="250px" style="margin-top:-50px;">
                </div>
            </div>
            <div class="section2">
                <div class="line clearfix">
                    <p class="line-left"> Precipitation: </p>
                    <p class="line-right">   ${getPrecip(data["currently"]["precipIntensity"])} </p>
                </div>
                <div class="line clearfix">
                    <p class="line-left"> Chance of Rain: </p>
                    <p class="line-right">  ${Math.round(data["currently"]["precipProbability"]*100)} % </p>
                </div>
                <div class="line clearfix">
                    <p class="line-left"> Wind Speed: </p>
                    <p class="line-right">  ${ data["currently"]["windSpeed"]} mph </p>
                </div>
                <div class="line clearfix">
                    <p class="line-left"> Humidity: </p>
                    <p class="line-right">   ${Math.round(data["currently"]["humidity"]*100)} % </p>
                </div>
                <div class="line clearfix">
                    <p class="line-left"> Visibility: </p>
                    <p class="line-right">   ${data["currently"]["visibility"]} mi </p>
                </div>
                <div class="line clearfix">
                    <p class="line-left"> Sunrise / Sunset: </p>
                    <p class="line-right">  ${getFormatedTime(sunrise)} / ${getFormatedTime(sunset)}   </p>
                </div>
            </div>
        </div>

        <div class="hourly" style="display: block";> 
            <h1 class="hourly-title" style="text-align: center; margin: 30px 0; ">Day's Hourly Weather</h1>
            <div class="icon" style="text-align: center;"><img style="width: 40px" onclick="toggle(event)" data-visible="false" src="https://cdn4.iconfinder.com/data/icons/geosm-e-commerce/18/point-down-512.png"/></div>
        </div>`;
        container.innerHTML = secondView;
    }

    
    
    function drawChart(payload) {

        google.charts.load('current', {packages: ['corechart', 'line']}).then(function () {
            var data = new google.visualization.arrayToDataTable(payload);

            var options = {
                hAxis: {
                    title: 'Time'
                },
                vAxis: {
                    title: 'Temperature',
                    textPosition: 'none'
                },
                colors: ["#A6CED6"]
            };

            var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
            chart.draw(data, options);

        });
    }
</script>

</html>
