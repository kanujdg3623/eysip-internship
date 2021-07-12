@extends('layouts.app')

@section('content')
<div class="container">
    <div class="notification is-warning is-light" style="display:none" id="analysisFormError">
    </div>
    <form class="analysisForm" onsubmit="submitDateForm(event)" method="get">
        @csrf
        <div class="field">
            <label class="label">Start Date</label>
            <div class="control">
                <input class="input" id="fromDate" onchange="onDateChange(event,'toDate')" type="date" placeholder="e.g 2020-09-01">
            </div>
        </div>

        <div class="field">
            <label class="label">End Date</label>
            <div class="control">
                <input class="input" id="toDate" onchange="onDateChange(event,'fromDate')" type="date" placeholder="e.g 2020-09-30">
            </div>
        </div>
        <div class="field">
            <button type="submit" class="button">Fetch</button>
        </div>
    </form>
    <div class="container" id="chart-container">
        <progress class="progress is-small is-primary" max="100">10%</progress>
    </div>
    <div class="box" id="chart-canvas-container" style="display:none;">
        <canvas id="clientChart" style="width:100%;height:100%;" width="960" height="400"></canvas>
    </div>
    <div class="box" id="map-canvas-container" style="display:none;">
        <div id="clientMap">
        </div>
    </div>
     <div class="box" id="table-canvas-container" style="display:none;">
        <div id="clientTable"></div>
    </div>
</div>
@endsection

@section("component-styles")
<link href="{{ asset('css/leafletApp.css') }}" rel="stylesheet">
<style>
#clientMap{
    height:400px;
}

.fadeIn {
    -webkit-animation: fadein 5s; /* Safari, Chrome and Opera > 12.1 */
    -moz-animation: fadein 5s; /* Firefox < 16 */
    -ms-animation: fadein 5s; /* Internet Explorer */
    -o-animation: fadein 5s; /* Opera < 12.1 */
    animation: fadein 5s;
}

@keyframes fadein {
    0% { opacity: 0; }
    30% { opacity: 1; }
    70% { opacity: 1; }
    100% { opacity: 0; }
}

/* Firefox < 16 */
@-moz-keyframes fadein {
    0% { opacity: 0; }
    30% { opacity: 1; }
    70% { opacity: 1; }
    100% { opacity: 0; }
}

/* Safari, Chrome and Opera > 12.1 */
@-webkit-keyframes fadein {
    0% { opacity: 0; }
    30% { opacity: 1; }
    70% { opacity: 1; }
    100% { opacity: 0; }
}

/* Internet Explorer */
@-ms-keyframes fadein {
    0% { opacity: 0; }
    30% { opacity: 1; }
    70% { opacity: 1; }
    100% { opacity: 0; }
}

/* Opera < 12.1 */
@-o-keyframes fadein {
    0% { opacity: 0; }
    30% { opacity: 1; }
    70% { opacity: 1; }
    100% { opacity: 0; }
}
</style>
@endsection

@section('component-script')
<script src="{{ asset('js/chartApp.js') }}" defer></script>
<script src="{{ asset('js/leafletApp.js') }}" defer></script>
<script>
window.chartColors = [
    'rgb(255, 99, 132)', // red
    'rgb(240,128,128)', // pink
    'rgb(255, 159, 64)', // orange
    'rgb(218,165,32)', // gold
    'rgb(255, 205, 86)', // yellow
    'rgb(75, 192, 192)', // green
    'rgb(85,107,47)', // darkgreen
    'rgb(32,178,170)', // lightblue
    'rgb(0,255,255)', // cyan
    'rgb(54, 162, 235)', // blue
    'rgb(25,25,112)', // darkblue
    'rgb(153, 102, 255)', // purple
    'rgb(245,222,179)', // wheat
    'rgb(139,69,19)', // brown
    'rgb(112,128,144)' // gray
    ] 
// Main Code
const analysisTabsAndSelect = {
    currentIndex:0,
    isReverse:false,
    config:[
        {
            tabName:"Location Analysis",
            query:"map",
            type:"map",
            toSort:false,
            selectedOption:0,
            selectOptions:[
                {
                    name:"Longitude Latitude Based",
                    field: "latLong",
                },
                {
                    name:"City Based",
                    field: "city",
                    scale: 10
                },
                {
                    name:"State Based",
                    field: "state",
                    scale: 50
                },
                {
                    name:"Country Based",
                    field: "country",
                    scale: 100
                },
            ]
        },
        {
            tabName:"User Analysis",
            query:"user",
            type:"table",
            selectedOption:0,
            toSort:false,
            selectOptions:[
                {
                    name:"User Count",
                    field: "isReturning",
                },
            ]
        },
        {
            tabName:"Device Analysis",
            query:"device",
            type:"chart",
            selectedOption:0,
            toSort:true,
            selectOptions:[
                {
                    name:"Browser",
                    field: "browser",
                },
                {
                    name:"Screen",
                    field: "screen",
                },
                {
                    name:"Mobile",
                    field: "mobile",
                },
                {
                    name:"OS",
                    field: "os",
                },
            ]
        }
    ]
};

let renderData = null;
let clientChart = null;
let chartElement = null;
let chartElementCtx = null;
let clientMap = null;
let clientMapMarkers = [];

const preData = {
    device:{
        screen:{},
        mobile:{},
        browser:{},
        os:{}
    },
    map:{
        latLong:[],
        city:{},
        state:{},
        country:{}
    },
    user:{
        new:0,
        returning:0,
        total:0
    }
}

function shuffle(arr) {
    let arrCopy = arr.slice();
    for (var i = arrCopy.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * i); // no +1 here!
        var temp = arrCopy[i];
        arrCopy[i] = arrCopy[j];
        arrCopy[j] = temp;
    }
    return arrCopy;
};

Date.prototype.toDateInputValue = (function() {
    var local = new Date(this);
    return local.toISOString().slice(0,10);
});

const onDateChange = (event, target) => {
    const targetElement = document.getElementById(target);
    switch (target) {
        case "fromDate":
            targetElement.max = event.target.value;
        break;
        case "toDate":
            targetElement.min = event.target.value;
        break;
        default:
            break;
    }
}

const submitDateForm = (event) => {
    event.preventDefault()
    const fromDate = document.getElementById("fromDate").value;
    const toDate = document.getElementById("toDate").value;
    getVisitorAnalysisData(fromDate?fromDate:undefined,toDate?toDate:undefined)
}

const preProcessData = (data) => {
    const analyseData = (data) => {
        data.forEach(eachResult => {
            // Location Based
            preData.map["latLong"].push([eachResult.latitude,eachResult.longitude])
            preData.map.city[eachResult.city] = preData.map.city[eachResult.city]?preData.map.city[eachResult.city]+1:1
            preData.map.state[eachResult.state] = preData.map.state[eachResult.state]?preData.map.state[eachResult.state]+1:1
            preData.map.country[eachResult.country] = preData.map.country[eachResult.country]?preData.map.country[eachResult.country]+1:1
            // User Based
            preData.user.new = eachResult.is_new?preData.user.new+1:preData.user.new
            preData.user.returning = eachResult.is_returning.length>1?preData.user.returning+1:preData.user.returning
            preData.user.total = preData.user.total + eachResult.is_returning.length
            // Device Based
            const osBrowser = JSON.parse(eachResult.os_browser)
            preData.device.screen[osBrowser.screen] = preData.device.screen[osBrowser.screen]?preData.device.screen[osBrowser.screen]+1:1
            preData.device.mobile[osBrowser.mobile] = preData.device.mobile[osBrowser.mobile]?preData.device.mobile[osBrowser.mobile]+1:1
            preData.device.browser[osBrowser.browser] = preData.device.browser[osBrowser.browser]?preData.device.browser[osBrowser.browser]+1:1
            preData.device.os[osBrowser.os] = preData.device.os[osBrowser.os]?preData.device.os[osBrowser.os]+1:1
        });
    }
    analyseData(data.result)
}

const analysisFormError = (data) => {
    const analysisFormError = document.getElementById("analysisFormError");
    analysisFormError.innerText = data
    analysisFormError.style.display = "block";
    analysisFormError.classList.add("fadeIn")
    setTimeout(function(){ 
        analysisFormError.classList.remove("fadeIn")
        analysisFormError.style.display = "none"
    }, 5000);
}

// Fetching PreData based on query and respective feild
const fetchPreData = (query, field, toSort, sortFieldBy) => {
    function fieldSorter(fields) {
        return function (a, b) {
            return fields
                .map(function (o) {
                    var dir = 1;
                    if (o[0] === '-') {
                    dir = -1;
                    o=o.substring(1);
                    }
                    if (a[o] > b[o]) return dir;
                    if (a[o] < b[o]) return -(dir);
                    return 0;
                })
                .reduce(function firstNonZeroValue (p,n) {
                    return p ? p : n;
                }, 0);
        };
    }
    const value = preData[query]

    if(query==="user"){
        return value
    }

    if(field==="latLong")
    {
        return value[field]
    }

    const keys = Object.keys(value[field]);
    
    const renderDataArray = keys.map((eachKey) => {
        const returnValue = {}
        returnValue[field] = eachKey
        returnValue["count"] = value[field][eachKey]
        return returnValue
    })
    return toSort?renderDataArray.sort(fieldSorter([...sortFieldBy])):renderDataArray
}

// Render Elements

const toggleReverse = (event) => {
    analysisTabsAndSelect.isReverse = event.target.checked
    const currentActiveTabConfig = analysisTabsAndSelect.config[analysisTabsAndSelect.currentIndex]
    const currentSelectField = `${currentActiveTabConfig.selectOptions[currentActiveTabConfig.selectedOption].field}`
    renderData = fetchPreData(currentActiveTabConfig.query, currentSelectField, currentActiveTabConfig.toSort, [`${analysisTabsAndSelect.isReverse?"-":""}${currentSelectField}`])
    renderUpdates(currentActiveTabConfig.type)
}

const changeSelectRender = (index) => {
    // Select Render
    const currentActiveTabConfig = analysisTabsAndSelect.config[analysisTabsAndSelect.currentIndex]
    currentActiveTabConfig.selectedOption = parseInt(index, 10);

    const currentSelectField = `${currentActiveTabConfig.selectOptions[currentActiveTabConfig.selectedOption].field}`
    renderData = fetchPreData(currentActiveTabConfig.query, currentSelectField, currentActiveTabConfig.toSort, [`${analysisTabsAndSelect.isReverse?"-":""}${currentSelectField}`])
    renderUpdates(currentActiveTabConfig.type)
    const initSelectRenderer = `
    <div class="field">
        <label class="label">Options</label>
        <div class="control">
            <div class="select is-primary">
                <select onchange="changeSelectRender(event.target.value)">
                    ${currentActiveTabConfig.selectOptions.map((eachSelect, index) => {
                    return `<option ${index===currentActiveTabConfig.selectedOption?"selected":""} value=${index}>${eachSelect.name}</option>`
                    }).join("")}
                </select>
            </div>
        </div>
    </div>
    ${
    currentActiveTabConfig.toSort?`<div class="field">
        <label class="checkbox">
            <input onchange="toggleReverse(event)" ${analysisTabsAndSelect.isRevserse?"checked":""} type="checkbox">
            Reverse
        </label>
    </div>`:""}
    `
    const chartSelectElement = document.getElementById("chart-container-select");
    chartSelectElement.innerHTML = `${initSelectRenderer}`;
}

const changeTabRenderer = (index) => {
    analysisTabsAndSelect.currentIndex = index;
    const currentActiveTabConfig = analysisTabsAndSelect.config[analysisTabsAndSelect.currentIndex]
    const chartContainer = document.getElementById("chart-container");
    // Tab Render
    const innerTabs = analysisTabsAndSelect.config.map((eachTab, index) => {
            return `
            <li class="${index===analysisTabsAndSelect.currentIndex?"is-active":""}" ${index!==analysisTabsAndSelect.currentIndex?`onclick="changeTabRenderer(${index})"`:""}>
                <a>
                    <span>${eachTab.tabName}</span>
                </a>
            </li>`
    })
    const initTabRenderer = `
    <div class="tabs is-centered" id="chart-container-tabs">
        <ul>
        ${innerTabs.join("")}
        </ul>
    </div>
    <div class="box is-centered" id="chart-container-select">
    </div>
    `
    chartContainer.innerHTML = `${initTabRenderer}`;
    changeSelectRender(`${currentActiveTabConfig.selectedOption}`)
}

function getColorRange(d, scale) {
    return d > 100*scale ? '#800026' :
           d > 50*scale  ? '#BD0026' :
           d > 20*scale  ? '#E31A1C' :
           d > 10*scale  ? '#FC4E2A' :
           d > 5*scale   ? '#FD8D3C' :
           d > 2*scale   ? '#FEB24C' :
           d > 1*scale   ? '#FED976' :
                      '#FFEDA0';
}


const renderUpdates = (elementType) => {
    const currentActiveTabConfig = analysisTabsAndSelect.config[analysisTabsAndSelect.currentIndex]
    const currentSelectedOption = currentActiveTabConfig.selectOptions[currentActiveTabConfig.selectedOption]

    if(elementType==="chart"){
        const chartParentElement = document.getElementById('chart-canvas-container')
        chartParentElement.style.display = "block"; 
        const mapParentElement = document.getElementById('map-canvas-container')
        mapParentElement.style.display = "none"; 
        const tableParentElement = document.getElementById('table-canvas-container')
        tableParentElement.style.display = "none";
        // Edit Chart
        clientChart.data.labels = renderData.map(a => a[currentSelectedOption.field])
        clientChart.data.datasets = [{
            label: currentSelectedOption.name,
            backgroundColor: shuffle(window.chartColors).splice(0, renderData.length),
            data: renderData.map(a => a.count),
            fill: false,
        }]
        clientChart.options.title.text = currentActiveTabConfig.tabName;
        clientChart.options.scales.xAxes.labelString = currentSelectedOption.name;
        clientChart.options.scales.yAxes.labelString = `count`;
        clientChart.update()
    } else if(elementType==="map"){
        clientMapMarkers.forEach(eachMarker => {
            clientMap.removeLayer(eachMarker);
        });
        let icon = new L.Icon.Default();
        icon.options.shadowSize = [0,0];

        const mapParentElement = document.getElementById('map-canvas-container')
        mapParentElement.style.display = "block"; 
        const chartParentElement = document.getElementById('chart-canvas-container')
        chartParentElement.style.display = "none"; 
        const tableParentElement = document.getElementById('table-canvas-container')
        tableParentElement.style.display = "none";
        if(currentSelectedOption.field === "latLong"){
            clientMapMarkers = []
            renderData.forEach((eachLocation, index) => {
                const marker = L.marker([eachLocation[0], eachLocation[1]],{icon : icon}).addTo(clientMap)
                clientMapMarkers.push(marker)
            });
        } else{
            clientMapMarkers = []
            renderData.forEach((eachLocation, index) => {
                $.ajax({
                    url : `https://nominatim.openstreetmap.org/search?${currentSelectedOption.field}=${eachLocation[currentSelectedOption.field]}&format=geojson&polygon_geojson=0&limit=1`,
                    type: "GET",
                    success: function(data, textStatus, jqXHR)
                    {
                        const value = data.features[0]
                        const marker = L.marker([value.geometry.coordinates[1], value.geometry.coordinates[0]],{icon : icon}).addTo(clientMap)
                        clientMapMarkers.push(marker)
                        marker.bindPopup(`${eachLocation[currentSelectedOption.field]}:${eachLocation.count}`);
                        marker.on('mouseover', function (e) {
                            this.openPopup();
                        });
                        marker.on('mouseout', function (e) {
                            this.closePopup();
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        alert(`Unable to fetch ${query}=${location}`)
                        return null;
                    }
                });
            });
        }
    } else if(elementType==="table"){
        const tableParentElement = document.getElementById('table-canvas-container')
        tableParentElement.style.display = "block"; 
        const chartParentElement = document.getElementById('chart-canvas-container')
        chartParentElement.style.display = "none"; 
        const mapParentElement = document.getElementById('map-canvas-container')
        mapParentElement.style.display = "none"; 
        const tableRenderer = `
        <table class="table" style="margin:0 auto;">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>New</th>
                    <th>Returning</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>User</th>
                    <td>${renderData.new}</td>
                    <td>${renderData.returning}</td>
                    <td>${renderData.total}</td>
                </tr>
            </tbody>
        </table>`
        const tableContainer = document.getElementById('clientTable')
        tableContainer.innerHTML = `${tableRenderer}`;
    } else{
        console.log("Improper elementType given")
    }
}

const initChartAndMap = () => {
    // init Chart
    chartElement = document.getElementById('clientChart')
    chartElementCtx = chartElement.getContext('2d');
    const config = {
        type: 'bar',
        data: {
            labels:[],
            datasets: [{
                label: [],
                backgroundColor: window.chartColors.red,
                borderColor: window.chartColors.red,
                borderWidth: 1,
                data: [],
                fill: false,
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: null
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
							display: true,
							labelString: ''
						}
                }],
                yAxes: [{
                    display: true,
                    ticks: {
                        beginAtZero: true
                    },
                    scaleLabel: {
							display: true,
							labelString: 'count'
						}
                }]
            }
        }
    };
    clientChart = new Chart(chartElementCtx, config);

    // Init Map
    clientMap = L.map('clientMap').setView([21.1458, 79.0882], 4);
    const attribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>'
    const tile = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution,
        maxZoom: 18,
    }).addTo(clientMap);
}


const getClientAnalysisData = (fromDate=undefined,toDate=undefined) => {
    $.ajax({
        url : "{{ route('client.analysis')}}",
        type: "GET",
        data: {fromDate, toDate},
        success: function(data, textStatus, jqXHR)
        {
            preProcessData(data);
            initChartAndMap()
            changeTabRenderer(analysisTabsAndSelect.currentIndex)
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            analysisFormError(`${textStatus.toUpperCase()}: ${JSON.parse(jqXHR.responseText).errors}`)
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    getClientAnalysisData();
    const toDate = document.getElementById("toDate");
    const todayInputValue = new Date().toDateInputValue();
    toDate.value = todayInputValue
    const fromDate = document.getElementById("fromDate");
    fromDate.max = todayInputValue
});
</script>
@endsection