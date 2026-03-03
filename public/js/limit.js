const amountInput = document.getElementById("amountOfExpanse");
const categorySelect = document.getElementById("paymentCategory");
const balanceInfoElements = document.querySelectorAll(".balanceInfo");
const dateInput = document.querySelector("#theDate");

const getLimitCategory = async (category) => {
    try {
        const res = await fetch(`/api/limit/${category}`);
        const data = await res.json();
        if (data.limit === null) return null;

        return parseFloat(data.limit);

    } catch (e) {
        console.log('Error ', e);
        return null;
    }
}

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

    const updateLimitInfo = async () => {
        const category = categorySelect.value;
        const limit = await getLimitCategory(category);

        document.querySelectorAll(".limitInfo").forEach(el => {

            if (limit === null) {
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
        const limit = await getLimitCategory(category);

        if (limit === null) {
            return {
                limit: null
            };
        }

        const date = dateInput.value;

        let monthlySpent = 0;

        if (date) {
            const [year, month] = date.split("-");
            monthlySpent = await getMonthlySpent(category, year, month);
        }

        const balance = limit - (monthlySpent + amount);

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

const updateBalanceInfo = async () => {
    const category = categorySelect.value;
    const amount = parseFloat(amountInput.value) || 0;

    const result = await getBalanceAfterOperation(category, amount);
    if (!result) return;

    balanceInfoElements.forEach(el => {

        if (result.limit === null) {
            el.textContent = "Limit nie ustawiony!!";
            el.classList.remove("negative");
            return;
        }


        el.textContent = `Twój bilans po tej operacji wynosi: ${result.balance.toFixed(2)} zł`;

        if (result.balance < 0) {
            el.classList.add("negative");
        } else {
            el.classList.remove("negative");
        }
    });


};

amountInput.addEventListener("input", updateBalanceInfo);