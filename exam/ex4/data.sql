CREATE TABLE person (id INTEGER PRIMARY KEY, name VARCHAR(255));
CREATE TABLE phone (person_id INTEGER, number VARCHAR(100));

INSERT INTO person VALUES (1, 'Alice');
INSERT INTO phone VALUES (1, 'n1');
INSERT INTO phone VALUES (1, 'n2');

INSERT INTO person VALUES (2, 'Carol');
INSERT INTO phone VALUES (2, 'n4');

INSERT INTO person VALUES (3, 'Bob');
INSERT INTO phone VALUES (3, 'n3');

INSERT INTO person VALUES (4, 'Alice');
INSERT INTO phone VALUES (4, 'n5');
