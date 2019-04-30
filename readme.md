# Project 4
+ By: Diane Bainbridge
+ Production URL: <http://p4.dianebainbridge.com>

## Feature summary
*Outline a summary of features that your application has. The following details are from a hypothetical project called "Movie Tracker". Note that it is similar to Foobooks, yet it has its own unique features. Delete this example and replace with your own feature summary*

+ Visitors can register/log in
+ Users can add/update/delete movies in their collection (title, release date, director, writer, summary, category)
+ There's a file uploader that's used to upload post images for each movie
+ User's can toggle whether movies in their collection are public or private
+ Each user has a public profile page which presents a short bio about their movie tastes, as well as a list of public movies in their collection. 
+ Each user has their own account page where they can edit their bio, email, password
+ Users can clone movies from another user's public collection into their collection
+ The home page features
  + a stream of recently added public movies
  + a list of categories, with a link to each category that shows a page of movies (with links) within that category

  
## Database summary
*Describe the tables and relationships used in your database. Delete the examples below and replace with your own info.*

+ My application has 3 tables in total (`users`, `movies`, `categories`)
+ There's a many-to-many relationship between `movies` and `categories`
+ There's a one-to-many relationship between `movies` and `users`

## Outside resources
*Your list of outside resources go here*

## Code style divergences
*List any divergences from PSR-1/PSR-2 and course guidelines on code style*

## Notes for instructor
*Any notes for me to refer to while grading; if none, omit this section*