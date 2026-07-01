const themeToggle = document.getElementById('themeToggle');
const scoreTrack = document.getElementById('scoreTrack');
const languageSelect = document.getElementById('languageSelect');
const hideScoresToggle = document.getElementById('hideScoresToggle');
const hideScoresText = document.getElementById('hideScoresText');
const signOutText = document.getElementById('signOutText');
const helpLink = document.getElementById('helpLink');
const myAccountLink = document.getElementById('myAccountLink');
const nbaIdLink = document.getElementById('nbaIdLink');
let scrollStep = 0;

const translations = {
    en: {
        hideScores: 'Hide Scores',
        signOut: 'Sign Out',
        help: 'Help',
        myAccount: 'My Account',
        nbaId: 'NBA ID Benefits'
    },
    es: {
        hideScores: 'Ocultar Marcadores',
        signOut: 'Cerrar Sesión',
        help: 'Ayuda',
        myAccount: 'Mi Cuenta',
        nbaId: 'Beneficios NBA ID'
    }
};

function setLanguage(lang) {
    localStorage.setItem('language', lang);
    document.documentElement.lang = lang;
    if (hideScoresText) hideScoresText.textContent = translations[lang].hideScores;
    if (signOutText) signOutText.textContent = translations[lang].signOut;
    if (helpLink) helpLink.textContent = translations[lang].help;
    if (myAccountLink) myAccountLink.textContent = translations[lang].myAccount;
    if (nbaIdLink) nbaIdLink.textContent = translations[lang].nbaId;
}

if (languageSelect) {
    languageSelect.addEventListener('change', (e) => {
        setLanguage(e.target.value);
    });
    const savedLang = localStorage.getItem('language') || 'es';
    languageSelect.value = savedLang;
    setLanguage(savedLang);
}

if (hideScoresToggle) {
    const savedHide = localStorage.getItem('hideScores') === 'true';
    hideScoresToggle.checked = savedHide;
    toggleScores(savedHide);
    hideScoresToggle.addEventListener('change', (e) => {
        localStorage.setItem('hideScores', e.target.checked);
        toggleScores(e.target.checked);
    });
}

function toggleScores(hide) {
    const scores = document.querySelectorAll('.score-display');
    scores.forEach(score => {
        score.style.display = hide ? 'none' : '';
    });
}

if (themeToggle) {
    const themeIcon = themeToggle.querySelector('i');

    themeToggle.addEventListener('click', () => {
        const isDark = document.body.dataset.theme === 'dark';
        document.body.dataset.theme = isDark ? 'light' : 'dark';
        document.documentElement.dataset.bsTheme = isDark ? 'light' : 'dark';
        themeIcon.className = isDark ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
    });
}

setInterval(() => {
    if (!scoreTrack || scoreTrack.scrollWidth <= scoreTrack.clientWidth) {
        return;
    }

    scrollStep += 305;
    if (scrollStep >= scoreTrack.scrollWidth - scoreTrack.clientWidth) {
        scrollStep = 0;
    }

    scoreTrack.scrollTo({ left: scrollStep, behavior: 'smooth' });
}, 4500);
