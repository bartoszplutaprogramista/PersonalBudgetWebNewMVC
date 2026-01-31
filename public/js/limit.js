const getLimitCategory = async (category) => {
    try {
        const res = await fetch(`../api/limit/${category}`);
        const data = await res.json();
        return data;
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
        return data.total || 0;
    } catch (e) {
        console.log('Error ', e);
        return 0;
    }
}

document.addEventListener("DOMContentLoaded", () => {

    const categorySelect = document.querySelector("#paymentCategory");
    const dateInput = document.querySelector("#theDate");

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
    });

    dateInput.addEventListener("change", updateSpentInfo);

    updateLimitInfo();
    updateSpentInfo();
});