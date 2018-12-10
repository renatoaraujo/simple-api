Code challenge for *****
========================

# The Task

Create a small REST API having several endpoints:
1. /token - an endpoint that will be used to create an authorisation token using email
and password.
2. /entities - an endpoint having CRUD functionality (use any entity structure you like).
3. /account - an endpoint returning account information identified by authorisation
token.
Use any language and framework that you think better fits the task.

## Questions

1. Please explain your choice of technologies.
	- I decided to use Symfony for simplicity of the code and application bootstrap. Using this framework helps saving some time on routes creation, dependency injection and on.
2. What is the difference between PUT and POST methods?
	- PUT: Update a resource. POST: Create a resource.
3. What approaches would you apply to make your API responding fast?
	- Caching on backend and on application server (eg apache or nginx).
4. How would you monitor your API?
	- Using some market tools such as APImetrics or Elastic Stack.
5. Which endpoints from the task could by publically cached?
	- Single person and person list.
	
# Architecture

When I decided to create this project I tough about something a little bit more over engineered but thinking about the problem it shoudn't be something too big or with a huge codebase.
I decided to use the Symfony framework as simple as possible, just using simple controllers injecting the services and the components on it.

## Another approach?

Another approach that can be really good for this project but with a little bit more effort would be using Event-driven Architecture releasing events on every system interaction, this could open some possibilities for CQRS and asynchronicity.

Isolation of the business logic using DDD approach would help not only in the architecture but also to create a framework agnostic application and more business oriented with understandable contexts.

#### What I don't like about this code?

This is clearly framework dependent with a single service, in real life I would create a isolated module using command pattern with Value Objects to isolate model and validations and single responsibility objects.

Using the service to access the database layer can be tricky as well so I would consider using Repository as Service to create another layer in application to isolate every database interactions.

Exceptions been thrown using the core \Exception is not good at all, I would put more specific exceptions on the service to give a little bit more of verbosity too.

#### Why I created a lot of things that I don't like?

This test should be simple, keep it simple, understandable and not should take long time to be implemented, so if I do have a small time like hours to create a small collections of endpoints I would avoid any kind of complexity because if the application is complex you can expect complex tests and setup.

#### Why just functional tests and not at least one Unit Test?

In my personal opinion unit tests helps you to improve the quality of your code, with expectations and some scenarios that can be a little bit overkilling. Doing functional tests you have more guarantee that your application behavior will works as expected.


# Install and run

If you have Docker installed in your machine and you're using Unix/OSX you can simple run the following commands:

To setup the environment (download the images, install dependencies and small test)
```bash
$ make setup  
```

To run your database and load the fixtures you can run:
```bash
$ make db
```

To run the tests simple run:
```bash
$ make test
```

If you're not using Docker simple ensure that you have PHP 7.2 with MySQL 8 running in your machine and run the following commands:

Install the dependencies and check needed extensions:
```bash
$ composer install 
```

Run the migrations to create the database structure:
```bash
$ bin/console doctrine:migrations:migrate
```

Load the database fixtures:
```bash
$ bin/console doctrine:fixtures:load
```

Run the server using Symfony server on 8080 port:
```bash
$ bin/console server:start *:8080
```

To run the tests just use the command:
```bash
$ bin/phpunit -c phpunit.xml
```

# Usage

If you have postman installed you can import the collection located on the root of the project.

Using CURL just type in your terminal:

#### To create person
```bash
curl -X POST \
  http://localhost:8080/api/person \
  -H 'Content-Type: application/json' \
  -H 'cache-control: no-cache' \
  -d '{
	"name": "Fulano",
	"email": "fulano@gmail.com",
	"birth_date": "20/01/1992"
}'
``` 

#### To list persons
```bash
curl -X GET \
  http://localhost:8080/api/person \
  -H 'Content-Type: application/json' \
  -H 'cache-control: no-cache'
```

#### To update person
```bash
curl -X PUT \
  http://localhost:8080/api/person/2fe674bf-ce18-401d-b663-a437b8a692d7 \
  -H 'Content-Type: application/json' \
  -H 'cache-control: no-cache' \
  -d '{
	"name": "Fulano",
	"email": "email@gmail.com",
	"birth_date": "20/01/1991"
}'
```

#### To update person
```bash
curl -X PUT \
  http://localhost:8080/api/person/2fe674bf-ce18-401d-b663-a437b8a692d7 \
  -H 'Content-Type: application/json' \
  -H 'cache-control: no-cache' \
  -d '{
	"name": "Fulano",
	"email": "email@gmail.com",
	"birth_date": "20/01/1991"
}'
```

#### To delete person
```bash
curl -X DELETE \
  http://localhost:8080/api/person/2fe674bf-ce18-401d-b663-a437b8a692d7 \
  -H 'Content-Type: application/json' \
  -H 'cache-control: no-cache'
```

#### To view person
```bash
curl -X GET \
  http://localhost:8080/api/person/51a6e34e-d712-4937-860b-a787f1ae0f4e \
  -H 'Content-Type: application/json' \
  -H 'cache-control: no-cache'
```

#### To view account information
```bash
curl -X GET \
  http://localhost:8080/api/account \
  -H 'Content-Type: application/json' \
  -H 'cache-control: no-cache'
```

#### To login in the application
```bash
curl -X GET \
  http://localhost:8080/token \
  -H 'Content-Type: application/json' \
  -H 'cache-control: no-cache' \
  -d '{
	"username": "john.doe@localhost.local",
	"password": "VeryDifficultPassword!"
}'
```

TIP: First login in the application and send the Authorization header with Bearer token from login request.

Hope you enjoy! :)
