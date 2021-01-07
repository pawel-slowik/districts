BEGIN;

INSERT INTO cities (id, name) VALUES (1, 'Test city with many districts');

INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 1', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 2', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 3', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 4', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 5', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 6', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 7', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 8', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 9', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 10', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 11', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 12', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 13', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 14', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 15', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 16', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 17', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 18', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 19', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 20', 1, 1);
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'District 21', 1, 1);

COMMIT;
