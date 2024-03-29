--
-- PostgreSQL database dump
--

-- Dumped from database version 15.1
-- Dumped by pg_dump version 15.1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: channel; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.channel (
    id bigint NOT NULL,
    value double precision NOT NULL,
    id_sensor bigint
);


ALTER TABLE public.channel OWNER TO postgres;

--
-- Name: channel_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.channel_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.channel_id_seq OWNER TO postgres;

--
-- Name: channel_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.channel_id_seq OWNED BY public.channel.id;


--
-- Name: hardware; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.hardware (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    type character varying(255) NOT NULL,
    description character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.hardware OWNER TO postgres;

--
-- Name: hardware_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.hardware_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.hardware_id_seq OWNER TO postgres;

--
-- Name: hardware_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.hardware_id_seq OWNED BY public.hardware.id;


--
-- Name: nodes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.nodes (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    location character varying(255) NOT NULL,
    id_user bigint,
    id_hardware bigint NOT NULL
);


ALTER TABLE public.nodes OWNER TO postgres;

--
-- Name: nodes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.nodes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.nodes_id_seq OWNER TO postgres;

--
-- Name: nodes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.nodes_id_seq OWNED BY public.nodes.id;


--
-- Name: sensor; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sensor (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    unit character varying(255) NOT NULL,
    id_node bigint,
    id_hardware bigint NOT NULL
);


ALTER TABLE public.sensor OWNER TO postgres;

--
-- Name: sensor_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sensor_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sensor_id_seq OWNER TO postgres;

--
-- Name: sensor_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sensor_id_seq OWNED BY public.sensor.id;


--
-- Name: user_person; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.user_person (
    id bigint NOT NULL,
    username character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    status boolean DEFAULT false NOT NULL,
    is_admin boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.user_person OWNER TO postgres;

--
-- Name: user_person_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_person_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_person_id_seq OWNER TO postgres;

--
-- Name: user_person_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.user_person_id_seq OWNED BY public.user_person.id;


--
-- Name: channel id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.channel ALTER COLUMN id SET DEFAULT nextval('public.channel_id_seq'::regclass);


--
-- Name: hardware id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.hardware ALTER COLUMN id SET DEFAULT nextval('public.hardware_id_seq'::regclass);


--
-- Name: nodes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nodes ALTER COLUMN id SET DEFAULT nextval('public.nodes_id_seq'::regclass);


--
-- Name: sensor id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sensor ALTER COLUMN id SET DEFAULT nextval('public.sensor_id_seq'::regclass);


--
-- Name: user_person id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_person ALTER COLUMN id SET DEFAULT nextval('public.user_person_id_seq'::regclass);


--
-- Data for Name: channel; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.channel (id, value, id_sensor) FROM stdin;
\.


--
-- Data for Name: hardware; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.hardware (id, name, type, description, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: nodes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.nodes (id, name, location, id_user, id_hardware) FROM stdin;
\.


--
-- Data for Name: sensor; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sensor (id, name, unit, id_node, id_hardware) FROM stdin;
\.


--
-- Data for Name: user_person; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.user_person (id, username, email, password, token, status, is_admin, created_at, updated_at) FROM stdin;
\.


--
-- Name: channel_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.channel_id_seq', 1, false);


--
-- Name: hardware_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.hardware_id_seq', 1, false);


--
-- Name: nodes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.nodes_id_seq', 1, false);


--
-- Name: sensor_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sensor_id_seq', 1, false);


--
-- Name: user_person_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_person_id_seq', 1, false);


--
-- Name: channel channel_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.channel
    ADD CONSTRAINT channel_pkey PRIMARY KEY (id);


--
-- Name: hardware hardware_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.hardware
    ADD CONSTRAINT hardware_pkey PRIMARY KEY (id);


--
-- Name: nodes nodes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nodes
    ADD CONSTRAINT nodes_pkey PRIMARY KEY (id);


--
-- Name: sensor sensor_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sensor
    ADD CONSTRAINT sensor_pkey PRIMARY KEY (id);


--
-- Name: user_person user_person_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_person
    ADD CONSTRAINT user_person_email_unique UNIQUE (email);


--
-- Name: user_person user_person_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_person
    ADD CONSTRAINT user_person_pkey PRIMARY KEY (id);


--
-- Name: user_person user_person_username_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_person
    ADD CONSTRAINT user_person_username_unique UNIQUE (username);


--
-- Name: channel channel_id_sensor_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.channel
    ADD CONSTRAINT channel_id_sensor_foreign FOREIGN KEY (id_sensor) REFERENCES public.sensor(id);


--
-- Name: nodes nodes_id_hardware_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nodes
    ADD CONSTRAINT nodes_id_hardware_foreign FOREIGN KEY (id_hardware) REFERENCES public.hardware(id);


--
-- Name: nodes nodes_id_user_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nodes
    ADD CONSTRAINT nodes_id_user_foreign FOREIGN KEY (id_user) REFERENCES public.user_person(id);


--
-- Name: sensor sensor_id_hardware_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sensor
    ADD CONSTRAINT sensor_id_hardware_foreign FOREIGN KEY (id_hardware) REFERENCES public.hardware(id);


--
-- Name: sensor sensor_id_node_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sensor
    ADD CONSTRAINT sensor_id_node_foreign FOREIGN KEY (id_node) REFERENCES public.nodes(id);


--
-- PostgreSQL database dump complete
--

