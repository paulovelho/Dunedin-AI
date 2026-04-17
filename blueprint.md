# Blueprint for Duneind AI

## Database
The database should save:
=> User
	- ID
	- e-mail
	- last login
	- active
	- status

=> Highlights
	- ID
	- user-id
	- text
	- origin (can be 'kindle', 'web', 'twitter', etc. Origin will be part of a list)
	- author (if it's a book, includes the book title)
	- date
	- hash (to garantee no repeated highlights)

=> Notes
	- ID
	- highlight-id
	- user-id
	- note
	- date
  
=> Files
	- ID
	- user-id
	- filename
	- type
	- imported_date
	- status

Considerations:
User+Highlight Text should create somehow a hash that will avoid the insertion o duplicate hihglights.
For now, the only file type will be "kindle3", but it might have new file types in a near future. There will be a file example when we're gonna work on the import.

## API
Users will connect through Firebase.
APIs will need an authentication, so one should only get the highlights related to its own user-id.
There should be an API for search that might in a smart way: you can make general requests with a query, get all the highlights from one author, or combine both.
There should also be APIs for importing

## App
App is really simple.
Authentication via Firebase. Register will be handled in a single page, and can be opened/closed from an app configuration.
First page of the app is a Search Box. And a button "search", as in the old times of Google.
There should be a small avatar + options in the corner, where user can see some statistics and work on the import of files. Other settings might be added later.
