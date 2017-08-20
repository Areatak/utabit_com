1. Pull this repository to your WP installation > wp-content > themes > create a folder and rename it jupiter

2. Install PHPCS, PHPCBF, and WPCS (for *Header Builder* only, so you can skip this if you are not working on it). Make sure you have PHP Composer installed *globally* before running the command below.

```
  composer create-project wp-coding-standards/wpcs --no-dev
```

*Otherwise*, if you do not have composer installed globally, go to https://getcomposer.org/download/ then run the install script. Once you have composer.phar in jupiter/composer.phar, please run:

```
  php composer.phar create-project wp-coding-standards/wpcs --no-dev
```

3. Step 2 above is optional in local, but will be run on our build server every single time you submit a Pull Request. It's best if you run it now on local, to avoid having your PRs for Header Builder PR's fail.

4. Go to js/ folder

```
  cd jupiter/js
```

5. Use node v6.0.0, or install it if it is not already installed.

```
  nvm use v6.10.0 || nvm install v6.10.0 || exit 1;
```

6. Install gulp CLI globally if it is not already installed.

```
  gulp --version || npm install -g gulp || exit 1;
```

7. Install yarn globally, if it is not already installed.

```
  yarn --version || npm install -g yarn || exit 1;
```

8. Install everything in package.json file.

```
  yarn
```

9. Build everything from the Jupiter source.

```
  gulp build
```

test for phpmd and phpcs 2
