async function isLoggedIn_nebula() {
  return await fetch(`https://${settings.host}/api/v1/users/me`, {
      method: 'GET',
      mode: 'cors',
      credentials: 'include',
      headers: {
          'Content-Type': 'application/json',
          'qlik-web-integration-id': settings.webIntegrationID,
      },
  });
}

async function getJWT_nebula() {
  return await fetch('/wordpress/wp-json/qs/v1/token', {
      method: 'GET',
      mode: 'cors',
      headers: {
          'Content-Type': 'application/json',
      },
  })
};


async function login_nebula(jwt) {
  var authHeader = `Bearer ${jwt}`;
  return await fetch(`https://${settings.host}/login/jwt-session?qlik-web-integration-id=${settings.webIntegrationID}`, {
      method: 'POST',
      mode: 'cors',
      credentials: 'include',
      withCredentials: true,
      headers: {
          'Authorization': authHeader,
          'qlik-web-integration-id': settings.webIntegrationID,
          'Content-Type': 'application/json'
      },
      rejectunAuthorized: false,
  });
};

const getCsrfTokenInfo = async () => {
  const response = await fetch(`https://${settings.host}/api/v1/csrf-token`, {
    credentials: "include",
    headers: { "qlik-web-integration-id": settings.webIntegrationID },
  });
  return response.headers.get("qlik-csrf-token");
}

const renderNebula = async (id, appid, elem) => {  
  const csrfToken = await getCsrfTokenInfo();

  const url = `wss://${settings.host}/app/${appid}/?qlik-web-integration-id=${settings.webIntegrationID}&qlik-csrf-token=${csrfToken}`;
  const schema = await ( await fetch("https://unpkg.com/enigma.js/schemas/3.2.json") ).json();
  const session = window.enigma.create({ schema, url });
  const app = await (await session.open()).openDoc(appid);

  const nuked = await window.stardust.embed(app, {
    types: [
      {
        name: "kpi",
        load: () => Promise.resolve(window["sn-kpi"])
      },
      {
        name: "scatterplot",
        load: () => Promise.resolve(window["sn-scatter-plot"])
      },
      {
        name: "distributionplot",
        load: () => Promise.resolve(window["sn-distributionplot"])
      },
      {
        name: "barchart",
        load: () => Promise.resolve(window["sn-bar-chart"])
      },
      {
        name: "linechart",
        load: () => Promise.resolve(window["sn-line-chart"])
      },
      {
        name: "table",
        load: () => Promise.resolve(window["sn-table"])
      },
      {
        name: "piechart",
        load: () => Promise.resolve(window["sn-pie-chart"])
      },
      {
        name: "sankeychart",
        load: () => Promise.resolve(window["sn-sankey-chart"])
      },
      {
        name: "funnelchart",
        load: () => Promise.resolve(window["sn-funnel-chart"])
      },
      {
        name: "mekkochart",
        load: () => Promise.resolve(window["sn-mekko-chart"])
      },
      {
        name: "gridchart",
        load: () => Promise.resolve(window["sn-grid-chart"])
      },
      {
        name: "bulletchart",
        load: () => Promise.resolve(window["sn-bullet-chart"])
      },
      {
        name: "combochart",
        load: () => Promise.resolve(window["sn-combo-chart"])
      },
    ]
  });

  if(id === 'selections') {
    const selections = await nuked.selections();
    selections.mount(elem);
  } else {
    await nuked.render({
      element: elem,
      id,
    });
  }

}


function displayErrorMessage(message) {

  const objectsContainers = document.querySelectorAll('.objects-containers');

  const errorDiv = document.createElement('div');
  errorDiv.style.color = '#595959';
  errorDiv.style.fontStyle = 'italic';
  errorDiv.style.padding = '40px 10px 40px 10px';
  errorDiv.style.background = '#ffebeb';
  errorDiv.textContent = message;

  if(objectsContainers.length > 0) {
    objectsContainers.forEach(container => {
          container.innerHTML = ''; 
          container.appendChild(errorDiv);
      });
  }

}


const initNebula = async () => {
  try {
    const loggedIn = await isLoggedIn_nebula();  
    if(loggedIn.status != 200) {
      const tokenRes = await getJWT_nebula();
        if (tokenRes.status == 200) {
          const respJson = await tokenRes.json();
          var loginRes = await login_nebula(respJson);
          if (loginRes.status != 200) {
              console.error('Something went wrong while logging in.')
          } else {
              const loggedIn = await isLoggedIn_nebula();
              if (loggedIn.status != 200) {
                displayErrorMessage('Third-party cookie blocking is preventing this app from loading. Try another browser or adjust your browser settings.');
                return;
              }
          }
      } else {
        const error =  await tokenRes.json();
          console.error('Something went wrong: ', error.message);
      }
    }
  } catch (err) {
      throw new Error(err)
  }

  // render viz
  const objs = document.querySelectorAll('[qlik-saas-object-id]');
  [].forEach.call(objs, (obj) => {
    const id = obj.getAttribute('qlik-saas-object-id');
    const theAppId = obj.getAttribute('app-id') !== '' ? obj.getAttribute('app-id') : settings.appID;
    renderNebula(id, theAppId, obj);
  });  
};

initNebula();