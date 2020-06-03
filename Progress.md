May 31st, 2020

The goal of this project was to document Generation 1 orbs with their information and relative values for testing purposes. This application also includes the ability to edit the relative value of an orb in the database to test if the connection to the database is working correctly. In my view, the problems that currently exist with this application exist due to the nature of the infrastructure of the orbs. Many of the relative values are not shown in the database so most of the values in the application show up as "N/A". Additionally, while the forms to change the values work, the changed value seems to be reverted when the orb database refreshes after a few seconds. The attempt to get around this was to disable the orbs by setting disable value to 1 in the database, but although not tested thoroughly, that does not seem to be working.

-Oliver
