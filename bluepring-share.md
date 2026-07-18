# Dunedin Share:

Create a functionality that will allow any user to share [one single Highlight] OR [a list of Highlights, which is the result of a search] publicly.
The share will create a link. That link will be public and anyone can view the highlights, even if it's not logged.

## app (logged)
A "hihglight icon" will be displayed in each HighlightResult individually, but also in a HighlightList, which will allow to share all the Highlights in the list
A "Shared Links" item will appear in the right side menu, with all the shared links created (and, a brief description, and the amount of highlights in each link)

## api
Api will have to create new endpoints to handle the share.
Database changes will have to be made, and in this case, you can change directly into the `schema.sql` once the data will be wiped. No need to create migrations.
The idea is to create an endpoint that will receive a list of Highlights, validate if all of them belongs to the logged user, and create some way to share them. 
We can create a new table called "shared_links", with:
- `user_id` BIGINT => User who created this share
- `uuid` => as we're gonna access the shared links publicly, we can't rely on ids, so we're gonna use uuids to access them. the uuid logic is already in the new version of MagratheaPHP
- `active` BOOLEAN => you will be able to enable or disable your shares through the app
- `description` VARCHAR 200; => it can be sent via API; for single highlights the description will be the first 100 chars of `text`, unless it is sent on the API; for lists, description will be sent or it will be called "list of n items", being n the number of items. When sharing a list, app will send the query search text, and the API will crop (if necessary) 100 caracters for the description. Every time the description is cropped, "..." is added at the end
- `visits` BIGINT => how many people already accessed the shared link
- `expire` DATE NULL => we're not gonna use it right now, but API will validate if the link is still valid if not null
- `highlights` TEXT => an array with the list of highlights ids that are being shared

# app (public)
A new public page will be added, to display the shared highlights. It will receive an UUID in the query param and ping the API to validate the UUID, get the highlights and display all of them in a page.
No need for pagination or any other element in the page.
You can display the hihghlights in the HihglightResult component, but remmeber to remove any link or action icon in the component if it is being public displayed. Try to reuse as much components as possible


