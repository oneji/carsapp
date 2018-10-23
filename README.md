### Installation
For server side of the project do the following:
```sh
$ composer install
```

For client side install the dependencies and devDependencies and start the development server. From the root of a project:

```sh
$ cd clientfront
$ npm install
$ npm run dev
```

Then you have to create `.env` file add these information in there:
```sh
JWT_SECRET=JDHYWppmF6chkRC6S1HqvqQaqJAabXYJ
JWT_TTL=1440
JWT_REFRESH_TTL=21600
JWT_BLACKLIST_GRACE_PERIOD=30
```
