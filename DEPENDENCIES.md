## Runtime targets
- PHP: start with 7.4 (stable for this 2018-era code). If we later confirm compatibility with PHP 8.x, we can bump; expect to enable extensions: `mysqli`, `pdo_mysql`, `mbstring`, `json`, `zip`, `xml`.
- Database: MySQL 5.7+ or MariaDB 10.3+ with GIS functions (`MBRContains`, `GeomFromText`) enabled.
- Web server: Apache with mod_php or PHP-FPM; document root should be the repo root so `index.php` resolves assets under `map_search/`.

## Front-end asset vendoring (preferred: commit assets)
The repo does not ship `map_search/bower_components/` or compiled CSS. Use HTTPS to fetch prebuilt artifacts, then commit them:

1) Create `map_search/bower_components/` if absent.  
2) Download assets (no SSH needed):
   - Prefer running `bash map_search/scripts/fetch_assets.sh` which pulls all public assets over HTTPS and stubs VC if needed.
   - Manual equivalents (if the script fails):
     - jQuery 2.0.3 → `map_search/bower_components/jquery/jquery.min.js`
     - jQuery UI 1.10.5 → CDN files into `map_search/bower_components/jqueryui` (script creates a `kfl-theme` folder using the smoothness theme).
     - jquery-ui-multiselect 1.13 → `map_search/bower_components/jquery-ui-multiselect/`
     - Collapsible → `map_search/bower_components/jquery-collapsible/`
     - jQRangeSlider 5.5.0 → `map_search/bower_components/jQRangeSlider-5.5.0/`
     - OpenLayers 2.13.1 → `map_search/bower_components/openlayers/OpenLayers.js`
     - Underscore 1.5.2, Backbone 1.1.0, Mustache 0.7.3 → respective folders under `map_search/bower_components/`
     - VC: script creates a minimal stub in `map_search/bower_components/vc/src/VC.js` and `SearchResultsLayer.js`. Replace with real library later if desired.
     - QuB PHP is already present under `map_search/search/QuB/`; upstream fetch is optional.

3) Compile SCSS → CSS once and commit:
   - Install dart-sass: `npm install --global sass`
   - Run: `sass map_search/main.scss map_search/main.css`

After running the above, commit `map_search/bower_components/` and `map_search/main.css`.

## Optional Box (file storage)
- The app can offload uploaded files to Box using the Box JWT PHP SDK. That code is optional for a demo; if needed later, place the SDK in `box-jwt-php/` and configure `box.config.php` plus key files (`*.pem`).

## Auth note
- Production expects Shibboleth to set a `kfl` cookie. For Docker/local, we’ll add a DEV_LOGIN toggle later to bypass external SSO.

## Local build helper (future)
- A helper script will be added under `map_search/scripts/` to download the above assets over HTTPS; network access is required when running it.
