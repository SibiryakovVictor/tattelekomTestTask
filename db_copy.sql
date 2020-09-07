--
-- PostgreSQL database dump
--

-- Dumped from database version 11.7 (Ubuntu 11.7-0ubuntu0.19.10.1)
-- Dumped by pg_dump version 11.7 (Ubuntu 11.7-0ubuntu0.19.10.1)

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

--
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO postgres;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- Name: direction_designation; Type: TYPE; Schema: public; Owner: vsib
--

CREATE TYPE public.direction_designation AS ENUM (
    '1_2',
    '2_1'
);


ALTER TYPE public.direction_designation OWNER TO vsib;

--
-- Name: count_duplicated_edges(character varying, smallint, smallint); Type: FUNCTION; Schema: public; Owner: vsib
--

CREATE FUNCTION public.count_duplicated_edges(g_name character varying, first_id smallint, second_id smallint) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
RETURN (SELECT COUNT(*) FROM edge e WHERE g_name=e.graph_name AND first_id=e.second_vertex_id AND second_id=e.first_vertex_id); 
END;
$$;


ALTER FUNCTION public.count_duplicated_edges(g_name character varying, first_id smallint, second_id smallint) OWNER TO vsib;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: edge; Type: TABLE; Schema: public; Owner: vsib
--

CREATE TABLE public.edge (
    graph_name character varying(20) NOT NULL,
    start_vertex_id smallint NOT NULL,
    end_vertex_id smallint NOT NULL,
    weight integer NOT NULL,
    CONSTRAINT positive_end_id CHECK ((end_vertex_id > 0)),
    CONSTRAINT positive_start_id CHECK ((start_vertex_id > 0)),
    CONSTRAINT positive_weight CHECK ((weight > 0))
);


ALTER TABLE public.edge OWNER TO vsib;

--
-- Name: graph; Type: TABLE; Schema: public; Owner: vsib
--

CREATE TABLE public.graph (
    name character varying(20) NOT NULL,
    CONSTRAINT name_length CHECK ((char_length((name)::text) >= 6))
);


ALTER TABLE public.graph OWNER TO vsib;

--
-- Name: vertex; Type: TABLE; Schema: public; Owner: vsib
--

CREATE TABLE public.vertex (
    graph_name character varying(20) NOT NULL,
    id smallint NOT NULL,
    CONSTRAINT positive_id CHECK ((id > 0))
);


ALTER TABLE public.vertex OWNER TO vsib;

--
-- Data for Name: edge; Type: TABLE DATA; Schema: public; Owner: vsib
--

COPY public.edge (graph_name, start_vertex_id, end_vertex_id, weight) FROM stdin;
graph_test_name	5	1	10
graph_test_name	1	11	5
graph_test_name	11	1	3
graph_test_name	1	7	8
graph_test_name	7	11	1
graph_test_name	11	7	70
\.


--
-- Data for Name: graph; Type: TABLE DATA; Schema: public; Owner: vsib
--

COPY public.graph (name) FROM stdin;
graph_test_name
\.


--
-- Data for Name: vertex; Type: TABLE DATA; Schema: public; Owner: vsib
--

COPY public.vertex (graph_name, id) FROM stdin;
graph_test_name	1
graph_test_name	5
graph_test_name	7
graph_test_name	10
graph_test_name	11
\.


--
-- Name: edge edge_pkey; Type: CONSTRAINT; Schema: public; Owner: vsib
--

ALTER TABLE ONLY public.edge
    ADD CONSTRAINT edge_pkey PRIMARY KEY (graph_name, start_vertex_id, end_vertex_id);


--
-- Name: graph graph_pkey; Type: CONSTRAINT; Schema: public; Owner: vsib
--

ALTER TABLE ONLY public.graph
    ADD CONSTRAINT graph_pkey PRIMARY KEY (name);


--
-- Name: vertex vertex_pkey; Type: CONSTRAINT; Schema: public; Owner: vsib
--

ALTER TABLE ONLY public.vertex
    ADD CONSTRAINT vertex_pkey PRIMARY KEY (graph_name, id);


--
-- Name: edge edge_graph_name_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vsib
--

ALTER TABLE ONLY public.edge
    ADD CONSTRAINT edge_graph_name_fkey FOREIGN KEY (graph_name, start_vertex_id) REFERENCES public.vertex(graph_name, id) ON DELETE CASCADE;


--
-- Name: edge edge_graph_name_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: vsib
--

ALTER TABLE ONLY public.edge
    ADD CONSTRAINT edge_graph_name_fkey1 FOREIGN KEY (graph_name, end_vertex_id) REFERENCES public.vertex(graph_name, id) ON DELETE CASCADE;


--
-- Name: vertex vertex_graph_name_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vsib
--

ALTER TABLE ONLY public.vertex
    ADD CONSTRAINT vertex_graph_name_fkey FOREIGN KEY (graph_name) REFERENCES public.graph(name) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

