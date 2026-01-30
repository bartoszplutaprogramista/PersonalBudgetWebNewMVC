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

document.addEventListener("DOMContentLoaded", () => {

    const categorySelect = document.querySelector("#paymentCategory");

    const updateLimitInfo = async () => {
        const category = categorySelect.value;

        const limit = await getLimitCategory(category);

        document.querySelectorAll(".limitInfo").forEach(el => {
            if (limit === null || limit === undefined) {
                el.textContent = "Nie ustawiono limitu dla tej kategorii";
            } else {
                el.textContent = `Ustawiłeś limit ${limit} zł na miesiąc dla tej kategorii`;
            }
        });
    };

    categorySelect.addEventListener("change", updateLimitInfo);
    updateLimitInfo();
});