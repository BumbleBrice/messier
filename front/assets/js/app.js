// Fonction pour envoyer une requête API
async function sendRequest(url, method, data = {}) {
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Erreur:', error);
        return { message: 'Une erreur est survenue.' };
    }
}

// Gestion de l'inscription
document.getElementById('signup-form').addEventListener('submit', async function (event) {
    event.preventDefault();

    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    const result = await sendRequest('http://localhost/signup.php', 'POST', { username, email, password });
    document.getElementById('message').innerHTML = result.message || 'Inscription réussie. Token: ' + result.jwt;
});

// Gestion de la connexion
document.getElementById('login-form').addEventListener('submit', async function (event) {
    event.preventDefault();

    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    const result = await sendRequest('http://localhost/login.php', 'POST', { email, password });
    document.getElementById('message').innerHTML = result.message || 'Connexion réussie. Token: ' + result.jwt;
});
