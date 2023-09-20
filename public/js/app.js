/**
 * Add jQuery Validation plugin method for a valid password
 * 
 * Valid passwords contain at least one letter and one number.
 */
$.validator.addMethod('validPassword',
    function (value, element, param) {

        if (value != '') {
            if (value.match(/.*[a-z]+.*/i) == null) {
                return false;
            }
            if (value.match(/.*\d+.*/) == null) {
                return false;
            }
        }

        return true;
    },
    'Musi zawierać przynajmniej jedną literę i jedną cyfrę'
);


// $.validator.addMethod('validCaptcha',
//     function () {

//         $secretKey = "";

//         $check = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.
//             '&response='.$_POST['g-recaptcha-response']);

//         $answer = json_decode($check);


//         if ($answer - > success == false) {
//             return false;
//         }
//     }
//     'Potwierdź, że nie jesteś botem!';

//     //     if (value != '') {
//     //         if (value.match(/.*[a-z]+.*/i) == null) {
//     //             return false;
//     //         }
//     //         if (value.match(/.*\d+.*/) == null) {
//     //             return false;
//     //         }
//     //     }

//     //     return true;
//     // },
//     // 'Musi zawierać przynajmniej jedną literę i jedną cyfrę'
// );