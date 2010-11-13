--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'SQL_ASCII';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: attendance; Type: TABLE; Schema: public; Owner: dbuser; Tablespace: 
--

CREATE TABLE attendance (
    service timestamp without time zone NOT NULL,
    person integer NOT NULL,
    comment text,
    status character varying(15),
    contacted boolean
);


ALTER TABLE public.attendance OWNER TO dbuser;

--
-- Name: emails; Type: TABLE; Schema: public; Owner: dbuser; Tablespace: 
--

CREATE TABLE emails (
    person integer NOT NULL,
    address character varying(75),
    "primary" boolean
);


ALTER TABLE public.emails OWNER TO dbuser;

--
-- Name: groups; Type: TABLE; Schema: public; Owner: dbuser; Tablespace: 
--

CREATE TABLE groups (
    id integer NOT NULL,
    description character varying(50),
    head integer,
    level integer
);


ALTER TABLE public.groups OWNER TO dbuser;

--
-- Name: groups_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE groups_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.groups_id_seq OWNER TO dbuser;

--
-- Name: groups_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE groups_id_seq OWNED BY groups.id;


--
-- Name: member_types; Type: TABLE; Schema: public; Owner: dbuser; Tablespace: 
--

CREATE TABLE member_types (
    type character varying(15) NOT NULL
);


ALTER TABLE public.member_types OWNER TO dbuser;

--
-- Name: memberships; Type: TABLE; Schema: public; Owner: dbuser; Tablespace: 
--

CREATE TABLE memberships (
    person integer NOT NULL,
    "group" integer NOT NULL
);


ALTER TABLE public.memberships OWNER TO dbuser;

--
-- Name: people; Type: TABLE; Schema: public; Owner: dbuser; Tablespace: 
--

CREATE TABLE people (
    id integer NOT NULL,
    title character varying(5),
    first_name character varying(20),
    middle_initial character(1),
    last_name character varying(20),
    suffix character varying(5),
    residence integer,
    member_type character varying(15),
    head integer,
    gender character(1),
    birthdate date
);


ALTER TABLE public.people OWNER TO dbuser;

--
-- Name: people_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE people_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.people_id_seq OWNER TO dbuser;

--
-- Name: people_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE people_id_seq OWNED BY people.id;


--
-- Name: phone_types; Type: TABLE; Schema: public; Owner: dbuser; Tablespace: 
--

CREATE TABLE phone_types (
    type character varying(5) NOT NULL
);


ALTER TABLE public.phone_types OWNER TO dbuser;

--
-- Name: phones; Type: TABLE; Schema: public; Owner: dbuser; Tablespace: 
--

CREATE TABLE phones (
    person integer NOT NULL,
    phone_type character varying(5),
    phone_number character varying(10)
);


ALTER TABLE public.phones OWNER TO dbuser;

--
-- Name: residences; Type: TABLE; Schema: public; Owner: dbuser; Tablespace: 
--

CREATE TABLE residences (
    id integer NOT NULL,
    address1 character varying(80),
    address2 character varying(80),
    city character varying(20) DEFAULT 'Columbus'::character varying,
    state character varying(2) DEFAULT 'IN'::character varying,
    zip character varying(9),
    phone character varying(10)
);


ALTER TABLE public.residences OWNER TO dbuser;

--
-- Name: residences_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE residences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.residences_id_seq OWNER TO dbuser;

--
-- Name: residences_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE residences_id_seq OWNED BY residences.id;


--
-- Name: services; Type: TABLE; Schema: public; Owner: dbuser; Tablespace: 
--

CREATE TABLE services (
    service timestamp without time zone NOT NULL
);


ALTER TABLE public.services OWNER TO dbuser;

--
-- Name: titles; Type: TABLE; Schema: public; Owner: dbuser; Tablespace: 
--

CREATE TABLE titles (
    title character varying(5) NOT NULL
);


ALTER TABLE public.titles OWNER TO dbuser;

--
-- Name: users; Type: TABLE; Schema: public; Owner: dbuser; Tablespace: 
--

CREATE TABLE users (
    id integer NOT NULL,
    username character varying(16),
    password character varying(16),
    access integer,
    person integer
);


ALTER TABLE public.users OWNER TO dbuser;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO dbuser;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE groups ALTER COLUMN id SET DEFAULT nextval('groups_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE people ALTER COLUMN id SET DEFAULT nextval('people_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE residences ALTER COLUMN id SET DEFAULT nextval('residences_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Name: attendance_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY attendance
    ADD CONSTRAINT attendance_pkey PRIMARY KEY (service, person);


--
-- Name: emails_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_pkey PRIMARY KEY (person);


--
-- Name: groups_head_key; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY groups
    ADD CONSTRAINT groups_head_key UNIQUE (head, level);


--
-- Name: groups_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY groups
    ADD CONSTRAINT groups_pkey PRIMARY KEY (id);


--
-- Name: member_types_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY member_types
    ADD CONSTRAINT member_types_pkey PRIMARY KEY (type);


--
-- Name: memberships_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY memberships
    ADD CONSTRAINT memberships_pkey PRIMARY KEY (person, "group");


--
-- Name: people_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY people
    ADD CONSTRAINT people_pkey PRIMARY KEY (id);


--
-- Name: phone_types_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY phone_types
    ADD CONSTRAINT phone_types_pkey PRIMARY KEY (type);


--
-- Name: phones_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY phones
    ADD CONSTRAINT phones_pkey PRIMARY KEY (person);


--
-- Name: residences_address1_key; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY residences
    ADD CONSTRAINT residences_address1_key UNIQUE (address1, address2);


--
-- Name: residences_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY residences
    ADD CONSTRAINT residences_pkey PRIMARY KEY (id);


--
-- Name: services_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY services
    ADD CONSTRAINT services_pkey PRIMARY KEY (service);


--
-- Name: titles_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY titles
    ADD CONSTRAINT titles_pkey PRIMARY KEY (title);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: attendance_person_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY attendance
    ADD CONSTRAINT attendance_person_fkey FOREIGN KEY (person) REFERENCES people(id) ON DELETE CASCADE;


--
-- Name: attendance_service_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY attendance
    ADD CONSTRAINT attendance_service_fkey FOREIGN KEY (service) REFERENCES services(service) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: emails_person_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_person_fkey FOREIGN KEY (person) REFERENCES people(id);


--
-- Name: groups_head_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY groups
    ADD CONSTRAINT groups_head_fkey FOREIGN KEY (head) REFERENCES people(id);


--
-- Name: memberships_group_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY memberships
    ADD CONSTRAINT memberships_group_fkey FOREIGN KEY ("group") REFERENCES groups(id);


--
-- Name: memberships_person_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY memberships
    ADD CONSTRAINT memberships_person_fkey FOREIGN KEY (person) REFERENCES people(id);


--
-- Name: people_member_type_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY people
    ADD CONSTRAINT people_member_type_fkey FOREIGN KEY (member_type) REFERENCES member_types(type);


--
-- Name: people_residence_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY people
    ADD CONSTRAINT people_residence_fkey FOREIGN KEY (residence) REFERENCES residences(id) ON DELETE RESTRICT;


--
-- Name: people_title_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY people
    ADD CONSTRAINT people_title_fkey FOREIGN KEY (title) REFERENCES titles(title);


--
-- Name: phones_person_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY phones
    ADD CONSTRAINT phones_person_fkey FOREIGN KEY (person) REFERENCES people(id);


--
-- Name: phones_phone_type_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY phones
    ADD CONSTRAINT phones_phone_type_fkey FOREIGN KEY (phone_type) REFERENCES phone_types(type);


--
-- Name: users_person_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_person_fkey FOREIGN KEY (person) REFERENCES people(id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

