--
-- PostgreSQL database dump
--

-- Dumped from database version 17.4
-- Dumped by pg_dump version 17.4

-- Started on 2025-05-26 19:13:02

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 2 (class 3079 OID 16557)
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- TOC entry 5065 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 218 (class 1259 OID 16594)
-- Name: asignacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.asignacion (
    id_asignacion integer NOT NULL,
    id_rol integer NOT NULL,
    id_tramite integer NOT NULL,
    estado_asignacion character varying(30) NOT NULL,
    fecha_asignacion date
);


ALTER TABLE public.asignacion OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 16597)
-- Name: asignacion_id_asignacion_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.asignacion_id_asignacion_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.asignacion_id_asignacion_seq OWNER TO postgres;

--
-- TOC entry 5066 (class 0 OID 0)
-- Dependencies: 219
-- Name: asignacion_id_asignacion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.asignacion_id_asignacion_seq OWNED BY public.asignacion.id_asignacion;


--
-- TOC entry 220 (class 1259 OID 16598)
-- Name: auditoria; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.auditoria (
    id_auditoria integer NOT NULL,
    id_rol integer NOT NULL,
    tipo_auditoria character varying
);


ALTER TABLE public.auditoria OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 16603)
-- Name: auditoria_id_auditoria_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.auditoria_id_auditoria_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.auditoria_id_auditoria_seq OWNER TO postgres;

--
-- TOC entry 5067 (class 0 OID 0)
-- Dependencies: 221
-- Name: auditoria_id_auditoria_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.auditoria_id_auditoria_seq OWNED BY public.auditoria.id_auditoria;


--
-- TOC entry 222 (class 1259 OID 16604)
-- Name: documentacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.documentacion (
    id_documentacion integer NOT NULL,
    estado_documentacion character varying(100) NOT NULL,
    id_entidades integer NOT NULL,
    id_usuario integer NOT NULL
);


ALTER TABLE public.documentacion OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 16607)
-- Name: documentacion_id_documentacion_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.documentacion_id_documentacion_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.documentacion_id_documentacion_seq OWNER TO postgres;

--
-- TOC entry 5068 (class 0 OID 0)
-- Dependencies: 223
-- Name: documentacion_id_documentacion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.documentacion_id_documentacion_seq OWNED BY public.documentacion.id_documentacion;


--
-- TOC entry 224 (class 1259 OID 16608)
-- Name: entidades; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.entidades (
    id_entidades integer NOT NULL,
    id_bancos integer NOT NULL,
    nom_bancos character varying(50) NOT NULL,
    id_transito integer,
    nom_transito character varying(50),
    id_camara_comercio integer,
    nom_camara_comercio character varying(50)
);


ALTER TABLE public.entidades OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 16611)
-- Name: entidades_id_entidades_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.entidades_id_entidades_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.entidades_id_entidades_seq OWNER TO postgres;

--
-- TOC entry 5069 (class 0 OID 0)
-- Dependencies: 225
-- Name: entidades_id_entidades_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.entidades_id_entidades_seq OWNED BY public.entidades.id_entidades;


--
-- TOC entry 226 (class 1259 OID 16612)
-- Name: estado_tramite; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.estado_tramite (
    id_estado_tramite integer NOT NULL,
    estado_tramite character varying(30) NOT NULL
);


ALTER TABLE public.estado_tramite OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 16615)
-- Name: estado_tramite_id_estado_tramite_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.estado_tramite_id_estado_tramite_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.estado_tramite_id_estado_tramite_seq OWNER TO postgres;

--
-- TOC entry 5070 (class 0 OID 0)
-- Dependencies: 227
-- Name: estado_tramite_id_estado_tramite_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.estado_tramite_id_estado_tramite_seq OWNED BY public.estado_tramite.id_estado_tramite;


--
-- TOC entry 228 (class 1259 OID 16616)
-- Name: funcionarios_id_funcionario_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.funcionarios_id_funcionario_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.funcionarios_id_funcionario_seq OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 16617)
-- Name: funcionarios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.funcionarios (
    id_funcionario integer DEFAULT nextval('public.funcionarios_id_funcionario_seq'::regclass) NOT NULL,
    id_rol integer NOT NULL,
    tipo_rol character varying(30) NOT NULL,
    nombres character varying(50) NOT NULL,
    apellidos character varying(50) NOT NULL,
    num_telefono character varying(20) NOT NULL,
    correo text NOT NULL,
    correoinstitucional text NOT NULL,
    correorecuperacion text NOT NULL,
    contrasenia character varying(255)
);


ALTER TABLE public.funcionarios OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 16623)
-- Name: usuarios_id_usuario_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.usuarios_id_usuario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_id_usuario_seq OWNER TO postgres;

--
-- TOC entry 5071 (class 0 OID 0)
-- Dependencies: 230
-- Name: usuarios_id_usuario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.usuarios_id_usuario_seq OWNED BY public.funcionarios.id_funcionario;


--
-- TOC entry 231 (class 1259 OID 16624)
-- Name: usuarios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuarios (
    id_usuario integer DEFAULT nextval('public.usuarios_id_usuario_seq'::regclass) NOT NULL,
    nombres character varying(50) NOT NULL,
    apellidos character varying(50) NOT NULL,
    nit character varying(30),
    cc character varying(30),
    tipo_persona character varying(10) NOT NULL,
    correo text,
    contrasenia character varying,
    id_rol integer,
    tipo_rol character varying(30),
    CONSTRAINT involucrados_tipo_persona_check CHECK (((tipo_persona)::text = ANY (ARRAY[('Natural'::character varying)::text, ('Juridica'::character varying)::text])))
);


ALTER TABLE public.usuarios OWNER TO postgres;

--
-- TOC entry 232 (class 1259 OID 16631)
-- Name: involucrados_id_involucrados_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.involucrados_id_involucrados_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.involucrados_id_involucrados_seq OWNER TO postgres;

--
-- TOC entry 5072 (class 0 OID 0)
-- Dependencies: 232
-- Name: involucrados_id_involucrados_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.involucrados_id_involucrados_seq OWNED BY public.usuarios.id_usuario;


--
-- TOC entry 233 (class 1259 OID 16632)
-- Name: login; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.login (
    id_login integer NOT NULL,
    id_rol integer NOT NULL,
    login character varying
);


ALTER TABLE public.login OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 16637)
-- Name: login_id_login_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.login_id_login_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.login_id_login_seq OWNER TO postgres;

--
-- TOC entry 5073 (class 0 OID 0)
-- Dependencies: 234
-- Name: login_id_login_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.login_id_login_seq OWNED BY public.login.id_login;


--
-- TOC entry 235 (class 1259 OID 16638)
-- Name: menu; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.menu (
    id_menu integer NOT NULL,
    id_rol integer NOT NULL,
    "menú" character varying
);


ALTER TABLE public.menu OWNER TO postgres;

--
-- TOC entry 236 (class 1259 OID 16643)
-- Name: menu_id_menu_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.menu_id_menu_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.menu_id_menu_seq OWNER TO postgres;

--
-- TOC entry 5074 (class 0 OID 0)
-- Dependencies: 236
-- Name: menu_id_menu_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.menu_id_menu_seq OWNED BY public.menu.id_menu;


--
-- TOC entry 237 (class 1259 OID 16644)
-- Name: rol; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rol (
    id_rol integer NOT NULL,
    id_asignacion integer NOT NULL,
    tipo_rol character varying(30) NOT NULL,
    descripcion character varying(100) NOT NULL
);


ALTER TABLE public.rol OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 16647)
-- Name: rol_id_rol_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rol_id_rol_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rol_id_rol_seq OWNER TO postgres;

--
-- TOC entry 5075 (class 0 OID 0)
-- Dependencies: 238
-- Name: rol_id_rol_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.rol_id_rol_seq OWNED BY public.rol.id_rol;


--
-- TOC entry 239 (class 1259 OID 16648)
-- Name: tipo_tramite; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tipo_tramite (
    id_tipo_tramite integer NOT NULL,
    id_tramite integer NOT NULL,
    tipo_tramite character varying(100) NOT NULL
);


ALTER TABLE public.tipo_tramite OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 16651)
-- Name: tipo_tramite_id_tipo_tramite_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tipo_tramite_id_tipo_tramite_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tipo_tramite_id_tipo_tramite_seq OWNER TO postgres;

--
-- TOC entry 5076 (class 0 OID 0)
-- Dependencies: 240
-- Name: tipo_tramite_id_tipo_tramite_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tipo_tramite_id_tipo_tramite_seq OWNED BY public.tipo_tramite.id_tipo_tramite;


--
-- TOC entry 241 (class 1259 OID 16652)
-- Name: tramite; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tramite (
    id_tramite integer NOT NULL,
    id_documentacion integer NOT NULL,
    id_tipo_tramite integer NOT NULL,
    id_estado_tramite integer NOT NULL,
    descripcion character varying(100) NOT NULL,
    fecha_creacion date,
    fecha_limite date
);


ALTER TABLE public.tramite OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 16655)
-- Name: tramite_id_tramite_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tramite_id_tramite_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tramite_id_tramite_seq OWNER TO postgres;

--
-- TOC entry 5077 (class 0 OID 0)
-- Dependencies: 242
-- Name: tramite_id_tramite_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tramite_id_tramite_seq OWNED BY public.tramite.id_tramite;


--
-- TOC entry 4835 (class 2604 OID 16656)
-- Name: asignacion id_asignacion; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion ALTER COLUMN id_asignacion SET DEFAULT nextval('public.asignacion_id_asignacion_seq'::regclass);


--
-- TOC entry 4836 (class 2604 OID 16657)
-- Name: auditoria id_auditoria; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditoria ALTER COLUMN id_auditoria SET DEFAULT nextval('public.auditoria_id_auditoria_seq'::regclass);


--
-- TOC entry 4837 (class 2604 OID 16658)
-- Name: documentacion id_documentacion; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.documentacion ALTER COLUMN id_documentacion SET DEFAULT nextval('public.documentacion_id_documentacion_seq'::regclass);


--
-- TOC entry 4838 (class 2604 OID 16659)
-- Name: entidades id_entidades; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entidades ALTER COLUMN id_entidades SET DEFAULT nextval('public.entidades_id_entidades_seq'::regclass);


--
-- TOC entry 4839 (class 2604 OID 16660)
-- Name: estado_tramite id_estado_tramite; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.estado_tramite ALTER COLUMN id_estado_tramite SET DEFAULT nextval('public.estado_tramite_id_estado_tramite_seq'::regclass);


--
-- TOC entry 4842 (class 2604 OID 16661)
-- Name: login id_login; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.login ALTER COLUMN id_login SET DEFAULT nextval('public.login_id_login_seq'::regclass);


--
-- TOC entry 4843 (class 2604 OID 16662)
-- Name: menu id_menu; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.menu ALTER COLUMN id_menu SET DEFAULT nextval('public.menu_id_menu_seq'::regclass);


--
-- TOC entry 4844 (class 2604 OID 16663)
-- Name: rol id_rol; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rol ALTER COLUMN id_rol SET DEFAULT nextval('public.rol_id_rol_seq'::regclass);


--
-- TOC entry 4845 (class 2604 OID 16664)
-- Name: tipo_tramite id_tipo_tramite; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tipo_tramite ALTER COLUMN id_tipo_tramite SET DEFAULT nextval('public.tipo_tramite_id_tipo_tramite_seq'::regclass);


--
-- TOC entry 4846 (class 2604 OID 16665)
-- Name: tramite id_tramite; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tramite ALTER COLUMN id_tramite SET DEFAULT nextval('public.tramite_id_tramite_seq'::regclass);


--
-- TOC entry 5035 (class 0 OID 16594)
-- Dependencies: 218
-- Data for Name: asignacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.asignacion (id_asignacion, id_rol, id_tramite, estado_asignacion, fecha_asignacion) FROM stdin;
\.


--
-- TOC entry 5037 (class 0 OID 16598)
-- Dependencies: 220
-- Data for Name: auditoria; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.auditoria (id_auditoria, id_rol, tipo_auditoria) FROM stdin;
\.


--
-- TOC entry 5039 (class 0 OID 16604)
-- Dependencies: 222
-- Data for Name: documentacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.documentacion (id_documentacion, estado_documentacion, id_entidades, id_usuario) FROM stdin;
\.


--
-- TOC entry 5041 (class 0 OID 16608)
-- Dependencies: 224
-- Data for Name: entidades; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.entidades (id_entidades, id_bancos, nom_bancos, id_transito, nom_transito, id_camara_comercio, nom_camara_comercio) FROM stdin;
1	1	Banco Agrario	36	Secretaria de Transito y Transporte de Piedecuesta	35	Camara de Comercio de Bucaramanga
2	2	Banco AV Villas	37	Direccion de Transito de Giron	\N	\N
3	3	Banco Micro de la MicroFinanzas-BancaMia SA	38	Direccion de Transito de FloridaBlanca	\N	\N
4	4	Bancolombia	39	Direccion de Transito de Bucaramanga	\N	\N
5	5	Mi Banco	\N	\N	\N	\N
6	6	Banco Coomeva S.A	\N	\N	\N	\N
7	7	Banco BBVA	\N	\N	\N	\N
8	8	Banco Caja Social	\N	\N	\N	\N
9	9	Banco Colpatria	\N	\N	\N	\N
10	10	Banco Coopcentral	\N	\N	\N	\N
11	11	Banco Davivienda	\N	\N	\N	\N
12	12	Banco de Bogotá	\N	\N	\N	\N
13	13	Banco de Occidente	\N	\N	\N	\N
14	14	Banco Falabella	\N	\N	\N	\N
15	15	Banco Finandina S.A	\N	\N	\N	\N
16	16	Banco Itaú	\N	\N	\N	\N
17	17	Banco Mundo Mujer	\N	\N	\N	\N
18	18	Banco Pichincha	\N	\N	\N	\N
19	19	Banco Popular	\N	\N	\N	\N
20	20	Banco SerFinanza S.A	\N	\N	\N	\N
21	21	Banco Sudameris GNB	\N	\N	\N	\N
22	22	Banco W S.A	\N	\N	\N	\N
23	23	Coomuldesa	\N	\N	\N	\N
24	24	Financiera Comultrasan	\N	\N	\N	\N
25	25	Crezcamos S.A Compañia de Financiamiento	\N	\N	\N	\N
26	26	Financiera JurisdiCoop	\N	\N	\N	\N
27	27	Banco Santander	\N	\N	\N	\N
28	28	CitiBank Colombia	\N	\N	\N	\N
29	29	CorfiColombia S.A	\N	\N	\N	\N
30	30	ColtiFinanciera	\N	\N	\N	\N
31	31	NEQUI S.A	\N	\N	\N	\N
32	32	DaviPlata	\N	\N	\N	\N
33	33	Bancolombia Ahorro a la Mano	\N	\N	\N	\N
34	34	Dinero Móvil BBVA	\N	\N	\N	\N
\.


--
-- TOC entry 5043 (class 0 OID 16612)
-- Dependencies: 226
-- Data for Name: estado_tramite; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.estado_tramite (id_estado_tramite, estado_tramite) FROM stdin;
\.


--
-- TOC entry 5046 (class 0 OID 16617)
-- Dependencies: 229
-- Data for Name: funcionarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.funcionarios (id_funcionario, id_rol, tipo_rol, nombres, apellidos, num_telefono, correo, correoinstitucional, correorecuperacion, contrasenia) FROM stdin;
5	5	ABOGADO_3	Jhon	Nuñez	3005678901	sebastyam1921@gmail.com	jhon.nunez@sena.co	jhon.recupera@gmail.com	$2a$06$wsUM63MjPJRe1DJXObhLeunVZYcak9qwDIyQSYmL8djSemYeG7EMO
4	4	ABOGADO_2	Jhan	Uribe	3004567890	jhanfranco204@gmail.com	jhan.uribe@sena.co	jhan.recupera@gmail.com	$2a$06$Iuved.lkFxDPVUVTcs7VceQeYm8blSDwkDPZvzfYI0CzRyI61rl2G
3	3	ABOGADO_1	Maicol	Barajas	3002345678	maicolbarajas7@gmail.com	maicol.barajas@sena.co	maicol.recupera@gmail.com	$2y$10$2aDNv8hjZLUt1tU2lVis3eG9FM8uKTP6QEmtIc8KkqRzOl5x3vm6C
2	2	ADMIN_TRAMITE	Fabian	Peña	3001234567	fabianrey2710@gmail.com	fabian.pena@sena.co	fabian.recupera@gmail.com	$2y$10$rAVtPgGSf5uiNrho2I7JNe/0NAI8n4GnMdGuJmCqH4hUL77OLv03y
1	1	ADMIN	Jonathan	Gomez	3003456789	jygd.94@gmail.com	jonathan.gomez@sena.co	jonathan.recupera@gmail.com	$2y$10$OK2CST3sM7XoGWXIjrq6.OAdr/b6fwKwenoWDZcnvbBmrTpKfB1Wq
\.


--
-- TOC entry 5050 (class 0 OID 16632)
-- Dependencies: 233
-- Data for Name: login; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.login (id_login, id_rol, login) FROM stdin;
1	6	2025-04-26 20:31:38.412464+00
\.


--
-- TOC entry 5052 (class 0 OID 16638)
-- Dependencies: 235
-- Data for Name: menu; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.menu (id_menu, id_rol, "menú") FROM stdin;
\.


--
-- TOC entry 5054 (class 0 OID 16644)
-- Dependencies: 237
-- Data for Name: rol; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.rol (id_rol, id_asignacion, tipo_rol, descripcion) FROM stdin;
1	0	ADMIN	Control total del sistema cobro coactivo
2	1	ADMIN_TRAMITES	Asigna tramites
3	2	ABOGADO_1	Responsable de tramites y documentacion
4	3	ABOGADO_2	Responsable de tramites y documentacion
5	4	ABOGADO_3	Responsable de tramites y documentacion
6	-1	USUARIOS	Vista Usuario
\.


--
-- TOC entry 5056 (class 0 OID 16648)
-- Dependencies: 239
-- Data for Name: tipo_tramite; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tipo_tramite (id_tipo_tramite, id_tramite, tipo_tramite) FROM stdin;
\.


--
-- TOC entry 5058 (class 0 OID 16652)
-- Dependencies: 241
-- Data for Name: tramite; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tramite (id_tramite, id_documentacion, id_tipo_tramite, id_estado_tramite, descripcion, fecha_creacion, fecha_limite) FROM stdin;
\.


--
-- TOC entry 5048 (class 0 OID 16624)
-- Dependencies: 231
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuarios (id_usuario, nombres, apellidos, nit, cc, tipo_persona, correo, contrasenia, id_rol, tipo_rol) FROM stdin;
3	Maria Jose	Gonzalez Gomez	\N	1132700987	Natural	maria.jose@gmail.com	$2a$06$H53moDvH8obs3dG2BbmIhevaKnztYx8J5xbBtMm4pyaohHXGzxtBS	6	USUARIOS
2	Juan Juan	Perez Zerep	18362457-10	\N	Juridica	juan.juan@gmail.com	$2a$06$i9upEKShioQqf4fjEPNuSOSfZpOJraGTWzc6HElicSDRyNBLwFBUO	6	USUARIOS
1	Maria Martha	Gomez Gomez	\N	1132700123	Natural	maria.fake.martha@gmail.com	$2y$10$CHKzdHLn6p3QPBk/Pugx2..6IB8l0Ah7uURTXFUmr9sbix8DXAX7u	6	USUARIOS
\.


--
-- TOC entry 5078 (class 0 OID 0)
-- Dependencies: 219
-- Name: asignacion_id_asignacion_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.asignacion_id_asignacion_seq', 1, false);


--
-- TOC entry 5079 (class 0 OID 0)
-- Dependencies: 221
-- Name: auditoria_id_auditoria_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.auditoria_id_auditoria_seq', 1, false);


--
-- TOC entry 5080 (class 0 OID 0)
-- Dependencies: 223
-- Name: documentacion_id_documentacion_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.documentacion_id_documentacion_seq', 1, false);


--
-- TOC entry 5081 (class 0 OID 0)
-- Dependencies: 225
-- Name: entidades_id_entidades_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.entidades_id_entidades_seq', 1, false);


--
-- TOC entry 5082 (class 0 OID 0)
-- Dependencies: 227
-- Name: estado_tramite_id_estado_tramite_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.estado_tramite_id_estado_tramite_seq', 1, false);


--
-- TOC entry 5083 (class 0 OID 0)
-- Dependencies: 228
-- Name: funcionarios_id_funcionario_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.funcionarios_id_funcionario_seq', 5, true);


--
-- TOC entry 5084 (class 0 OID 0)
-- Dependencies: 232
-- Name: involucrados_id_involucrados_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.involucrados_id_involucrados_seq', 3, true);


--
-- TOC entry 5085 (class 0 OID 0)
-- Dependencies: 234
-- Name: login_id_login_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.login_id_login_seq', 1, false);


--
-- TOC entry 5086 (class 0 OID 0)
-- Dependencies: 236
-- Name: menu_id_menu_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.menu_id_menu_seq', 1, false);


--
-- TOC entry 5087 (class 0 OID 0)
-- Dependencies: 238
-- Name: rol_id_rol_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.rol_id_rol_seq', 1, true);


--
-- TOC entry 5088 (class 0 OID 0)
-- Dependencies: 240
-- Name: tipo_tramite_id_tipo_tramite_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tipo_tramite_id_tipo_tramite_seq', 1, false);


--
-- TOC entry 5089 (class 0 OID 0)
-- Dependencies: 242
-- Name: tramite_id_tramite_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tramite_id_tramite_seq', 1, false);


--
-- TOC entry 5090 (class 0 OID 0)
-- Dependencies: 230
-- Name: usuarios_id_usuario_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuarios_id_usuario_seq', 1, false);


--
-- TOC entry 4849 (class 2606 OID 16667)
-- Name: asignacion asignacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT asignacion_pkey PRIMARY KEY (id_asignacion);


--
-- TOC entry 4851 (class 2606 OID 16669)
-- Name: auditoria auditoria_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditoria
    ADD CONSTRAINT auditoria_pkey PRIMARY KEY (id_auditoria);


--
-- TOC entry 4853 (class 2606 OID 16671)
-- Name: documentacion documentacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.documentacion
    ADD CONSTRAINT documentacion_pkey PRIMARY KEY (id_documentacion);


--
-- TOC entry 4855 (class 2606 OID 16673)
-- Name: entidades entidades_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entidades
    ADD CONSTRAINT entidades_pkey PRIMARY KEY (id_entidades);


--
-- TOC entry 4857 (class 2606 OID 16675)
-- Name: estado_tramite estado_tramite_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.estado_tramite
    ADD CONSTRAINT estado_tramite_pkey PRIMARY KEY (id_estado_tramite);


--
-- TOC entry 4863 (class 2606 OID 16677)
-- Name: usuarios involucrados_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT involucrados_pkey PRIMARY KEY (id_usuario);


--
-- TOC entry 4869 (class 2606 OID 16679)
-- Name: login login_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.login
    ADD CONSTRAINT login_pkey PRIMARY KEY (id_login);


--
-- TOC entry 4871 (class 2606 OID 16681)
-- Name: menu menu_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.menu
    ADD CONSTRAINT menu_pkey PRIMARY KEY (id_menu);


--
-- TOC entry 4873 (class 2606 OID 16683)
-- Name: rol rol_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rol
    ADD CONSTRAINT rol_pkey PRIMARY KEY (id_rol);


--
-- TOC entry 4875 (class 2606 OID 16685)
-- Name: tipo_tramite tipo_tramite_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tipo_tramite
    ADD CONSTRAINT tipo_tramite_pkey PRIMARY KEY (id_tipo_tramite);


--
-- TOC entry 4877 (class 2606 OID 16687)
-- Name: tramite tramite_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tramite
    ADD CONSTRAINT tramite_pkey PRIMARY KEY (id_tramite);


--
-- TOC entry 4859 (class 2606 OID 16689)
-- Name: funcionarios unique_correo_correoinstitucional_correorecuperacion_contraseñ; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.funcionarios
    ADD CONSTRAINT "unique_correo_correoinstitucional_correorecuperacion_contraseñ" UNIQUE (correo, correoinstitucional, correorecuperacion, contrasenia);


--
-- TOC entry 4865 (class 2606 OID 16691)
-- Name: usuarios unique_nit_cc; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT unique_nit_cc UNIQUE (nit, cc);


--
-- TOC entry 4867 (class 2606 OID 16693)
-- Name: usuarios usuarios_correo_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_correo_key UNIQUE (correo);


--
-- TOC entry 4861 (class 2606 OID 16695)
-- Name: funcionarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.funcionarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id_funcionario);


--
-- TOC entry 4878 (class 2606 OID 16696)
-- Name: asignacion asignacion_id_rol_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT asignacion_id_rol_fkey FOREIGN KEY (id_rol) REFERENCES public.rol(id_rol) ON UPDATE CASCADE;


--
-- TOC entry 4879 (class 2606 OID 16701)
-- Name: asignacion asignacion_id_tramite_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT asignacion_id_tramite_fkey FOREIGN KEY (id_tramite) REFERENCES public.tramite(id_tramite) ON UPDATE CASCADE;


--
-- TOC entry 4880 (class 2606 OID 16706)
-- Name: auditoria auditoria_id_rol_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditoria
    ADD CONSTRAINT auditoria_id_rol_fkey FOREIGN KEY (id_rol) REFERENCES public.rol(id_rol);


--
-- TOC entry 4881 (class 2606 OID 16711)
-- Name: documentacion documentacion_id_entidades_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.documentacion
    ADD CONSTRAINT documentacion_id_entidades_fkey FOREIGN KEY (id_entidades) REFERENCES public.entidades(id_entidades) ON UPDATE CASCADE;


--
-- TOC entry 4882 (class 2606 OID 16716)
-- Name: documentacion documentacion_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.documentacion
    ADD CONSTRAINT documentacion_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id_usuario) ON UPDATE CASCADE;


--
-- TOC entry 4883 (class 2606 OID 16721)
-- Name: funcionarios funcionarios_id_rol_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.funcionarios
    ADD CONSTRAINT funcionarios_id_rol_fkey FOREIGN KEY (id_rol) REFERENCES public.rol(id_rol) ON UPDATE CASCADE;


--
-- TOC entry 4885 (class 2606 OID 16726)
-- Name: login login_id_rol_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.login
    ADD CONSTRAINT login_id_rol_fkey FOREIGN KEY (id_rol) REFERENCES public.rol(id_rol);


--
-- TOC entry 4886 (class 2606 OID 16731)
-- Name: menu menu_id_rol_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.menu
    ADD CONSTRAINT menu_id_rol_fkey FOREIGN KEY (id_rol) REFERENCES public.rol(id_rol) ON UPDATE CASCADE;


--
-- TOC entry 4887 (class 2606 OID 16736)
-- Name: tramite tramite_id_documentacion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tramite
    ADD CONSTRAINT tramite_id_documentacion_fkey FOREIGN KEY (id_documentacion) REFERENCES public.documentacion(id_documentacion) ON UPDATE CASCADE;


--
-- TOC entry 4888 (class 2606 OID 16741)
-- Name: tramite tramite_id_estado_tramite_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tramite
    ADD CONSTRAINT tramite_id_estado_tramite_fkey FOREIGN KEY (id_estado_tramite) REFERENCES public.estado_tramite(id_estado_tramite) ON UPDATE CASCADE;


--
-- TOC entry 4889 (class 2606 OID 16746)
-- Name: tramite tramite_id_tipo_tramite_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tramite
    ADD CONSTRAINT tramite_id_tipo_tramite_fkey FOREIGN KEY (id_tipo_tramite) REFERENCES public.tipo_tramite(id_tipo_tramite) ON UPDATE CASCADE;


--
-- TOC entry 4884 (class 2606 OID 16751)
-- Name: usuarios usuarios_id_rol_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_id_rol_fkey FOREIGN KEY (id_rol) REFERENCES public.rol(id_rol) ON UPDATE CASCADE;


-- Completed on 2025-05-26 19:13:02

--
-- PostgreSQL database dump complete
--

