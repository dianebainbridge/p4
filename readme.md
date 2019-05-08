# Project 4
+ By: Diane Bainbridge
+ Production URL: <http://p4.dianebainbridge.com>

## Feature summary
+ Visitors can calculate a single fuel consumption without logging in.
+ Visitors can register/log in
+ Logged in users can enter fuel log entries which are saved to a log.
+ Only ogged in users can add/update/delete fuel log entries.
+ Logged in users can also view their fuel log entries and export them to excel.
  
## Database summary
*Describe the tables and relationships used in your database. Delete the examples below and replace with your own info.*

+ My application has 2 tables in total (`users`, `fuel_log_entires`)
+ There's a one-to-many relationship between `fuel log entries` and `users` (one user can have may fuel log entries)

## Outside resources
+ https://docs.laravel-excel.com/3.1/getting-started/
+ https://itsolutionstuff.com/post/how-to-add-header-row-in-export-excel-file-with-maatwebsite-in-laravel-58example.html

## Code style divergences
My Exports class conttains a sql query that spans mor than one line.  
I tried to use alt enter to split the line in several different places but the query kept breaking.

## Notes for instructor
*Any notes for me to refer to while grading; if none, omit this section