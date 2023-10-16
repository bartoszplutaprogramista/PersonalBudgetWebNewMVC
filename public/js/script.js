let date = new Date();

let day = date.getDate();
let month = date.getMonth() + 1;
let year = date.getFullYear();

if (month < 10) month = "0" + month;
if (day < 10) day = "0" + day;

let today = year + "-" + month + "-" + day;

input = document.getElementById('theDate');
input2 = document.getElementById('theDate1');

if (input != null) {
    document.getElementById('theDate').value = today;
    document.getElementById('theDate').setAttribute("max", today);

} else if (input2 != null) {
    document.getElementById('theDate1').value = today;
    document.getElementById('theDate1').setAttribute("max", today);
    document.getElementById('theDate2').value = today;
    document.getElementById('theDate2').setAttribute("max", today);
}

function onlyNumberKey(evt) {
    let ASCIICode = (evt.which) ? evt.which : evt.keyCode
    if ((ASCIICode > 31 && ASCIICode < 44) || (ASCIICode > 44 && ASCIICode < 48) || (ASCIICode > 57))
        return false;
    return true;
}