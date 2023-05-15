let date = new Date();

let day = date.getDate();
let month = date.getMonth() + 1;
let year = date.getFullYear();

if (month < 10) month = "0" + month;
if (day < 10) day = "0" + day;

let today = year + "-" + month + "-" + day;

// console.log("taday", today);
//let today = day + "-" + month + "-" + year;

input = document.getElementById('theDate');
input2 = document.getElementById('theDate1');

// console.log("theDate ", input);
// console.log("theDate ", input2);

if (input != null) {
    document.getElementById('theDate').value = today;
    document.getElementById('theDate').setAttribute("max", today);
    // inputProba = document.getElementById('theDate');
    // console.log("theDate ", inputProba);
} else if (input2 != null) {
    document.getElementById('theDate1').value = today;
    document.getElementById('theDate1').setAttribute("max", today);
    document.getElementById('theDate2').value = today;
    document.getElementById('theDate2').setAttribute("max", today);
    // inputProba2 = document.getElementById('theDate1');
    // console.log("theDate1 ", inputProba2);
    // inputProba3 = document.getElementById('theDate2');
    // console.log("theDate2 ", inputProba3);
}

function onlyNumberKey(evt) {
    let ASCIICode = (evt.which) ? evt.which : evt.keyCode
    if ((ASCIICode > 31 && ASCIICode < 44) || (ASCIICode > 44 && ASCIICode < 48) || (ASCIICode > 57))
        return false;
    return true;
}