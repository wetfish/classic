## To start this with docker

1. Create a **.env** based on `.env.example`
2. Create a **src/config.php** based on `src/config.example.php`, as well as **src/ban.php** based on `src/ban.example.php`
3. Run ```docker-compose up -d```
4. Run ```docker-compose exec wiki npm install --prefix src```

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

