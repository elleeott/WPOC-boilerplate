<?php function my_calendar_help() { ?>

<div class="wrap jd-my-calendar">
<h2><?php _e('How to use My Calendar','my-calendar'); ?></h2>
<div class="postbox-container" style="width: 70%">
<div class="metabox-holder">

<div class="ui-sortable meta-box-sortables">   
<div class="postbox">
	<h3><?php _e('My Calendar Help','my-calendar'); ?></h3>
	<div class="inside">
	<ul class="mc-settings">
		<li><a href="#mc-shortcodes"><?php _e('Shortcodes','my-calendar'); ?></a></li>
		<li><a href="#icons"><?php _e('Icons','my-calendar'); ?></a></li>
		<li><a href="#mc-styles"><?php _e('Styles','my-calendar'); ?></a></li>
		<li><a href="#templates"><?php _e('Templating','my-calendar'); ?></a></li>
		<li><a href="#get-support"><?php _e('Support Form','my-calendar'); ?></a></li>
		<li><a href="#notes"><?php _e('Helpful Information','my-calendar'); ?></a></li>
	</ul>
	</div>
</div>
</div>

<div class="ui-sortable meta-box-sortables" id="get-started">
<div class="postbox">
<h3 id="help"><?php _e('Getting Started','my-calendar'); ?></h3>
	<div class="inside">
	<p>
	<?php _e('Although the My Calendar plug-in is very complicated in terms of what can be customized, the basic usage is quite simple.','my-calendar'); ?>
	</p>
	<ul>
		<li> - <?php _e('Add the My Calendar shortcode (<code>[my_calendar]</code>) to a page.','my-calendar'); ?></li>
		<li> - <?php _e('Add events by clicking on the Add/Edit Events link in the admin sidebar or on "Add Events" in the admin toolbar.','my-calendar'); ?></li>
		<li> - <?php _e('Select your preferred stylesheet in the Styles Editor','my-calendar'); ?></li>
	</ul>
	<p>
	<?php _e('Read more of the basic help documentation on this page or purchase the My Calendar User\'s Guide to customize further -- but the above is all that you need to do to begin using the calendar.','my-calendar'); ?>
	</p>
	</div>
</div>
</div>

<div class="ui-sortable meta-box-sortables">
<div class="postbox" id="mc-shortcodes">
	<h3><?php _e('Shortcode Syntax','my-calendar'); ?></h3>
	<div class="inside">	
<p>
<?php _e('These shortcodes can be used in Posts, Pages, or in text widgets.','my-calendar'); ?>
</p>
<h4><?php _e('Main Calendar Shortcode (List or Grid, Weekly or Monthly view)','my-calendar'); ?></h4>
<p><code>[my_calendar]</code></p>
<p>
<?php _e('This basic shortcode will show the calendar on a post or page including all categories and the category key, in a traditional month-by-month format.','my-calendar'); ?>
</p>
<p><code>[my_calendar format="list" showkey="yes" shownav="yes" toggle="no" time="week"]</code></p>
<p>
<?php _e('The shortcode supports these attributes:','my-calendar'); ?>
</p>
	<ul>
	<li><code>category</code>: <?php _e('Names or IDs of categories included in this calendar, comma or pipe separated.','my-calendar'); ?></li>
	<li><code>format</code>: <?php _e('Either "list" or "mini" to show the list view or the mini calendar; exclude or any other value to show the main grid calendar.','my-calendar'); ?></li>
	<li><code>showkey</code>: <?php _e('Set as "no" to hide the category key.','my-calendar'); ?></li>
	<li><code>shownav</code>: <?php _e('Set as "no" to hide the month-by-month navigation.','my-calendar'); ?></li>
	<li><code>showjump</code>: <?php _e('Set to "no" or "yes" to override the default settings.','my-calendar'); ?></li>
	<li><code>toggle</code>: <?php _e('Set as "yes" to show a link to switch between list and grid formats.','my-calendar'); ?></li>
	<li><code>time</code>: <?php _e('Set to "week" to show a one week view or to "day" to show a single day view. Any other value will show a month view. (Day view shows as a list regardless of format setting.)','my-calendar'); ?></li>
	<li><code>ltype</code>: <?php _e('The type of location data to restrict by.','my-calendar'); ?></li>
	<li><code>lvalue</code>: <?php _e('The specific location information to filter to.','my-calendar'); ?></li>
	<li><code>author</code>: <?php _e('Author or comma-separated list of authors (usernames or IDs) to show events from.','my-calendar'); ?></li>
	<li><code>host</code>: <?php _e('Host or comma-separated list of hosts (WordPress usernames or IDs) to show events from.','my-calendar'); ?></li>
	</ul>
<p>
<em><?php _e('The main My Calendar short code can be generated from a button in your post and page editor. The mini calendar can also be accessed and configured as a widget.','my-calendar'); ?></em>
</p>
<h4><?php _e('Additional Calendar Views (Upcoming events, today\'s events)','my-calendar'); ?></h4>
<p><code>[my_calendar_upcoming before="3" after="3" type="event" fallback="No events coming up!" category="General" author="1" template="{title} {date}" order="asc" show_today="yes" skip="0"]</code></p>
<p>
	<?php _e('This shortcode displays the output of the Upcoming Events widget. The <code>before</code> and <code>after</code> attributes should be numbers; the <code>type</code> attribute can be either "event" or "days", and the <code>category</code> and <code>author</code> attributes work the same way as on the main calendar shortcode. Templates work using the template codes listed below. <code>fallback</code> provides text in case there are no events meeting your criteria. Order provides a sort order for the events list &ndash; either ascending (<code>asc</code>) or descending (<code>desc</code>). <code>show_today</code> is an indicator whether or not to include today\'s events in the list. <code>Skip</code> is the number of events to skip in the upcoming events.','my-calendar'); ?>
</p>
<p><code>[my_calendar_today category="" author="1" fallback="Nothing today!" template="{title} {date}"]</code></p>
<p>
	<?php _e('Predictably enough, this shortcode displays the output of the Today\'s Events widget, with four configurable attributes: category, author, template and fallback text.','my-calendar'); ?>
</p>
<p>
<em><?php _e('Both Upcoming Events and Today\'s Events can also be configured using widgets.','my-calendar'); ?></em>
</p>
<p><code>[my_calendar_event event="" template="&lt;h3&gt;{title}&lt;/h3&gt;{description}" list="&lt;li&gt;{date}, {time}&lt;/li&gt;" before="&lt;ul&gt;" after="&lt;/ul&gt;"]</code></p>
<p>
	<?php _e('Displays a single event and/or all dates for that event. If template is set to a blank value, will only display the list of occurrences. If the list attribute is set blank, will only show the event template','my-calendar'); ?>
</p>

<h4><?php _e('Supplement Features (Locations filter, Categories filter)','my-calendar'); ?></h4>

<p><code>[my_calendar_locations show="list" type="saved" datatype="name"]</code></p>
<p>
	<?php _e('This shortcode produces a list of event locations, either as a list of links or as a select dropdown form. The <code>show</code> attribute can either be <code>list</code> or <code>form</code>, <code>type</code> is either <code>saved</code> (to show items from your stored locations), or <code>custom</code> (to show the options configured in your user settings). <code>datatype</code> must be the type of data your limits are choosing from: <code>name</code> (business name), <code>city</code>, <code>state</code>, <code>country</code>, <code>zip</code> (postal code), or <code>region</code>.','my-calendar'); ?>
</p>
<p><code>[my_calendar_show_locations datatype="" template=""]</code></p>
<p>
	<?php _e('If you want to display a list of locations in your database, use this shortcode. The <code>datatype</code> is the type of data displayed; all lists will include a link to the map of that location. In addition to basic location information as in the above shortcode, you can also use "hcard" to display all available location information.','my-calendar'); ?>
	<?php _e('Use the <code>template</code> attribute to show your own customized set of data. The data will be sorted by the <code>datatype</code> value.','my-calendar'); ?>
	</p>
<p><code>[my_calendar_categories show="list"]</code></p>
<p>
	<?php _e('This shortcode produces a list of event categories, either as a list of links or as a select dropdown form. The <code>show</code> attribute can either be <code>list</code> or <code>form</code>.','my-calendar'); ?>
</p>	
</div>
</div>


<div class="ui-sortable meta-box-sortables" id="icons">
	<div class="postbox">
	<h3><?php _e('Category Icons','my-calendar'); ?></h3>
		<div class="inside">	
		<p>
		<?php _e('My Calendar is designed to manage multiple calendars. The basis for these calendars are categories; you can easily setup a calendar page which includes all categories, or you can dedicate separate pages to calendars in each category. For an example, this might be useful for you in managing the tour calendars for multiple bands; event calendars for a variety of locations, etc.','my-calendar'); ?>
		</p>
		<p>
		<?php _e('The pre-installed category icons may not be especially useful for your needs or design. I\'m assuming that you\'re going to upload your own icons -- all you need to do is upload them to the plugin\'s icons folder, and they\'ll be available for immediate use, or place them in a folder at "my-calendar-custom" to avoid having them overwritten by upgrades.','my-calendar'); ?> <?php _e('Your icons folder is:','my-calendar'); ?> <code><?php echo plugin_dir_path( __FILE__ ).'icons/'; ?></code> <?php _e('You can alternately place icons in:','my-calendar'); ?> <code><?php echo str_replace( '/my-calendar','',plugin_dir_path( __FILE__ ) ).'my-calendar-custom/'; ?></code>
		</p>
		</div>
	</div>
</div>


<div class="ui-sortable meta-box-sortables" id="mc-styles">
	<div class="postbox">
	<h3><?php _e('Custom Styles','my-calendar'); ?></h3>
		<div class="inside">	
		<p>
		<?php _e('My Calendar comes with four basic stylesheets. My Calendar will retain changes to these basic stylesheets on upgrade, but if you want to add an entirely new stylesheet, you may wish to store it in the My Calendar custom styles directory.','my-calendar'); ?>
		</p>
		<ul>
		<li><?php _e('Your stylesheet directory is','my-calendar'); ?>: <code><?php echo plugin_dir_path( __FILE__ ).'styles/'; ?></code></li>
		<li><?php _e('Your custom stylesheets directory is','my-calendar'); ?>: <code><?php echo str_replace( '/my-calendar','',plugin_dir_path( __FILE__ ) ).'my-calendar-custom/styles/'; ?></code></li>
		</ul>
		<p>
		<?php _e('You can also add custom styles to your theme directory to provide print styles, mobile styles, and tablet styles. <code>mc-print.css</code>, <code>mc-mobile.css</code>, and <code>mc-tablet.css</code>.','my-calendar'); ?>
		</p>
		</div>
	</div>
</div>

<div class="ui-sortable meta-box-sortables" id="templates">
<div class="postbox">
<h3 id="template"><?php _e('Widget Templating','my-calendar'); ?></h3>
	<div class="inside">
		<p>
		<?php _e('All template tags support two attributes: before="value" and after="value". The values of the attributes will be placed before and after the output value. These attribute values <strong>must</strong> be wrapped in double quotes.','my-calendar'); ?>
		</p>
		<p>
		<?php _e('Date/Time template tags support the "format" attribute: format="M, Y", where the value is a PHP formatted date string. Only <code>dtstart</code> and <code>dtend</code> include the full date/time information for formatting.','my-calendar'); ?>
		</p>
		<p>
		<strong><?php _e('Example:','my-calendar'); ?></strong> <code>{title before="&lt;h3&gt;" after="&lt;/h3&gt;"}</code>
		</p>
		<h4><?php _e('Event Template Tags','my-calendar'); ?></h4>
		<dl>
		<dt><code>{title}</code></dt>
		<dd><?php _e('Displays the title of the event.','my-calendar'); ?></dd>

		<dt><code>{link_title}</code></dt>
		<dd><?php _e('Displays title of the event as a link if a URL is present, or the title alone if no URL is available.','my-calendar'); ?></dd>

		<dt><code>{time}</code></dt>
		<dd><?php _e('Displays the start time for the event.','my-calendar'); ?></dd>

		<dt><code>{usertime}</code></dt>
		<dd><?php _e('Displays the start time for the event adjusted to the current user\'s time zone settings. Returns <code>{time}</code> if user settings are disabled or if the user has not selected a preferred time zone.','my-calendar'); ?></dd>

		<dt><code>{endusertime}</code></dt>
		<dd><?php _e('Displays the end time for the event adjusted to the current user\'s time zone settings. Returns <code>{endtime}</code> if user settings are disabled or if the user has not selected a preferred time zone.','my-calendar'); ?></dd>

		<dt><code>{date}</code></dt>
		<dd><?php _e('Displays the date on which the event begins.','my-calendar'); ?></dd>

		<dt><code>{began}</code></dt>
		<dd><?php _e('Displays the date on which the series of events began (for recurring events).','my-calendar'); ?></dd>

		<dt><code>{enddate}</code></dt>
		<dd><?php _e('Displays the date on which the event ends.','my-calendar'); ?></dd>

		<dt><code>{endtime}</code></dt>
		<dd><?php _e('Displays the time at which the event ends.','my-calendar'); ?></dd>

		<dt><code>{daterange}</code></dt>
		<dd><?php _e('Displays the beginning date to the end date for events. Does not show end date if same as start date.','my-calendar'); ?></dd>

		<dt><code>{timerange}</code></dt>
		<dd><?php _e('Displays the beginning and end times for events. Does not show end time if same as start or if marked as hidden.','my-calendar'); ?></dd>	
		
		<dt><code>{dtstart}</code></dt>
		<dd><?php _e('Timestamp for beginning of event.','my-calendar'); ?></dd>
		
		<dt><code>{dtend}</code></dt>
		<dd><?php _e('Timestamp for end of event.','my-calendar'); ?></dd>
		
		<dt><code>{multidate}</code></dt>
		<dd><?php _e('For multi-day events displays an unordered list of dates and times for events in this group. Otherwise, beginning date/time.','my-calendar'); ?></dd>

		<dt><code>{author}</code></dt>
		<dd><?php _e('Displays the WordPress author who posted the event.','my-calendar'); ?></dd>

		<dt><code>{host}</code></dt>
		<dd><?php _e('Displays the name of the person assigned as host for the event.','my-calendar'); ?></dd>

		<dt><code>{host_email}</code></dt>
		<dd><?php _e('Displays the email address of the person assigned as host for the event.','my-calendar'); ?></dd>

		<dt><code>{shortdesc}</code></dt>
		<dd><?php _e('Displays the short version of the event description.','my-calendar'); ?></dd>

		<dt><code>{shortdesc_raw}</code></dt>
		<dd><?php _e('Displays short description without converting paragraphs.','my-calendar'); ?></dd>

		<dt><code>{shortdesc_stripped}</code></dt>
		<dd><?php _e('Displays short description with any HTML stripped out.','my-calendar'); ?></dd>

		<dt><code>{description}</code></dt>
		<dd><?php _e('Displays the description of the event.','my-calendar'); ?></dd>

		<dt><code>{description_raw}</code></dt>
		<dd><?php _e('Displays description without converting paragraphs.','my-calendar'); ?></dd>

		<dt><code>{description_stripped}</code></dt>
		<dd><?php _e('Displays description with any HTML stripped out.','my-calendar'); ?></dd>

		<dt><code>{image}</code></dt>
		<dd><?php _e('Image associated with the event.','my-calendar'); ?></dd>

		<dt><code>{link}</code></dt>
		<dd><?php _e('Displays the URL provided for the event.','my-calendar'); ?></dd>

		<dt><code>{ical_link}</code></dt>
		<dd><?php _e('Produces the URL to download an iCal formatted record for the event.','my-calendar'); ?></dd>

		<dt><code>{ical_html}</code></dt>
		<dd><?php _e('Produces a hyperlink to download an iCal formatted record for the event.','my-calendar'); ?></dd>

		<dt><code>{recurs}</code></dt>
		<dd><?php _e('Shows the recurrence status of the event. (Daily, Weekly, etc.)','my-calendar'); ?></dd>

		<dt><code>{repeats}</code></dt>
		<dd><?php _e('Shows the number of repetitions of the event.','my-calendar'); ?></dd>

		<dt><code>{details}</code></dt>
		<dd><?php _e('Provides a link to an auto-generated page containing all information on the given event.','my-calendar'); ?> <strong><?php _e('Requires that the site URL has been provided on the Settings page','my-calendar'); ?></strong>

		<dt><code>{event_open}</code></dt>
		<dd><?php _e('Displays text indicating whether registration for the event is currently open or closed; displays nothing if that choice is selected in the event.','my-calendar'); ?></dd>

		<dt><code>{event_status}</code></dt>
		<dd><?php _e('Displays the current status of the event: either "Published" or "Reserved" - primary used in email templates.','my-calendar'); ?></dd>
		</dl>
		<h4><?php _e('Location Template Tags','my-calendar'); ?></h4>

		<dl>
		<dt><code>{location}</code></dt>
		<dd><?php _e('Displays the name of the location of the event.','my-calendar'); ?></dd>

		<dt><code>{street}</code></dt>
		<dd><?php _e('Displays the first line of the site address.','my-calendar'); ?></dd>

		<dt><code>{street2}</code></dt>
		<dd><?php _e('Displays the second line of the site address.','my-calendar'); ?></dd>

		<dt><code>{city}</code></dt>
		<dd><?php _e('Displays the city for the location.','my-calendar'); ?></dd>

		<dt><code>{state}</code></dt>
		<dd><?php _e('Displays the state for the location.','my-calendar'); ?></dd>

		<dt><code>{postcode}</code></dt>
		<dd><?php _e('Displays the postcode for the location.','my-calendar'); ?></dd>

		<dt><code>{region}</code></dt>
		<dd><?php _e('Shows the custom region entered for the location.','my-calendar'); ?></dd>

		<dt><code>{country}</code></dt>
		<dd><?php _e('Displays the country for the event location.','my-calendar'); ?></dd>

		<dt><code>{sitelink}</code></dt>
		<dd><?php _e('Output the URL for the location link.','my-calendar'); ?></dd>

		<dt><code>{phone}</code></dt>
		<dd><?php _e('Output the stored phone number for the location.','my-calendar'); ?></dd>
		
		<dt><code>{sitelink_html}</code></dt>
		<dd><?php _e('Output a hyperlink to the location\'s listed link with default link text.','my-calendar'); ?></dd>

		<dt><code>{hcard}</code></dt>
		<dd><?php _e('Displays the event address in <a href="http://microformats.org/wiki/hcard">hcard</a> format.','my-calendar'); ?></dd>

		<dt><code>{link_map}</code></dt>
		<dd><?php _e('Displays a link to a Google Map of the event, if sufficient address information is available. If not, will be empty.','my-calendar'); ?></dd>
		</dl>
		<h4><?php _e('Category Template Tags','my-calendar'); ?></h4>

		<dl>
		<dt><code>{category}</code></dt>
		<dd><?php _e('Displays the name of the category the event is in.','my-calendar'); ?></dd>

		<dt><code>{icon}</code></dt>
		<dd><?php _e('Produces the address of the current event\'s category icon.','my-calendar'); ?></dd>

		<dt><code>{icon_html}</code></dt>
		<dd><?php _e('Produces the HTML for the current event\'s category icon.','my-calendar'); ?></dd>

		<dt><code>{color}</code></dt>
		<dd><?php _e('Produces the hex code for the current event\'s category color.','my-calendar'); ?></dd>

		<dt><code>{category_id}</code></dt>
		<dd><?php _e('Displays the ID for
		 the category the event is in.','my-calendar'); ?></dd>
		</dl>

		<h4><?php _e('Special use Template Tags','my-calendar'); ?></h4>

		<dl>
		<dt><code>{dateid}</code></dt>
		<dd><?php _e('A unique ID for the current instance of an event.','my-calendar'); ?></dd>

		<dt><code>{id}</code></dt>
		<dd><?php _e('The ID for the event record associated with the current instance of an event.','my-calendar'); ?></dd>

		</dl>

		</div>
	</div>
</div>

<div class="ui-sortable meta-box-sortables" id="get-support">
<div class="postbox">
<h3 id="support"><?php _e('Get Plug-in Support','my-calendar'); ?></h3>
	<div class="inside">
	<?php if ( current_user_can('administrator') ) { ?>
		<?php jcd_get_support_form(); ?>
	<?php } else { ?>
		<?php _e('My Calendar support requests can only be sent by administrators.','my-calendar'); ?>
	<?php } ?>	
	</div>
</div>
		
<div class="ui-sortable meta-box-sortables" id="notes">
<div class="postbox">
<h3 id="help"><?php _e('Helpful Information','my-calendar'); ?></h3>
	<div class="inside">
	<p>
	<?php _e('<strong>Uninstalling the plugin</strong>: Although the WordPress standard and expectation is for plug-ins to delete any custom database tables when they\'re uninstalled, My Calendar <em>does not do this</em>. This was a conscious decision on my part -- the data stored in your My Calendar tables is yours; with the sole exception of the "General" category, you added every piece of it yourself. As such, I feel it would be a major disservice to you to delete this information if you uninstall the plug-in. As a result, if you wish to get rid of the plug-in completely, you\'ll need to remove those tables yourself. All your My Calendar settings will be deleted, however.','my-calendar'); ?>
	</p>
	<p>
	<?php _e('<strong>Donations</strong>: I appreciate anything you can give. $2 may not seem like much, but it can really add up when thousands of people are using the software. Please note that I am not a non-profit organization, and your gifts are not tax deductible. Thank you!','my-calendar'); ?>
	</p>
	</div>
</div>
</div>

</div>
</div>
</div>
</div>
	<?php jd_show_support_box(); ?>

</div>
<?php } ?>