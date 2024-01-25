async function isLoggedIn() {
    return await fetch(`https://${qs_settings.host}/api/v1/users/me`, {
        method: 'GET',
        mode: 'cors',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'qlik-web-integration-id': qs_settings.webIntegrationID,
        },
    });
}

async function getJWT() {
    return await fetch('/wordpress/wp-json/qs/v1/token', {
        method: 'GET',
        mode: 'cors',
        headers: {
            'Content-Type': 'application/json',
        },
    });
};

async function login_sheet(jwt) {
    var authHeader = `Bearer ${jwt}`;
    return await fetch(`https://${qs_settings.host}/login/jwt-session?qlik-web-integration-id=${qs_settings.webIntegrationID}`, {
        method: 'POST',
        mode: 'cors',
        credentials: 'include',
        withCredentials: true,
        headers: {
            'Authorization': authHeader,
            'qlik-web-integration-id': qs_settings.webIntegrationID,
            'Content-Type': 'application/json'
        },
        rejectunAuthorized: false
    });
};

function renderSheet(sheetContainer, sheetID, appId, width, height) {
    const iframe = document.createElement('iframe');
    iframe.src = `https://${qs_settings.host}/single/?appid=${appId}&sheet=${sheetID}&opt=ctxmenu`;
    iframe.style.width = width;
    iframe.style.height = height;
    sheetContainer.innerHTML = ''; // Clear previous content
    sheetContainer.appendChild(iframe);
}


function switchTab(tab) {
    const sheetID = tab.dataset.sheetId;
    const tabContainer = tab.closest('.qlik-tab-wrapper');
    const activeTab = tabContainer.querySelector('.qlik-tabs .active');
    const activeContent = tabContainer.querySelector('.qlik-sheet-content.active');

    // Remove active class from current active tab and content
    if (activeTab) {
        activeTab.classList.remove('active');
    }
    if (activeContent) {
        activeContent.classList.remove('active');
        activeContent.innerHTML = ''; // Clear content
    }

    // Add active class to clicked tab and corresponding content
    tab.classList.add('active');
    const newActiveContent = tabContainer.querySelector(`.qlik-sheet-content[data-sheet-id="${sheetID}"]`);
    newActiveContent.classList.add('active');

    // Render sheet in new active content
    const appId = newActiveContent.getAttribute('data-app-id') !== '' ? newActiveContent.getAttribute('data-app-id') : qs_settings.appID;
    const height = newActiveContent.getAttribute('data-height');
    renderSheet(newActiveContent, sheetID, appId, '100%', height);
}

function displayErrorMessage(message) {
    
    const tabsContainers = document.querySelectorAll('.qlik-tab-wrapper');
    const sheetsContainers = document.querySelectorAll('.sheet-container');

    const errorDiv = document.createElement('div');
    errorDiv.style.color = '#595959';
    errorDiv.style.fontStyle = 'italic';
    errorDiv.style.padding = '40px 10px 40px 10px';
    errorDiv.style.background = '#ffebeb';
    errorDiv.textContent = message;

    if(tabsContainers.length > 0) {
        tabsContainers.forEach(container => {
            container.innerHTML = ''; 
            container.appendChild(errorDiv);
        });
    }

    if(sheetsContainers.length > 0) {
        sheetsContainers.forEach(container => {
            container.innerHTML = ''; 
            container.appendChild(errorDiv);
        });
    }

}

async function init() {
    try {
        const loggedIn = await isLoggedIn();  
        if (loggedIn.status !== 200) {
            const jwtResponse = await getJWT();
            if (jwtResponse.status === 200) {
                const jwt = await jwtResponse.json();
                // await login_sheet(jwt);
                var loginRes = await login_sheet(jwt);
                if (loginRes.status != 200) {
                    console.error('Something went wrong while logging in.')
                } else {
                    const loggedIn = await isLoggedIn();
                    if (loggedIn.status != 200) {
                        displayErrorMessage('Third-party cookie blocking is preventing this app from loading. Try another browser or adjust your browser settings.');
                        return;
                    }
                }
            } else {
                console.error('Error fetching JWT.');
            }
        }

        // Initialize all sheet objects that are not part of tabs
        const singleSheets = document.querySelectorAll('[qlik-saas-sheet-id]:not(.qlik-sheet-content)');

        singleSheets.forEach(container => {
            const sheetID = container.getAttribute('qlik-saas-sheet-id');
            const appId = container.getAttribute('app-id') || qs_settings.appID;
            const height = container.getAttribute('height') || '800px'; // Default height
            renderSheet(container, sheetID, appId, '100%', height);
        });

        // Initialize tabs if they exist
        const tabElements = document.querySelectorAll('.qlik-tab');

        if (tabElements.length > 0) {
            tabElements.forEach(tab => {
                tab.addEventListener('click', () => switchTab(tab));
                // Automatically click the first tab to load it
                if (tab.classList.contains('active')) {
                    tab.click();
                }
            });
        }
    } catch (err) {
        console.error('Error during initialization: ', err);
    }
}

// Start the initialization process
init();