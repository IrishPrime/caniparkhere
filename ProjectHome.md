# Problem #
Different parking passes and permits allow the use of different parking spots that have different rules at different times of day and change further during particular campus events.
# Solution #
Keeping track of the different parking areas, the location of a vehicle, and the permit associated with that vehicle could quickly answer the question of whether an individual can park in a given area. Can I Park Here aims to be a system to address one of the most frustrating aspects of visiting campus: parking.
# General (Framework) #
  * Define lots (and sub-lots?).
  * Do something clever to make it work on any campus.
# Website (PHP & MySQL) #
  * Associate optional account with permit type.
  * Mouse over a permit type to highlight approved lots.
  * Mouse over a lot to view approved permit types.
# Handheld (Google Android) #
  * Login to account or select permit type.
  * Determine which parking area a user is in based on their current position.
  * Compare permit type with the parking area.
  * Alert user if in danger of the wrath of Parking Services.