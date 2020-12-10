## To start this with docker

1. Create a **.env** based on the `.env.example`, with your site URL
2. Run ```docker-compose up -d```
3. Run ```docker-compose exec app npm install```

## To get search, tags, etc to work

Open your local wiki in a browser, and edit the page source

 - Popular

```js
left,load{popular.php}
```

 - Browse

```js
load{fun/browse.php}
```

- Search

```js
load{search.php}
```

- Tags

```js
load{src/pages/tags.php} 
 
 
See also {{tag cloud}}!
```

