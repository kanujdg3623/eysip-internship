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
        <canvas id="visitorChart" style="width:100%;height:100%;" width="960" height="400"></canvas>
    </div>
</div>
@endsection

@section("component-styles")
<style>
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
<script>
window.chartColors = {
    red: 'rgb(255, 99, 132)',
    orange: 'rgb(255, 159, 64)',
    yellow: 'rgb(255, 205, 86)',
    green: 'rgb(75, 192, 192)',
    blue: 'rgb(54, 162, 235)',
    purple: 'rgb(153, 102, 255)',
    grey: 'rgb(201, 203, 207)'
};
// Main Code
const analysisTabsAndSelect = {
    currentIndex:0,
    isReverse:false,
    config:[
        {
            tabName:"Daily Analysis",
            query:"daily",
            toSort:false,
            selectedOption:0,
            isWithoutBounce:false,
            selectOptions:[
                {
                    name:"Total Visits",
                    field: "totalVisits",
                    isWithoutBounce:false
                },
                {
                    name:"Total Pages Visited",
                    field: "totalPagesVisited",
                    isWithoutBounce:true
                },
                {
                    name:"Avg. Pages Visited",
                    field: "avgPagesVisited",
                    isWithoutBounce:true
                },
                {
                    name:"Avg. Visitor Duration",
                    field: "avgVisitorDuration",
                    isWithoutBounce:false
                },
                {
                    name:"Client Cookie Accepted",
                    field: "clientCookieAccepted",
                    isWithoutBounce:false
                },
                {
                    name:"No. Of Bounces",
                    field: "noOfBounces",
                    isWithoutBounce:false
                }
            ]
        },
        {
            tabName:"Page Analysis",
            query:"pages",
            selectedOption:0,
            toSort:true,
            isWithoutBounce:false,
            selectOptions:[
                {
                    name:"Avg. Page Duration",
                    field: "avgPageDuration",
                    isWithoutBounce:true
                },
                {
                    name:"No. Of Bounces",
                    field: "noOfBounces",
                    isWithoutBounce:false
                },
                {
                    name:"Total Visits",
                    field: "totalVisits",
                    isWithoutBounce:false
                }
            ]
        }
    ]
};

let chartData = null;
let visitorChart = null;
const preData = {
    daily:{},
    pages:{}
}

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
    const getDateKeys = (fromDate, toDate) => {
        const target = {};
        toDate.setDate(toDate.getDate() + 1);
        while (fromDate.toDateInputValue() !== toDate.toDateInputValue()) {
            target[fromDate.toDateInputValue()] = {
                totalVisits:0,
                noOfBounces:0,
                avgVisitorDuration:0,
                avgPagesVisited:0,
                avgPagesVisitedWithoutBounce:0,
                clientCookieAccepted:0,
                totalPagesVisited:0,
                totalPagesVisitedWithoutBounce:0,
            };
            fromDate.setDate(fromDate.getDate() + 1);
        }
        return target;
    };

    const analyseData = (data) => {
        data.forEach(eachResult => {
            const createdAt = new Date(eachResult.created_at)
            const beforeData = preData.daily[createdAt.toDateInputValue()]
            // Visitor Analysis
            const prevTotalVists = beforeData.totalVisits 
            beforeData.totalVisits = beforeData.totalVisits + 1
            beforeData.clientCookieAccepted = beforeData.clientCookieAccepted + (eachResult.client_id?1:0)
            let avgPagesVisited = 0
            let avgPagesVisitedWithoutBounce = 0
            let visitorDuration = 0;
            eachResult.vis_session_details.forEach(eachSession => {
                visitorDuration += eachSession.duration
                beforeData.noOfBounces = beforeData.noOfBounces + (eachSession.duration < 30?1:0)
                avgPagesVisited += 1
                avgPagesVisitedWithoutBounce += (eachSession.duration >= 30?1:0)
                // Page analysis
                const currentPage = preData.pages[eachSession.URI.pathname]?preData.pages[eachSession.URI.pathname]:{}
                const prevPageVisits = currentPage.totalVisits!==undefined ?  currentPage.totalVisits : 0;
                currentPage.totalVisits = (currentPage.totalVisits!==undefined?currentPage.totalVisits + 1:1)
                currentPage.noOfBounces = (currentPage.noOfBounces!==undefined?currentPage.noOfBounces + (eachSession.duration < 30?1:0):(eachSession.duration < 30?1:0))
                currentPage.avgPageDuration = currentPage.avgPageDuration!==undefined?(currentPage.avgPageDuration*prevPageVisits + (eachSession.duration>=30?eachSession.duration:0))/(prevPageVisits+1):(eachSession.duration>=30?eachSession.duration:0)
                if(eachSession.duration >= 30){
                    const prevPageVisitsWithoutBounce = prevPageVisits - currentPage.noOfBounces
                    currentPage.avgPageDurationWithoutBounce = currentPage.avgPageDurationWithoutBounce!==undefined?(currentPage.avgPageDurationWithoutBounce*prevPageVisitsWithoutBounce + eachSession.duration)/(prevPageVisitsWithoutBounce+1):eachSession.duration
                }
                preData.pages[eachSession.URI.pathname] = currentPage
            });
            beforeData.avgVisitorDuration = (beforeData.avgVisitorDuration*prevTotalVists + visitorDuration)/beforeData.totalVisits
            beforeData.totalPagesVisited = beforeData.totalPagesVisited + avgPagesVisited;
            beforeData.totalPagesVisitedWithoutBounce = beforeData.totalPagesVisitedWithoutBounce + avgPagesVisitedWithoutBounce;
            beforeData.avgPagesVisited = (beforeData.avgPagesVisited*prevTotalVists + avgPagesVisited)/beforeData.totalVisits
            beforeData.avgPagesVisitedWithoutBounce = (beforeData.avgPagesVisitedWithoutBounce*prevTotalVists + avgPagesVisitedWithoutBounce)/beforeData.totalVisits
            preData.daily[createdAt.toDateInputValue()] = beforeData
        });
    }
    
    const fromDate = new Date(data.fromDate);
    const toDate = new Date(data.toDate);
    preData.daily = getDateKeys(fromDate,toDate)
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
    const keys = Object.keys(value);
    const chartDataArray = keys.map((eachKey) => {
        const returnValue = {}
        returnValue[query] = eachKey
        returnValue[field] = value[eachKey][field]
        return returnValue
    })
    return toSort?chartDataArray.sort(fieldSorter([...sortFieldBy])):chartDataArray
}

// Render Elements
const changeBounceSelectRender = (shouldRender, withoutBounce="false") => {
    const currentActiveTabConfig = analysisTabsAndSelect.config[analysisTabsAndSelect.currentIndex]
    currentActiveTabConfig.isWithoutBounce = withoutBounce==="true"?true:false
    const currentSelectField = `${currentActiveTabConfig.selectOptions[currentActiveTabConfig.selectedOption].field}${currentActiveTabConfig.isWithoutBounce?"WithoutBounce":""}`
    chartData = fetchPreData(currentActiveTabConfig.query, currentSelectField, currentActiveTabConfig.toSort, [`${analysisTabsAndSelect.isReverse?"-":""}${currentSelectField}`])
    renderUpdatedChart(chartData.map(a => a[currentActiveTabConfig.query]),
                 chartData.map(a => a[currentSelectField]),
                  currentActiveTabConfig.tabName,`${currentActiveTabConfig.selectOptions[currentActiveTabConfig.selectedOption].name}${currentActiveTabConfig.isWithoutBounce?" Without Bounce":""}`)
    const initBounceSelectRender = shouldRender?`
    <div class="field">
        <label class="label">Without Bounce</label>
        <div class="control">
            <div class="select is-primary">
                <select onchange="changeBounceSelectRender(true, event.target.value)">
                    <option value=${false}>False</option>
                    <option value=${true}>True</option>
                </select>
            </div>
        </div>
    </div>
    `:""
    return initBounceSelectRender;
}

const toggleReverse = (event) => {
    analysisTabsAndSelect.isReverse = event.target.checked
    const currentActiveTabConfig = analysisTabsAndSelect.config[analysisTabsAndSelect.currentIndex]
    const currentSelectField = `${currentActiveTabConfig.selectOptions[currentActiveTabConfig.selectedOption].field}${currentActiveTabConfig.isWithoutBounce?"WithoutBounce":""}`
    chartData = fetchPreData(currentActiveTabConfig.query, currentSelectField, currentActiveTabConfig.toSort, [`${analysisTabsAndSelect.isReverse?"-":""}${currentSelectField}`])
    renderUpdatedChart(chartData.map(a => a[currentActiveTabConfig.query]),
                chartData.map(a => a[currentSelectField]),
                currentActiveTabConfig.tabName,`${currentActiveTabConfig.selectOptions[currentActiveTabConfig.selectedOption].name}${currentActiveTabConfig.isWithoutBounce?" Without Bounce":""}`)
}

const changeSelectRender = (index) => {
    // Select Render
    const currentActiveTabConfig = analysisTabsAndSelect.config[analysisTabsAndSelect.currentIndex]
    currentActiveTabConfig.selectedOption = parseInt(index, 10);
    const isWithoutBounce = currentActiveTabConfig.selectOptions[currentActiveTabConfig.selectedOption]
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
    ${changeBounceSelectRender(isWithoutBounce.isWithoutBounce)}
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

const renderUpdatedChart = (labels,values,queryName,fieldName,chartType="line") => {
    visitorChart.type = chartType
    visitorChart.data.labels = labels
    visitorChart.data.datasets = [{
        label: fieldName,
        backgroundColor: window.chartColors.red,
        borderColor: window.chartColors.red,
        data: values,
        fill: false,
    }]
    visitorChart.options.title.text = queryName;
    visitorChart.update();
}

const initChart = () => {
    const chartElement = document.getElementById('visitorChart')
    const chartParentElement = document.getElementById('chart-canvas-container')
    chartParentElement.style.display = "block"; 
    const ctx = chartElement.getContext('2d');
    const config = {
        type: 'line',
        data: {
            labels:[],
            datasets: [{
                label: [],
                backgroundColor: window.chartColors.red,
                borderColor: window.chartColors.red,
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
                    scaleLabel: {
							display: true,
							labelString: ''
						}
                }]
            }
        }
    };
    visitorChart = new Chart(ctx, config);
}


const getVisitorAnalysisData = (fromDate=undefined,toDate=undefined) => {
    $.ajax({
        url : "{{ route('visitor.analysis')}}",
        type: "GET",
        data: {fromDate, toDate},
        success: function(data, textStatus, jqXHR)
        {
            preProcessData(data);
            visitorChart===null?initChart():null
            changeTabRenderer(analysisTabsAndSelect.currentIndex)
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            analysisFormError(`${textStatus.toUpperCase()}: ${JSON.parse(jqXHR.responseText).errors}`)
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    getVisitorAnalysisData();
    const toDate = document.getElementById("toDate");
    const todayInputValue = new Date().toDateInputValue();
    toDate.value = todayInputValue
    const fromDate = document.getElementById("fromDate");
    fromDate.max = todayInputValue
});
</script>
@endsection