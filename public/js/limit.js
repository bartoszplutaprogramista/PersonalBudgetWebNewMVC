const amountInput = document.getElementById("amountOfExpanse");
const categorySelect = document.getElementById("paymentCategory");
const balanceInfoElements = document.querySelectorAll(".balanceInfo");
const dateInput = document.querySelector("#theDate");

const getLimitCategory = async (category) => {
    try {
        // const res = await fetch(`../api/limit/${category}`);
        const res = await fetch(`/api/limit/${category}`);
        const data = await res.text();
        return parseFloat(data);
    } catch (e) {
        console.log('Error ', e);
    }
}

// document.addEventListener("DOMContentLoaded", () => {

//     const categorySelect = document.querySelector("#paymentCategory");
//     const limitInfo = document.querySelector("#limitInfo");
//     const spentInfo = document.querySelector("#spentInfo");

//     const updateLimitInfo = async () => {
//         const category = categorySelect.value;

//         const limit = await getLimitCategory(category);

//         if (limit !== null && limit !== undefined) {
//             limitInfo.textContent = `Ustawiłeś limit ${limit} zł na miesiąc dla tej kategorii`;
//         } else {
//             limitInfo.textContent = `Nie ustawiono limitu dla tej kategorii`;
//         }

//         // Jeśli masz API do wydatków, możesz to też pobrać
//         // spentInfo.textContent = `Wydałeś ${spent} zł w tym miesiącu dla tej kategorii`;
//     };

//     // Aktualizacja przy zmianie kategorii
//     categorySelect.addEventListener("change", updateLimitInfo);

//     // Aktualizacja przy pierwszym załadowaniu strony
//     updateLimitInfo();
// });

// document.addEventListener("DOMContentLoaded", () => {

//     const categorySelect = document.querySelector("#paymentCategory");

//     const updateLimitInfo = async () => {
//         const category = categorySelect.value;

//         const limit = await getLimitCategory(category);

//         document.querySelectorAll(".limitInfo").forEach(el => {
//             if (limit === null || limit === undefined) {
//                 el.textContent = "Nie ustawiono limitu dla tej kategorii";
//             } else {
//                 el.textContent = `Ustawiłeś limit ${limit} zł na miesiąc dla tej kategorii`;
//             }
//         });
//     };

//     categorySelect.addEventListener("change", updateLimitInfo);
//     updateLimitInfo();
// });

const getMonthlySpent = async (category, year, month) => {
    try {
        const res = await fetch(`../api/expenses/summary/${category}/${year}/${month}`);
        const data = await res.json();
        return parseFloat(data.total) || 0;
    } catch (e) {
        console.log('Error ', e);
        return 0;
    }
}

document.addEventListener("DOMContentLoaded", () => {

    // const categorySelect = document.querySelector("#paymentCategory");
    // const dateInput = document.querySelector("#theDate");

    const updateLimitInfo = async () => {
        const category = categorySelect.value;
        const limit = await getLimitCategory(category);

        document.querySelectorAll(".limitInfo").forEach(el => {
            if (!limit) {
                el.textContent = "Nie ustawiono limitu dla tej kategorii";
            } else {
                el.textContent = `Ustawiłeś limit ${limit} zł na miesiąc dla tej kategorii`;
            }
        });
    };

    const updateSpentInfo = async () => {
        const category = categorySelect.value;
        const date = dateInput.value;

        if (!date) return;

        const [year, month] = date.split("-");

        const spent = await getMonthlySpent(category, year, month);

        document.querySelectorAll(".spentInfo").forEach(el => {
            // el.textContent = `Wydałeś ${spent} zł w tym miesiącu dla tej kategorii`;
            if (spent === 0) {
                el.textContent = "Nie wydałeś żadnych pieniędzy dla tej kategorii w tym miesiącu";
            } else {
                el.textContent = `Wydałeś ${spent} zł w tym miesiącu dla tej kategorii`;
            }
        });
    };

    categorySelect.addEventListener("change", () => {
        updateLimitInfo();
        updateSpentInfo();
        updateBalanceInfo();
    });

    // dateInput.addEventListener("change", updateSpentInfo);

    dateInput.addEventListener("change", () => {
        updateSpentInfo();
        updateBalanceInfo();
    });


    updateLimitInfo();
    updateSpentInfo();
    updateBalanceInfo();
});



const getBalanceAfterOperation = async (category, amount) => {
    try {
        const data = await getLimitCategory(category);
        const limit = data ? parseFloat(data) : 0;

        // Pobieramy datę z inputa
        // const date = document.getElementById("theDate").value;
        const date = dateInput.value;

        let monthlySpent = 0;

        if (date) {
            const [year, month] = date.split("-");
            monthlySpent = await getMonthlySpent(category, year, month);
        }

        console.log("monthlySpent ", monthlySpent, "limit ", limit);
        const balance = limit - (monthlySpent + amount);
        console.log("balance ", balance);

        return {
            limit,
            amount,
            monthlySpent,
            balance
        };
    } catch (e) {
        console.log('Error getBalanceAfterOperation ', e);
    }
}

// const getBalanceAfterOperation = async (category, amount) => {
//     try {
//         const data = await getLimitCategory(category);
//         console.log("DATA: ", data);
//         const limit = data ? parseFloat(data) : 0;
//         const balance = limit - amount;

//         console.log("LIMIT:", limit, "AMOUNT:", amount, "BALANCE:", balance);

//         return {
//             limit,
//             amount,
//             balance
//         };
//     } catch (e) {
//         console.log('Error getBalanceAfterOperation ', e);
//     }
// }


// const amountInput = document.getElementById("amountOfExpanse");
// const categorySelect = document.getElementById("paymentCategory");
// const balanceInfoElements = document.querySelectorAll(".balanceInfo");
// const dateInput = document.querySelector("#theDate");


const updateBalanceInfo = async () => {
    const category = categorySelect.value;
    const amount = parseFloat(amountInput.value) || 0;


    // console.log("ZMIANA!", category, amount);

    const result = await getBalanceAfterOperation(category, amount);
    if (!result) return;

    // balanceInfoElements.forEach(el => {
    //     el.textContent =
    //         `Twój bilans po tej operacji wynosi: ${result.balance.toFixed(2)} zł`;
    // });
    balanceInfoElements.forEach(el => {
        el.textContent = `Twój bilans po tej operacji wynosi: ${result.balance.toFixed(2)} zł`;

        if (result.balance < 0) {
            el.classList.add("negative");
        } else {
            el.classList.remove("negative");
        }
    });


};

amountInput.addEventListener("input", updateBalanceInfo);
// categorySelect.addEventListener("change", updateBalanceInfo);