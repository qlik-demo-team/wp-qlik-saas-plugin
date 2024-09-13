// qlik-embed.js

async function getAccessToken() {
    const response = await fetch('/wp-json/qs/v1/token', {
        method: 'GET',
        credentials: 'include',
        redirect: 'follow',
        mode: 'cors'
    });
    if (response.status === 200) {
        const accessToken = await response.json();
        return accessToken;
    }
    const err = new Error("Unexpected serverside authentication error");
    err.status = response.status;
    throw err;
}

document.addEventListener('DOMContentLoaded', () => {
    console.log("qlik-embed.js loaded and initialized.");
});