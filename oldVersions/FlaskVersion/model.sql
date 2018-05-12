--- Data model for CTB_DOM (PostgreSQL) :
----------------------------------------------


CREATE TABLE real_pots
(
  id         uuid        not null,
  pos        integer     null,
  name       character   varying(1000),
  amount     decimal     not null,
  CONSTRAINT real_pots_pkey PRIMARY KEY (id)
) WITH (OIDS=FALSE);
ALTER TABLE real_pots OWNER TO barba;


CREATE TABLE acc_pots
(
  id         uuid        not null,
  pos        integer     null,
  name       character   varying(1000),
  amount     decimal     not null,
  parent_id  uuid        null,
  CONSTRAINT acc_pots_pkey PRIMARY KEY (id)
) WITH (OIDS=FALSE);
ALTER TABLE acc_pots OWNER TO barba;


---------------------------------------------------------------------------------------------------


insert into real_pots
select uuid_generate_v4(), 1, 'Cta. Vell',          0 union all
select uuid_generate_v4(), 2, 'Cta. Nou', 			0 union all
select uuid_generate_v4(), 3, 'Efectiu caixa', 		0 union all
select uuid_generate_v4(), 4, 'Efectiu cartera', 	0 union all
select uuid_generate_v4(), 5, 'Cta. BS', 			0 union all
select uuid_generate_v4(), 6, 'Termini BS', 		0 union all
select uuid_generate_v4(), 7, 'Traspas C', 			0;


insert into acc_pots
select uuid_generate_v4(), 1, 'General',		0 union all
select uuid_generate_v4(), 2, 'Ingressos',		0 union all
select uuid_generate_v4(), 3, 'Prestecs',		0 union all
select uuid_generate_v4(), 4, 'Vivenda',		0 union all
select uuid_generate_v4(), 5, 'Serveis',		0 union all
select uuid_generate_v4(), 6, 'Cotxe',			0 union all
select uuid_generate_v4(), 7, 'Material',		0 union all
select uuid_generate_v4(), 8, 'Menjar',			0 union all
select uuid_generate_v4(), 9, 'Gastos varis',	0;

update comptes_comptables set codi_compte_comptable_pare = null, id_ultim_moviment = null;

insert into comptes_comptables
select '1.1. Estalvi',					'1. General',		'Estalvi', 				0, 0 		union all
select '1.2. RBE',						'1. General',		'RBE', 					0, 0 		union all
select '1.3. Traspas R',				'1. General',		'Traspas R', 			0, 0 		union all
select '2.1. Atmira Nomina',			'2. Ingressos',		'Atmira Nomina', 		0, 0 		union all
select '2.2. Atmira SNC',				'2. Ingressos',		'Atmira SNC', 			0, 0 		union all
select '2.3. Atmira Hores extres',		'2. Ingressos',		'Atmira Hores extres', 	0, 0 		union all
select '2.4. Atmira Dietes',			'2. Ingressos',		'Atmira Dietes', 		0, 0 		union all
select '2.5. Interessos bancaris',		'2. Ingressos',		'Interessos bancaris', 	0, 0 		union all
select '2.6. Regals',					'2. Ingressos',		'Regals', 				0, 0 		union all
select '2.7. Altres',					'2. Ingressos',		'Altres', 				0, 0 		union all
select '3.1. Ivan-1',					'3. Prestecs',		'Ivan-1', 				0, 0 		union all
select '3.2. Marc-1',					'3. Prestecs',		'Marc-1', 				0, 0 		union all
select '4.1. Lloguer st. Gaieta',		'4. Vivenda',		'Lloguer st. Gaieta', 	0, 0 		union all
select '4.2. Seguro pis',				'4. Vivenda',		'Seguro pis', 			0, 0 		union all
select '4.3. Fiança',					'4. Vivenda',		'Fiança', 				0, 0 		union all
select '4.4. Obres',					'4. Vivenda',		'Obres', 				0, 0 		union all
select '5.1. Electricitat',				'5. Serveis',		'Electricitat', 		0, 0 		union all
select '5.2. Gas',						'5. Serveis',		'Gas', 					0, 0 		union all
select '5.3. Aigua',					'5. Serveis',		'Aigua', 				0, 0 		union all
select '5.4. Internet',					'5. Serveis',		'Internet', 			0, 0 		union all
select '5.5. Mòbil',					'5. Serveis',		'Mòbil', 				0, 0 		union all
select '5.6. CCOO',						'5. Serveis',		'CCOO', 				0, 0 		union all
select '5.7. Centre Excurcionista',		'5. Serveis',		'Centre Excurcionista', 0, 0 		union all
select '5.8. Seguro FEEC',				'5. Serveis',		'Seguro FEEC', 			0, 0 		union all
select '5.9. Creu roja',				'5. Serveis',		'Creu roja', 			0, 0 		union all
select '5.10. Tintoreria',				'5. Serveis',		'Tintoreria', 			0, 0 		union all
select '5.11. Tren',					'5. Serveis',		'Tren', 				0, 0 		union all
select '5.12. Esmorzar',				'5. Serveis',		'Esmorzar', 			0, 0 		union all
select '6.1. Gasolina',					'6. Cotxe',			'Gasolina', 			0, 0 		union all
select '6.2. Seguro cotxe',				'6. Cotxe',			'Seguro cotxe', 		0, 0 		union all
select '6.3. Impost circulació',		'6. Cotxe',			'Impost circulació', 	0, 0 		union all
select '6.4. Quota Teletac',			'6. Cotxe',			'Quota Teletac', 		0, 0 		union all
select '6.5. Peatges',					'6. Cotxe',			'Peatges', 				0, 0 		union all
select '6.6. Reparacions',				'6. Cotxe',			'Reparacions', 			0, 0 		union all
select '7.1. Roba',						'7. Material',		'Roba', 				0, 0 		union all
select '7.2. Mobiliari Pis',			'7. Material',		'Mobiliari Pis', 		0, 0 		union all
select '7.3. Tecnologia',				'7. Material',		'Tecnologia', 			0, 0 		union all
select '7.4. Btt & Run',				'7. Material',		'Btt & Run', 			0, 0 		union all
select '7.5. Cultura',					'7. Material',		'Cultura', 				0, 0 		union all
select '7.6. Crema grans',				'7. Material',		'Crema grans', 			0, 0 		union all
select '7.7. Finesterida',				'7. Material',		'Finesterida', 			0, 0 		union all
select '7.8. Altres',					'7. Material',		'Altres', 				0, 0 		union all
select '8.1. Compra menor',				'8. Menjar',		'Compra menor', 		0, 0 		union all
select '8.2. Compra major',				'8. Menjar',		'Compra major', 		0, 0 		union all
select '8.3. Fruiteria',				'8. Menjar',		'Fruiteria', 			0, 0 		union all
select '8.4. Pa entrepans',				'8. Menjar',		'Pa entrepans', 		0, 0 		union all
select '9.1. Altres',					'9. Gastos varis',	'Altres', 				0, 0 		union all
select '9.2. Restaurants',				'9. Gastos varis',	'Restaurants', 			0, 0 		union all
select '9.3. Vacances',					'9. Gastos varis',	'Vacances', 			0, 0 		union all
select '9.4. Multes',					'9. Gastos varis',	'Multes', 				0, 0 		union all
select '9.5. Despedida Eriko',			'9. Gastos varis',	'Despedida Eriko', 		0, 0 		union all
select '9.6. Despedida Sanlle',			'9. Gastos varis',	'Despedida Sanlle', 	0, 0;

update comptes_comptables set id_ultim_moviment = null;





---------------------------------------------------------------------------------------------------
  
CREATE TABLE moviments
(
  id_moviment integer NOT NULL,
  id_operacio integer NOT NULL,
  data_moviment date NOT NULL,
  import numeric(15,2) NOT NULL,
  descripcio text,
  codi_compte_real text NOT NULL,
  codi_compte_comptable text NOT NULL,
  id_periodificacio integer,
  CONSTRAINT moviments_pk PRIMARY KEY (id_moviment)
  DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE moviments
  OWNER TO barba;
  
---------------------------------------------------------------------------------------------------
  
CREATE TABLE operacions
(
  id_operacio integer NOT NULL,
  descripcio text,
  periodificacio_sn character(1) NOT NULL,
  CONSTRAINT operacions_pk PRIMARY KEY (id_operacio)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE operacions
  OWNER TO barba;
  
---------------------------------------------------------------------------------------------------
  
CREATE TABLE periodificacions
(
  id_periodificacio integer NOT NULL,
  data_inici date NOT NULL,
  data_fi date NOT NULL,
  tipus_amd character(1) NOT NULL,
  codi_compte_comptable text NOT NULL,
  import numeric(15,2) NOT NULL,
  CONSTRAINT periodificacions_pk PRIMARY KEY (id_periodificacio)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE periodificacions
  OWNER TO barba;

---------------------------------------------------------------------------------------------------
  
CREATE TABLE log_joel
(
  id_numero integer NOT NULL,
  fecha timestamp without time zone NOT NULL,
  text text,
  CONSTRAINT log_joel_pk PRIMARY KEY (id_numero)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE log_joel
  OWNER TO barba;