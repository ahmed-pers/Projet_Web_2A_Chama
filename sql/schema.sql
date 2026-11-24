CREATE TABLE CATEGORIE (
    id_categorie INT PRIMARY KEY,
    nomCategorie VARCHAR(100),
    description VARCHAR(255)
);

CREATE TABLE SERVICE (
    id_service INT PRIMARY KEY,
    nomService VARCHAR(100),
    status VARCHAR(50),
    id_categorie INT,
    FOREIGN KEY (id_categorie) REFERENCES CATEGORIE(id_categorie)
);

CREATE TABLE TICKET (
    id_Ticket INT PRIMARY KEY,
    id_client INT,
    id_service INT,
    date DATE,
    time TIME,
    FOREIGN KEY (id_service) REFERENCES SERVICE(id_service)
);
