# Dunedin AI

## Objective:
A web application which you can have access to your kindle/web highlights. As notes.
When you highlight a note in kindle, it keeps stored in a file called my_clippings.txt. The idea is to import that file to this web app, and allow you to search and add comments to your highlights.
It should be possible also to add private highlights, that, in pair with a Google Chrome Extension, shall add any selection from the web to your highlights.
User will be able to upload kindle files and import the content to the database. The upload and import of the files will also be managed by this project.

## Structure:
### API:
A backend that will consult the database and return the data through the endpoints

### App:
A web application that will check the API and search your highlights
