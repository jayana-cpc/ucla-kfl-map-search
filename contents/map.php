<?php
// Map page uses its own asset set; clear inherited includes to avoid conflicts
$cms->css = array();
$cms->js = array();

// Restore site layout CSS (absolute paths to avoid base tag issues)
$cms->css[] = "/css/layout.css";
$cms->css[] = "/css/menu.css";

// CSS for widgets and map UI
$cms->css[] = "/map_search/bower_components/jqueryui/themes/base/jquery-ui.css";
$cms->css[] = "/map_search/bower_components/jquery-ui-multiselect/jquery.multiselect.css";
$cms->css[] = "/map_search/bower_components/jQRangeSlider-5.5.0/css/classic.min.css";
$cms->css[] = "/map_search/main.css";

// JS (ordered: core -> UI -> plugins -> app)
$cms->js[] = "/map_search/bower_components/jquery/jquery.min.js";          // jQuery 2.0.3
$cms->js[] = "/map_search/bower_components/jqueryui/jquery-ui.min.js";     // jQuery UI 1.10.x browser build
$cms->js[] = "/map_search/bower_components/jquery-ui-multiselect/jquery.multiselect.js";
// Local jQRangeSlider plugins (no CDN)
$cms->js[] = "/map_search/bower_components/jQRangeSlider-5.5.0/jQRangeSliderMouseTouch.min.js";
$cms->js[] = "/map_search/bower_components/jQRangeSlider-5.5.0/jQRangeSliderDraggable.min.js";
$cms->js[] = "/map_search/bower_components/jQRangeSlider-5.5.0/jQRangeSliderHandle.min.js";
$cms->js[] = "/map_search/bower_components/jQRangeSlider-5.5.0/jQRangeSliderBar.min.js";
$cms->js[] = "/map_search/bower_components/jQRangeSlider-5.5.0/jQRangeSliderLabel.min.js";
$cms->js[] = "/map_search/bower_components/jQRangeSlider-5.5.0/jQRangeSlider.min.js";
$cms->js[] = "/map_search/bower_components/jQRangeSlider-5.5.0/jQRuler.min.js";
$cms->js[] = "/map_search/bower_components/jquery-collapsible/jquery.collapsible.js";
$cms->js[] = "/map_search/bower_components/underscore/underscore-min.js";
$cms->js[] = "/map_search/bower_components/backbone/backbone-min.js";
$cms->js[] = "/map_search/bower_components/mustache/mustache.js";
$cms->js[] = "/map_search/bower_components/openlayers/OpenLayers.js";
$cms->js[] = "/map_search/bower_components/vc/src/VC.js";
$cms->js[] = "/map_search/bower_components/vc/src/SearchResultsLayer.js";

// App scripts
$cms->js[] = "/map_search/AppRoot.js";
$cms->js[] = "/map_search/input_form/Form.js";
$cms->js[] = "/map_search/input_form/Query.js";
$cms->js[] = "/map_search/map/ContextMonitor.js";
$cms->js[] = "/map_search/map/Map.js";
$cms->js[] = "/map_search/map/ResultLayer.js";
$cms->js[] = "/map_search/result_list/Result.js";
$cms->js[] = "/map_search/result_list/List.js";
$cms->js[] = "/map_search/result_list/Collection.js";
$cms->js[] = "/map_search/result_list/SummaryItem.js";

$dbConn = get_connection();

$query = "SELECT MIN(context_date) AS min_date, MAX(context_date) AS max_date "
            . " FROM context WHERE context_date IS NOT NULL AND context_date >= '1900-01-01'";
$rsc = mysqli_query($dbConn, $query) or die("SQL Error: ". mysqli_error());
$result = mysqli_fetch_assoc($rsc);

mysqli_close($dbConn);
?>
<script type="text/javascript">
    var CONSTANTS = {
        minDate: "<?php echo $result['min_date']; ?>",
        maxDate: "<?php echo $result['max_date']; ?>"
    };
</script>
<script type="text/template" id="kfa-input-form-template">
    <form action="/map" onsubmit="return false;" class="search-form">
        <div class="fieldset-header collapsible">Collector<span></span></div>
        <div class="fieldset-body">
            <label for="collector-gender">Gender:</label>
            <select name="collector-gender" class="collector-gender">
                <option value="m">Male</option>
                <option value="f">Female</option>
            </select>
            <label for="collector-occupation">Occupation:</label>
            <input type="text" name="collector-occupation" class="collector-occupation" />
            <label for="collector-age">Age:</label>
            <!--<input type="text" name="collector-age" class="collector-age" />-->
            <br/>
            <div class="collector-age"></div>
            <label for="collector-language">Languages Spoken:</label>
            <select name="collector-language" class="collector-language">
                <option value="english">English</option>
                <option value="korean">Korean</option>
                <option value="japanese">Japanese</option>
                <option value="chinese">Chinese</option>
                <option value="spanish">Spanish</option>
                <option value="other">Other</option>
            </select>
            <div class="background-filler"></div>
        </div>
        <div class="fieldset-header collapsible">Consultant<span></span></div>
        <div class="fieldset-body">
            <label for="consultant-gender">Gender:</label>
            <select name="consultant-gender" class="consultant-gender">
                <option value="m">Male</option>
                <option value="f">Female</option>
            </select>
            <label for="consultant-occupation">Occupation:</label>
            <input type="text" name="consultant-occupation" class="consultant-occupation" />
            <label for="consultant-age">Age:</label>
            <!--<input type="text" name="consultant-age" class="consultant-age" />-->
            <div class="consultant-age"></div>
            <label for="consultant-language">Languages Spoken:</label>
            <select name="consultant-language" class="consultant-language">
                <option value="english">English</option>
                <option value="korean">Korean</option>
                <option value="japanese">Japanese</option>
                <option value="chinese">Chinese</option>
                <option value="spanish">Spanish</option>
                <option value="other">Other</option>
            </select>
            <div class="background-filler"></div>
        </div>
        <div class="fieldset-header collapsible">Context<span></span></div>
        <div class="fieldset-body">
            <label for="context-name">Name:</label>
            <input type="text" name="context-name" class="context-name" />
            <label for="context-event-type">Event Type:</label>
            <select name="context-event-type" title="context-event-type" class="context-event-type">
                <optgroup label="Celebration">
                    <option value="Birthday">Birthday</option>
                    <option value="Seasonal/Holiday">Holiday</option>
                    <option value="Wedding">Wedding</option>
                    <option value="Funeral">Funeral</option>
                    <option value="Graduation">Graduation</option>
                    <option value="Other Celebration">Other</option>
                <optgroup>
                <optgroup label="Performance">
                    <option value="Oral History">Oral History</option>
                    <option value="Storytelling">Storytelling</option>
                    <option value="Folk Speech/Gesture">Folk Speech/Gesture</option>
                    <option value="Drama">Drama</option>
                    <option value="Song">Song</option>
                    <option value="Dance">Dance</option>
                    <option value="Other Performance">Other</option>
                </optgroup>
                <optgroup label="Material Culture">
                    <option value="Architecture">Architecture</option>
                    <option value="Costume/Clothing">Costume/Clothing</option>
                    <option value="Body Art or Adornment">Body Art or Adornment</option>
                    <option value="Folk Art or Craft">Folk Art or Craft</option>
                    <option value="Cooking">Cooking</option>
                    <option value="Other Material Culture">Other</option>
                </optgroup>
                <optgroup label="Other">
                    <option value="General Observation">General Observation</option>
                </optgroup>
            </select>
            <label for="context-time-of-day">Time of Day:</label>
            <select name="context-time-of-day" class="context-time-of-day">
                <option value="morning">Morning</option>
                <option value="afternoon">Afternoon</option>
                <option value="evening">Evening</option>
                <option value="night">Night</option>
            </select>
            <label for="context-date-from">Date From:</label>
            <input type="text" name="context-date-from" class="context-date-from" />
            <label for="context-date-to">Date To:</label>
            <input type="text" name="context-date-to" class="context-date-to" />
            <label for="collection-weather">Weather:</label>
            <select name="collection-weather" class="collection-weather">
                <option value="sunny">Sunny</option>
                <option value="overcast">Overcast</option>
                <option value="raining">Raining</option>
                <option value="snowing">Snowing</option>
            </select>
            <label for="collection-language">Language:</label>
            <select name="collection-language" class="collection-language">
                <option value="english">English</option>
                <option value="korean">Korean</option>
                <option value="japanese">Japanese</option>
                <option value="chinese">Chinese</option>
                <option value="spanish">Spanish</option>
                <option value="other">Other</option>
            </select>
            <label for="collection-place-type">Place Type:</label>
            <select name="collection-place-type" class="collection-place-type">
                <option value="business">Business</option>
                <option value="residence">Residence</option>
                <option value="public">Public Place</option>
            </select>
            <label for="collection-others-present">Number of Others Present:</label>
            <select name="collection-others-present" class="collection-others-present">
                <option value="1">1</option>
                <option value="2-5">2-5</option>
                <option value="5+">5 or more</option>
            </select>
            <label for="collection-method">Collection Method:</label>
            <select name="collection-method" class="collection-method">
                <option value="tape">Tape Recorder</option>
                <option value="video">Video Camera</option>
                <option value="camera">Still Camera</option>
                <option value="notes">Notes</option>
            </select>
            <label for="collection-description">Description:</label>
            <input type="text" name="collection-description" class="collection-description" />
            <div class="background-filler"></div>
        </div>
        <div class="fieldset-header collapsible last">Data<span></span></div>
        <div class="fieldset-body">
            <label for="project-title">Project Title:</label>
            <input type="text" name="project-title" class="project-title" />
            <label for="media">Media:</label>
            <select title="media" class="media">
                <option value="fieldnotes">Field notes</option>
                <option value="images">Images</option>
                <option value="video">Video</option>
                <option value="audio">Audio</option>
            </select>
            <label for="description">Description (full text search):</label>
            <input type="text" name="description" class="description" />
            <div class="background-filler"></div>
        </div>
        <div class="search-button-wrapper">
            <input type="submit" name="submit" value="Search" class="search" />
        </div>
    </form>
</script>

<script type="text/template" id="kfa-summary-item-template">
    <div class="summary-item">
        <ul class="result-title">
            <li><a href="{{url}}" target="_blank">{{projectTitle}}</a></li>
            <li><a href="{{url}}" target="_blank">{{date}}</a></li>
            <li><a href="{{url}}" target="_blank">{{city}}</a></li>
        </ul>
        <div class="description">{{description}}</div>
    </div>
</script>


<div id="search-wrapper"></div>
<div id="map-wrapper">
    <div id="map"></div>
</div>
<div id="result-list-wrapper">
    <h2>Results</h2>
    <span class="prev-page">&lt;</span>
    <span class="next-page">&gt;</span>
    <div class="result-list">
    </div>
</div>
<script>
$(function () {
    var root = new KFA.AppRoot();
    root.render();
});
</script>
