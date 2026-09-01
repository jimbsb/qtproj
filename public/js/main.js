() => {
    const header = document.querySelector('header');
    const main = document.querySelector('main');
    const body = document.body;
    const isDarkMode = window.matchMedia?.('(prefers-color-scheme: dark)').matches;

    body.classList.toggle('dark', isDarkMode);
    main.style.height = `${window.innerHeight - header.offsetHeight}px`;
};