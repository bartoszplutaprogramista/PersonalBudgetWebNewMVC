function getChartDataPoint(elements) {
    const $entryElementsIncomes = $(elements);
    // const $entryElementsIncomes = $('#chart_icome_1[data-entry-id]');

    // console.log("Goła wartość: ", $entryElements4);

    const $entryDispalyArrayElementsIncome = $.map($entryElementsIncomes, item => $(item).data('entryId'));

    console.log("moje jest: ", $entryDispalyArrayElementsIncome);
    console.log("moje jest poj: ", $entryDispalyArrayElementsIncome[0]);
    console.log("długość: ", $entryDispalyArrayElementsIncome.length);



    let valueOfDataPoints = [];

    let sumChartIncomes = 0;

    let lastVal = 0;

    let singleVal = 0;

    let sumSingleVal = 0;

    for (let i = 0; i < $entryDispalyArrayElementsIncome.length - 1; i = i + 2) {
        sumChartIncomes += Number($entryDispalyArrayElementsIncome[i]);
        // if (i = $entryDispalyArrayElementsIncome.length - 2) {
        //     lastVal = 100 - sumChartIncomes;
        // }
    }



    console.log("lastVal ", lastVal);
    console.log("sumChartIncomes ", sumChartIncomes);

    // for (let i = 0; i < $entryDispalyArrayElementsIncome.length - 1; i = i + 2) {
    //     sumChartIncomes += Number($entryDispalyArrayElementsIncome[i]);
    // }

    for (let i = 0; i < $entryDispalyArrayElementsIncome.length - 1; i = i + 2) {

        // if ($entryDispalyArrayElementsIncome.length > 2) &&

        if (i == $entryDispalyArrayElementsIncome.length - 2) {
            lastVal = 100 - sumSingleVal;
            console.log("lastVal ", lastVal);
            singleVal = lastVal;

            // console.log(singleVal);
        } else {
            singleVal = Math.round((Number($entryDispalyArrayElementsIncome[i]) / sumChartIncomes) * 10000) / 100;
            sumSingleVal += singleVal;
            sumSingleVal = Math.round((sumSingleVal) * 100) / 100;
            // sumSingleVal = Math.ceil(sumSingleVal * 100) / 100;
            console.log("singleVal ", singleVal);
            console.log("sumSingleVal ", sumSingleVal);
        }

        valueOfDataPoints.push({
            y: singleVal,
            label: $entryDispalyArrayElementsIncome[i + 1]
        });
    }
    console.log("Value oif DATA POINTS: ", valueOfDataPoints);
    return valueOfDataPoints;
}

// function expChart(expElem) {

//     const $entryElementsIncomes = $(expElem);
//     // console.log("Goła wartość: ", $entryElements4);

//     const $entryDispalyArrayElementsIncome = $.map($entryElementsIncomes, item => $(item).data('entryId'));

//     console.log("moje jest: ", $entryDispalyArrayElementsIncome);
//     console.log("moje jest poj: ", $entryDispalyArrayElementsIncome[0]);
//     console.log("długość: ", $entryDispalyArrayElementsIncome.length);



//     let valueOfDataPoints = [];

//     let sumChartIncomes = 0;

//     let lastVal = 0;

//     let singleVal = 0;

//     let sumSingleVal = 0;

//     for (let i = 0; i < $entryDispalyArrayElementsIncome.length - 1; i = i + 2) {
//         sumChartIncomes += Number($entryDispalyArrayElementsIncome[i]);
//         // if (i = $entryDispalyArrayElementsIncome.length - 2) {
//         //     lastVal = 100 - sumChartIncomes;
//         // }
//     }



//     console.log("lastVal ", lastVal);
//     console.log("sumChartIncomes ", sumChartIncomes);

//     // for (let i = 0; i < $entryDispalyArrayElementsIncome.length - 1; i = i + 2) {
//     //     sumChartIncomes += Number($entryDispalyArrayElementsIncome[i]);
//     // }

//     for (let i = 0; i < $entryDispalyArrayElementsIncome.length - 1; i = i + 2) {

//         // if ($entryDispalyArrayElementsIncome.length > 2) &&

//         if (i == $entryDispalyArrayElementsIncome.length - 2) {
//             lastVal = 100 - sumSingleVal;
//             console.log("lastVal ", lastVal);
//             singleVal = lastVal;

//             // console.log(singleVal);
//         } else {
//             singleVal = Math.round((Number($entryDispalyArrayElementsIncome[i]) / sumChartIncomes) * 10000) / 100;
//             sumSingleVal += singleVal;
//             sumSingleVal = Math.round((sumSingleVal) * 100) / 100;
//             // sumSingleVal = Math.ceil(sumSingleVal * 100) / 100;
//             console.log("singleVal ", singleVal);
//             console.log("sumSingleVal ", sumSingleVal);
//         }

//         valueOfDataPoints.push({
//             y: singleVal,
//             label: $entryDispalyArrayElementsIncome[i + 1]
//         });
//     }
//     return valueOfDataPoints;
// }


// let help = Number($entryIds[0]) + Number($entryIds[2]) + Number($entryIds[4]);

// console.log("help", help);

// let entrtyIds1 = $entryIds[0] / help;

// entrtyIds1 = entrtyIds1.toFixed(4) * 100;

// console.log("entrtyIds1 ", entrtyIds1);

// let entrtyIds2 = $entryIds[2] / help;

// entrtyIds2 = entrtyIds2.toFixed(4) * 100;

// let entrtyIds3 = $entryIds[4] / help;

// entrtyIds3 = entrtyIds3.toFixed(4) * 100;


window.onload = function () {

    var chart = new CanvasJS.Chart("chartContainer", {
        theme: "light2", // "light1", "light2", "dark1", "dark2"
        exportEnabled: true,
        animationEnabled: true,
        title: {
            text: "Zestawienie przychodów od " + dateFromToCurrentYear
        },
        data: [{
            type: "pie",
            startAngle: 25,
            toolTipContent: "<b>{label}</b>: {y}%",
            showInLegend: "true",
            legendText: "{label}",
            indexLabelFontSize: 16,
            indexLabel: "{label} - {y}%",
            // dataPoints: valueOfDataPoints
            // dataPoints: getChartDataPoint(sumOfIncomesElements)
            dataPoints: getChartDataPoint(sumOfIncomesElements)
        }]
    });
    let dataPointIncomes = getChartDataPoint(sumOfIncomesElements);
    console.log("DATA DO END OF CHArt ", dataPointIncomes);
    if (dataPointIncomes !== '') {
        chart.render();
    }

    var chart = new CanvasJS.Chart("chartContainerExpenses", {
        theme: "light2", // "light1", "light2", "dark1", "dark2"
        exportEnabled: true,
        animationEnabled: true,
        title: {
            text: "Zestawienie wydatków od " + dateFromToCurrentYear
        },
        data: [{
            type: "pie",
            startAngle: 25,
            toolTipContent: "<b>{label}</b>: {y}%",
            showInLegend: "true",
            legendText: "{label}",
            indexLabelFontSize: 16,
            indexLabel: "{label} - {y}%",
            // dataPoints: getChartDataPoint(sumOfExpensesElements)
            dataPoints: getChartDataPoint(sumOfExpensesElements)
        }]
    });
    let dataPointExpenses = getChartDataPoint(sumOfExpensesElements);
    console.log("DATA DO END OF CHArt ", dataPointExpenses);
    if (dataPointExpenses !== '') {
        chart.render();
    }

}
// console.log("DATA DO END OF CHArt ", dataPointIncomes);
// console.log("DATA DO END OF CHArt ", dataPointExpenses);