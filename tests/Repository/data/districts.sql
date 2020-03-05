BEGIN;

INSERT INTO districts (id, city_id, name, area, population) VALUES ( 1, 1, 'Plugh',   10.0, 5000);
INSERT INTO districts (id, city_id, name, area, population) VALUES ( 2, 1, 'Garply',  20.0, 1000);
INSERT INTO districts (id, city_id, name, area, population) VALUES ( 3, 1, 'Quuz',     5.0, 1000);
INSERT INTO districts (id, city_id, name, area, population) VALUES ( 4, 1, 'Corge',    7.7, 2000);
INSERT INTO districts (id, city_id, name, area, population) VALUES ( 5, 1, 'Qux',    100.0, 1000);
INSERT INTO districts (id, city_id, name, area, population) VALUES ( 6, 1, 'Fred',     8.5, 1234);
INSERT INTO districts (id, city_id, name, area, population) VALUES ( 7, 1, 'Waldo',    9.4, 5678);
INSERT INTO districts (id, city_id, name, area, population) VALUES ( 8, 1, 'Thud',    20.0, 4000);
INSERT INTO districts (id, city_id, name, area, population) VALUES ( 9, 1, 'Grault', 120.0, 4000);
INSERT INTO districts (id, city_id, name, area, population) VALUES (10, 1, 'Quux',   100.1,  987);
INSERT INTO districts (id, city_id, name, area, population) VALUES (11, 1, 'Xyzzy',   20.9, 3000);

INSERT INTO districts (id, city_id, name, area, population) VALUES (12, 2, 'Wibble',  40.4, 5001);
INSERT INTO districts (id, city_id, name, area, population) VALUES (13, 2, 'Wubble',  50.5, 6002);
INSERT INTO districts (id, city_id, name, area, population) VALUES (14, 2, 'Flob',    60.6, 7003);
INSERT INTO districts (id, city_id, name, area, population) VALUES (15, 2, 'Wobble',  70.7, 8004);

COMMIT;
