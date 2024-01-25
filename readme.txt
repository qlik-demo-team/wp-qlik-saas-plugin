=== Plugin Name ===
Contributors: qlik1oim
Tags: qlik, saas,
Requires at least: 5.0
Tested up to: 5.8.1
Stable tag: 1.0.7
License: MIT
License URI: https://opensource.org/licenses/MIT

Allows you to create a mashup by embedding Qlik Saas sheets inside WordPress pages.

== Description ==

This is a simple plugin to connect to your Qlik Saas tenant and create a mashup by getting the sheet with a shortcode inside a page within the admin panel

== How to Configure ==

Before the plugin can be used, it must be configured as follows:
1. Login to your WordPress Admin Portal.
1. On the left hand navigation panel, select "Qlik Saas". 
1. Enter the relevant Qlik Saas server URL, WebIntegrationID, App ID, Private Key and KeyID to connect to your Qlik Saas tenant.

== Prepare Installation in Qlik Saas ==
- Create Signed Tokens for JWT Authorization<br>
https://qlik.dev/authenticate/jwt/create-signed-tokens-for-jwt-authorization/#create-a-public--private-key-pair-for-signing-jwts
- Configure JWT identity provider
https://qlik.dev/authenticate/jwt/create-signed-tokens-for-jwt-authorization/#configure-jwt-identity-provider
- Add Public key to the config
https://qlik.dev/authenticate/jwt/create-signed-tokens-for-jwt-authorization/#add-the-public-key-to-the-configuration
- Input issuer <br>
https://qlik.dev/authenticate/jwt/create-signed-tokens-for-jwt-authorization/#input-issuer-and-key-id-values

Since the Plugin uses JWT auth, you need to place your apps in a managed space and have the group anon-view added as a member of the space to view.

== Installation ==
 - Add `Host` of Qlik Saas as `<tenant>.<region>.qlikcloud.com`
 - Add your WebIntegrationID <br>
https://help.qlik.com/en-US/cloud-services/Subsystems/Hub/Content/Sense_Hub/Admin/mc-adminster-web-integrations.htm
 - Add you AppID
 - Add your Private key from first step (Create a public / private key pair for signing JWTs) <br>
https://qlik.dev/authenticate/jwt/create-signed-tokens-for-jwt-authorization/#create-a-public--private-key-pair-for-signing-jwts
 - Add the Key ID created from previous step <br>
https://qlik.dev/authenticate/jwt/create-signed-tokens-for-jwt-authorization/#input-issuer-and-key-id-values

== How to Use ==

The plugin utilizes a WordPress shortcode to insert Qlik Saas objects into a page. There are currently 3 shortcode availables to embed Qlik content.

=== Qlik Sense Sheet ===

This shortcode allows you to iframe a sheet. The shortcode syntax is as follows:

`[qlik-saas-single-sheet id="1ff88551-9c4d-41e0-b790-37f4c11d3df8" appid="bc579b15-afae-4721-aef5-1b4535ab5e9b" height="400px" width="500px"]`

Parameters are as follows:
* id="" (Required): Is the sheet id (open your app and sheet in Qlik Cloud and find it from the URL "sense/app/bc579b15-afae-4721-aef5-1b4535ab5e9b/sheet/**e63a8a7a-ffff-411e-b80d-d2175fefafd7**/state/edit/").
* height="" (Required): The height of the embed in pixels.
* width="" (optional): The width of the embed in pixels or percentage.
* appid="" (optional): The variable qs_appid is added to store the value from the custom field appid. The custom field is used to be able to use a separate app for each sheet rather than the apps defined in the plugin config.

=== Multiple Qlik Sense Sheets ===

This shortcode allows you to iframe multiple sheets from the same app as tabs. The shortcode syntax is as follows:

`[qlik-saas-multi-sheet ids="1ff88551-9c4d-4c02-b790-37f4c11d3df8,354342d8-2a6a-4596-bc6e-d23eb85f211c,03abcb4b-f9a1-4a10-81cd-2a307d90a68f" titles="Dashboard,Geo Analysis,Comparative Analysis" height="1000px" width="100%" ]`

Parameters are as follows:
* ids="" (Required): A comma seperated list of sheet ids. 
* titles="" (Required): A comma seperated list of tab titles in the same order as the sheet ids.
* height="" (Required): The height of the embed in pixels.
* width="" (Optional): The width of the embed in pixels or pertentage.

=== Qlik Sense Object ===

This shortcode allows you to embed objects for mashup. The shortcode syntax is as follows:
- Selections toolbar:
`[qlik_saas_object id="selections" height="50px"]`

- Charts:
`[qlik_saas_object id="CSxZqS" height="400px"]`
*[full list of supported visualizations]https://qlik.dev/embed/foundational-knowledge/visualizations*

Parameters are as follows:
* id="": Is the object id (right click a chart in your qlik app, click share then embed and look for Object ID under the preview). 
         enter "selections" as the id to get current selections toolbar
* height="": The height of the visualization in pixels.

== Installation ==

1. Click on Clone or Download 
2. Click on Download ZIP
3. In your Wordpress admin panel, click on "Plugins", "Add New Plugin", then "Upload Plugin"
4. Choose the previously downloaded zip file and click "Install Now".
5. WordPress will install the plugin for you. Once complete, click on "Activate Plugin". Click the "Activate" button to complete the installation.
6. The plugin is now installed and ready to Configure.

== Frequently Asked Questions ==

== Screenshots ==

1. Admin Settings Page
2. Shortcode with the sheet id
3. Preview iframed sheet
4. Shortcodes for mashup with object ids
5. Helpdesk sheet 1 with object ids
5. Helpdesk sheet 2 with object ids

== Changelog ==

= 1.0.7 =
* Add object ids for mashups

= 1.0.6 =
* Support multiple shortcodes / sheet iframes in one page

= 1.0.5 =
* Init with iframing a sheet
