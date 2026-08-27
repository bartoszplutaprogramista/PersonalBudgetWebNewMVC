let cachedAdvice = null;

document.getElementById('openBtn').addEventListener('click', async () => {
    const modal = document.getElementById('modal');
    const textContainer = document.querySelector('.my-text');

    if (cachedAdvice !== null) {
        textContainer.innerHTML = cachedAdvice;
        modal.style.display = 'block';
        return;
    }

    // loader
    textContainer.innerHTML = "Ładowanie porady finansowej...";

    // wywołanie backendu
    const response = await fetch('/personalbudget/getAdviceAjax');
    const data = await response.text();

    // zapisujemy poradę do cache
    cachedAdvice = data;

    // wstawienie odpowiedzi
    textContainer.innerHTML = data;

    // otwarcie modala
    modal.style.display = 'block';
});

document.getElementById('closeBtn').addEventListener('click', () => {
    document.getElementById('modal').style.display = 'none';
});