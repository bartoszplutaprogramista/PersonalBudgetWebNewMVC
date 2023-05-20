/*
function check() {

    if (document.getElementById('inputName').value = "") {
        let info = "TO POLE MUSISZ UZUPEŁNIĆ";
        return info;
    }
} */

/*
$('inputName').click(function () {
    let data = $(this).attr('title');
    //alert(data.name);
    eval(data);
});

function testFunction() {
    alert("TO POLE MUSISZ UZUPEŁNIĆ");
} */

/*
function check() {
    let txt = document.getElementById("inputName").value;
    let txtLen = txt.length;
    if (txtLen == 0) {
        document.getElementById("inputName").setAttribute("title", "Pole wymagane");
    }
} */

function ValidateEmail(input) {
    let validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
    if (input.value.match(validRegex)) {
        return true;
    } else {
        return false;
    }
}

// let correctOrNot = ValidateEmail(document.form.email);
// console.log("correct or not:", correctOrNot);

function check() {
    let nameUser = document.forms["form"]["nameOfUser"].value;
    let lengthOfName = nameUser.length;
    if (lengthOfName == 0) {
        document.getElementById("errorNameEmpty").innerHTML = "Pole wymagane";
        //        return false;
    } else {
        document.getElementById("errorNameEmpty").innerHTML = "";
    }
    let emailUser = document.forms["form"]["email"].value;
    let correctOrNot = ValidateEmail(document.form.email);
    // let correctOrNot = ValidateEmail(emailUser);


    console.log("document.form.email:", document.form.email);
    console.log("email user: ", emailUser);
    // let correctOrNot = ValidateEmail(emailUser);
    let lengthOfEmail = emailUser.length;
    console.log("lengthOfEmail: ", lengthOfEmail);
    console.log("correct or not:", correctOrNot);



    //   let validEmail = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;


    if (lengthOfEmail == 0) {
        document.getElementById("error2EmailEmpty").innerHTML = "Pole wymagane";
        //      return false;
    } else {
        document.getElementById("error2EmailEmpty").innerHTML = "";
    }
    if ((lengthOfEmail > 0) && (correctOrNot === false)) {
        document.getElementById("error3IncorrectEmail").innerHTML = "Podaj poprawny email";
        //       return false;
    } else if ((lengthOfEmail > 0) && (correctOrNot === true)) {
        document.getElementById("error3IncorrectEmail").innerHTML = "";
    }
    return false
}