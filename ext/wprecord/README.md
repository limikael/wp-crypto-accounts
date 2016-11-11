# WpRecord
WpRecord is a simple implementation of the [active record pattern](https://en.wikipedia.org/wiki/Active_record_pattern), specifically created for a WordPress environment.

* [What is active record?](#what-is-active-record)
* [An example](#an-example)
* [Using WpRecord](#using-wprecord)
* [Methods](#methods)

## What is active record?

The active record pattern is a design pattern. It is used to bridge the concepts of [object oriented programming](https://en.wikipedia.org/wiki/Object-oriented_programming) and [relational database management systems](https://en.wikipedia.org/wiki/Relational_database_management_system). When using the active record pattern, we can think of each row in a database table as representing an instance of an object. The class of the object corresponds to the table of the database. The columns in the database corresponds to the fields of the object.

## An example

For example, we might have a database table representing persons. The table would have the columns `id`, `firstName` and `lastName`. Let's look at the code to fetch and display the information about a particular person, with and without WpRecord. For the example, let's use the person with `id=5`.

Using the `$wpdb` object in WordPress, our code might look something like this:

```php
$person=$wpdb->get_row($wpdb->prepare("SELECT * FROM persons WHERE id=%s",5),ARRAY_A);
echo $person["firstName"]." ".$person["lastName"];
```

Using WpRecord, the code would instead be:

```php
$person=Person::findOne(5);
echo $person->firstName." ".$person->lastName;
```

Say that we instead would like to fetch all persons with the surname "Lee". Using the `$wpdb` object, the code would be:

```php
$persons=$wpdb->get_result($wpdb->prepare("SELECT * FROM persons WHERE lastName=%s","Lee"),ARRAY_A);
```

Using WpRecord, the code would be:

```php
$persons=Person::findAllBy("lastName","Lee");
```

SQL is a powerful language used to extract information from a database. However, in a real world database application, many of the queries are quite simple. They are often of the type in the example, such as "find an object with a particular value for the primary key", or "find all objects with a specific value for a given column". In these situations, having SQL statements in the middle of the code might make the code cluttered and difficult to read. Here, using an active record implementation will make the code smaller and cleaner.

## Using WpRecord

In order to use WpRecord, we create a class that extends the `WpRecord` class. In our class, we need to implement the static method `initialize`, which tells WpRecord the structure of the underlying database table. Remember, each class corresponds to a database table. From the `initialize` method, we make calls to the static method `field` to tell WpRecord which fields our database table should have. Let's do this for the Person object used above:

```php
class Person extends WpRecord {
    static function initialize() {
        self::field("id", "integer not null auto_increment");
        self::field("firstName", "varchar(255) not null");        
        self::field("lastName", "varchar(255) not null");        
    }
}
```

This is really all we need!  Now, we can call the `install` function, and have the schema syncronized to the
underlying database. This only needs to be done whenever our schema changes,
so a good place to do it is in the [plugin activation hook](https://developer.wordpress.org/plugins/the-basics/activation-deactivation-hooks/).

```php
Person::install();
```

We can now create a `Person` object and save it to the database:

```php
$person=new Person();
$person->firstName="Mikael";
$person->lastName="Lindqvist";
$person->save();
```

## Methods

The following methods are implemented in the base class, and are available in all classes extending `WpRecord`. For illustration, we will assume that we are operating on the `Person` object explained above. The methods used to retreive information from the database are static, since they do not act on a specific object. The methods acting on a specific object (`save` and `delete`) are not-static.

### save

```php
$person->save();
```

Saves an object to the database. If the object has a value for the primary key field, it will be updated in the database. If not, a new row will be crated and the primary key value will be set in the object.

### delete

```php
$person->delete();
```

Deletes an object form the database.

### findOne
```php
$person=Person::findOne($id);
```

Finds one object by primary key value.

### findAll
```php
$persons=Person::findAll();
```

Finds all the objects in the database table.

### findOneBy
```php
$person=Person::findOneBy("firstName","Mikael");
```

or
```php
$person=Person::findOneBy(array(
    "firstName"=>"Mikael",
    "lastName"=>"Lindqvist"
));
```

Finds one object by matching against one or several fields.

### findAllBy

Works the same as `findOneBy` but returns an array of objects.
