# Football Manager Calls 

## Description
This project is a simple and fast developed system to manage football calls.

## Why?
It's hard ask person by person every week if is available for the next match, so, I've developed this basic tool to do this work for me.

## How it works?
+ Every season, the admin user(s) creates a new season and the planned matches for these season.
+ Some days before any match, the user gets an email with the information for next match and the link to set availability.
+ The admin user(s) can close the call and the players get an email with the final call.

## Installation:
+ Deploy the code
+ Add write privileges to folder and subfolders app/tmp/
+ Modify database profile in app/config/database.config.php
+ Create database and fill with db schema (folder db/schema.sql).
+ Fill the tables season, matches and players (sorry, no admin pages for fill the information, use mysql queries directly).
+ If you want enable the email alerts for calls, add a cron job for the script app/cron/sendCall.script.php

For more informations, see the
[**dedicated page**](https://github.com/manelpm10/Football-Manager-Calls).
