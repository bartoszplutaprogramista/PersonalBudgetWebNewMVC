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

function atLeastOneNumber(input) {
    let validRegex = /.*\d+.*/i;
    if (input.match(validRegex)) {
        return true;
    } else {
        return false;
    }
}

function atLeastOneLetter(input) {
    let validRegex = /.*[a-z]+.*/i;
    if (input.match(validRegex)) {
        return true;
    } else {
        return false;
    }
}

function ValidateEmail(input) {
    let validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
    if (input.match(validRegex)) {
        return true;
    } else {
        return false;
    }
}

// let correctOrNot = ValidateEmail(document.form.email);
// console.log("correct or not:", correctOrNot);

function check() {
    let nameUser = document.forms["form"]["name"].value;
    let lengthOfName = nameUser.length;
    if (lengthOfName == 0) {
        document.getElementById("errorNameEmpty").innerHTML = "Pole wymagane";
        //        return false;
    } else {
        document.getElementById("errorNameEmpty").innerHTML = "";
    }

    let emailUser = document.forms["form"]["email"].value;
    let correctOrNot = ValidateEmail(emailUser);
    // let correctOrNot = ValidateEmail(emailUser);


    // console.log("document.form.email:", document.form.email);
    console.log("email user: ", emailUser);
    // let correctOrNot = ValidateEmail(emailUser);
    let lengthOfEmail = emailUser.length;
    let passwordUser = document.forms["form"]["password"].value;
    let lengthOfPassword = passwordUser.length;
    let passwordOneLetter = atLeastOneLetter(passwordUser);
    let passwordOneNumber = atLeastOneNumber(passwordUser);

    let helpCounting = 0;

    // console.log("lengthOfEmail: ", lengthOfEmail);
    // console.log("correct or not:", correctOrNot);



    //   let validEmail = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;


    if (lengthOfEmail == 0) {
        document.getElementById("error2EmailEmpty").innerHTML = "Pole wymagane";
        //      return false;
    } else {
        document.getElementById("error2EmailEmpty").innerHTML = "";
        helpCounting++;
    }

    if ((lengthOfEmail > 0) && (correctOrNot === false)) {
        document.getElementById("error3IncorrectEmail").innerHTML = "Podaj poprawny email";
        //       return false;
    } else if ((lengthOfEmail > 0) && (correctOrNot === true)) {
        document.getElementById("error3IncorrectEmail").innerHTML = "";
        helpCounting++;
    }

    if (lengthOfPassword == 0) {
        document.getElementById("error4PasswordEmpty").innerHTML = "Pole wymagane";
        //      return false;
    } else {
        document.getElementById("error4PasswordEmpty").innerHTML = "";
        helpCounting++;
    }

    if ((lengthOfPassword > 0) && (lengthOfPassword < 6)) {
        document.getElementById("error5PasswordTooShort").innerHTML = "Wprowadź conajmniej 6 znaków";
    } else if ((lengthOfPassword > 0) && (lengthOfPassword >= 6)) {
        document.getElementById("error5PasswordTooShort").innerHTML = "";
        helpCounting++;
    }

    if ((lengthOfPassword > 0) && (passwordOneLetter === false)) {
        document.getElementById("error6AtLeastOneLetter").innerHTML = 'Hasło powinno zawierać conajmniej jedną literę';
    } else if ((lengthOfPassword > 0) && (passwordOneLetter === true)) {
        document.getElementById("error6AtLeastOneLetter").innerHTML = "";
        helpCounting++;
    }

    if ((lengthOfPassword > 0) && (passwordOneNumber === false)) {
        document.getElementById("error7AtLeastOneNumber").innerHTML = 'Hasło powinno zawierać conajmniej jedną liczbę';
    } else if ((lengthOfPassword > 0) && (passwordOneNumber === true)) {
        document.getElementById("error7AtLeastOneNumber").innerHTML = "";
        helpCounting++;
    }

    // if (preg_match('/.*\d+.*/i', $this - > password) == 0) {
    //     $this - > errors[] = 'NEW Password needs at least one number';
    // }
    /*
    if ((lengthOfPassword > 0) && (correctOrNot === false)) {
        document.getElementById("error3IncorrectEmail").innerHTML = "Podaj poprawny email";
        //       return false;
    } else if ((lengthOfEmail > 0) && (correctOrNot === true)) {
        document.getElementById("error3IncorrectEmail").innerHTML = "";
    } */

    console.log("helpCounting: ", helpCounting);

    // return false;

    if (helpCounting == 6) {
        return true;
        // console.log("helpCounting: ", helpCounting);
    } else {
        return false;
    }
}