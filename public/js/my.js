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

function check() {
    let nameUser = document.forms["form"]["name"].value;
    let lengthOfName = nameUser.length;
    if (lengthOfName == 0) {
        document.getElementById("errorNameEmpty").innerHTML = "Pole wymagane";
    } else {
        document.getElementById("errorNameEmpty").innerHTML = "";
    }

    let emailUser = document.forms["form"]["email"].value;
    let correctOrNot = ValidateEmail(emailUser);
    console.log("email user: ", emailUser);
    let lengthOfEmail = emailUser.length;
    let passwordUser = document.forms["form"]["password"].value;
    let lengthOfPassword = passwordUser.length;
    let passwordOneLetter = atLeastOneLetter(passwordUser);
    let passwordOneNumber = atLeastOneNumber(passwordUser);

    let helpCounting = 0;

    if (lengthOfEmail == 0) {
        document.getElementById("error2EmailEmpty").innerHTML = "Pole wymagane";
    } else {
        document.getElementById("error2EmailEmpty").innerHTML = "";
        helpCounting++;
    }

    if ((lengthOfEmail > 0) && (correctOrNot === false)) {
        document.getElementById("error3IncorrectEmail").innerHTML = "Podaj poprawny email";
    } else if ((lengthOfEmail > 0) && (correctOrNot === true)) {
        document.getElementById("error3IncorrectEmail").innerHTML = "";
        helpCounting++;
    }

    if (lengthOfPassword == 0) {
        document.getElementById("error4PasswordEmpty").innerHTML = "Pole wymagane";
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

    if (helpCounting == 6) {
        return true;
    } else {
        return false;
    }
}