= UW-MADISON WORDPRESS THEME =

* by University Communications and Marketing at University of Wisconsin-Madison (http://uc.wisc.edu/), based on Twenty Eleven, developed by the WordPress team (http://wordpress.org/)

== CHANGELOG ==

Version 1.0.13
- Fix bug in output_uw_wordpress_banner() function; call get_bloginfo instead of bloginfo()

Version 1.0.12
- Wrap all theme function in function_exists() tests so they can be overridden by child themes

Version 1.0.11
- Fixes deprecation notice about WP_Widget constructor method

Version 1.0.10
- Use a protocol-agnostic URL for Google Fonts

Version 1.0.9
- Add support for installing and updating the theme via Composer

Version 1.0.8
- Set .hentry to position: static in IE7 to work around ie7 z-index bug (as noted by Derek Tessman, International Studies)

Version 1.0.7
- See 'menu' class on main_menu container (as noted by Ian McNamara, DoIT AT)

Version 1.0.6
- Set #primary to float: none for @media < 760px
- Undo complex image/caption styling and :before pseudo selector rule
- Stylesheet path references now use stylesheet_directory

Version 1.0.5
- #siteTitle now uses Pontana Sans Google font instead of requireing a type image

Version 1.0.4
- Fix a responsive menu bug
- Adds a favicon

Version 1.0.3
- Use jQuery rather than DomDocument to add carets to menu items with children.
- Don't load unused theme options javascript.

Version 1.0.2 -- custom.css
- load custom.css in header.php for people to include their own CSS rules

Version 1.0.1 -- first released version available for download