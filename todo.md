TO DO LIST
==========

- Leave the IndigoPlateau class in its own file (fuck PHP)

- Refactor the admin panel's code: urrghhhhhhhh

- Use JavaScript to create the tables from a player's JSON file
-- [indigo_plateau_ranking] creates the HTML to be manipulated by JS
-- After page load, fetches the JSON and processes it
-- Maybe create a json.php file and call it from JS
-- Dead simple solution: create the JSON and add it as a variable inside a <script> tag
-- How the fuck to load JS/CSS from the plugin?

http://www.sutanaryan.com/wordpress/how-to-include-jquery-and-css-in-wordpress-the-wordpress-way/
http://planetozh.com/blog/2008/04/how-to-load-javascript-with-your-wordpress-plugin/

- Use jQuery's UI datepicker, autocomplete and accordion:
-- datepicker for easier input of event dates
-- autocomplete for players's names and events
-- accordion on the ranking table to show each player's history.

- On entries creation
-- Group entries as a JSON before sending them to the server
-- So it's possible to add multiple entries at once

- Use a checkbox do remove old entries. Where should it appear?
-- Show the checkbox on the entries inside the accordion
-- So it's possible to remove multiple entries
