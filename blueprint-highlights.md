# implement highlights

The main screen is going to change. it will be a simple search box, just like Google.

## api
API will have a new endpoint `/search`, which will receive a query param `?q=` with the query to be searched.
The query will be searched inside `text` value, but create a smart query to bring nice results from the sql.
`/search` can also receive a query param `?author=` that will look for the field `author`.
the endpoint can receive both params (in that case, it will look for texts from that author), or any of them

## app
The first/main screen will be replaced with a Google-like page, with a simple text box and a search button (also triggered via Enter).
there will be a link that shows another text box below that one, which will allow for the user to look for the author as well.

The result of the API will be a list of highlights, limited by pagination, with a load-more option if more results are available.
For this, you can use the MagratheaPHP2 own pagination functions. use 20 results as default, but allow /search endpoint to accept the page and a `count`, that would allow more results in a single page.

in the top of the result list, show a small select box that allow repeat the same request with 50 or 100 results.

Each highlight result will return in a single panel, showing the text, author, origin, and date.
Follow the same style of the current application, that you built yourself and it's already awesome.



