function getChartDataPoint(elements) {
    const $entryElementsIncomes = $(elements);

    const $entryDispalyArrayElementsIncome = $.map($entryElementsIncomes, item => $(item).data('entryId'));

    let valueOfDataPoints = [];

    let sumChartIncomes = 0;

    let lastVal = 0;

    let singleVal = 0;

    let sumSingleVal = 0;

    for (let i = 0; i < $entryDispalyArrayElementsIncome.length - 1; i = i + 2) {
        sumChartIncomes += Number($entryDispalyArrayElementsIncome[i]);
    }

    for (let i = 0; i < $entryDispalyArrayElementsIncome.length - 1; i = i + 2) {

        if (i == $entryDispalyArrayElementsIncome.length - 2) {
            lastVal = 100 - sumSingleVal;
            singleVal = lastVal;
        } else {
            singleVal = Math.round((Number($entryDispalyArrayElementsIncome[i]) / sumChartIncomes) * 10000) / 100;
            sumSingleVal += singleVal;
            sumSingleVal = Math.round((sumSingleVal) * 100) / 100;
        }

        valueOfDataPoints.push({
            y: singleVal,
            label: $entryDispalyArrayElementsIncome[i + 1]
        });
    }
    return valueOfDataPoints;
}
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
            dataPoints: getChartDataPoint(sumOfIncomesElements)
        }]
    });
    let dataPointIncomes = getChartDataPoint(sumOfIncomesElements);
    if (dataPointIncomes.length !== 0) {
        chart.render();
    } else {
        document.getElementById("chartContainer").style.height = "0px";
        let element = document.querySelector("#table_incomes");
        element.classList.replace("mb-4", "mb-0");
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
            dataPoints: getChartDataPoint(sumOfExpensesElements)
        }]
    });
    let dataPointExpenses = getChartDataPoint(sumOfExpensesElements);
    console.log("DATA DO END OF CHArt ", dataPointExpenses);
    if (dataPointExpenses.length !== 0) {
        chart.render();
    } else {
        document.getElementById("chartContainerExpenses").style.height = "0px";
        let element = document.querySelector("#table_expenses");
        element.classList.replace("mb-4", "mb-0");
    }

}