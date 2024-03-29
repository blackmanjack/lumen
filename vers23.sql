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
1	perftest	faiz_m17@apps.ipb.ac.id	$2y$10$DctfQ3cMOQhEpubaGc1j3eVPiEBOv/45craI1IDDzfwTQsoDNvmPO	cGVyZnRlc3Q6cGVyZnRlc3Q=	t	f	2023-01-23 08:56:05	2023-01-23 08:56:05
2	cwqzhckw09@example.com	dx07uz	cc5be9f73e1895a1fa411902d0141048d4cc7c1625652555ab581c062fa6cccb	t	72da64d6ad4f5cc333d751f668bdd9b0674f8ecf8cf68dfa4e24ccebe25baee8	f
3	6q6ynk1zrx@example.com	z0gz8l	e988042fa61742daad83b8b08929945971c35cad96ec4ea5ca6c5f846b310da1	t	a310f6399b4a61287532089d4030d9a314bcbe46df4c55dad9cd200214d80755	f
1	bintangf00code@gmail.com	perftest	3c31bc6fa467cea84245bf86d594f17936880674f320b94b2cef9f73ac71e51f	t	dbb68d97021afbdb7bf0f2beb87705ecd9073a5737a7ced8c9be4680ee9d3549	f
4	7d6k3cd39u@example.com	dff7m2	0377f75479c1af00999db0b54c16002a8928f4455eac26ed2d874aac3d8c4018	t	75bf9cabe64508a6b01e909bf3ab90c7507e9d77544af68c3cceaf5809ffeb09	f
5	nu39kwq0kt@example.com	9lt90g	e6f4a0ef1f97361cd3a4d75aa97389c04139205c68680e54aa178627d2e05b6b	t	5315f2b697e8ed6742de42ca3a53e17e817227480e1e172fa97b5ca0cbb70805	f
6	4hhhqfmqgf@example.com	nmtwlk	ba3446e0f62eebe299f0b2ad597c3243d90e6f0611e50618d3de6b73b2cd919c	t	e6cd13039cbee7ed1ad20c9ed1f6cc4ffe9ad0a1f98a60ff17e9d85e6501ba21	f
7	y0jgci6igk@example.com	9532is	8399c4de7db741fd5ce6e017273320e5b338a5698b321080a52217acee87dde9	t	4c6d7e1aef5dfda66f07fa9748daa324a111ac290f3daf4f2cda9160e4d8b547	f
8	ryzu6jguk6@example.com	qiouqn	fe117b58d921a0424196f8127eec6a251e6f472aba556a284812ad92e7e5a226	t	54e396d9c3fc82242ffd90dc6e7f02dfd52f71d1f01c53039ca2dea0c01d1dad	f
9	780qno6nf5@example.com	wdh36k	06dbcfeba1291a7ebc3972d9a61705e34a86cd591e08fcdfb0187463bfd19de9	t	cef081c04cf6f304c3e70d43b0e52ee26d23bb07fa490b6fe4d1feed8b523e26	f
10	y330htnqf3@example.com	5k8j91	236cce6f5bef215e2ff3caebbf5b9e87797ce557eec34090f723a3e6a938684c	t	77dc78328e27237d5614f7c4802dffce1a0f68c6f5132f1a9f947401e54aa27b	f
11	d6mmvw9xd5@example.com	f19x5i	864f5fcd83b85cdfa06e988a3c41af0d2b63d104eadd91bbaddcfcb259426d56	t	d35b8a5a5d7e680ec5b69178306b78241473e29360a3de08f10ee6b20a1b221b	f
12	ns2uqli97s@example.com	o38la7	88938418782aec8cd139d93930c8bf178f76087f4cac7981155c4e0dce7ebc27	t	52b82f3885f2eaefb0a06667af8b14cc81410db28984c61f2b64652c490e7bd3	f
13	r2320502he@example.com	f2mh38	52f5e5fde9b8e9727ff1bb1e7666b9ddc2da44802bd4294598a1f01853d72835	t	9ef619b5e3bae2f91a9de420c391345d6f727377fc59306ae567538f1405d8a0	f
14	63kkwevoc5@example.com	qc7v9d	8486666b6fd05a299ff1f2647c3c749479dd46b1bc465deed00cf8fa2b7c6658	t	47b6a6a6f1f6dc0a105440e3b144ff857c415fd2ddc8bc4d7e52233548294aac	f
15	nevx0jejq8@example.com	bhhlv8	2fc6d38c87478cfcff7f77e99b56bfb96478bc48f036dd42049c2c759ab24b02	t	1295779478cee26bb6a2c0209796db06d787db432f5e618bc7ca34133e123abf	f
16	ivrkqfi95k@example.com	r15v13	6dc60b451e38ee7a5445f0855e07b8cf4ddeb76b3167f197b699adb654ba42f6	t	dfc7af0d523720f7da68c42322e1303b6645e03b63f4cbc52e30c2389405bf73	f
17	qt9mb1824j@example.com	c6dbfh	5160457d6340a161e7526e632334601256a1b74eac3edd5499346cd0b720ae7a	t	db495c78064f13078d485cde23b87608be305c0b06084191c2c815191d4b7320	f
18	j1yevgnxqx@example.com	9a4dwu	d07af3b7339a85bfd8845649f6b5e8e8cd0715b1b823af542d5e7c4f718d3559	t	615815310dfd70757f65f0b272085654abfbf639daebe5c08bb9c972c98ba900	f
19	nc8cjckgzw@example.com	lvsm5o	ec75b56396f7e146aeefa64636236d57c0142168aba285d1af69a4822bf7de86	t	b76ae57154f62a34c8cf98ddb8dbf4bbad11ff8dc1a23b301c6c332afa49f51c	f
20	rln19b0v6c@example.com	3puivv	5fdaf1b132970b38ae352c244526830fd8f8ffa271785260824b44b1b53a038a	t	a5757908298bbffe041da3d9724d66c9a2caf2c6a89c35a906708fb2e0a1ad0f	f
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

SELECT pg_catalog.setval('public.user_person_id_seq', 1, true);


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

