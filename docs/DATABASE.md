# Database setup

Data for deploying GigaDB's PostgreSQL database is now kept in a collection of
CSV files which are located in the `gigadb-website/data` directory. For example,
the CSV files to deploy a database for development work are in `data/dev`:
```
$ pwd
/path/to/gigadb-website/data/dev
$ ls -lh
alternative_identifiers.csv dataset_funder.csv          extdb.csv                   file_relationship.csv       manuscript.csv              rss_message.csv             type.csv
attribute.csv               dataset_log.csv             external_link.csv           file_sample.csv             news.csv                    sample.csv                  unit.csv
author.csv                  dataset_project.csv         external_link_type.csv      file_type.csv               prefix.csv                  sample_attribute.csv
curation_log.csv            dataset_sample.csv          file.csv                    funder_name.csv             project.csv                 sample_experiment.csv
dataset.csv                 dataset_type.csv            file_attributes.csv         gigadb_user.csv             publisher.csv               sample_rel.csv
dataset_attributes.csv      exp_attributes.csv          file_experiment.csv         image.csv                   relation.csv                search.csv
dataset_author.csv          experiment.csv              file_format.csv             link.csv                    relationship.csv            species.csv
```

Each file is named after the PostgreSQL table whose data can be found in CSV
format in the file.

## Creating CSV files

These CSV files have been created using 
`gigadb-website/data/scripts/export_csv.sh` which queries a GigaDB PostgreSQL 
database for data corresponding to a list of internal dataset ids. These 
identifiers are used in a list of SQL SELECT commands to export data associated 
with GigaDB datasets from database tables. To use the `export_csv.sh` script, we
need to ensure that the following variables have been configured in the .secrets 
file:
```
EXPORT_CSV_GIGADB_DB=
EXPORT_CSV_GIGADB_HOST=
EXPORT_CSV_GIGADB_USER=
EXPORT_CSV_GIGADB_PASSWORD=
```

We can now run the `export_csv.sh` script through the `test` service container 
making use of its `psql` client:
```
$ docker-compose run --rm test data/scripts/export_csv.sh -i "8 22 144 200" -o custom
```

The identifiers are provided to `export_csv.sh` as a string in a command line
argument using the `-i` flag. Other arguments which are required include `-o` 
an output directory where CSV will be saved to, and `-d` the name of the
database for querying. 

## Converting CSV files into Yii migration scripts

The CSV files must be converted to Yii migration scripts before the data can be
uploaded into a database. This is achieved using the `csv-to-migrations` docker
service as follows:
```
$ docker-compose up csv-to-migrations
```

A JavaScript program called `csv_yii_migration.js` is executed which will 
convert a directory containing CSV files into Yii migration scripts. This 
directory is specified by the `CSV_DIR` variable in the `.env` file and has a 
default value of `dev` which means that the CSV files in 
`gigadb-website/data/dev` will be transformed into Yii migration scripts and 
these will be located in the `gigadb-website/protected/migrations/data/dev`
directory.

## Running database migrations

Now that Yii migration scripts are available, a database can be deployed for the
GigaDB website. This is achieved with two commands:
```
# Create schema tables
$ docker-compose run --rm  application ./protected/yiic migrate --migrationPath=application.migrations.schema --interactive=0
# Upload data into tables
$ docker-compose run --rm  application ./protected/yiic migrate --migrationPath=application.migrations.data.dev --interactive=0
```

The first command will create the schema tables for GigaDB's PostgreSQL 
database using the migration scripts in `gigadb-website/protected/migrations/schema`.
The second command will upload data into the tables using migration scripts that
were created as part of `docker-compose run --rm config` execution.

If you make changes to the schema and/or add new data by updating by creating 
new Yii migration scripts then you might want to reset the PostgreSQL database 
and re-run the schema creation and data upload migration scripts:
```
# Delete tables and other database objects
$ docker-compose run --rm  application ./protected/yiic migrate to 300000_000000 --migrationPath=application.migrations.admin --interactive=0
# Reset tbl_migration table for logging database migrations
$ docker-compose run --rm  application ./protected/yiic migrate mark 000000_000000 --interactive=0
# Re-create schema tables
$ docker-compose run --rm  application ./protected/yiic migrate --migrationPath=application.migrations.schema --interactive=0
# Upload data into tables
$ docker-compose run --rm  application ./protected/yiic migrate --migrationPath=application.migrations.data.dev --interactive=0
```




