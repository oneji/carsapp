### Installation

Clone an application. Install the dependencies and devDependencies and start the development server. From the root of a project:

```sh
$ cd vuefront
$ npm install
$ npm run dev
```

For Laravel project copy `vendor` folder into the root of the project.
```sh
$ composer update
```

Then you have to create `.env` file add these information in there:
```sh
JWT_SECRET=JDHYWppmF6chkRC6S1HqvqQaqJAabXYJ
JWT_TTL=1440
JWT_REFRESH_TTL=21600
JWT_BLACKLIST_GRACE_PERIOD=30
```
