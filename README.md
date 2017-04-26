# Project: test0 


[![Build Status](https://travis-ci.org/rubenknol/test0.svg?branch=master)](https://travis-ci.org/rubenknol/test0)

## Requirements:

* Docker (1.13+)
* Docker Compose (1.10+)

## Running the application

To simply run the application (no development), use the following command:

```
$ make up
```

Once all containers are fetched and running, you can access the project at the following URL:

* [http://localhost:5500](http://localhost:5500)

To halt the containers:

```
$ make down
```

To destroy the containers (all persistent data will be lost):

```
$ make destroy
```

## Developing on the application

### CI Builds of the Docker container

Important to understand is that each time a push to the repository with new commits happens, the CI build job
will build a new version of the application's Docker container and push it to the **rknol/test0** Docker Hub 
repository under the tag **latest**.

### Provisioning

If you want to develop on the application and provision the project for development on your local machine, 
start the containers with the following command:

```
$ make dev
```

To tear down the development containers:



### Tips & Tricks

#### Rebuild the application container

To rebuild the application container for local development:

```
$ make build
```

#### PHP code style check

Run the PHP code style check like this:

```
$ make style
```

#### Access application container shell
 
To enter a bash shell in the application container:

```
$ make shell
```

#### Access the MySQL database

To enter a MySQL prompt in the database container:

```
mysql -h 127.0.0.1 -u test0 -ptest0_pw test0
```