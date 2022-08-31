CREATE TABLE status (
  id    INTEGER PRIMARY KEY AUTO_INCREMENT, 
  name  TEXT,
  type  TEXT,
  deleted_at DATETIME DEFAULT NULL
);

CREATE TABLE tables (
  id    INTEGER PRIMARY KEY AUTO_INCREMENT, 
  number  INTEGER,
  status  INTEGER,
  deleted_at DATETIME DEFAULT NULL,
  FOREIGN KEY(status) REFERENCES status(id)
);

CREATE TABLE employee_type (
  id    INTEGER PRIMARY KEY AUTO_INCREMENT, 
  name  TEXT,
  deleted_at DATETIME DEFAULT NULL
);

CREATE TABLE product (
  id    INTEGER PRIMARY KEY AUTO_INCREMENT, 
  name  TEXT,
  price NUMERIC,
  eta   NUMERIC,
  employee_type INTEGER,
  deleted_at DATETIME DEFAULT NULL,
  FOREIGN KEY(employee_type) REFERENCES employee_type(id)
);

CREATE TABLE employee (
  id    INTEGER PRIMARY KEY AUTO_INCREMENT, 
  uuid  TEXT,
  full_name TEXT,
  password   TEXT,
  type INTEGER,
  deleted_at DATETIME DEFAULT NULL,
  FOREIGN KEY(type) REFERENCES employee_type(id)
);

CREATE TABLE orders (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  number TEXT,
  eta  TEXT,
  completed_time TEXT,
  status   INTEGER,
  waiter   INTEGER,
  associated_table   INTEGER,
  amount   NUMERIC,
  customer_name   TEXT,
  picture_path   TEXT,
  deleted_at DATETIME DEFAULT NULL,
  FOREIGN KEY(status) REFERENCES status(id),
  FOREIGN KEY(waiter) REFERENCES employee(id),
  FOREIGN KEY(associated_table) REFERENCES tables(id)
);

CREATE TABLE order_items (
  id    INTEGER PRIMARY KEY AUTO_INCREMENT, 
  product INTEGER,
  status   INTEGER,
  order_id INTEGER,
  eta DATETIME,
  completed_time DATETIME DEFAULT NULL,
  deleted_at DATETIME DEFAULT NULL,
  FOREIGN KEY(order_id) REFERENCES orders(id),
  FOREIGN KEY(product) REFERENCES product(id),
  FOREIGN KEY(status) REFERENCES status(id)
);

CREATE TABLE survey (
  id    INTEGER PRIMARY KEY AUTO_INCREMENT, 
  table_points INTEGER,
  restaurant_points   INTEGER,
  cook_points INTEGER,
  waiter_points INTEGER,
  comment VARCHAR(66),
  associated_table INTEGER,
  associated_order INTEGER,
  deleted_at DATETIME DEFAULT NULL,
  FOREIGN KEY(associated_order) REFERENCES orders(id),
  FOREIGN KEY(associated_table) REFERENCES tables(id)
);

INSERT INTO status VALUES (1, "En espera", "order", NULL);
INSERT INTO status VALUES (2, "En preparacion", "order", NULL);
INSERT INTO status VALUES (3, "Listo para servir", "order", NULL);
INSERT INTO status VALUES (4, "Entregada", "order", NULL);
INSERT INTO status VALUES (5, "Terminada", "order", NULL);
INSERT INTO status VALUES (6, "Cliente esperando pedido", "table", NULL);
INSERT INTO status VALUES (7, "Cliente comiendo", "table", NULL);
INSERT INTO status VALUES (8, "Cliente pagando", "table", NULL);
INSERT INTO status VALUES (9, "Cerrada", "table", NULL);

INSERT INTO employee_type VALUES (1, "SOCIO", NULL);
INSERT INTO employee_type VALUES (2, "MOZO", NULL);
INSERT INTO employee_type VALUES (3, "COCINERO", NULL);
INSERT INTO employee_type VALUES (4, "BARTENDER", NULL);
INSERT INTO employee_type VALUES (5, "CERVECERO", NULL);

INSERT INTO employee VALUES (1, "abianchini", "Alejo Bianchini", "password", 1, NULL);
INSERT INTO employee VALUES (2, "gmazzeo", "Giuliana Mazzeo", "password", 1, NULL);
INSERT INTO employee VALUES (3, "tchapin", "Tom Chapin", "password", 2, NULL);
INSERT INTO employee VALUES (4, "tacosta", "Tuli Acosta", "password", 2, NULL);
INSERT INTO employee VALUES (5, "goku", "Goku San", "password", 4, NULL);
INSERT INTO employee VALUES (6, "swright", "Suzan Wright", "password", 4, NULL);
INSERT INTO employee VALUES (7, "jsnow", "John Snow", "password", 3, NULL);
INSERT INTO employee VALUES (8, "gosso", "Guido Osso", "password", 3, NULL);

INSERT INTO tables VALUES (1, "00001", 4, NULL);
INSERT INTO tables VALUES (2, "00002", 4, NULL);
INSERT INTO tables VALUES (3, "00003", 4, NULL);
INSERT INTO tables VALUES (4, "00004", 4, NULL);
INSERT INTO tables VALUES (5, "00005", 4, NULL);
INSERT INTO tables VALUES (6, "00006", 4, NULL);
INSERT INTO tables VALUES (7, "00007", 4, NULL);
INSERT INTO tables VALUES (8, "00008", 4, NULL);
INSERT INTO tables VALUES (9, "00009", 4, NULL);
INSERT INTO tables VALUES (10, "00010", 4, NULL);
INSERT INTO tables VALUES (11, "00011", 4, NULL);
INSERT INTO tables VALUES (12, "00012", 4, NULL);
INSERT INTO tables VALUES (13, "00013", 4, NULL);
INSERT INTO tables VALUES (14, "00014", 4, NULL);
INSERT INTO tables VALUES (15, "00015", 4, NULL);

INSERT INTO product VALUES (1, "Hamburguesa de Garbanzo", 750, 10, 3, NULL);
INSERT INTO product VALUES (2, "Cerveza Corona", 470, 2, 5, NULL);
INSERT INTO product VALUES (3, "Daikiri", 500, 3, 4, NULL);
INSERT INTO product VALUES (4, "Milanesa a Caballo", 1500, 12, 3, NULL);
INSERT INTO product VALUES (5, "Suprema a Caballo", 1200, 10, 3, NULL);
INSERT INTO product VALUES (6, "Pure de Papa", 650, 5, 3, NULL);
INSERT INTO product VALUES (7, "Papas Fritas con Cheddar", 350, 8, 3, NULL);
INSERT INTO product VALUES (8, "Nachos con Guacamole", 430, 5, 3, NULL);
INSERT INTO product VALUES (9, "Super Pancho", 250, 5, 3, NULL);
INSERT INTO product VALUES (10, "Hamburguesa La Comanda", 1100, 9, 3, NULL);