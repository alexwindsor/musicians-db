Musicians DB

This is a dummy project that I put together quickly in order to demonstrate my skills with Laravel. It uses the Tailwind css framework (using node.js / Vite server for development) and the Jquery CDN for the Javascript.

The relational database works as follows.

The 'musicians' table contains just the musicians' first and last names. There is also a table of 'instruments' that consists solely of the name of the instruments. There is a many-to-many relationship between these tables, so that a musician can play one or more instrument. The joining table between the two is instrument_musician.

There is a one-to-many relationship between the 'musicians' table and the 'musician_details' table - a musician can have many 'musician_details' rows but each 'musician_details' row is only associated with one musician. A 'musician_details' row can be a phone number, email address, facebook link etc.. These are stored in a set table called 'detail_types' and so there is a many-to-one relationship between the 'musician_details' and 'detail_types' table.

Each musician may or may not have a 'profile'. To save space on the database, the profile is kept in a separate 'profiles' table and so there is a one-to-one relationship between 'musicians' and 'profiles'.

To get this project running on your computer, you need a server running node.js (for Tailwind), PHP 7 or higher and MYSQL. Create a database called 'musicians'. Download the project and type the following in the root directory :

'php artisan migrate:fresh --seed'

'npm run dev'

..then in a different terminal type :

'php artisan serve'

Then point your browser to http://localhost:8000